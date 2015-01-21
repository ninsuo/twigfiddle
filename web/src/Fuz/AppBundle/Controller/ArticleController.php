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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;

class ArticleController extends BaseController
{

    /**
     * @Route("/about", name="about")
     * @Route("/help", name="help")
     * @Template()
     */
    public function aboutAction()
    {
        return array ();
    }

}