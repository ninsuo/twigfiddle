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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Fuz\AppBundle\Api\TagContainerInterface;

/**
 * Fiddle
 *
 * @ORM\Table(
 *      name="fiddle",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="fiddle_idx", columns={"hash", "revision"})}
 * )
 * @ORM\Entity(repositoryClass="Fuz\AppBundle\Repository\FiddleRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Fiddle implements TagContainerInterface
{

    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_UNLISTED = 'unlisted';
    const VISIBILITY_PRIVATE = 'private';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * fiddle.hash_regexp
     *
     * @ORM\Column(name="hash", type="string", length=128)
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
     * @ORM\OneToOne(targetEntity="FiddleContext", mappedBy="fiddle", cascade={"all"})
     * @Assert\Type(type="Fuz\AppBundle\Entity\FiddleContext")
     * @Assert\Valid()
     */
    protected $context;

    /**
     * @var ArrayCollection[FiddleTemplate]
     *
     * fiddle.max_templates
     *
     * @ORM\OneToMany(targetEntity="FiddleTemplate", mappedBy="fiddle", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"main" = "DESC"})
     * @Assert\Count(min = 1, minMessage = "You need at least 1 template.")
     * @Assert\Count(max = 10, maxMessage = "You can't create more than 15 templates.")
     * @Assert\Valid()
     */
    protected $templates;

    /**
     * @var string
     *
     * @ORM\Column(name="twig_engine", type="string", length=32)
     * @Assert\NotBlank
     */
    protected $twigEngine;

    /**
     * @var string
     *
     * @ORM\Column(name="twig_version", type="string", length=32)
     * @Assert\NotBlank
     */
    protected $twigVersion;

    /**
     * @var int
     *
     * @ORM\Column(name="with_c_extension", type="boolean")
     */
    protected $withCExtension = false;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\Length(max = 255)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="visibility", type="string", length=16)
     */
    protected $visibility = self::VISIBILITY_PUBLIC;

    /**
     * @var ArrayCollection[FiddleTag]
     *
     * fiddle.max_tags
     *
     * @ORM\OneToMany(targetEntity="FiddleTag", mappedBy="fiddle", cascade={"all"}, orphanRemoval=true)
     * @Assert\Count(max = 5, maxMessage = "You can't set more than 5 tags.")
     * @Assert\Valid()
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
    protected $visitsCount = 0;

    public function __construct()
    {
        $this->context = new FiddleContext();
        $this->templates = new ArrayCollection();
        $this->templates->add(new FiddleTemplate());
        $this->tags = new ArrayCollection();
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $hash
     * @return Fiddle
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param integer $revision
     * @return Fiddle
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * @return integer
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * @param User $user
     * @return Fiddle
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param FiddleContext|null $context
     * @return Fiddle
     */
    public function setContext(FiddleContext $context = null)
    {
        if (!is_null($context))
        {
            $context->setFiddle($this);
        }

        $this->context = $context;

        return $this;
    }

    /**
     * @return FiddleContext|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param FiddleTemplate $template
     * @return Fiddle
     */
    public function addTemplate(FiddleTemplate $template)
    {
        if (!$this->templates->contains($template))
        {
            $template->setFiddle($this);
            $this->templates->add($template);
        }
        return $this;
    }

    /**
     * Remove template
     *
     * @param FiddleTemplate $template
     * @return Fiddle
     */
    public function removeTemplate(FiddleTemplate $template)
    {
        if ($this->templates->contains($template))
        {
            $this->templates->removeElement($template);
        }
        return $this;
    }

    /**
     * Clear templates
     *
     * @return Fiddle
     */
    public function clearTemplates()
    {
        $this->templates = new ArrayCollection();
        return $this;
    }

    /**
     * @return FiddleTemplate[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param string $twigEngine
     * @return Fiddle
     */
    public function setTwigEngine($twigEngine)
    {
        $this->twigEngine = $twigEngine;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwigEngine()
    {
        return $this->twigEngine;
    }

    /**
     * @param string $twigVersion
     * @return Fiddle
     */
    public function setTwigVersion($twigVersion)
    {
        $this->twigVersion = $twigVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwigVersion()
    {
        return $this->twigVersion;
    }

    /**
     * @param string $withCExtension
     * @return Fiddle
     */
    public function setWithCExtension($withCExtension)
    {
        $this->withCExtension = $withCExtension;

        return $this;
    }

    /**
     * @return string
     */
    public function isWithCExtension()
    {
        return $this->withCExtension;
    }

    /**
     * @param string $title
     * @return Fiddle
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param bool $visibility
     * @return Fiddle
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set tags
     *
     * @param ArrayCollection[FiddleTag]
     * @return Fiddle
     */
    public function setTags(ArrayCollection $tags)
    {
        foreach ($tags as $tag)
        {
            $tag->setFiddle($this);
        }

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
     * @param \DateTime $creationTm
     * @return Fiddle
     */
    public function setCreationTm($creationTm)
    {
        $this->creationTm = $creationTm;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreationTm()
    {
        return $this->creationTm;
    }

    /**
     * @param \DateTime $updateTm
     * @return Fiddle
     */
    public function setUpdateTm($updateTm)
    {
        $this->updateTm = $updateTm;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateTm()
    {
        return $this->updateTm;
    }

    /**
     * @param integer $visitsCount
     * @return Fiddle
     */
    public function setVisitsCount($visitsCount)
    {
        $this->visitsCount = $visitsCount;

        return $this;
    }

    /**
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
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->setUpdateTm(new \DateTime());
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

    public function mapBookmark(UserBookmark $bookmark)
    {
        $this->setTitle($bookmark->getTitle());
        $collection = new ArrayCollection();
        foreach ($bookmark->getTags() as $tag)
        {
            $tagObj = new FiddleTag();
            $tagObj->setTag($tag->getTag());
            $collection->add($tagObj);
        }
        $this->setTags($collection);
        return $this;
    }

    public function __clone()
    {
        $this->id = null;
        $this->user = null;

        if ($this->context)
        {
            $this->context = clone $this->context;
        }

        if ($this->templates)
        {
            $templates = $this->templates;
            $this->clearTemplates();
            foreach ($templates as $template)
            {
                $this->addTemplate(clone $template);
            }
        }

        if ($this->tags)
        {
            $oldTags = $this->tags;
            $newTags = new ArrayCollection();
            foreach ($oldTags as $tag)
            {
                $newTags->add(clone $tag);
            }
            $this->setTags($newTags);
        }

        $this->visitsCount = 0;
    }

}
