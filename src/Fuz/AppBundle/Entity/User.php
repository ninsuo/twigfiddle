<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=16)
     */
    private $provider;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_seen", type="datetime")
     */
    private $lastSeen;

    /**
     * @var integer
     *
     * @ORM\Column(name="signin_count", type="integer")
     */
    private $signinCount = 0;

    /**
     * @var array[UserPreference]
     *
     * @ORM\OneToMany(targetEntity="UserPreference", mappedBy="user_id")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    private $preferences;

    /**
     * @var array[UserFavorite]
     *
     * @ORM\OneToMany(targetEntity="UserFavorite", mappedBy="user_id")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    private $favorites;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set provider
     *
     * @param string $provider
     * @return User
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set lastSeen
     *
     * @param \DateTime $lastSeen
     * @return User
     */
    public function setLastSeen($lastSeen)
    {
        $this->lastSeen = $lastSeen;

        return $this;
    }

    /**
     * Get lastSeen
     *
     * @return \DateTime
     */
    public function getLastSeen()
    {
        return $this->lastSeen;
    }

    /**
     * Set signinCount
     *
     * @param integer $signinCount
     * @return User
     */
    public function setSigninCount($signinCount)
    {
        $this->signinCount = $signinCount;

        return $this;
    }

    /**
     * Get signinCount
     *
     * @return integer
     */
    public function getSigninCount()
    {
        return $this->signinCount;
    }

    /**
     * Set preferences
     *
     * @param array[UserPreference] $preferences
     * @return User
     */
    public function setPreferences(array $preferences)
    {
        $this->preferences = $preferences;

        return $this;
    }

    /**
     * Get preferences
     *
     * @return array[UserPreference]
     */
    public function getPreferences()
    {
        return $this->preferences;
    }

    /**
     * Set favorites
     *
     * @param array[UserFavorite] $favorites
     * @return User
     */
    public function setFavorites(array $favorites)
    {
        $this->favorites = $favorites;

        return $this;
    }

    /**
     * Get favorites
     *
     * @return array[UserFavorite]
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setLastSeen(new \DateTime());
    }

}
