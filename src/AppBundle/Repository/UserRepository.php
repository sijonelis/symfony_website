<?php
namespace AppBundle\Repository;


use AppBundle\Entity\Note;
use AppBundle\Entity\TeaFavourite;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Tests\StringableObject;

class UserRepository extends EntityRepository
{
    public function findUserByOpenid($openId)
    {
        $user = $this->getEntityManager()
            ->createQuery(
                'SELECT u 
                      FROM AppBundle:User u 
                      INNER JOIN AppBundle:WeixinProfile wp WITH u.weixinAccount = wp.id
                      WHERE wp.openId = :openId AND u.weixinAccount IS NOT NULL'
            )
            ->setParameters(['openId' => $openId])
            ->getOneOrNullResult();
        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    public function getFavouriteTeas($user) {

        $favouriteTeas = $user->getFavouriteTeas()->toArray();
        $response['count'] = count($favouriteTeas);
        $response['teas']= [];
        /** @var TeaFavourite $tea */
        foreach ($favouriteTeas as $tea) {
            $teaObject = new \stdClass();
            //id, name, type, bgimage
            $teaObject->id = $tea->getTea()->getId();
            $teaObject->name = $tea->getTea()->getName();
            $teaObject->type = $tea->getTea()->getTeaType()->getName();
            $teaObject->cover_image = $tea->getTea()->getCoverImage();
            $response['teas'][] = $teaObject;
        }
        return  json_decode(json_encode($response), true);
    }

    /**
     * @param User $user
     * @return string
     */
    public function getFavouriteTeaCount($user) {
        return count($user->getFavouriteTeas());
    }

    /**
     * @param User $user
     * @return string
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserNoteCount($user) {
        $noteCount = $this->getEntityManager()
            ->createQuery(
                'SELECT COUNT (n.id)
                      FROM AppBundle:Note n 
                      WHERE n.user = :user'
            )
            ->setParameters(['user' => $user])->getSingleScalarResult();
        return isset($noteCount) ? (int) $noteCount : 0;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getUserNotes($user)
    {
        $notes = $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.note as text, n.updatedAt as updated_at, t.coverImage as tea_image, t.id as tea_id, t.name as tea_name
                      FROM AppBundle:Note n 
                      INNER JOIN AppBundle:Tea t WITH n.tea = t.id
                      WHERE n.user = :user'
            )
            ->setParameters(['user' => $user])
            ->getArrayResult();
        $notes = $this->parseNotes($notes);
        $response['count'] = count($notes);
        $response['notes'] = $notes;
        return $response;
    }

    private function parseNotes($notes) {
        $parsedNotes = [];
        foreach ($notes as $key => $note) {
            $parsedNote = [];
            $parsedNote['note']['updated_at'] = $note['updated_at']->format('Y.m.d');
            $parsedNote['note']['id'] = $note['id'];
            $parsedNote['note']['text'] = $note['text'];
            $parsedNote['tea']['cover_image'] = $note['tea_image'];
            $parsedNote['tea']['id'] = $note['tea_id'];
            $parsedNote['tea']['name'] = $note['tea_name'];
            $parsedNotes[] = $parsedNote;
        }
        return $parsedNotes;
    }

    /**
     * @param User $user
     * @param $image
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUserImage(User $user, $image): array
    {
        $user->setAvatar($image['fileName']);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return [
            'uploaded' => true,
            'avatar' => $user->getAvatar()
        ];
    }

    /**
     * @param User $user
     * @param array $updatedParams
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateUser(User $user, array $updatedParams) {
        if (array_key_exists('receive_push_notifications', $updatedParams))
            $user->setReceivePushNotifications($updatedParams['receive_push_notifications']);
        if (array_key_exists('nickname', $updatedParams))
            $user->setNickname($updatedParams['nickname']);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return [
            'receive_push_notifications' => $user->getReceivePushNotifications(),
            'nickname' => $user->getNickname()
        ];
    }

    /**
     * @param User $user
     * @param array $updatedParams
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function syncUser(User $user, array $updatedParams) {
        if (!$updatedParams || !is_array($updatedParams))
            return false;

        $user->setReceivePushNotifications($updatedParams['receive_push_notifications']);
        $user->setNickname($updatedParams['nickname']);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return true;
    }
}