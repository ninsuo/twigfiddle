<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * UserFavorite
 *
 * @ORM\Table(name="user_favorite")
 * @ORM\Entity
 */
class UserFavorite
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", cascade="remove", inversedBy="favorites")
     */
    protected $user;

    /**
     * @var Fiddle
     *
     * @ORM\ManyToOne(targetEntity="Fiddle", cascade="remove")
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var ArrayCollection[UserFavoriteTag]
     *
     * @ORM\OneToMany(targetEntity="UserFavoriteTag", mappedBy="userFavorite")
     */
    protected $tags;

    /**
     * Set id
     *
     * @param int $id
     * @return UserFavorite
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return UserFavorite
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set fiddle
     *
     * @param Fiddle $fiddle
     * @return UserFavorite
     */
    public function setFiddle(Fiddle $fiddle)
    {
        $this->fiddle = $fiddle;

        return $this;
    }

    /**
     * Get fiddle
     *
     * @return Fiddle
     */
    public function getFiddle()
    {
        return $this->fiddle;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return UserFavorite
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set tags
     *
     * @param ArrayCollection[UserFavoriteTag] $tags
     * @return UserFavorite
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return ArrayCollection[UserFavoriteTag]
     */
    public function getTags()
    {
        return $this->tags;
    }

}
