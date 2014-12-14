<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @Assert\NotBlank
     */
    protected $format = self::FORMAT_YAML;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\Length(max = 8192)
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

    /**
     * @Assert\Callback
     */
    public function validateFormat(ExecutionContextInterface $context)
    {
        if (!in_array($this->format, $this->getSupportedFormats()))
        {
            $context->buildViolation('Choose a supported context format.')
               ->atPath('format')
               ->addViolation()
            ;
        }
    }

    public static function getSupportedFormats()
    {
        return array (
                self::FORMAT_YAML,
                self::FORMAT_XML,
                self::FORMAT_JSON,
                self::FORMAT_INI,
        );
    }

}
