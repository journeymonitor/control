<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;

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
     * @var string
     *
     * @ORM\GeneratedValue(strategy="UUID")
     * @Groups({"testcase"})
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \AppBundle\Entity\User
     * @Exclude()
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\User", inversedBy="testcases")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection|Testresult[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Testresult", mappedBy="testcase",fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"datetimeRun" = "DESC"})
     */
    private $testresults;

    /**
     * @var boolean
     * @Groups({"testcase"})
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled = false;

    /**
     * @var string
     * @Groups({"testcase"})
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @var string
     * @Groups({"testcase"})
     * @ORM\Column(name="cadence", type="string", length=4, nullable=false)
     */
    private $cadence = '*/15';

    /**
     * @var string
     * @Groups({"testcase"})
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

    public function __construct()
    {
        $this->testresults = new ArrayCollection();
    }

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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Testcase
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ArrayCollection|Testresult[]
     */
    public function getTestresults()
    {
        return $this->testresults;
    }

    /**
     * @return ArrayCollection|Testresult[]
     */
    public function getLimitedTestresults($limit)
    {
        $testresults = $this->getTestresults();
        return $testresults->slice(0, $limit);
    }

    /**
     * @param ArrayCollection|Testresult[] $testresults
     */
    public function setTestresults($testresults)
    {
        $this->testresults = $testresults;
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

    /**
     * @VirtualProperty()
     * @SerializedName("notifyEmail")
     * @Groups({"testcase"})
     */
    public function getNotifyEmail()
    {
        return $this->getUser()->getEmailCanonical();
    }
}
