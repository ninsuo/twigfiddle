<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\BrowseFilters;
use Fuz\AppBundle\Form\BrowseFiltersType;
use Fuz\AppBundle\Entity\UserBookmark;
use Fuz\AppBundle\Form\UserBookmarkType;

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
        list($data) = $this->createBrowseFilters($request, $tag);

        return array (
                'tag' => $tag,
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
     * @Template()
     */
    public function searchAction(Request $request, $tag)
    {
        list($data, $filters) = $this->createBrowseFilters($request, $tag);

        if ($filters->isSubmitted() && !$filters->isValid())
        {
            $data = new BrowseFilters();
        }

        $fiddles = $this->get('app.search_fiddle')->search($data, $this->getUser());

        $list_left = $list_right = array ();
        foreach ($fiddles as $key => $fiddle)
        {
            if ($key % 2)
            {
                $list_right[$key] = $fiddle;
            }
            else
            {
                $list_left[$key] = $fiddle;
            }
        }

        return array (
                'tag' => $tag,
                'filters' => $filters->createView(),
                'list_left' => $list_left,
                'list_right' => $list_right,
        );
    }

    protected function createBrowseFilters(Request $request, $tag)
    {
        $data = new BrowseFilters();
        if (!is_null($tag))
        {
            $data->setTags(array ($tag));
        }
        $filters = $this->createForm(new BrowseFiltersType(), $data);
        $filters->handleRequest($request);
        return array ($data, $filters);
    }

    /**
     * Runs a fiddle
     *
     * @Route(
     *      "/result/{revision}/{hash}",
     *      name = "result_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "version" = "^\d+$"
     *      }
     * )
     * @Template()
     */
    public function resultAction($hash, $revision)
    {
        $fiddle = $this->getFiddle($hash, $revision);

        $bookmark = $this->getUserBookmark($fiddle);
        if ($bookmark)
        {
            $fiddle->mapBookmark($bookmark);
        }

        $bookmarkData = new UserBookmark();
        $bookmarkData->mapFiddle($fiddle);

        $form = $this->createForm(new UserBookmarkType(), $bookmarkData)->createView();

        return array(
                'fiddle' => $fiddle,
                'form' => $form,
        );
    }

}
