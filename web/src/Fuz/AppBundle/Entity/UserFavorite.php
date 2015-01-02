<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Fuz\AppBundle\Api\TagContainerInterface;

/**
 * UserFavorite
 *
 * @ORM\Table(name="user_favorite")
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\UserFavoriteRepository")
 */
class UserFavorite implements TagContainerInterface
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $user;

    /**
     * @var Fiddle
     *
     * @ORM\ManyToOne(targetEntity="Fiddle")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", onDelete="cascade")
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length(max = 255)
     */
    protected $title;

    /**
     * @var ArrayCollection[UserFavoriteTag]
     *
     * fiddle.max_tags
     *
     * @ORM\OneToMany(targetEntity="UserFavoriteTag", mappedBy="userFavorite", cascade={"all"}, orphanRemoval=true)
     * @Assert\Count(max = 5, maxMessage = "You can't set more than 5 tags.")
     * @Assert\Valid()
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
        foreach ($tags as $tag)
        {
            $tag->setUserFavorite($this);
        }

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
