<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
class Fiddle
{

    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_UNLISTED = 'unlisted';
    const VISIBILITY_PRIVATE = 'private';

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
     * @ORM\Column(name="hash", type="string", length=16)
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
    protected $user = null;

    /**
     * @var FiddleContext
     *
     * @ORM\OneToOne(targetEntity="FiddleContext", mappedBy="fiddle", cascade={"persist"})
     * @Assert\Type(type="Fuz\AppBundle\Entity\FiddleContext")
     * @Assert\Valid()
     */
    protected $context;

    /**
     * @var ArrayCollection[FiddleTemplate]
     *
     * fiddle.max_templates
     *
     * @ORM\OneToMany(targetEntity="FiddleTemplate", mappedBy="fiddle", cascade={"persist"})
     * @ORM\OrderBy({"isMain" = "DESC"})
     * @Assert\Count(min = 1, minMessage = "You need at least 1 template.")
     * @Assert\Count(max = 10, maxMessage = "You can't create more than 15 templates.")
     * @Assert\Valid()
     */
    protected $templates;

    /**
     * @var string
     *
     * @ORM\Column(name="twig_version", type="string", length=32)
     * @Assert\NotBlank
     */
    protected $twigVersion;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length(max = 255)
     */
    protected $title;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="string", length=16)
     */
    protected $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @var ArrayCollection[UserTag]
     *
     * fiddle.max_tags
     *
     * @ORM\OneToMany(targetEntity="FiddleTag", mappedBy="fiddle")
     * @Assert\Count(max = 5, maxMessage = "You can't set more than 5 tags.")
     */
    protected $tags;

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

    public function __construct()
    {
        $this->context = new FiddleContext();
        $this->templates = new ArrayCollection();
        $this->templates->add(new FiddleTemplate());
    }

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
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set context
     *
     * @param FiddleTag $context
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
     * Set templates
     *
     * @param  ArrayCollection[FiddleTemplate] $templates
     * @return Fiddle
     */
    public function setTemplates(ArrayCollection $templates)
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
     * Set twigVersion
     *
     * @param string $twigVersion
     * @return Fiddle
     */
    public function setTwigVersion($twigVersion)
    {
        $this->twigVersion = $twigVersion;

        return $this;
    }

    /**
     * Get twigVersion
     *
     * @return string
     */
    public function getTwigVersion()
    {
        return $this->twigVersion;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Fiddle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set visibility
     *
     * @param boolean $visibility
     * @return Fiddle
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return boolean
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set tags
     *
     * @param ArrayCollection[FiddleTag] $tags
     * @return Fiddle
     */
    public function setTags(ArrayCollection $tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return ArrayCollection[FiddleTag]
     */
    public function getTags()
    {
        return $this->tags;
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
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->setCreationTm(new \DateTime());
        $this->setUpdateTm(new \DateTime());
        foreach ($this->templates as $template)
        {
            $template->setFiddle($this);
        }
        $this->context->setFiddle($this);
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdateTm(new \DateTime());
        foreach ($this->templates as $template)
        {
            $template->setFiddle($this);
        }
    }

    /**
     * @Assert\Callback
     */
    public function validateTemplates(ExecutionContextInterface $context)
    {
        $isMainCount = 0;
        foreach ($this->templates as $template)
        {
            $isMainCount += (int) $template->isMain();
        }

        if ($isMainCount == 0)
        {
            $context->buildViolation('You need to set a main template.')
               ->atPath('templates')
               ->addViolation();
        }

        if ($isMainCount >= 2)
        {
            $context->buildViolation('You need to set only one main template.')
               ->atPath('templates')
               ->addViolation();
        }
    }

    /**
     * @Assert\Callback
     */
    public function validateVisibility(ExecutionContextInterface $context)
    {
        if (!in_array($this->visibility,
              array (
                   self::VISIBILITY_PUBLIC,
                   self::VISIBILITY_UNLISTED,
                   self::VISIBILITY_PRIVATE,
           )))
        {
            $context->buildViolation('You should choose a valid visibility.')
               ->atPath('visibility')
               ->addViolation();
        }
    }

}
