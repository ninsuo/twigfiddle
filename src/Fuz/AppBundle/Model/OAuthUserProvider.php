<?php

namespace Fuz\AppBundle\Model;

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Fuz\AppBundle\Entity\User;

class OAuthUserProvider extends BaseUserProvider
{

    protected $session;
    protected $em;

    public function __construct($session, $em)
    {
        $this->session = $session;
        $this->em = $em;
    }

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
        list($provider, $providerId) = json_decode($username);
        return $this->em->getRepository('FuzAppBundle:User')->getUserByProviderId($provider, $providerId);
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $provider = $response->getResourceOwner()->getName();
        $providerId = $response->getUsername();
        $nickname = $this->getNickname($provider, $response);

        $user = $this->em->getRepository('FuzAppBundle:User')->getUserByProviderId($provider, $providerId);
        if (is_null($user))
        {
            $user = new User();
            $user->setProvider($provider);
            $user->setProviderId($providerId);
            $user->setUsername($nickname);
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

        $json = json_encode(array($provider, $providerId));
        $this->session->set('user', $json);
        return $this->loadUserByUsername($json);
    }

    public function getNickname($provider, $response)
    {
        $nickname = null;
        switch ($provider)
        {
            case 'google':
                $nickname = $response->getNickname();
                break;
            default:
                break;
        }
        return $nickname;
    }

    public function supportsClass($class)
    {
        return $class === 'Fuz\\AppBundle\\Entity\\User';
    }

}
