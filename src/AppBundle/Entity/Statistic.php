<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statistic
 *
 * @ORM\Table(name="statistic",indexes={@ORM\Index(name="idx_testresult_id", columns={"testresult_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Statistic
{
    /**
     * @var \AppBundle\Entity\Testcase
     *
     * @ORM\OneToOne(targetEntity="\AppBundle\Entity\Testresult", inversedBy="statistic")
     * @ORM\JoinColumn(name="testresult_id", referencedColumnName="id", nullable=false)
     * @ORM\Id
     */
    private $testresult;

    /**
     * @var integer
     *
     * @ORM\Column(name="runtimeMilliseconds", type="integer")
     */
    private $runtimeMilliseconds;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOf200", type="integer")
     */
    private $numberOf200;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOf400", type="integer")
     */
    private $numberOf400;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOf500", type="integer")
     */
    private $numberOf500;

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
     * Set testresult
     *
     * @param \AppBundle\Entity\Testresult $testresult
     * @return Statistic
     */
    public function setTestresult(Testresult $testresult)
    {
        $this->testresult = $testresult;

        return $this;
    }

    /**
     * Get testresult
     *
     * @return \AppBundle\Entity\Testresult
     */
    public function getTestresult()
    {
        return $this->testresult;
    }

    /**
     * Set runtimeMilliseconds
     *
     * @param integer $runtimeMilliseconds
     * @return Statistic
     */
    public function setRuntimeMilliseconds($runtimeMilliseconds)
    {
        $this->runtimeMilliseconds = $runtimeMilliseconds;

        return $this;
    }

    /**
     * Get runtimeMilliseconds
     *
     * @return integer 
     */
    public function getRuntimeMilliseconds()
    {
        return $this->runtimeMilliseconds;
    }

    /**
     * Set numberOf200
     *
     * @param integer $numberOf200
     * @return Statistic
     */
    public function setNumberOf200($numberOf200)
    {
        $this->numberOf200 = $numberOf200;

        return $this;
    }

    /**
     * Get numberOf200
     *
     * @return integer 
     */
    public function getNumberOf200()
    {
        return $this->numberOf200;
    }

    /**
     * Set numberOf400
     *
     * @param integer $numberOf400
     * @return Statistic
     */
    public function setNumberOf400($numberOf400)
    {
        $this->numberOf400 = $numberOf400;

        return $this;
    }

    /**
     * Get numberOf400
     *
     * @return integer 
     */
    public function getNumberOf400()
    {
        return $this->numberOf400;
    }

    /**
     * Set numberOf500
     *
     * @param integer $numberOf500
     * @return Statistic
     */
    public function setNumberOf500($numberOf500)
    {
        $this->numberOf500 = $numberOf500;

        return $this;
    }

    /**
     * Get numberOf500
     *
     * @return integer 
     */
    public function getNumberOf500()
    {
        return $this->numberOf500;
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
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
