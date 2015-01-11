<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\Fiddle;
use Fuz\AppBundle\Entity\UserBookmark;
use Fuz\AppBundle\Form\UserBookmarkType;

class FiddleController extends BaseController
{

    /**
     * Runs a fiddle
     *
     * @Route(
     *      "/run/{revision}/{hash}",
     *      name = "run_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Method({"POST"})
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @return JsonResponse
     */
    public function runAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function(Fiddle $fiddle)
           {
               $response = array ();

               $result = $this->get('app.run_fiddle')->run($fiddle);

               $response['result'] = $this
                  ->get('templating')
                  ->render('FuzAppBundle:Fiddle:result.html.twig', array ('data' => $result))
               ;

               if ($result->getResult() && $result->getResult()->getContext())
               {
                   $response['context'] = $this
                      ->get('templating')
                      ->render('FuzAppBundle:Fiddle:result-context.html.twig', array ('data' => $result))
                   ;
               }

               return $response;
           });
    }

    /**
     * Saves a fiddle
     *
     * @Route(
     *      "/save/{revision}/{hash}",
     *      name = "save_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 0
     *      }
     * )
     * @Method({"POST"})
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @return JsonResponse
     */
    public function saveAction(Request $request, $hash, $revision)
    {
        $fiddle = null;
        if ($revision > 0)
        {
            $fiddle = $this->getFiddle($hash, $revision);
            $bookmark = $this->getUserBookmark($fiddle);
            if ($bookmark)
            {
                return $this->saveUserBookmark($request, $bookmark);
            }
        }

        return $this->validateAjaxFiddle($request, $hash, $revision,
              function (Fiddle $fiddle) use ($request, $hash, $revision)
           {
               $user = $this->getUser();
               $saveService = $this->get('app.save_fiddle');

               if (is_null($fiddle->getId()) || !$saveService->ownsFiddle($fiddle, $user))
               {
                   $revision = 0;
               }

               if ($fiddle->getId())
               {
                   $hash = $fiddle->getHash();
               }

               if (!$saveService->validateHash($hash))
               {
                   $hash = null;
               }

               $originalId = $fiddle->getId();

               $saved = $saveService->save($hash, $revision, $fiddle, $user);
               $saveService->saveFiddleToSession($saved->getId(), $user);

               if ($originalId !== $saved->getId())
               {
                   $url = $this->generateUrl('fiddle',
                      array ('hash' => $saved->getHash(), 'revision' => $saved->getRevision()));

                   return array ('relocate' => $url);
               }
           }, $fiddle);
    }

    /**
     * Bookmark / Unbookmark a fiddle
     *
     * @Route(
     *      "/bookmark/{revision}/{hash}",
     *      name = "bookmark_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "revision" = "^\d+$"
     *      }
     * )
     * @Method({"POST"})
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @return JsonResponse
     */
    public function bookmarkAction(Request $request, $hash, $revision)
    {
        $response = array (
                'isBookmarked' => false,
        );

        $user = $this->getUser();

        if ((!$request->isXmlHttpRequest()) || (is_null($user)))
        {
            return new RedirectResponse($this->generateUrl('fiddle', $response, Response::HTTP_PRECONDITION_REQUIRED));
        }

        $fiddle = $this->getFiddle($hash, $revision);

        if ($this->get('app.save_fiddle')->ownsFiddle($fiddle, $user))
        {
            return new JsonResponse($response);
        }

        $bookmarkRepo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:UserBookmark')
        ;

        $em = $this->getDoctrine()->getManager();

        $old = $bookmarkRepo->getBookmark($fiddle, $user);
        if ($old)
        {
            $em->remove($old);
            $em->flush();
            return new JsonResponse($response);
        }

        $new = new UserBookmark();
        $new->setUser($user);
        $new->setFiddle($fiddle);

        return $this->saveUserBookmark($request, $new);
    }

    protected function saveUserBookmark(Request $request, UserBookmark $bookmark)
    {
        $response = array (
                'isBookmarked' => false,
        );

        $form = $this->createForm(new UserBookmarkType(), $bookmark,
           array (
                'data_object' => $bookmark,
        ));

        $form->handleRequest($request);

        if (!$form->isValid())
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
            return new JsonResponse($response);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($bookmark);
        $em->flush();

        $response['isBookmarked'] = true;
        return new JsonResponse($response);
    }

    /**
     * Fiddle's samples
     *
     * @Template()
     * @return array
     */
    public function samplesAction()
    {
        $webConfig = $this->container->getParameter('web');

        return array (
                'categories' => $webConfig['samples'],
        );
    }

    /**
     * Displays twigfiddle's editor
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Method({"GET"})
     * @Template()
     * @param string|null $hash
     * @param int $revision
     * @return array
     */
    public function indexAction($hash, $revision)
    {
        $fiddleRepo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
        ;

        $user = $this->getUser();
        $fiddle = $this->getFiddle($hash, $revision);
        $fiddleRepo->incrementVisitCount($fiddle);

        $bookmark = $this->getUserBookmark($fiddle);
        if ($bookmark)
        {
            $fiddle->mapBookmark($bookmark);
        }

        $form = $this->createForm('FiddleType', $fiddle);

        return array (
                'form' => $form->createView(),
                'data' => $fiddle,
                'hash' => $hash,
                'revision' => $revision,
                'canSave' => $this->get('app.save_fiddle')->canClickSave($fiddle, $user),
                'revisionBrowser' => $fiddleRepo->getRevisionList($fiddle, $user),
                'bookmark' => $bookmark,
        );
    }

    /**
     * Performs a callback if Fiddle's form is properly submitted and valid
     *
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @param callable $onValid
     * @param Fiddle|null $fiddle
     * @return RedirectResponse|JsonResponse
     */
    protected function validateAjaxFiddle(Request $request, $hash, $revision, $onValid, Fiddle $fiddle = null)
    {
        $response = array ();

        if (!$request->isXmlHttpRequest())
        {
            return new RedirectResponse($this->generateUrl('fiddle', $response, Response::HTTP_PRECONDITION_REQUIRED));
        }

        if (is_null($fiddle))
        {
            $fiddle = $this->getFiddle($hash, $revision);
        }

        $form = $this->createForm('FiddleType', $fiddle, array (
                'data_object' => $fiddle,
        ));
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $result = $onValid($fiddle);
            if (is_array($result))
            {
                $response = array_merge($response, $result);
            }
        }
        else
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
        }

        return new JsonResponse($response);
    }

}
