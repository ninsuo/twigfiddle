<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fiddle
 *
 * @ORM\Table(
 *      name="fiddle",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="fiddle_idx", columns={"hash", "revision"})}
 * )
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Entity\FiddleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Fiddle implements \Serializable
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=8)
     */
    protected $hash;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision", type="integer")
     */
    protected $revision = 1;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_tm", type="datetime")
     */
    protected $creationTm;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_tm", type="datetime")
     */
    protected $updateTm;

    /**
     * @var integer
     *
     * @ORM\Column(name="visits_count", type="integer")
     */
    protected $visitsCount;

    /**
     * @var FiddleConfig
     *
     * @ORM\OneToOne(targetEntity="FiddleConfig")
     * @ORM\JoinColumn(name="id", referencedColumnName="fiddle_id")
     */
    protected $config;

    /**
     * @var array[FiddleTemplate]
     *
     * @ORM\OneToMany(targetEntity="FiddleTemplate", mappedBy="fiddle_id")
     * @ORM\JoinColumn(name="id", referencedColumnName="fiddle_id")
     */
    protected $templates;

    /**
     * @var FiddleContext
     *
     * @ORM\OneToOne(targetEntity="FiddleContext")
     * @ORM\JoinColumn(name="id", referencedColumnName="fiddle_id")
     */
    protected $context;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Fiddle
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set revision
     *
     * @param integer $revision
     * @return Fiddle
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * Get revision
     *
     * @return integer
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Fiddle
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creationTm
     *
     * @param \DateTime $creationTm
     * @return Fiddle
     */
    public function setCreationTm($creationTm)
    {
        $this->creationTm = $creationTm;

        return $this;
    }

    /**
     * Get creationTm
     *
     * @return \DateTime
     */
    public function getCreationTm()
    {
        return $this->creationTm;
    }

    /**
     * Set updateTm
     *
     * @param \DateTime $updateTm
     * @return Fiddle
     */
    public function setUpdateTm($updateTm)
    {
        $this->updateTm = $updateTm;

        return $this;
    }

    /**
     * Get updateTm
     *
     * @return \DateTime
     */
    public function getUpdateTm()
    {
        return $this->updateTm;
    }

    /**
     * Set visitsCount
     *
     * @param integer $visitsCount
     * @return Fiddle
     */
    public function setVisitsCount($visitsCount)
    {
        $this->visitsCount = $visitsCount;

        return $this;
    }

    /**
     * Get visitsCount
     *
     * @return integer
     */
    public function getVisitsCount()
    {
        return $this->visitsCount;
    }

    /**
     * Set config
     *
     * @param FiddleConfig $config
     * @return Fiddle
     */
    public function setConfig(FiddleConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return FiddleConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set templates
     *
     * @param  array[FiddleTemplate] $templates
     * @return Fiddle
     */
    public function setTemplates(array $templates)
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * Get templates
     *
     * @return array[FiddleTemplate]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Set context
     *
     * @param FiddleContext $context
     * @return Fiddle
     */
    public function setContext(FiddleContext $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return FiddleContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreationTm(new \DateTime());
        $this->setUpdateTm(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdateTm(new \DateTime());
    }

    /**
     * Can't use $em->detach as we require FiddleConfig, FiddleTemplate[] and FiddleContext entities inside the serialized object.
     */
    public function serialize()
    {
        return serialize(array (
                $this->id,
                $this->hash,
                $this->revision,
                $this->config,
                $this->templates,
                $this->context
        ));
    }

    public function unserialize($serialized)
    {
        list(
           $this->id,
           $this->hash,
           $this->revision,
           $this->config,
           $this->templates,
           $this->context
           ) = unserialize($serialized);
    }

}
