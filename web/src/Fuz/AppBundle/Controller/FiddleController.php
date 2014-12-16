<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;

class FiddleController extends BaseController
{

    /**
     * Fiddle's runner
     *
     * Validates and run a fiddle
     *
     * @Route(
     *      "/run/{hash}/{revision}",
     *      name = "run_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     */
    public function runAction(Request $request, $hash, $revision)
    {
        if (!$request->isXmlHttpRequest())
        {
            return new RedirectResponse($this->generateUrl('fiddle',
                  array (
                       'hash' => $hash,
                       'revision' => $revision
                  ), Response::HTTP_PRECONDITION_REQUIRED));
        }

        $response = array (
                'hash' => $hash,
                'revision' => $revision,
        );

        $repository = $this->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle');

        $data = $repository->getFiddle($hash, $revision, $this->getUser());
        $form = $this->createForm('FiddleType', $data);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            // ...
        }
        else
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
        }

        return new JsonResponse($response);
    }

    /**
     * Fiddle's loader
     *
     * Displays twigfiddle's editor
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Template()
     */
    public function indexAction($hash, $revision)
    {
        $repository = $this->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle');

        $data = $repository->getFiddle($hash, $revision, $this->getUser());
        $form = $this->createForm('FiddleType', $data);

        return array (
                'form' => $form->createView(),
                'data' => $data,
                'hash' => $hash,
                'revision' => $revision,
        );
    }

}
