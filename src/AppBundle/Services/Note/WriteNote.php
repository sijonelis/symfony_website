<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-06-30
 * Time: 16:21
 */

namespace AppBundle\Services\Note;


use AppBundle\Entity\Note;
use Doctrine\ORM\EntityManager;

class WriteNote
{
    private $entityManager;

    /**
     * WriteNote constructor.
     * @param EntityManager $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function findNoteByUserAndTea($user, $tea) {
    }

    /**
     * @param Note $note
     * @return bool
     * @internal param $tea
     */
    public function write($note) {
        $tea = $this->entityManager->getRepository("AppBundle:Tea")->find($note->getTea()->getId());
        if (!$tea) return false;

        $oldNote = $this->entityManager->getRepository("AppBundle:Note")->findOneBy(['user' =>$note->getUser(), 'tea' => $tea]);
        if (!$oldNote) {
            $note->setCreatedAt(new \DateTime());
            $note->setTea($tea);
        } else {
            $oldNote->setNotifyStaff($note->getNotifyStaff());
            $oldNote->setNote($note->getNote());
            $note = $oldNote;
        }

        $note->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($note);
        $this->entityManager->flush();
        return true;
    }
}