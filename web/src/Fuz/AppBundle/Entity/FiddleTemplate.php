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
use JMS\Serializer\Annotation as Serializer;

/**
 * FiddleTemplate.
 *
 * @ORM\Table(name="fiddle_template")
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @Serializer\ExclusionPolicy("NONE")
 */
class FiddleTemplate
{
    /**
     * @var Fiddle
     *
     * @ORM\ManyToOne(targetEntity="Fiddle", inversedBy="templates")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     * @Serializer\Exclude
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=64)
     * @ORM\Id
     * @Assert\NotBlank()
     * @Assert\Length(min = 1, max = 21)
     * @Assert\Regex("/\.[tT][wW][iI][gG]$/", message = "Your template name must end by .twig.")
     * @Assert\Regex(
     *      "/(?!\.)^[A-Za-z0-9-_\.]+$/",
     *      message = "Your template name must be composed of alphanumeric, _ and - characters."
     * )
     * @Serializer\Type("string")
     */
    protected $filename = 'main.twig';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\Length(max = 8192)
     * @Assert\NotBlank
     * @Serializer\Type("string")
     */
    protected $content = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="is_main", type="boolean")
     * @Assert\Type(type="bool")
     * @Serializer\Type("boolean")
     */
    protected $main = true;

    /**
     * Set fiddle.
     *
     * @param Fiddle $fiddle
     *
     * @return FiddleTemplate
     */
    public function setFiddle(Fiddle $fiddle)
    {
        $this->fiddle = $fiddle;

        return $this;
    }

    /**
     * Get fiddle.
     *
     * @return Fiddle|null
     */
    public function getFiddle()
    {
        return $this->fiddle;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return FiddleTemplate
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return FiddleTemplate
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set main.
     *
     * @param bool $main
     *
     * @return FiddleTemplate
     */
    public function setMain($main)
    {
        $this->main = $main;

        return $this;
    }

    /**
     * Get main.
     *
     * @return bool
     */
    public function isMain()
    {
        return $this->main;
    }
}
