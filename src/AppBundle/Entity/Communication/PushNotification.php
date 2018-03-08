<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-20
 * Time: 13:20
 */

namespace AppBundle\Entity\Communication;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PushNotificationRepository")
 * @ORM\Table(name="communication_push_notification")
 */
class PushNotification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Communication\UserGroup", inversedBy="pushNotifications")
     * @ORM\JoinColumn(name="push_notification_id", referencedColumnName="id", nullable=false)
     */
    protected $userSegment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $sendAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isPeriodic;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @ORM\Column(type="boolean", options={"default":true})
     */
    protected $sendToIos = true;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    protected $sendToAndroid;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $sentAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $oneSignalResponse;

    /**
     * @ORM\Column(type="string")
     */
    protected $url = 'yipiao://tea/';

    function __toString()
    {
        return $this->getTitle();
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserSegment()
    {
        return $this->userSegment;
    }

    /**
     * @param mixed $userSegment
     */
    public function setUserSegment($userSegment)
    {
        $this->userSegment = $userSegment;
    }

    /**
     * @return mixed
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @param mixed $sendAt
     */
    public function setSendAt($sendAt)
    {
        $this->sendAt = $sendAt;
    }

    /**
     * @return mixed
     */
    public function getisPeriodic()
    {
        return $this->isPeriodic;
    }

    /**
     * @param mixed $isPeriodic
     */
    public function setIsPeriodic($isPeriodic)
    {
        $this->isPeriodic = $isPeriodic;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getSendToIos()
    {
        return $this->sendToIos;
    }

    /**
     * @param mixed $sendToIos
     */
    public function setSendToIos($sendToIos)
    {
        $this->sendToIos = $sendToIos;
    }

    /**
     * @return mixed
     */
    public function getSendToAndroid()
    {
        return $this->sendToAndroid;
    }

    /**
     * @param mixed $sendToAndroid
     */
    public function setSendToAndroid($sendToAndroid)
    {
        $this->sendToAndroid = $sendToAndroid;
    }

    /**
     * @return mixed
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * @param mixed $sentAt
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;
    }

    /**
     * @return mixed
     */
    public function getOneSignalResponse()
    {
        return $this->oneSignalResponse;
    }

    /**
     * @param mixed $oneSignalResponse
     */
    public function setOneSignalResponse($oneSignalResponse)
    {
        $this->oneSignalResponse = $oneSignalResponse;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}