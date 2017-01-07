<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Model;

use Fuz\AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider as BaseUserProvider;

class OAuthUserProvider extends BaseUserProvider
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        list($resourceOwner, $resourceOwnerId) = json_decode($username, true);

        $user = $this->em->getRepository('FuzAppBundle:User')
           ->getUserByResourceOwnerId($resourceOwner, $resourceOwnerId);

        return $user;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwner = $response->getResourceOwner()->getName();
        $resourceOwnerId = $response->getUsername();
        $name = $this->getNameToDisplay($resourceOwner, $response);
        $json = json_encode([$resourceOwner, $resourceOwnerId]);
        $user = $this->loadUserByUsername($json);

        $reload = false;
        if (is_null($user)) {
            $user = new User();
            $user->setResourceOwner($resourceOwner);
            $user->setResourceOwnerId($resourceOwnerId);
            $user->setUsername($json);
            $user->setNickname($name);
            $user->setSigninCount(1);
            $this->em->persist($user);
            $this->em->flush($user);
            $reload = true;
        } else {
            $user->setNickname($name);
            $user->setSigninCount($user->getSigninCount() + 1);
            $this->em->persist($user);
            $this->em->flush($user);
        }

        if ($reload) {
            return $this->loadUserByUsername($json);
        }

        return $user;
    }

    public function getNameToDisplay($resourceOwner, $response)
    {
        $name = null;
        switch ($resourceOwner) {
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
            case 'github':
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
