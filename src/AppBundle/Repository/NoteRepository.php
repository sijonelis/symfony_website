<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Note;
use AppBundle\Entity\Tea;
use AppBundle\Entity\User;
use AppBundle\Services\Note\WriteNote;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;

/**
 * NoteRepository
 *
 */
class NoteRepository extends BaseRepository
{
    public function getUnreadNotesForTea($teaId)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n FROM AppBundle:Note n WHERE n.tea = :teaId AND n.viewed = FALSE ORDER BY n.id DESC'
            )
            ->setParameters(['teaId' => $teaId])
            ->getResult();
    }

    public function getNote($id, $user)
    {
        $note = $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.note, n.updatedAt as updated_at, t.coverImage as tea_image, t.id as tea_id, t.name as tea_name
                      FROM AppBundle:Note n 
                      INNER JOIN AppBundle:Tea t WITH n.tea = t.id
                      WHERE n.user = :user AND n.id = :id'
            )
            ->setParameters(['user' => $user, 'id' => $id])
            ->getOneOrNullResult();
        if ($note)
            $note['updated_at'] = $note['updated_at']->format('Y.m.d');
        return $note;
    }

    public function writeNote($note, User $user) {
        if(!is_object($note)) {
            $noteData = $note;

            $tea = $this->getReference('Tea', $noteData['tea_id']);
            $oldNote = $this->getEntityManager()->getRepository("AppBundle:Note")->findOneBy(['user' =>$user, 'tea' => $tea]);
            //Note already exists on the server. We should not rewrite it
            if ($oldNote) return false;

            $note = new Note();
            $note->setNote($noteData['note']);
            $note->setNotifyStaff($noteData['notify_staff']);
            $note->setTea($tea);
            $note->setUser($user);
        }
        //check if note is valid
        /** @var Note $note */
        if ($note->getTea() == null || $note->getNote() === null) return false;
        $note->setUser($user);
        if (!isset($tea))
            $tea = $this->getReference('Tea', $note->getTea()->getId());
        if (!$tea) return false;

        //update old note if such is present
        $oldNote = $this->getEntityManager()->getRepository("AppBundle:Note")->findOneBy(['user' =>$note->getUser(), 'tea' => $tea]);
        if (!$oldNote) {
            $note->setCreatedAt(new \DateTime());
            $note->setTea($tea);
        } else {
            $oldNote->setNotifyStaff($note->getNotifyStaff());
            $oldNote->setNote($note->getNote());
            $note = $oldNote;
        }

        //save note
        $note->setUpdatedAt(new \DateTime());
        $this->getEntityManager()->persist($note);
        try {
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException | ForeignKeyConstraintViolationException $e) {
            return false;
        }
        return $note;
    }
}
