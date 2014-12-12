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
     * Displays the twigfiddle editor
     *
     * @param string $hash
     * @param int $version
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction($hash = null, $version = 1)
    {

        $repository = $this->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle');
        $fiddle = $repository->getFiddle($hash, $version, $this->getUser());


        return array ();
    }

}
