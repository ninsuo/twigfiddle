<?php

namespace Fuz\AppBundle\Connect;

use Symfony\Component\Security\Core\User\UserInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use JMS\DiExtraBundle\Annotation\Service;

/**
 * @Service("hwi_oauth.account.connector")
 */
class AccountConnect implements AccountConnectorInterface
{

    public function connect(UserInterface $user, UserResponseInterface $response)
    {

    }

}
