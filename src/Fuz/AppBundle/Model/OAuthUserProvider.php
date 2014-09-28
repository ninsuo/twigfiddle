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
        $name = $this->getName($provider, $response);

//        echo '<pre>';
//        echo "username = ", $response->getUsername(), PHP_EOL;
//        echo "nickname = ", $response->getNickname(), PHP_EOL;
//        echo "realname = ", $response->getRealName(), PHP_EOL;
//        echo "email = ", $response->getUsername(), PHP_EOL;
//        echo "picture = ", $response->getProfilePicture(), PHP_EOL;
//        die();

        $user = $this->em->getRepository('FuzAppBundle:User')->getUserByProviderId($provider, $providerId);
        if (is_null($user))
        {
            $user = new User();
            $user->setProvider($provider);
            $user->setProviderId($providerId);
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

        $json = json_encode(array($provider, $providerId));
        $this->session->set('user', $json);
        return $this->loadUserByUsername($json);
    }

    public function getName($provider, $response)
    {
        $name = null;
        switch ($provider)
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
