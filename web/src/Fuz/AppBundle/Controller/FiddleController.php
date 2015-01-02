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
use Fuz\AppBundle\Entity\UserFavorite;
use Fuz\AppBundle\Form\UserFavoriteType;

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
                  ->render('FuzAppBundle:Fiddle:result-pane.html.twig', array ('data' => $result))
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
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function (Fiddle $fiddle) use ($request, $hash, $revision)
           {
               $user = $this->getUser();

               $fav = $this
                  ->getDoctrine()
                  ->getRepository('FuzAppBundle:UserFavorite')
                  ->getFavorite($fiddle, $user)
               ;

               if ($fav)
               {
                   // todo
                   return $this->saveUserFavorite($request, $fav);
               }

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
           });
    }

    /**
     * Mark / Unmark a fiddle as favorite
     *
     * @Route(
     *      "/fav/{revision}/{hash}",
     *      name = "fav_fiddle",
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
    public function favAction(Request $request, $hash, $revision)
    {
        $response = array (
                'isFavorite' => false,
        );

        $user = $this->getUser();

        if ((!$request->isXmlHttpRequest()) || (is_null($user)))
        {
            return new RedirectResponse($this->generateUrl('fiddle', $response, Response::HTTP_PRECONDITION_REQUIRED));
        }

        $fiddle = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision, $user)
        ;

        if ($this->get('app.save_fiddle')->ownsFiddle($fiddle, $user))
        {
            return new JsonResponse($response);
        }

        $favRepo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:UserFavorite')
        ;

        $em = $this->getDoctrine()->getManager();

        $old = $favRepo->getFavorite($fiddle, $user);
        if ($old)
        {
            $em->remove($old);
            $em->flush();
            return new JsonResponse($response);
        }

        $new = new UserFavorite();
        $new->setUser($user);
        $new->setFiddle($fiddle);

        $saved = $this->saveUserFavorite($request, $new);
        return new JsonResponse($saved);
    }

    protected function saveUserFavorite(Request $request, UserFavorite $fav)
    {
        $response = array (
                'isFavorite' => false
        );

        $form = $this->createForm(new UserFavoriteType(), $fav, array (
                'data_object' => $fav,
        ));

        $form->handleRequest($request);

        if (!$form->isValid())
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($fav);
        $em->flush();

        $response['isFavorite'] = true;
        return $response;
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

        $favRepo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:UserFavorite')
        ;

        $user = $this->getUser();
        $fiddle = $fiddleRepo->getFiddle($hash, $revision, $user);
        $fiddleRepo->incrementVisitCount($fiddle);

        $form = $this->createForm('FiddleType', $fiddle);

        return array (
                'form' => $form->createView(),
                'data' => $fiddle,
                'hash' => $hash,
                'revision' => $revision,
                'canSave' => $this->get('app.save_fiddle')->canClickSave($fiddle, $user),
                'revisionBrowser' => $fiddleRepo->getRevisionList($fiddle, $user),
                'favorite' => $favRepo->getFavorite($fiddle, $user),
        );
    }

    /**
     * Validates fiddle's form and if valid, performs a callback
     *
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @param callable $onValid
     * @return RedirectResponse|JsonResponse
     * @return JsonResponse
     */
    protected function validateAjaxFiddle(Request $request, $hash, $revision, $onValid)
    {
        $response = array (
                'hash' => $hash,
                'revision' => $revision,
        );

        if (!$request->isXmlHttpRequest())
        {
            return new RedirectResponse($this->generateUrl('fiddle', $response, Response::HTTP_PRECONDITION_REQUIRED));
        }

        $fiddle = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision, $this->getUser())
        ;

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
