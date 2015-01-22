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
use Symfony\Component\Validator\Constraints as Assert;
use Fuz\AppBundle\Api\TagInterface;

/**
 * FiddleTag
 *
 * @ORM\Table(name="fiddle_tag")
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class FiddleTag implements TagInterface
{

    /**
     * @var Fiddle
     *
     * @ORM\ManyToOne(targetEntity="Fiddle", inversedBy="tags")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=32)
     * @ORM\Id
     * @Assert\NotBlank()
     */
    protected $tag;

    /**
     * Set fiddle
     *
     * @param Fiddle $fiddle
     * @return FiddleTag
     */
    public function setFiddle(Fiddle $fiddle)
    {
        $this->fiddle = $fiddle;

        return $this;
    }

    /**
     * Get fiddle
     *
     * @return Fiddle|null
     */
    public function getFiddle()
    {
        return $this->fiddle;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return FiddleTag
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
