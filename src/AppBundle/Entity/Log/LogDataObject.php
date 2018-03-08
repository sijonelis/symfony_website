<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-02-23
 * Time: 23:48
 */

namespace AppBundle\Entity\Log;

/**
 * Class to hold the engagement data from tracking api
 * Class LogDataObject
 * @package AppBundle\Entity\Log
 */
class LogDataObject
{
    private $dataType;
    private $eventType;
    private $teaId;
    private $noteId;
    private $newsfeedId;

    public function __construct(array $dataArray)
    {
        if (!array_key_exists('data_type', $dataArray) || !array_key_exists('event_type', $dataArray)) return;
        $this->dataType = $dataArray['data_type'];
        $this->eventType = $dataArray['event_type'];
        if (array_key_exists('tea_id', $dataArray))
            $this->teaId = $dataArray['tea_id'];
        if (array_key_exists('note_id', $dataArray))
            $this->noteId = $dataArray['note_id'];
        if (array_key_exists('newsfeed_id', $dataArray))
            $this->newsfeedId = $dataArray['newsfeed_id'];
        $this->dataType = $dataArray['data_type'];
        $this->eventType = $dataArray['event_type'];
    }

    /**
     * @return mixed
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return mixed
     */
    public function getTeaId()
    {
        return $this->teaId;
    }

    /**
     * @return mixed
     */
    public function getNoteId()
    {
        return $this->noteId;
    }

    /**
     * @return mixed
     */
    public function getNewsfeedId()
    {
        return $this->newsfeedId;
    }

    public function validateEventType(): bool
    {
        if ($this->dataType == 1) {
            return in_array($this->eventType, Engagement::TRACKING_API_ALLOWED_EVENTS) ? true : false;
        }
        return false;
    }
}