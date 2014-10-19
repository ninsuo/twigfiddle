<?php

namespace Fuz\AppBundle\Model;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use JMS\DiExtraBundle\Annotation\Service;
use JMS\DiExtraBundle\Annotation\Inject;
use Fuz\AppBundle\Entity\User;

/**
 * @Service("app.oauth_user_provider")
 */
class OAuthUserProvider extends BaseUserProvider
{

    /**
     * @Inject("session");
     */
    public $session;

    /**
     * @Inject("doctrine.orm.entity_manager")
     */
    public $em;

    public function loadUserByUsername($username)
    {
        if (!is_null($this->session->get('user')))
        {
            $username = $this->session->get('user');
        }
        if (is_null($username))
        {
            return null;
        }
        list($resourceOwner, $resourceOwnerId) = json_decode($username);
        return $this->em->getRepository('FuzAppBundle:User')->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwner = $response->getResourceOwner()->getName();
        $resourceOwnerId = $response->getUsername();
        $name = $this->getNameToDisplay($resourceOwner, $response);

        $user = $this->em->getRepository('FuzAppBundle:User')->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);
        if (is_null($user))
        {
            $user = new User();
            $user->setResourceOwner($resourceOwner);
            $user->setResourceOwnerId($resourceOwnerId);
            $user->setUsername($name);
            $user->setSigninCount(1);
            $this->em->persist($user);
            $this->em->flush();
        }
        else
        {
            $user->setSigninCount($user->getSigninCount() + 1);
            $this->em->persist($user);
            $this->em->flush();
        }

        $json = json_encode(array($resourceOwner, $resourceOwnerId));
        $this->session->set('user', $json);
        return $this->loadUserByUsername($json);
    }

    public function getNameToDisplay($resourceOwner, $response)
    {
        $name = null;
        switch ($resourceOwner)
        {
            case 'google':
                $name = $response->getNickname();
                break;
            case 'facebook':
                $name = $response->getRealName();
                break;
            case 'twitter':
                $name = $response->getNickname();
                break;
            case 'sensio_connect':
                $name = $response->getNickname();
                break;
            default:
                break;
        }
        return $name;
    }

    public function supportsClass($class)
    {
        return $class === 'Fuz\\AppBundle\\Entity\\User';
    }

}
