<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * fiddle.hash_regexp
     *
     * @Route(
     *      "/run/{revision}/{hash}",
     *      name = "run_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,32}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Method({"POST"})
     */
    public function runAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function(Fiddle $fiddle) use ($request)
           {
               $response = array ();

               $this->detachFiddle($fiddle);

               if (!$this->get('app.captcha')->check($request, 'run'))
               {
                   $response['captcha'] = true;
                   return $response;
               }

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
     * fiddle.hash_regexp
     *
     * @Route(
     *      "/save/{revision}/{hash}",
     *      name = "save_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,32}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 0
     *      }
     * )
     * @Method({"POST"})
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
               $response = array ();

               $user = $this->getUser();
               $saveService = $this->get('app.save_fiddle');

               if (is_null($fiddle->getId()) || !$saveService->ownsFiddle($fiddle, $user))
               {
                   $this->detachFiddle($fiddle);
                   $revision = 0;
               }

               if (!$revision && !$this->get('app.captcha')->check($request, 'save'))
               {
                   $this->detachFiddle($fiddle);
                   $response['captcha'] = true;
                   return $response;
               }

               if ($fiddle->getId())
               {
                   $hash = $fiddle->getHash();
               }

               if (!$saveService->validateHash($hash))
               {
                   $this->detachFiddle($fiddle);
                   $hash = null;
               }

               $originalId = $fiddle->getId();

               $saved = $saveService->save($hash, $revision, $fiddle, $user);
               $saveService->saveFiddleToSession($saved->getId(), $user);

               if ($originalId !== $saved->getId())
               {
                   $response['relocate'] = $this->generateUrl('fiddle',
                      array ('hash' => $saved->getHash(), 'revision' => $saved->getRevision()));

                   return $response;
               }
           }, $fiddle);
    }

    protected function detachFiddle(Fiddle $fiddle)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $em->detach($fiddle);
        if ($fiddle->getContext())
        {
            $em->detach($fiddle->getContext());
        }
        $em->detach($fiddle->getTemplates());
        $em->detach($fiddle->getTags());
        if ($fiddle->getUser() && $this->getUser() && !$fiddle->getUser()->isEqualTo($this->getUser()))
        {
            $em->detach($fiddle->getUser());
        }
    }

    /**
     * Bookmark / Unbookmark a fiddle
     *
     * fiddle.hash_regexp
     *
     * @Route(
     *      "/bookmark/{revision}/{hash}",
     *      name = "bookmark_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,32}$",
     *          "revision" = "^\d+$"
     *      }
     * )
     * @Method({"POST"})
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
     * fiddle.hash_regexp
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,32}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Method({"GET"})
     * @Template()
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
                'canSave' => $this->get('app.save_fiddle')->canSaveFiddle($fiddle, $user),
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
            $this->detachFiddle($fiddle);
            $errors = $this->getErrorMessagesAjaxFormat($form);
            if (!array_key_exists('#', $errors))
            {
                $plurial = count($errors) > 1 ? 's' : '';
                $errors['#'] = array (
                        "Form contains error{$plurial}, please check messages below involved field{$plurial}.",
                );
            }
            $response['errors'] = $errors;
        }

        return new JsonResponse($response);
    }

}
