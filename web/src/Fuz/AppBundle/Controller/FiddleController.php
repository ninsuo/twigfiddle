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
use Fuz\AppBundle\Entity\FiddleTag;

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
              function(Fiddle $data)
           {
               $response = array ();

               $result = $this->get('app.run_fiddle')->run($data);

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
              function (Fiddle $data) use ($hash, $revision)
           {
               $saveService = $this->get('app.save_fiddle');
               $user = $this->getUser();

               if (is_null($data->getId()) || !$saveService->ownsFiddle($data, $user))
               {
                   $revision = 0;
               }

               if ($data->getId())
               {
                   $hash = $data->getHash();
               }

               if (!$saveService->validateHash($hash))
               {
                   $hash = null;
               }

               $originalId = $data->getId();

               $saved = $saveService->save($hash, $revision, $data, $user);
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
        $repo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
        ;

        $user = $this->getUser();
        $data = $repo->getFiddle($hash, $revision, $user);
        $repo->incrementVisitCount($data);

        $form = $this->createForm('FiddleType', $data);

        return array (
                'form' => $form->createView(),
                'data' => $data,
                'hash' => $hash,
                'revision' => $revision,
                'canSave' => $this->get('app.save_fiddle')->canClickSave($data, $user),
                'revisionBrowser' => $repo->getRevisionList($data, $user),
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

        $data = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision, $this->getUser())
        ;

        $form = $this->createForm('FiddleType', $data, array (
                'data_object' => $data,
        ));
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $result = $onValid($data);
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
