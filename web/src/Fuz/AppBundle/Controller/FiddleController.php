<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
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
     * @Template("FuzAppBundle:Fiddle:index.html.twig")
     */
    public function runAction(Request $request, $hash, $revision)
    {
        $repository = $this->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle');

        $fiddleData = $repository->getFiddle($hash, $revision, $this->getUser());
        $fiddleForm = $this->createForm('FiddleType', $fiddleData);
        $fiddleForm->handleRequest($request);

        if ($fiddleForm->isValid())
        {
            // ...
        }

        return array (
                'fiddleForm' => $fiddleForm->createView(),
                'fiddleData' => $fiddleData,
                'hash' => $hash,
                'revision' => $revision,
        );
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

        $fiddleData = $repository->getFiddle($hash, $revision, $this->getUser());
        $fiddleForm = $this->createForm('FiddleType', $fiddleData);

        return array (
                'fiddleForm' => $fiddleForm->createView(),
                'fiddleData' => $fiddleData,
                'hash' => $hash,
                'revision' => $revision,
        );
    }

}
