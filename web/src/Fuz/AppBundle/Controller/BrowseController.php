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
        list($data, $form) = $this->createBrowseForm($tag);

        return array (
                'tag' => $tag,
                'form' => $form->createView(),
                'data' => $data,
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

    protected function createBrowseForm($tag)
    {
        $data = new Browse();
        if (!is_null($tag))
        {
            $data->setTags(array($tag));
        }
        $form = $this->createForm(new BrowseType(), $data);
        return array($data, $form);
    }

}
