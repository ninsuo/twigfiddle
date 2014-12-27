<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Fuz\AppBundle\Base\BaseController;

class LoginController extends BaseController
{

    /**
     * @Route("/login", name="login")
     * @Method({"GET"})
     */
    public function loginAction()
    {
        if ($this->getUser())
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
     * @Method({"GET"})
     */
    public function logoutAction()
    {
        $this->container->get('security.context')->setToken(null);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}
