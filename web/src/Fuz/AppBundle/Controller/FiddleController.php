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
     *      "/run/{hash}/{revision}",
     *      name = "run_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
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
     *      "/save/{hash}/{revision}",
     *      name = "save_fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     */
    public function saveAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision,
              function (Fiddle $data)
           {
               if (!$this->canSave($data))
               {
                   return $this->forward('FuzAppBundle:Fiddle:saveAsNewRevision',
                         array (
                              'hash' => $data->getHash(),
                              'revision' => $data->getRevision(),
                   ));
               }

               $id = $this
                  ->getDoctrine()
                  ->getRepository('FuzAppBundle:Fiddle')
                  ->saveFiddle($data, $this->getUser())
               ;

               $this->saveFiddleToSession($id);
           });
    }

    /**
     * Saves a fiddle as a new revision
     *
     * @Route(
     *      "/save-as-new-revision/{hash}/{revision}",
     *      name = "save_fiddle_as_new_revision",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     */
    public function saveAsNewRevisionAction(Request $request, $hash, $revision)
    {
        return $this->validateAjaxFiddle($request, $hash, $revision, function (Fiddle $data)
           {

           });
    }

    /**
     * Fiddle's loader
     *
     * Displays twigfiddle's editor
     *
     * @Route(
     *      "/{hash}/{revision}",
     *      name = "fiddle",
     *      requirements = {
     *          "hash" = "^[a-zA-Z0-9]{1,16}$",
     *          "version" = "^\d+$"
     *      },
     *      defaults = {
     *          "hash" = null,
     *          "revision" = 1
     *      }
     * )
     * @Template()
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

    protected function validateAjaxFiddle(Request $request, $hash, $revision, $callable)
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
            $response = array_merge($response, $callable($data));
        }
        else
        {
            $response['errors'] = $this->getErrorMessagesAjaxFormat($form);
        }

        return new JsonResponse($response);
    }

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

    protected function saveFiddleToSession($id)
    {
        $session = $this->get('session');
        if (!$session->has('recent-fiddles'))
        {
            $session->set('recent-fiddles', array ($id));
        }
        else
        {
            $session->set('recent-fiddles', array_merge($session->get('recent-fiddles'), array ($id)));
        }
    }

}
