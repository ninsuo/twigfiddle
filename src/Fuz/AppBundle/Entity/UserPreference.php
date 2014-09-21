<?php

namespace Fuz\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserPreference
 *
 * @ORM\Table(name="user_preference")
 * @ORM\Entity
 */
class UserPreference
{

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="hook", type="string", length=64)
     * @ORM\Id
     */
    private $hook;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    /**
     * Set userId
     *
     * @param integer $userId
     * @return UserPreference
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set hook
     *
     * @param string $hook
     * @return UserPreference
     */
    public function setHook($hook)
    {
        $this->hook = $hook;

        return $this;
    }

    /**
     * Get hook
     *
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return UserPreference
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

}
