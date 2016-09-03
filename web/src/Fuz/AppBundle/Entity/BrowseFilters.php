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

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Entity used by the fiddle's browser filters form.
 *
 * @see Fuz\AppBundle\Controller\BrowseController.php
 */
class BrowseFilters
{
    /**
     * @var array
     *
     * @Assert\Count(max = 10)
     */
    protected $keywords = array();

    /**
     * @var bool
     *
     * @Assert\Choice(choices = {0, 1})
     */
    protected $bookmark;

    /**
     * @var bool
     *
     * @Assert\Choice(choices = {0, 1})
     */
    protected $mine;

    /**
     * @var string
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
              array(
                   Fiddle::VISIBILITY_PUBLIC,
                   Fiddle::VISIBILITY_UNLISTED,
                   Fiddle::VISIBILITY_PRIVATE,
           ))) {
            $context->buildViolation('You should choose a valid visibility.')
               ->atPath('visibility')
               ->addViolation();
        }
    }
}
