<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fuz\AppBundle\Api\TagInterface;

/**
 * FiddleTag
 *
 * @ORM\Table(name="user_bookmark_tag")
 * @ORM\Entity
 */
class UserBookmarkTag implements TagInterface
{

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBookmark", inversedBy="tags")
     * @ORM\JoinColumn(name="user_bookmark_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    protected $userBookmark;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=32)
     * @ORM\Id
     */
    protected $tag;

    /**
     * Set userBookmark
     *
     * @param UserBookmark $userBookmark
     * @return UserBookmarkTag
     */
    public function setUserBookmark(UserBookmark $userBookmark)
    {
        $this->userBookmark = $userBookmark;

        return $this;
    }

    /**
     * Get userBookmark
     *
     * @return UserBookmark
     */
    public function getUserBookmark()
    {
        return $this->userBookmark;
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
