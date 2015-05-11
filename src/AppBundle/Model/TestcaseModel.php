<?php

namespace AppBundle\Model;

class TestcaseModel
{
    private $id;
    private $userId;
    private $title;
    private $notifyEmail;
    private $cadence;
    private $script;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    }

    public function getCadence()
    {
        return $this->cadence;
    }

    public function setCadence($cadence)
    {
        $this->cadence = $cadence;
    }

    public function getScript()
    {
        return $this->script;
    }

    public function setScript($script)
    {
        $this->script = $script;
    }
}
