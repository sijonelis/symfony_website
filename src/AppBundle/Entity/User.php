<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")"
 * @ORM\Table(name="user")
 * @Vich\Uploadable()
 */
class User extends BaseUser
{
    const LOGIN_TYPE = [
        'web' => 1,
        'weixin' => 2,
        'email' => 3,
        'passwordReset' => 4
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Tea", mappedBy="user")
     */
    protected $teas;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="user")
     */
    protected $notes;

    /**
     * @ORM\OneToOne(targetEntity="WeixinProfile", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="weixin_account_id", referencedColumnName="id")
     */
    protected $weixinAccount = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $accessToken = null;

    /**
     * @ORM\OneToMany(targetEntity="TeaFavourite", mappedBy="user")
     */
    protected $favouriteTeas;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default":"yipiao"})
     */
    protected $nickname;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":true})
     */
    protected $receivePushNotifications = true;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     */
    protected $passwordResetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true, options={"default":null})
     */
    protected $passwordResetTokenExpiresAt;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":null})
     */
    protected $lastLoginType;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     */
    protected $lastIpAddress;

     /**
     * @Vich\UploadableField(mapping="user_avatar", fileNameProperty="avatar")
     * @var File
     */
    private $coverImageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->favouriteTeas = new ArrayCollection();
        parent::__construct();
    }

    public function getUserBlock() {
        return [
            'access-token' => $this->accessToken
        ];
    }

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }
    /**
     * @return mixed
     */
    public function getWeixinAccount()
    {
        return $this->weixinAccount;
    }

    /**
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @param mixed $avatar
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function setAvatarFile(File $image = null)
    {
        $this->avatarFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * @param mixed $weixinAccount
     */
    public function setWeixinAccount($weixinAccount)
    {
        $this->weixinAccount = $weixinAccount;
    }

    /**
     * @return mixed
     */
    public function getFavouriteTeas()
    {
        return $this->favouriteTeas;
    }

    /**
     * @param mixed $favouriteTeas
     */
    public function setFavouriteTeas($favouriteTeas)
    {
        $this->favouriteTeas = $favouriteTeas;
    }

    /**
     * @return mixed
     */
    public function getTeas()
    {
        return $this->teas;
    }

    /**
     * @param mixed $teas
     */
    public function setTeas($teas)
    {
        $this->teas = $teas;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
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
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @param mixed $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * @return bool
     */
    public function getReceivePushNotifications()
    {
        return $this->receivePushNotifications == 0 ? false : true;
    }

    /**
     * @param mixed $receivePushNotifications
     */
    public function setReceivePushNotifications($receivePushNotifications)
    {
        $this->receivePushNotifications = $receivePushNotifications;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * @param mixed $passwordResetToken
     */
    public function setPasswordResetToken($passwordResetToken)
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    /**
     * @return mixed
     */
    public function getPasswordResetTokenExpiresAt()
    {
        return $this->passwordResetTokenExpiresAt;
    }

    /**
     * @param mixed $passwordResetTokenExpiresAt
     */
    public function setPasswordResetTokenExpiresAt($passwordResetTokenExpiresAt)
    {
        $this->passwordResetTokenExpiresAt = $passwordResetTokenExpiresAt;
    }

    /**
     * @return mixed
     */
    public function getLastLoginType()
    {
        return $this->lastLoginType;
    }

    /**
     * @param mixed $lastLoginType
     */
    public function setLastLoginType($lastLoginType)
    {
        $this->lastLoginType = $lastLoginType;
    }

    /**
     * @return mixed
     */
    public function getLastIpAddress()
    {
        return $this->lastIpAddress;
    }

    /**
     * @param mixed $lastIpAddress
     */
    public function setLastIpAddress($lastIpAddress): void
    {
        $this->lastIpAddress = $lastIpAddress;
    }
}