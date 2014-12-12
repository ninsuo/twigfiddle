<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;

class AboutController extends BaseController
{

    /**
     * @Route("/about", name="about")
     * @Template()
     */
    public function indexAction()
    {
        return array ();
    }

}