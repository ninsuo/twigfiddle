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

/**
 * CaptchaSessionHit
 *
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\CaptchaSessionHitRepository")
 * @ORM\Table(name="captcha_session_hit")
 * @ORM\HasLifecycleCallbacks
 */
class CaptchaSessionHit
{

    /**
     * @var string
     *
     * @ORM\Column(name="session_id", type="string", length=32)
     * @ORM\Id
     */
    protected $sessionId;

    /**
     * @var string
     *
     * @ORM\Column(name="strategy", type="string", length=32)
     * @ORM\Id
     */
    protected $strategy;

    /**
     * @var int
     *
     * @ORM\Column(name="hits", type="integer", options={"unsigned"=true})
     */
    protected $hits;

    /**
     * @var string
     *
     * @ORM\Column(name="creation_tm", type="datetime")
     */
    protected $creationTm;

    public function __construct()
    {
        $this->creationDate = new \DateTime();
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    public function getHits()
    {
        return $this->hits;
    }

    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    public function getCreationTm()
    {
        return $this->creationTm;
    }

    public function setCreationTm($creationTm)
    {
        $this->creationTm = $creationTm;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreationTm(new \DateTime());
    }

}
