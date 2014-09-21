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

    const FORMAT_YAML = 'YAML';
    const FORMAT_XML = 'XML';
    const FORMAT_JSON = 'JSON';
    const FORMAT_INI = 'INI';
    const FORMAT_KEY_VALUE = 'key = value';

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
     * @ORM\Column(name="format", type="string", length=8)
     */
    private $format = self::FORMAT_YAML;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content = '';

    /**
     * Set fiddleId
     *
     * @param integer $fiddleId
     * @return FiddleContext
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
