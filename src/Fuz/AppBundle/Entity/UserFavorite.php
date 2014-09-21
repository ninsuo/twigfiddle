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
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="fiddle_id", type="integer")
     * @ORM\Id
     */
    private $fiddleId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string", length=64, nullable=true)
     */
    private $tags;

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserFavorite
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set fiddleId
     *
     * @param integer $fiddleId
     * @return UserFavorite
     */
    public function setFiddleId($fiddleId)
    {
        $this->fiddleId = $fiddleId;

        return $this;
    }

    /**
     * Get fiddleId
     *
     * @return integer
     */
    public function getFiddleId()
    {
        return $this->fiddleId;
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
     * @param string $tags
     * @return UserFavorite
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

}
