<?php
namespace AppBundle\Entity\Log;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Engagement
 * @package AppBundle\Entity\Log
 * @ORM\Table(name="log_engagement", options={"comment":"Engagement tracking"})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EngagementRepository")
 */
class Engagement extends BaseTracking
{
    //tea details engagement sources (tracking api)
    const TEA_ENGAGEMENT_NEWSFEED = 0;
    const TEA_ENGAGEMENT_SEARCH = 1;
    const TEA_ENGAGEMENT_PROFILE = 2;
    const TEA_ENGAGEMENT_SHARE = 3;
    //external links if we have any
    const TEA_ENGAGEMENT_EXTERNAL = 4;

    //note engagement save
    const NOTE_ENGAGEMENT_TEA = 10;
    const NOTE_ENGAGEMENT_PROFILE = 11;
    //note engagement + send to staff
    const NOTE_ENGAGEMENT_TEA_SEND = 12;
    const NOTE_ENGAGEMENT_PROFILE_SEND = 13;
    //share engagement(tracking api)
    const TYPE_SHARE_APP = 14;
    const TYPE_SHARE_TEA = 15;
    //favourite engagement
    const TYPE_FAVOURITE = 16;
    const TYPE_UNFAVOURITE = 17;
    //tea suggest engagement
    const TYPE_TEA_SUGGESTION_SEARCH = 18;
    const TYPE_TEA_SUGGESTION_PROFILE = 19;
    //newsfeed engagement (left-right)(tracking api)
    const TYPE_NEWSFEED_SWIPE = 20;
    //profile updates
    const TYPE_PROFILE_DETAILS = 21;
    const TYPE_PROFILE_IMAGE = 22;
    //search engagement(tracking api)
    const TYPE_SEARCH = 18;

    const TRACKING_API_ALLOWED_EVENTS = [0, 1, 2, 3, 14, 15, 18, 20];
}