<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\Browse;
use Fuz\AppBundle\Form\BrowseType;

class BrowseController extends BaseController
{

    /**
     * Loads fiddle's browser
     *
     * @Route(
     *      "/{tag}",
     *      name = "browse",
     *      defaults = {
     *          "tag" = null,
     *      }
     * )
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction($tag)
    {
        $form = $this->createForm(new BrowseType(), new Browse());

        return array (
                'tag' => $tag,
                'form' => $form->createView(),
        );
    }

    /**
     * Searches for results
     *
     * @Route(
     *      "/search/{tag}",
     *      name = "search",
     *      defaults = {
     *          "tag" = null,
     *      }
     * )
     * @Template("FuzAppBundle:Browse:results.html.twig")
     */
    public function searchAction($tag)
    {
        return array (
                'tag' => $tag,
        );
    }

}
