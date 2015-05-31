<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Testcase
 *
 * @ORM\Table(name="testresult",indexes={@ORM\Index(name="idx_testcase_id_datetime_run", columns={"testcase_id", "datetime_run"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TestresultRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Testresult
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Testcase
     *
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Testcase", inversedBy="testresults")
     * @ORM\JoinColumn(name="testcase_id", referencedColumnName="id", nullable=false)
     */
    private $testcase;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime_run", type="datetime", nullable=false)
     */
    private $datetimeRun;

    /**
     * @var int
     *
     * @ORM\Column(name="exit_code", type="smallint", nullable=false)
     */
    private $exitCode;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text")
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(name="fail_screenshot_filename", type="text")
     */
    private $failScreenshotFilename;

    /**
     * @var string
     *
     * @ORM\Column(name="har", type="text")
     */
    private $har;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @return Testresult
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
     * Set testcase
     *
     * @param \AppBundle\Entity\Testcase $testcase
     * @return Testresult
     */
    public function setTestcase(\AppBundle\Entity\Testcase $testcase)
    {
        $this->testcase = $testcase;

        return $this;
    }

    /**
     * Get testcase
     *
     * @return \AppBundle\Entity\Testcase
     */
    public function getTestcase()
    {
        return $this->testcase;
    }

    /**
     * Set datetimeRun
     *
     * @param \DateTime $datetimeRun
     * @return Testresult
     */
    public function setDatetimeRun($datetimeRun)
    {
        $this->datetimeRun = $datetimeRun;

        return $this;
    }

    /**
     * Get datetimeRun
     *
     * @return \DateTime
     */
    public function getDatetimeRun()
    {
        return $this->datetimeRun;
    }

    /**
     * Set exitCode
     *
     * @param int $exitCode
     * @return Testresult
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;

        return $this;
    }

    /**
     * Get exitCode
     *
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }


    /**
     * Set output
     *
     * @param string $output
     * @return Testresult
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get output
     *
     * @return string 
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set failScreenshotFilename
     *
     * @param string $failScreenshotFilename
     * @return Testresult
     */
    public function setFailScreenshotFilename($failScreenshotFilename)
    {
        $this->failScreenshotFilename = $failScreenshotFilename;

        return $this;
    }

    /**
     * Get failScreenshotFilename
     *
     * @return string
     */
    public function getFailScreenshotFilename()
    {
        return $this->failScreenshotFilename;
    }

    /**
     * Set har
     *
     * @param string $har
     * @return Testresult
     */
    public function setHar($har)
    {
        $this->har = $har;

        return $this;
    }

    /**
     * Get har
     *
     * @return string
     */
    public function getHar()
    {
        return $this->har;
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
