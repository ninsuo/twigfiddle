<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\BrowseFilters;
use Fuz\AppBundle\Form\BrowseFiltersType;

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
    public function indexAction(Request $request, $tag)
    {
        list($data, $form) = $this->createBrowseForm($request, $tag);

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
    public function searchAction(Request $request, $tag)
    {
        list($data, $form) = $this->createBrowseForm($request, $tag);

        $list = array();
        if ($form->isSubmitted() && !$form->isValid())
        {
            $data = new BrowseFilters();
        }

        $list = $this->get('app.search_fiddle')->search($data, $this->getUser());

        return array (
                'tag' => $tag,
                'form' => $form->createView(),
                'data' => $data,
                'list' => $list,
        );
    }

    protected function createBrowseForm(Request $request, $tag)
    {
        $data = new BrowseFilters();
        if (!is_null($tag))
        {
            $data->setTags(array ($tag));
        }
        $form = $this->createForm(new BrowseFiltersType(), $data);
        $form->handleRequest($request);
        return array ($data, $form);
    }

}
