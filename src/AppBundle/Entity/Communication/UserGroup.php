<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-10-20
 * Time: 13:25
 */

namespace AppBundle\Entity\Communication;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserGroupRepository")
 * @ORM\Table(name="communication_user_group")
 */
class UserGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="integer")
     */
    protected $pushNotificationSendFrequencyDays;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $ruleDescription;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Communication\PushNotification", mappedBy="user_segment", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $pushNotifications;

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
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
    public function getPushNotificationSendFrequencyDays()
    {
        return $this->pushNotificationSendFrequencyDays;
    }

    /**
     * @param mixed $pushNotificationSendFrequencyDays
     */
    public function setPushNotificationSendFrequencyDays($pushNotificationSendFrequencyDays)
    {
        $this->pushNotificationSendFrequencyDays = $pushNotificationSendFrequencyDays;
    }

    /**
     * @return mixed
     */
    public function getRuleDescription()
    {
        return $this->ruleDescription;
    }

    /**
     * @param mixed $ruleDescription
     */
    public function setRuleDescription($ruleDescription)
    {
        $this->ruleDescription = $ruleDescription;
    }

    /**
     * @return mixed
     */
    public function getPushNotifications()
    {
        return $this->pushNotifications;
    }

    /**
     * @param mixed $pushNotifications
     */
    public function setPushNotifications($pushNotifications)
    {
        $this->pushNotifications = $pushNotifications;
    }

}