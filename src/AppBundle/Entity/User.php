<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @var string
     *
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    protected $id;

    /**
     * @var \ArrayCollection|Testcase[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Testcase", mappedBy="user")
     */
    private $testcases;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="activated_at", nullable=true)
     */
    private $activatedAt = null;

    public function __construct()
    {
        $this->testcases = new \ArrayCollection();
        parent::__construct();
    }
    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return \ArrayCollection|Testcase[]
     */
    public function getTestcases()
    {
        return $this->testcases;
    }

    /**
     * @param \ArrayCollection|Testcase[] $testcases
     */
    public function setTestcases($testcases)
    {
        $this->testcases = $testcases;
    }

    /**
     * @return \DateTime
     */
    public function getActivatedAt()
    {
        return $this->activatedAt;
    }

    /**
     * @param \DateTime $activatedAt
     */
    public function setActivatedAt($activatedAt)
    {
        $this->activatedAt = $activatedAt;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
