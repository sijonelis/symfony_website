<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-06-18
 * Time: 22:27
 */

namespace AppBundle\EventListener;


use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiResponseListener implements EventSubscriberInterface
{
    /** @var  UserRepository*/
    private $userRepostory;
    /** @var  EntityManager */
    private $entityManager;

    public function __construct($userRepository, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepostory = $userRepository;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array('onKernelView', 50),
        );
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $accessToken = $event->getRequest()->headers->get('authorization');
        if (!$accessToken && $event->getControllerResult() && array_key_exists('access_token', $event->getControllerResult()))
            $accessToken = $event->getControllerResult()['access_token'];

        $userBlock = [
            'logged_in' => false,
            'receive_push_notifications' => false
        ];

        if($accessToken) {
            /** @var User $user */
            $user = $this->userRepostory->findOneBy(['accessToken' => $accessToken]);
            $userBlock['logged_in'] = empty($user) ? true : false;
            $userBlock['receive_push_notifications'] = !empty($user) ? $user->getReceivePushNotifications() : false;
        }

        $result['data'] = $event->getControllerResult();
        $result['user'] =  $userBlock;
        $event->setControllerResult($result);
    }
}