<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-21
 * Time: 15:29
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\Log\Exposure;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ExposureRepository extends BaseRepository
{
    /** @var Exposure $exposure */
    private $exposure;
    /** @var  Device $device */
    private $device;

    public function trackAppShare() {

    }

    /**
     * Finalizes and saves the data tracker
     */
    private function saveTracker(): void {
        try {
            $this->getEntityManager()->persist($this->exposure);
            $this->getEntityManager()->flush($this->exposure);
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        } catch (ForeignKeyConstraintViolationException $e){
        }
    }

    private function saveEntityArray(array $entities): void {
        try {
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
     * @return Exposure
     */
    private function createNewExposure() : Exposure {
        return new Exposure($this->device);
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
        $this->exposure = $this->createNewExposure();
    }

    /**
     * @param array $teas
     * @param int $exposureType
     */
    public function trackTeas(array $teas, int $exposureType): void {
        $shownTeas = [];
        foreach ($teas as $tea) {
            $exposure = $this->createNewExposure();
            $exposure->setType($exposureType);
            $exposure->setTea($this->getReference('Tea', $tea['id']));
            $shownTeas[] = $exposure;
        }
        $this->saveEntityArray($shownTeas);
    }

    /**
     * @param array $notes
     */
    public function trackNotes(array $notes): void {
        $shownNotes = [];
        foreach ($notes as $note) {
            $exposure = $this->createNewExposure();
            $exposure->setType(Exposure::NOTE_EXPOSURE_PROFILE);
            $exposure->setTea($this->getReference('Tea', $note['tea']['id']));
            $exposure->setNote($this->getReference('Note', $note['note']['id']));
            $shownNotes[] = $exposure;
        }
        $this->saveEntityArray($shownNotes);
    }

    /**
     *
     */
    public function trackProfile(): void
    {
        $this->exposure->setType(Exposure::PROFILE_EXPOSURE);
        $this->saveTracker();
    }

    /**
     * @param int $teaId
     * @param int $teaFeaturedId
     */
    public function trackNewsfeed(int $teaId, int $teaFeaturedId): void {
        $this->exposure->setType(Exposure::NEWSFEED_EXPOSURE);

        $this->exposure->setTea($this->getReference('Tea', $teaId));
        $this->exposure->setNewsfeed($this->getReference('TeaFeatured', $teaFeaturedId));
        $this->saveTracker();
    }

    /**
     *
     */
    public function trackSearch()
    {
    }

    /**
     *
     */
    public function trackTeaSuggest()
    {
    }
}