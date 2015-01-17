<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CaptchaSessionIp
 *
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\CaptchaSessionIpRepository")
 * @ORM\Table(name="captcha_session_ip")
 * @ORM\HasLifecycleCallbacks
 */
class CaptchaSessionIp
{

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="integer", options={"unsigned"=true})
     * @ORM\Id
     */
    protected $ip;

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
     * @ORM\Column(name="creation_tm", type="datetime")
     */
    protected $creationTm;

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
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
