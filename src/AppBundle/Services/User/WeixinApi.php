<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-12-22
 * Time: 01:48
 */

namespace AppBundle\Services\User;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\Log\WeixinApiAccess;
use Doctrine\ORM\EntityManager;

class WeixinApi
{
    private $appId;
    private $appSecret;
    private $entityManager;
    /** @var Device $device */
    private $device;
    private $weixinProfileDataUrl = 'https://api.weixin.qq.com/sns/userinfo?';
    private $weixinProfileOauthUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?';


    /**
     * WeixinApi constructor.
     * @param $appId
     * @param $appSecret
     * @param EntityManager $entityManager
     */
    public function __construct($appId, $appSecret, $entityManager)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->entityManager = $entityManager;
    }

     public function getProfileFromWeixin($code, Device $device)
    {
        //set the calling device
        /** @var Device $device */
        $this->device = $device;


        $weixinUrl = $this->weixinProfileOauthUrl . "appId=" . $this->appId . "&secret=" . $this->appSecret . "&code=" . $code . "&grant_type=authorization_code";
        $authData = json_decode($this->call($weixinUrl));
        if (!$authData || !property_exists($authData, 'access_token')) {
            $this->logWeixinAccess(json_encode($authData), 'oauth', false);
            return $authData;
        }

        $this->logWeixinAccess(json_encode($authData), 'oauth', true);
        return $this->getProfileData($authData->access_token, $authData->openid);
    }

    private function getProfileData($accessToken, $openId)
    {
        $weixinUrl = $this->weixinProfileDataUrl . "access_token=$accessToken&openid=$openId";
        $profileData = $this->call($weixinUrl);
        $profileJson = json_decode($profileData);

        if (!isset($profileJson)){
            $this->logWeixinAccess($profileData, 'get_profile', false);
            return false;
        }

        $this->logWeixinAccess($profileData, 'get_profile', property_exists($profileJson, 'errcode') ? false : true);
        return $profileJson;
    }

    private function call($weixinUrl) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $weixinUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $weixinAuthResponse = curl_exec($ch);
        curl_close($ch);
        return $weixinAuthResponse;
    }

    private function logWeixinAccess($data, $action, $isSuccess)
    {
        $weixinLog = new WeixinApiAccess();
        $weixinLog->setData($data);
        $weixinLog->setAction($action);
        $weixinLog->setIsSuccess($isSuccess);
        $weixinLog->setUdid($this->device->getUdid());
        $weixinLog->setAppVersion($this->device->getAv());
        $weixinLog->setDeviceVersion($this->device->getUdv());
        $this->entityManager->persist($weixinLog);
        $this->entityManager->flush($weixinLog);
    }
}