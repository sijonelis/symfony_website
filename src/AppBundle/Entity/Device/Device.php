<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-12-26
 * Time: 01:58
 */

namespace AppBundle\Entity\Device;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserDeviceRepository")"
 * @ORM\Table(name="user_device")
 **/
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Engagement
 * @package AppBundle\Entity\Device
 * @ORM\Table(name="log_device", options={"comment":"User device tracking"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeviceRepository")
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device unique Device ID"})
     */
    protected $udid;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling device Version"})
     */
    protected $udv;

    /**
     * @ORM\Column(type="string", length=128, nullable=false, options={"comment":"Calling app Version"})
     */
    protected $av;

    /**
     * @ORM\Column(type="string", length=128, nullable=true, options={"comment":"Calling device ip address"})
     */
    protected $ipAddress;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="teas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * Device constructor.
     * @param string $udid
     * @param string $udv
     * @param User $user
     */
    public function __construct(string $udid, string $udv, string $av, ?User $user)
    {
        $this->udid = $udid;
        $this->udv = $udv;
        $this->av = $av;
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUdid()
    {
        return $this->udid;
    }

    /**
     * @return mixed
     */
    public function getUdv()
    {
        return $this->udv;
    }

    /**
     * @return mixed
     */
    public function getAv()
    {
        return $this->av;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }
}