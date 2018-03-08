<?php
namespace AppBundle\Services;

use AppBundle\Entity\Communication\PushNotification;
use OneSignal\Config as OScfg;
use OneSignal\OneSignal as OS;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Client\Common\HttpMethodsClient as HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;

class OneSignal
{
    private $oneSignal;
    private $applicationId;
    private $apiKey;

    /**
     * OneSignal constructor.
     * @param $applicationId
     * @param $apiKey
     * @param $userAuthKey
     */
    public function __construct($applicationId, $apiKey, $userAuthKey)
    {
        $this->applicationId = $applicationId;
        $this->apiKey = $apiKey;

        $config = new OScfg();
        $config->setApplicationId($applicationId);
        $config->setApplicationAuthKey($apiKey);
        $config->setUserAuthKey($userAuthKey);

        $guzzle = new GuzzleClient([
        ]);

        $client = new HttpClient(new GuzzleAdapter($guzzle), new GuzzleMessageFactory());

        $this->oneSignal = new OS($config, $client);
    }

    /**
     * @param PushNotification $pushNotification
     * @return array
     */
    public function newMessage($pushNotification) {
        $notificationData = [
            'included_segments' => ['All'],
            'contents' => [
                'en' => $pushNotification->getText(),
            ],
            'headings' => [
                'en' => $pushNotification->getTitle(),
            ],
//            'buttons' => [
//                [
//                    'id' => 'button_id',
//                    'text' => 'Button text',
//                    'icon' => 'button_icon',
//                ],
//            ],
//            'filters' => [
//                [
//                    'field' => 'tag',
//                    'key' => 'level',
//                    'relation' => '>',
//                    'value' => '10',
//                ],
//            ],
//            'send_after' => 'Sep 24 2017 14:00:00 GMT-0700',
            'isIos' => $pushNotification->getSendToIos(),
            'isAndroid' => $pushNotification->getSendToAndroid(),
            'data' => [
                'notification_id' => $pushNotification->getId(),
            ],
            'url' => $pushNotification->getUrl()
        ];

        return $this->oneSignal->notifications->add($notificationData);
    }
}