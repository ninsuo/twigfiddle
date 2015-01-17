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
 * CaptchaIpLimit
 *
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\CaptchaIpLimitRepository")
 * @ORM\Table(name="captcha_ip_limit")
 * @ORM\HasLifecycleCallbacks
 */
class CaptchaIpLimit
{

    /**
     * @var int
     *
     * @ORM\Column(name="ip", type="integer", options={"unsigned"=true})
     * @ORM\Id
     */
    protected $ip;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_allowed_sessions", type="integer", options={"unsigned"=true})
     */
    protected $limit;

    /**
     * @var string
     *
     * @ORM\Column(name="update_tm", type="datetime")
     */
    protected $updateTm;

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getUpdateTm()
    {
        return $this->updateTm;
    }

    public function setUpdateTm($updateTm)
    {
        $this->updateTm = $updateTm;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function onPrePersistUpdate()
    {
        $this->setUpdateTm(new \DateTime());
    }

}
