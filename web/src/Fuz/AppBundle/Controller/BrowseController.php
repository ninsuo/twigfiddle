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

use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\BrowseFilters;
use Fuz\AppBundle\Entity\UserBookmark;
use Fuz\AppBundle\Form\BrowseFiltersType;
use Fuz\AppBundle\Form\UserBookmarkType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class BrowseController extends BaseController
{
    /**
     * Searches for results.
     *
     * @Route(
     *      "/search",
     *      name = "browse_search"
     * )
     * @Template()
     */
    public function searchAction(Request $request)
    {
        list($data, $filters) = $this->createBrowseFilters($request);

        if ($request->getMethod() === 'GET') {
            $this->get('app.paginator')->reset('browseFiddles');
        }

        if ($filters->isSubmitted() && !$filters->isValid()) {
            $data = new BrowseFilters();
        }

        list($pagination, $fiddles) = $this->get('app.search_fiddle')->search($request, $data, $this->getUser());

        $list_left = $list_right = [];
        foreach ($fiddles as $key => $fiddle) {
            if ($key % 2) {
                $list_right[] = $fiddle;
            } else {
                $list_left[] = $fiddle;
            }
        }

        return [
                'filters'    => $filters->createView(),
                'list_left'  => $list_left,
                'list_right' => $list_right,
                'pagination' => $pagination,
        ];
    }

    /**
     * Displays a fiddle widget.
     *
     * fiddle.hash_regexp
     *
     * @Route(
     *      "/result/{revision}/{hash}",
     *      name = "browse_result",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,128}$",
     *          "version" = "^\d+$"
     *      }
     * )
     * @Template()
     */
    public function resultAction(Request $request, $hash, $revision)
    {
        $fiddle = $this->getFiddle($hash, $revision);

        $bookmark = $this->getUserBookmark($fiddle);
        if ($bookmark) {
            $fiddle->mapBookmark($bookmark);
        }

        $bookmarkData = new UserBookmark();
        $bookmarkData->mapFiddle($fiddle);

        $form = $this->createForm(UserBookmarkType::class, $bookmarkData)->createView();

        return [
                'fiddle'   => $fiddle,
                'form'     => $form,
                'bookmark' => $bookmark,
                'isAjax'   => $request->isXmlHttpRequest(),
        ];
    }

    /**
     * Loads fiddle's browser.
     *
     * @Route("/", name = "browse")
     * @Method({"GET"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        list($data) = $this->createBrowseFilters($request);

        return [
                'data' => $data,
        ];
    }

    protected function createBrowseFilters(Request $request)
    {
        $data    = new BrowseFilters();
        $filters = $this->createForm(BrowseFiltersType::class, $data);
        $filters->handleRequest($request);

        return [$data, $filters];
    }
}
