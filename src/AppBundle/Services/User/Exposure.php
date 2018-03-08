<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-01-29
 * Time: 23:07
 */

namespace AppBundle\Services\User;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\User;
use AppBundle\Repository\ExposureRepository;
use AppBundle\Repository\TeaRepository;

class Exposure
{
    /** @var ExposureRepository $exposureRepository */
    private $exposureRepository;
    /** @var TeaRepository $teaRepository */
    private $teaRepository;
    /**
     * User and Device are injected from aBaseApiController
     */
    /** @var  Device $device */
    private $device;

    /**
     * Engagement constructor.
     * @param ExposureRepository $exposureRepository
     * @param TeaRepository $teaRepository
     */
    public function __construct(ExposureRepository $exposureRepository, TeaRepository $teaRepository)
    {
        $this->exposureRepository = $exposureRepository;
        $this->teaRepository = $teaRepository;
    }

    /**
     * @param array $teaList
     * @param int $exposureType
     */
    public function logTeaShow(array $teaList, int $exposureType) {
        $this->exposureRepository->trackTeas($teaList, $exposureType);
    }

    /**
     * Logs note exposure in profile page
     * @param array $noteList
     */
    public function logNoteShow(array $noteList): void {
        $this->exposureRepository->trackNotes($noteList);
    }

    /**
     *
     */
    public function logProfileShow(): void {
        $this->exposureRepository->trackProfile();
    }

    /**
     * App open
     * @param int $teaId
     * @param int $newsfeedId
     */
    public function logNewsfeed(int $teaId, int $newsfeedId): void {
        $this->exposureRepository->trackNewsfeed($teaId, $newsfeedId);
    }

    /**
     * App open
     */
    public function logOpenApp() {
//        $this->exposureRepository->trackNewsfeed();
    }

    /**
     *
     */
    public function logSearch() {
        $this->exposureRepository->trackSearch();
    }

    /**
     *
     */
    public function logTeaSuggest() {
        $this->exposureRepository->trackTeaSuggest();
    }

    public function setDevice(Device $device) {
        $this->device = $device;
        $this->exposureRepository->setUp($device);
    }
}