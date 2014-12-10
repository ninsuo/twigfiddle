<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $hasUser = $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($hasUser)
        {
            return $this->redirect($this->getRequest()->headers->get('referer'));
        }
        else
        {
            return $this->forward('HWIOAuthBundle:Connect:connect');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        $this->container->get('security.context')->setToken(null);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}
