<?php

namespace Fuz\AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entity used by the fiddle's browser filters form.
 *
 * @see Fuz\AppBundle\Controller\BrowseController.php
 */
class Browse
{

    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     */
    protected $keywords;

    /**
     * @var array
     *
     * fiddle.max_tags
     *
     * @Assert\Count(max = 5, maxMessage = "Fiddles contains at last 5 tags.")
     */
    protected $tags;

    /**
     * @var bool
     *
     * @Choice(choices = {0, 1})
     */
    protected $bookmark;

    /**
     * @var bool
     *
     * @Choice(choices = {0, 1})
     */
    protected $mine;

    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     */
    protected $visibility;

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function getBookmark()
    {
        return $this->bookmark;
    }

    public function setBookmark($bookmark)
    {
        $this->bookmark = $bookmark;
        return $this;
    }

    public function getMine()
    {
        return $this->mine;
    }

    public function setMine($mine)
    {
        $this->mine = $mine;
        return $this;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @Assert\Callback
     */
    public function validateVisibility(ExecutionContextInterface $context)
    {
        if ($this->visibility && !in_array($this->visibility,
              array (
                   Fiddle::VISIBILITY_PUBLIC,
                   Fiddle::VISIBILITY_UNLISTED,
                   Fiddle::VISIBILITY_PRIVATE,
           )))
        {
            $context->buildViolation('You should choose a valid visibility.')
               ->atPath('visibility')
               ->addViolation();
        }
    }

}
