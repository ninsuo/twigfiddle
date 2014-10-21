<?php

namespace Fuz\AppBundle\Connect;

use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountConnect implements AccountConnectorInterface
{

    public function connect(UserInterface $user, UserResponseInterface $response)
    {

    }

}
