<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    private $user;

    /**
     * @var Fiddle
     *
     * @ORM\ManyToOne(targetEntity="Fiddle")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", nullable=true, onDelete="cascade")
     * @ORM\Id
     */
    private $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var array[UserTag]
     *
     * @ORM\OneToMany(targetEntity="UserTag", mappedBy="fiddle_id")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="user_id", referencedColumnName="user_id"),
     *    @ORM\JoinColumn(name="fiddle_id", referencedColumnName="fiddle_id")
     * })
     */
    protected $tags;

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
     * @param FiddleTag $tags
     * @return Fiddle
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return FiddleTag
     */
    public function getTags()
    {
        return $this->tags;
    }

}
