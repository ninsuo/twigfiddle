<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fuz\AppBundle\Api\TagInterface;

/**
 * FiddleTag
 *
 * @ORM\Table(name="user_favorite_tag")
 * @ORM\Entity
 */
class UserFavoriteTag implements TagInterface
{

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserFavorite", inversedBy="tags")
     * @ORM\JoinColumn(name="user_favorite_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    protected $userFavorite;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=32)
     * @ORM\Id
     */
    protected $tag;

    /**
     * Set userFavorite
     *
     * @param UserFavorite $userFavorite
     * @return UserFavoriteTag
     */
    public function setUserFavorite(UserFavorite $userFavorite)
    {
        $this->userFavorite = $userFavorite;

        return $this;
    }

    /**
     * Get userFavorite
     *
     * @return UserFavorite
     */
    public function getUserFavorite()
    {
        return $this->userFavorite;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return UserTag
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

}
