<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FiddleContext
 *
 * @ORM\Table(name="fiddle_context")
 * @ORM\Entity
 */
class FiddleContext
{

    const FORMAT_YAML = 'YML';
    const FORMAT_XML = 'XML';
    const FORMAT_JSON = 'JSON';
    const FORMAT_INI = 'INI';

    /**
     * @var Fiddle
     *
     * @ORM\OneToOne(targetEntity="Fiddle", inversedBy="context")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="format", type="string", length=8)
     */
    protected $format = self::FORMAT_YAML;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content = '';

    /**
     * Set fiddle
     *
     * @param Fiddle $fiddle
     * @return FiddleTemplate
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
     * Set format
     *
     * @param string $format
     * @return FiddleContext
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return FiddleContext
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

}
