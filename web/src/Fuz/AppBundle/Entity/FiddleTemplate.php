<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FiddleTemplate
 *
 * @ORM\Table(name="fiddle_template")
 * @ORM\Entity
 */
class FiddleTemplate
{

    /**
     * @var Fiddle
     *
     * @ORM\OneToOne(targetEntity="Fiddle")
     * @ORM\JoinColumn(name="fiddle_id", referencedColumnName="id", onDelete="cascade")
     * @ORM\Id
     */
    protected $fiddle;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=64)
     * @ORM\Id
     */
    protected $filename = 'main.twig';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    protected $content = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    protected $isMain = true;

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
     * Set filename
     *
     * @param string $filename
     * @return FiddleTemplate
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return FiddleTemplate
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
     * Set isMain
     *
     * @param boolean $isMain
     * @return FiddleTemplate
     */
    public function setIsMain($isMain)
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * Get isMain
     *
     * @return boolean
     */
    public function isMain()
    {
        return $this->isMain;
    }

}
