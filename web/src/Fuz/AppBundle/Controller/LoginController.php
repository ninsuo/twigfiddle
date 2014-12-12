<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Fuz\AppBundle\Base\BaseController;

class LoginController extends BaseController
{

    /**
     * @Route("/login", name="login")
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
     */
    public function logoutAction()
    {
        $this->container->get('security.context')->setToken(null);
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

}
