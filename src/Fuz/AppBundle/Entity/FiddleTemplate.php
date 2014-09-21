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
     * @var integer
     *
     * @ORM\Column(name="fiddle_id", type="integer")
     * @ORM\Id
     */
    private $fiddleId;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=64)
     * @ORM\Id
     */
    private $filename = 'main.twig';

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content = '';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_main", type="boolean")
     */
    private $isMain = true;

    /**
     * Set fiddleId
     *
     * @param integer $fiddleId
     * @return FiddleTemplate
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
    public function getIsMain()
    {
        return $this->isMain;
    }

}
