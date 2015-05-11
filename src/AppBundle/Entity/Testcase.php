<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Testcase
 *
 * @ORM\Table(name="testcases")
 * @ORM\Entity
 */
class Testcase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=36)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="userId", type="string", length=36)
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="notifyEmail", type="string", length=128)
     */
    private $notifyEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="cadence", type="string", length=4)
     */
    private $cadence;

    /**
     * @var string
     *
     * @ORM\Column(name="script", type="text")
     */
    private $script;

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
     * @param string $userId
     * @return Testcase
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string 
     */
    public function getUserId()
    {
        return $this->userId;
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
     * Set notifyEmail
     *
     * @param string $notifyEmail
     * @return Testcase
     */
    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;

        return $this;
    }

    /**
     * Get notifyEmail
     *
     * @return string 
     */
    public function getNotifyEmail()
    {
        return $this->notifyEmail;
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
}
