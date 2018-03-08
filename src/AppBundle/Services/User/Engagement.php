<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-21
 * Time: 14:28
 */

namespace AppBundle\Services\User;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\Note;
use AppBundle\Entity\User;
use AppBundle\Repository\EngagementRepository;
use AppBundle\Repository\TeaRepository;
class Engagement
{
    /** @var EngagementRepository $engagementRepository */
    private $engagementRepository;
    /** @var TeaRepository $teaRepository */
    private $teaRepository;
    /**
     * User and Device are injected from aBaseApiController
     */
    /** @var  Device $device */
    private $device;
    /** @var User $user */
    private $user;

    /**
     * Engagement constructor.
     * @param EngagementRepository $engagementRepository
     * @param TeaRepository $teaRepository
     */
    public function __construct(EngagementRepository $engagementRepository, TeaRepository $teaRepository)
    {
        $this->engagementRepository = $engagementRepository;
        $this->teaRepository = $teaRepository;
    }

    public function logProfileUpdate(int $type){
        $this->engagementRepository->trackProfileUpdate($type);
    }

    /**
     * Tea suggestion
     * @param int $suggestionSource
     */
    public function logNewTeaSuggest(int $suggestionSource) {
        $this->engagementRepository->trackNewTeaSuggest($suggestionSource);
    }

    /**
     * Search engagement
     * @param null|string $searchString
     */
    public function logSearch(?string $searchString) {
        $this->engagementRepository->trackSearch($searchString);
    }

    /**
     * Note engagements
     */

    /**
     * @param int $teaId
     */
    public function logNoteView(int $teaId) {
        $this->engagementRepository->trackNoteOpen($teaId);
    }

    /**
     * @param Note $note
     * @param int $source
     */
    public function logNoteModify(Note $note, int $source) {
        $this->engagementRepository->trackNoteModify($note, $source);
    }

    /**
     * Tea engagements
     */

    /**
     * @param int $teaId
     * @param bool $isFavourite
     */
    public function logTeaFavourite(int $teaId, bool $isFavourite) {
        $this->engagementRepository->trackTeaFavourite($teaId, $isFavourite);
    }

    /**
     * @param int $teaId
     */
    public function logTeaDetails(int $teaId) {
        $this->engagementRepository->trackTeaDetails($teaId);
    }

    /**
     * Newsfeed engagement (open tea details)
     * Logged as tea engagement
         * @param int $newsfeedTeaId
     */
    public function logNewsfeedSwipe(int $newsfeedTeaId) {
        //todo implement with updated newsfeed
        $this->engagementRepository->trackNewsfeedSwipe(0, $newsfeedTeaId);
    }

    /**
     * Share engagement
     * @param int $type
     * @param null $teaId
     */
    public function logShare(int $type, $teaId = null) {
        if ($type == self::TYPE_SHARE_APP) {
            $this->logAppShare();
        }
        if ($type == self::TYPE_SHARE_TEA) {
            $this->logTeaShare($teaId);
        }
    }

    /**
     * Share engagement helpers
     */
    private function logAppShare() {
        $this->engagementRepository->trackAppShare();
    }

    private function logTeaShare(int $teaId) {

    }

    public function logGeneralEngagement($engagementData) {
        $this->engagementRepository->trackGeneralEngagement($engagementData);
    }

    public function setDevice(Device $device) {
        $this->device = $device;
        $this->engagementRepository->setUp($device);
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}