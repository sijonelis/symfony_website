<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-12-26
 * Time: 01:24
 */

namespace AppBundle\Controller\Api;


use AppBundle\Entity\Device\Device;
use AppBundle\Entity\User;
use AppBundle\Services\User\Engagement;
use AppBundle\Services\User\Exposure;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseApiController extends FOSRestController
{
    private $request;
    /** @var  Device $device */
    private $device;

    /** @var  Engagement $engagementTracker*/
    private $engagementTracker;
    /** @var  Exposure $exposureTracker*/
    private $exposureTracker;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /**
     * MUST BE CALLED AT THE BEGINNING OF EVERY API
     * @return bool
     */
    public function isValid() : bool {
        $ipAddress = $this->getRequest()->getClientIp();
        if ($this->getUser()) {
            $this->updateUserIpAddress($ipAddress);
        }
        if (!empty($this->getUdid()) && !empty($this->getDeviceVersion()) && !empty($this->getAppVersion())) {
            $this->device = new Device($this->getUdid(), $this->getDeviceVersion(), $this->getAppVersion(), $this->getUser());
            $this->device->setIpAddress($ipAddress);
            $this->setUpUserTrackers();
        }
        return isset($this->device);
    }

    /**
     * Gets request
     */
    public function getRequest() : Request {
        return $this->request;
    }

    /**
     * Sets request
     * @param Request $request
     */
    public function setRequest(request $request) {
        $this->request = $request->request;
    }

    /**
     * Returns calling device details
     * @return Device
     */
    public function getDevice(): ?Device {
        return $this->device;
    }

    /**
     * @param int $default
     * @return int
     */
    public function getSource(int $default): int {
        return $this->request->get('s', $default);
    }

    public function apiError() : Response{
        return new Response(
            json_encode([
                'errors' =>
                [
                    'status' => 400,
                    'title' => 'Access Error.',
                    'details' => 'Bad Headers'
                ]
            ])
            ,Response::HTTP_BAD_REQUEST);
    }

    /**
     * Gets a single header
     * @param $key
     * @return string
     */
    private function getHeader($key) : ?string {
        $request = $this->getRequest();

        if (!$request) return '';
        return $request->headers->get($key);
    }

    /**
     * Gets device id
     * @return string
     */
    private function getUdid()  : ?string {
        return $this->getHeader('device-id');
    }

    /**
     * Gets device version
     * @return string
     */
    private function getDeviceVersion() : ?string {
        return $this->getHeader('device-version');
    }
    /**
     * Gets device version
     * @return string
     */
    private function getAppVersion()  : ?string {
        return $this->getHeader('app-version');
    }

    private function setUpUserTrackers() {
        $this->engagementTracker = $this->get('app.log.engagement');
        $this->engagementTracker->setDevice($this->device);

        $this->exposureTracker = $this->get('app.log.exposure');
        $this->exposureTracker->setDevice($this->device);
    }

    private function updateUserIpAddress(string $ipAddress) {
        /** @var User $user */
        $user = $this->getUser();
        if ($user->getLastIpAddress() != $ipAddress) {
            $user->setLastIpAddress($ipAddress);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * @return Engagement
     */
    public function getEngagementTracker() : Engagement
    {
        return $this->engagementTracker;
    }
    /**
     * @return Exposure
     */
    public function getExposureTracker() : Exposure
    {
        return $this->exposureTracker;
    }
}