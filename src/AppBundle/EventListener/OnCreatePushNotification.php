<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 3/14/2017
 * Time: 9:02 PM
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Communication\PushNotification;
use AppBundle\Entity\Tea;
use AppBundle\Services\OneSignal;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PostSetEntityUserListener
 * @package AppBundle\EventListener
 *
 * This event listener sets owning user for Tea entity
 */
class OnCreatePushNotification
{

    private $oneSignal;

    public function __construct(OneSignal $oneSignal)
    {
        $this->oneSignal = $oneSignal;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof PushNotification) {
            if(!$entity->getSendAt() && !$entity->getSentAt()) {
                $entity->setOneSignalResponse(json_encode($this->oneSignal->newMessage($entity)));
                $entity->setSentAt(new \DateTime());
            }
        }
    }
}
