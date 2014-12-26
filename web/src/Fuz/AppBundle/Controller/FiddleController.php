<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\AppBundle\Base\BaseController;
use Fuz\AppBundle\Entity\Fiddle;

class FiddleController extends BaseController
{

    /**
     * Runs a fiddle
     *
     * @Route(
     *      "/run/{revision}/{hash}",
     *      name = "run_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @return JsonResponse
     */
    public function runAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function(Fiddle $data)
           {
               $response = array ();

               $result = $this->get('app.run_fiddle')->run($data);

               $response['result'] = $this
                  ->get('templating')
                  ->render('FuzAppBundle:Fiddle:result-pane.html.twig', array ('data' => $result))
               ;

               if ($result->getResult() && $result->getResult()->getContext())
               {
                   $response['context'] = $this
                      ->get('templating')
                      ->render('FuzAppBundle:Fiddle:result-context.html.twig', array ('data' => $result))
                   ;
               }

               return $response;
           });
    }

    /**
     * Saves a fiddle
     *
     * @Route(
     *      "/save/{revision}/{hash}",
     *      name = "save_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 0
     *      }
     * )
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @return JsonResponse
     */
    public function saveAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function (Fiddle $data) use ($hash, $revision)
           {
               $originalId = $data->getId();

               if (is_null($data->getId()) || !$this->canSave($data))
               {
                   $revision = 0;
               }

               if ($data->getId())
               {
                   $hash = $data->getHash();
               }

               if (!$this->validateHash($hash))
               {
                   $hash = null;
               }

               $saved = $this
                  ->get('app.helper.fiddle_helper')
                  ->save($hash, $revision, $data, $this->getUser())
               ;

               $this->saveFiddleToSession($saved->getId());

               if ($originalId !== $saved->getId())
               {
                   $url = $this->generateUrl('fiddle',
                      array ('hash' => $saved->getHash(), 'revision' => $saved->getRevision()));

                   return array ('relocate' => $url);
               }
           });
    }

    /**
     * Displays twigfiddle's editor
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9-]{1,16}$",
     *          "revision" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Template()
     * @param string|null $hash
     * @param int $revision
     * @return array
     */
    public function indexAction($hash, $revision)
    {
        $repo = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
        ;

        $data = $repo->getFiddle($hash, $revision, $this->getUser());
        $repo->incrementVisitCount($data);

        $form = $this->createForm('FiddleType', $data);

        return array (
                'form' => $form->createView(),
                'data' => $data,
                'hash' => $hash,
                'revision' => $revision,
                'canSave' => $this->canSave($data),
        );
    }

    /**
     * Validates fiddle's form and if valid, performs a callback
     *
     * @param Request $request
     * @param string|null $hash
     * @param int $revision
     * @param callable $onValid
     * @return RedirectResponse|JsonResponse
     * @return JsonResponse
     */
    protected function validateAjaxFiddle(Request $request, $hash, $revision, $onValid)
    {
        $response = array (
                'hash' => $hash,
                'revision' => $revision,
        );

        if (!$request->isXmlHttpRequest())
        {
            return new RedirectResponse($this->generateUrl('fiddle', $response, Response::HTTP_PRECONDITION_REQUIRED));
        }

        $data = $this
           ->getDoctrine()
           ->getRepository('FuzAppBundle:Fiddle')
           ->getFiddle($hash, $revision, $this->getUser())
        ;

        $form = $this->createForm('FiddleType', $data);
        $form->handleRequest($request);

        if ($form->isValid())
        {
            $result = $onValid($data);
            if (is_array($result))
            {
                $response = array_merge($response, $result);
            }
        }
        else
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
        }

        return new JsonResponse($response);
    }

    /**
     * Checks whether user can save current fiddle's revision.
     *
     * @param Fiddle $fiddle
     * @return bool
     */
    protected function canSave(Fiddle $fiddle)
    {
        if (is_null($fiddle->getId()))
        {
            return true;
        }

        if ($fiddle->getUser() && $this->getUser() && $fiddle->getUser()->isEqualTo($this->getUser()))
        {
            return true;
        }

        $session = $this->get('session');
        if ($session->has('recent-fiddles') && in_array($fiddle->getId(), $session->get('recent-fiddles')))
        {
            return true;
        }

        return false;
    }

    /**
     * Checks whether a hash can be used to reference a fiddle
     *
     * @param string|null $hash
     * @return bool
     */
    protected function validateHash($hash)
    {
        if (is_null($hash))
        {
            return false;
        }

        if (!preg_match('/^[a-zA-Z0-9-]{1,16}$/', $hash))
        {
            return false;
        }

        $routes = $this->get('router')->getRouteCollection();
        $reserved = array ();
        foreach ($routes->getIterator() as $route)
        {
            $path = substr($route->getPath(), 1);
            if (false !== strpos($path, '/'))
            {
                $path = substr($path, 0, strpos($path, '/'));
            }
            if (!in_array($path, $reserved))
            {
                $reserved[] = $path;
            }
        }
        if (in_array($hash, $reserved))
        {
            return false;
        }

        return true;
    }

    /**
     * Used to let unregistered users update recent fiddle's revisions they own.
     *
     * @param int $id
     */
    protected function saveFiddleToSession($id)
    {
        if (!is_null($this->getUser()))
        {
            return;
        }
        $session = $this->get('session');
        if (!$session->has('recent-fiddles'))
        {
            $session->set('recent-fiddles', array ($id));
        }
        else
        {
            $web = $this->container->getParameter('web');
            $list = array_merge($session->get('recent-fiddles'), array ($id));
            $session->set('recent-fiddles', array_slice($list, 0, $web['max_fiddles_in_session']));
        }
    }

}
