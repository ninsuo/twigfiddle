<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;

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
        return array (
                'tag' => $tag,
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
