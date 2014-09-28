<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
/**
 * User
 *
 * @ORM\Table(
 *      name="user",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="provider_idx", columns={"provider", "provider_id"})}
 * )
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Entity\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, EquatableInterface
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="provider", type="string", length=16)
     */
    protected $provider;

    /**
     * @var string
     *
     * @ORM\Column(name="provider_id", type="string", length=255)
     */
    protected $providerId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_seen", type="datetime")
     */
    protected $lastSeen;

    /**
     * @var integer
     *
     * @ORM\Column(name="signin_count", type="integer")
     */
    protected $signinCount = 0;

    /**
     * @var array[UserPreference]
     *
     * @ORM\OneToMany(targetEntity="UserPreference", mappedBy="user_id")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $preferences;

    /**
     * @var array[UserFavorite]
     *
     * @ORM\OneToMany(targetEntity="UserFavorite", mappedBy="user_id")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    protected $favorites;

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
     * Set providerId
     *
     * @param string $providerId
     * @return User
     */
    public function setProviderId($providerId)
    {
        $this->providerId = $providerId;

        return $this;
    }

    /**
     * Get providerId
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->providerId;
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
     * {@inheritDoc}
     */
    public function getUsername()
    {
        return $this->username;
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

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setLastSeen(new \DateTime());
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        return array ('ROLE_USER');
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        return true;
    }

    public function isEqualTo(UserInterface $user)
    {
        if ((int) $this->getId() === $user->getId())
        {
            return true;
        }

        return false;
    }

}
