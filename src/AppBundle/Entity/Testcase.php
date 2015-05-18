<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Testcase
 *
 * @ORM\Table(name="testcase")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TestcaseRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Testcase
{
    /**
     * @var integer
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=36)
     * @ORM\ManyToOne(targetEntity="User", inversedBy="testcases")
     * @ORM\JoinColumn(name="user_id")
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="cadence", type="string", length=4, nullable=false)
     */
    private $cadence = '*/15';

    /**
     * @var string
     *
     * @ORM\Column(name="script", type="text")
     */
    private $script;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="activated_at", type="datetime", nullable=true)
     */
    private $activatedAt = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt = null;

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Testcase
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set userId
     *
     * @param string $user
     * @return Testcase
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Testcase
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Testcase
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
     * Set cadence
     *
     * @param string $cadence
     * @return Testcase
     */
    public function setCadence($cadence)
    {
        $this->cadence = $cadence;

        return $this;
    }

    /**
     * Get cadence
     *
     * @return string 
     */
    public function getCadence()
    {
        return $this->cadence;
    }

    /**
     * Set script
     *
     * @param string $script
     * @return Testcase
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Get script
     *
     * @return string 
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * @param mixed $activatedAt
     */
    public function setActivatedAt($activatedAt)
    {
        $this->activatedAt = $activatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
