<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-12-26
 * Time: 00:26
 */

namespace AppBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * Weixin api access log (for user regstration)
 *
 * @ORM\Table(name="log_weixin_access", options={"comment":"Weixin access log"})
 * @ORM\Entity()
 */
class WeixinApiAccess
{
    public function __construct()
    {
        if(!isset($this->accessTime)) $this->accessTime = new \DateTime('now');
    }

  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
    protected $id;

    /**
     * @ORM\Column(type="string", length=2048, nullable=true, options={"comment":"Weixin api response data"})
     */
    protected $data;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"comment":"Is weixin api call success"})
     */
    protected $is_success;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Weixin action "})
     */
    protected $action;

    /**
     * @ORM\Column(type="datetime", nullable=false, options={"comment":"API call time"})
     */
    protected $accessTime;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device unique Device ID"})
     */
    protected $udid;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device Version"})
     */
    protected $deviceVersion;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device Version"})
     */
    protected $appVersion;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @return mixed
     */
    public function getAccessTime()
    {
        return $this->accessTime;
    }

    /**
     * @param mixed $accessTime
     */
    public function setAccessTime($accessTime)
    {
        $this->accessTime = $accessTime;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getisSuccess()
    {
        return $this->is_success;
    }

    /**
     * @param mixed $is_success
     */
    public function setIsSuccess($is_success)
    {
        $this->is_success = $is_success;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
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
}