<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;

class FiddleController extends BaseController
{

    /**
     * Home
     *
     * Displays twigfiddle's editor
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "home",
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
        $fiddle = $repository->getFiddle($hash, $revision, $this->getUser());


        return array ();
    }

}
