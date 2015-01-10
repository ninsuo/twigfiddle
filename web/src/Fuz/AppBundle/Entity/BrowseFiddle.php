<?php

namespace Fuz\AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class BrowseFiddle
{

    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     */
    protected $title;

    /**
     * @var array
     *
     * fiddle.max_tags
     *
     * @Assert\Count(max = 5, maxMessage = "You can't set more than 5 tags.")
     */
    protected $tags = array();

    /**
     * Set title
     *
     * @param string $title
     * @return BrowseFiddle
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
     * @param array $tags
     * @return BrowseFiddle
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }
}