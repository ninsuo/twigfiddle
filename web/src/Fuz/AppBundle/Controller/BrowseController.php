<?php

namespace Fuz\AppBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\BrowseFilters;
use Fuz\AppBundle\Form\BrowseFiltersType;
use Fuz\AppBundle\Entity\UserBookmark;
use Fuz\AppBundle\Entity\UserBookmarkTag;
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
     * @Template("FuzAppBundle:Browse:results.html.twig")
     */
    public function searchAction(Request $request, $tag)
    {
        list($data, $filters) = $this->createBrowseFilters($request, $tag);

        if ($filters->isSubmitted() && !$filters->isValid())
        {
            $data = new BrowseFilters();
        }

        $fiddles = $this->get('app.search_fiddle')->search($data, $this->getUser());
        $forms = $this->createBookmarkFormsFromFiddles($fiddles);

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
                'forms' => $forms,
                'data' => $data,
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
     * Yes. That's a hack.
     *
     * We can't use a collection as we'll use FuzAppBundle:Fiddle:save to
     * update a bookmark.
     *
     * @param array $fiddles
     * @return array
     */
    protected function createBookmarkFormsFromFiddles(array $fiddles)
    {
        $bookmarkForms = array ();
        foreach ($fiddles as $key => $fiddle)
        {
            $bookmarkData = new UserBookmark();
            $bookmarkData->setTitle($fiddle->getTitle());

            $tags = new ArrayCollection();
            foreach ($fiddle->getTags() as $fiddleTag)
            {
                $tag = new UserBookmarkTag();
                $tag->setTag($fiddleTag->getTag());
                $tags->add($tag);
            }
            $bookmarkData->setTags($tags);

            $bookmarkForms[$key] = $this->createForm(new UserBookmarkType($key), $bookmarkData)->createView();
        }
        return $bookmarkForms;
    }

}
