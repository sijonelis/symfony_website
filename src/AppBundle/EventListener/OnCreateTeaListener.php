<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 3/14/2017
 * Time: 9:02 PM
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\Tea;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PostSetEntityUserListener
 * @package AppBundle\EventListener
 *
 * This event listener sets owning user for Tea entity
 */
class OnCreateTeaListener
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $event)
    {
        $entity = $event->getObject();
        if ($entity instanceof Tea && !$entity->getUser()) {
            $entity->setUser($this->tokenStorage->getToken()->getUser());
        }
    }
}
