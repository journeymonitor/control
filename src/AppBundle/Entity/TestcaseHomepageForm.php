<?php

namespace AppBundle\Entity;

class TestcaseHomepageForm
{
    private $title;

    private $notifyEmail;
    
    private $password;

    private $cadence;

    private $script;

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }


    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;

        return $this;
    }

    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    public function setCadence($cadence)
    {
        $this->cadence = $cadence;

        return $this;
    }

    public function getCadence()
    {
        return $this->cadence;
    }

    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    public function getScript()
    {
        return $this->script;
    }
}
