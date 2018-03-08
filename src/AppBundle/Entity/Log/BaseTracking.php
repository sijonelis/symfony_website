<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-29
 * Time: 23:08
 */

namespace AppBundle\Entity\Log;

use AppBundle\Entity\Note;
use AppBundle\Entity\Tea;
use AppBundle\Entity\TeaFeatured;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Device\Device;

class BaseTracking
{
    /**
     *  constructor.
     * @param Device $device
     */
    public function __construct(Device $device)
    {
        if(!isset($this->timestamp)) $this->timestamp = new \DateTime('now');
        $this->user = $device->getUser();
        $this->udid = $device->getUdid();
        $this->deviceVersion = $device->getUdv();
        $this->appVersion = $device->getAv();
        $this->user = $device->getUser();
        $this->ip = $device->getIpAddress();
    }

    public function setDeviceData(Device $device) {

    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"comment":"Engagement type. 1-favourite, 2-tea-details, 3-note, 4-newsfeed, 5-search, 6-share, 7-tea-suggestion, 100-unfavourite"})
     */
    protected $type;

    /**
     * @ORM\Column(type="datetime", nullable=false, options={"comment":"Engagement time"})
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device unique Device ID"})
     */
    protected $udid;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device Id"})
     */
    protected $deviceVersion;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device Version"})
     */
    protected $appVersion;

    /**
     * @ORM\Column(type="string", length=128, nullable=true, options={"comment":"Calling device ip address"})
     */
    protected $ip;

    /**
     * @ORM\Column(type="string", length=128, nullable=true, options={"comment":"Calling device country"})
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Tea")
     * @ORM\JoinColumn(name="tea_id", referencedColumnName="id", nullable=true)
     */
    protected $tea;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Note")
     * @ORM\JoinColumn(name="note_id", referencedColumnName="id", nullable=true)
     */
    protected $note;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TeaFeatured")
     * @ORM\JoinColumn(name="tea_featured_id", referencedColumnName="id", nullable=true)
     */
    protected $newsfeed;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getUdid()
    {
        return $this->udid;
    }

    /**
     * @param mixed $udid
     */
    public function setUdid($udid)
    {
        $this->udid = $udid;
    }

    /**
     * @return mixed
     */
    public function getDeviceVersion()
    {
        return $this->deviceVersion;
    }

    /**
     * @param mixed $deviceVersion
     */
    public function setDeviceVersion($deviceVersion)
    {
        $this->deviceVersion = $deviceVersion;
    }

    /**
     * @return mixed
     */
    public function getAppVersion()
    {
        return $this->appVersion;
    }

    /**
     * @param mixed $appVersion
     */
    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getTea()
    {
        return $this->tea;
    }

    /**
     * @param mixed $tea
     */
    public function setTea(?Tea $tea)
    {
        $this->tea = $tea;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getNote(): Note
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote(?Note $note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNewsfeed(): TeaFeatured
    {
        return $this->newsfeed;
    }

    /**
     * @param mixed $newsfeed
     */
    public function setNewsfeed(?TeaFeatured $newsfeed): void
    {
        $this->newsfeed = $newsfeed;
    }

}