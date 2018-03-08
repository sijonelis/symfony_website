<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-21
 * Time: 15:29
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\Log\Engagement;
use AppBundle\Entity\Log\LogDataObject;
use AppBundle\Entity\Note;
use AppBundle\Entity\User;
use ClassesWithParents\E;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class EngagementRepository extends BaseRepository
{
    /** @var  Device $device */
    private $device;

    /** @var Engagement $engagement */
    private $engagement;

    /**
     * Finalizes and saves the data tracker
     */
    private function saveTracker(): void {
        try {
            $this->getEntityManager()->persist($this->engagement);
            $this->getEntityManager()->flush($this->engagement);
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        } catch (ForeignKeyConstraintViolationException $e){
        }
    }

    private function saveEntityArray(array $entities): void {
        try{
            foreach ($entities as $entity) {
                $this->getEntityManager()->persist($entity);
            }
            $this->getEntityManager()->flush();
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        } catch (ForeignKeyConstraintViolationException $e){
        }
    }

    /**
     * @return Engagement
     */
    private function createNewEngagement() : Engagement {
        return new Engagement($this->device);
    }

    /**
     * @param $device
     */
    private function setDevice($device)
    {
        $this->device = $device;
    }

    public function setUp(Device $device) {
        $this->setDevice($device);
        $this->engagement = $this->createNewEngagement();
    }


    /**
     * @param int $teaId
     * @param bool $isFavourite
     */
    public function trackTeaFavourite(int $teaId, bool $isFavourite)
    {
        $this->engagement->setType($isFavourite ? Engagement::TYPE_FAVOURITE : Engagement::TYPE_UNFAVOURITE);
        $this->engagement->setTea($this->getReference('Tea', $teaId));

        $this->saveTracker();
    }

    /**
     *
     */
    public function trackNewsfeedDetails()
    {
    }

    /**
     * @param int $teaId
     * @param int $newsfeedTeaId
     */
    public function trackNewsfeedSwipe(int $teaId, int $newsfeedTeaId)
    {
        $this->engagement->setType(Engagement::TYPE_NEWSFEED_SWIPE);
        $this->engagement->setNewsfeed($this->getReference('TeaFeatured', $newsfeedTeaId));
        $this->engagement->setTea($this->getReference('Tea', $teaId));

        $this->saveTracker();
    }

    /**
     *
     */
    public function trackAppShare() {
        //todo implement in new api
    }

    /**
     * @param int $teaId
     */
    public function trackTeaDetails(int $teaId)
    {
        //todo implement in new api
    }

    /**
     * @param Note $note
     * @param int $engagementSource
     */
    public function trackNoteModify(Note $note, int $engagementSource)
    {
        if($note->getNotifyStaff())
            $this->engagement->setType($engagementSource == 0 ? Engagement::NOTE_ENGAGEMENT_TEA_SEND : Engagement::NOTE_ENGAGEMENT_TEA);
        else
            $this->engagement->setType($engagementSource == 0 ? Engagement::NOTE_ENGAGEMENT_PROFILE_SEND : Engagement::NOTE_ENGAGEMENT_PROFILE);
        $this->engagement->setNote($this->getReference('Note', $note->getId()));
        $this->engagement->setTea($this->getReference('Tea', $note->getTea()->getId()));

        try {
            $this->saveTracker();
        } catch (ORMException $e) {
        }
    }

    /**
     * @param int $teaId
     */
    public function trackNoteOpen(int $teaId)
    {
        //todo implement in new api
    }

    /**
     * @param string $searchString
     */
    public function trackSearch(string $searchString)
    {
    }

    /**
     * @param int $type
     */
    public function trackNewTeaSuggest(int $type)
    {
        $this->engagement->setType($type);
        $this->saveTracker();
    }

    /**
     * Logs profile update and profile image update
     * @param int $type
     */
    public function trackProfileUpdate(int $type)
    {
        $this->engagement->setType($type);
        $this->saveTracker();
    }

    /**
     * Logs engagement data from tracking api
     * @param array $engagementData
     */
    public function trackGeneralEngagement(array $engagementData) {
        $engagementsToSave = [];
        foreach ($engagementData as $singleEngagementRecord) {
            $singleEngagementRecord = new LogDataObject($singleEngagementRecord);
            if ($singleEngagementRecord->getDataType() == 1 && $singleEngagementRecord->validateEventType()) {
                $engagement = $this->createNewEngagement();
                $engagement->setType($singleEngagementRecord->getEventType());
                $engagement->setTea($this->getReference('Tea', $singleEngagementRecord->getTeaId()));
                $engagement->setNote($this->getReference('Note', $singleEngagementRecord->getNoteId()));
                $engagement->setNewsfeed($this->getReference('TeaFeatured', $singleEngagementRecord->getNewsfeedId()));
                $engagementsToSave[] = $engagement;
            }
        }
        $this->saveEntityArray($engagementsToSave);
    }
}