<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Fuz\AppBundle\Base\BaseController;

class LoginController extends BaseController
{

    /**
     * @Route("/login", name="login")
     * @Method({"GET"})
     */
    public function loginAction(Request $request)
    {
        if ($this->getUser())
        {
            return $this->redirect($request->headers->get('referer'));
        }
        else
        {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
    }

    /**
     * @Route("/logout", name="logout")
     * @Method({"GET"})
     */
    public function logoutAction(Request $request)
    {
        $this->container->get('security.context')->setToken(null);
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/connect/{service}", name="connect")
     * @Method({"GET"})
     */
    public function connectAction(Request $request, $service)
    {
        $this->get('session')->set('referer', $request->headers->get('referer'));
        return $this->forward('HWIOAuthBundle:Connect:redirectToService', array('service' => $service));
    }

    /**
     * @Route("/welcome", name="welcome")
     * @Method({"GET"})
     */
    public function welcomeAction()
    {
        $referer = $this->get('session')->get('referer');
        if (is_null($referer))
        {
            return new RedirectResponse($this->generateUrl('fiddle'));
        }
        return new RedirectResponse($referer);
    }

}
