<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class FiddleController extends Controller
{

    /**
     * @param string $fiddleHash
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction($fiddleHash = null)
    {
        return array ();
    }


}