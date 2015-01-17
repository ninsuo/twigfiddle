<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Fuz\AppBundle\Api\TagContainerInterface;

/**
 * UserBookmark
 *
 * @ORM\Table(name="user_bookmark")
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\UserBookmarkRepository")
 */
class UserBookmark implements TagContainerInterface
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
     * @var ArrayCollection[UserBookmarkTag]
     *
     * fiddle.max_tags
     *
     * @ORM\OneToMany(targetEntity="UserBookmarkTag", mappedBy="userBookmark", cascade={"all"}, orphanRemoval=true)
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
     * @return UserBookmark
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
     * @return UserBookmark
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
     * @return UserBookmark
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
     * @param ArrayCollection[UserBookmarkTag] $tags
     * @return UserBookmark
     */
    public function setTags(ArrayCollection $tags)
    {
        foreach ($tags as $tag)
        {
            $tag->setUserBookmark($this);
        }

        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return ArrayCollection[UserBookmarkTag]
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function mapFiddle(Fiddle $fiddle)
    {
        $this->setTitle($fiddle->getTitle());

        $tags = new ArrayCollection();
        foreach ($fiddle->getTags() as $fiddleTag)
        {
            $tag = new UserBookmarkTag();
            $tag->setTag($fiddleTag->getTag());
            $tags->add($tag);
        }
        $this->setTags($tags);

        return $this;
    }

}
