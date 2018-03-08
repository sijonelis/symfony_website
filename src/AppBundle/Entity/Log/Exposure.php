<?php

namespace AppBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Exposure
 * @package AppBundle\Entity\Log
 * @ORM\Table(name="log_exposure", options={"comment":"Exposure tracking"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ExposureRepository")
 */
class Exposure extends BaseTracking
{
    const TEA_EXPOSURE_NEWSFEED = 0;
    const TEA_EXPOSURE_SEARCH = 1;
    const TEA_EXPOSURE_PROFILE = 2;
    const TEA_EXPOSURE_SHARE = 3;
    const NEWSFEED_EXPOSURE = 10;
    const PROFILE_EXPOSURE = 11;
    const NOTE_EXPOSURE_PROFILE = 12;
}