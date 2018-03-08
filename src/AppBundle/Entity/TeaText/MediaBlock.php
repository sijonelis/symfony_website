<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 2/18/2017
 * Time: 4:26 PM
 */

namespace AppBundle\Entity\TeaText;


class MediaBlock
{
    const TYPE_MAIN_TITLE = 'main_title';
    const TYPE_TITLE = 'title';
    const TYPE_PARAGRAPH = 'paragraph';
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';

    private $value;
    private $type;

    public function __construct($value, $type) {
        $this->value = $value;
        $this->type = $type;
    }
}
?>

