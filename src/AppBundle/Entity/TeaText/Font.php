<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 2/18/2017
 * Time: 4:24 PM
 */

namespace AppBundle\Entity\TeaText;


class Font
{
    const TYPE_TITLE = 'title';
    const TYPE_PARAGRAPH = 'paragraph';

    private $name;
    private $size;
    private $color;
    private $weight;
    private $type;

    public function __construct($type)
    {
            switch ($type) {
                case self::TYPE_TITLE:
                    $this->name = 'PingFang SC';
                    $this->size = 17;
                    $this->color = '#BC9F59';
                    $this->weight = 'regular';
                    $this->type = self::TYPE_TITLE;
                    break;
                case self::TYPE_PARAGRAPH:
                    $this->name = 'PingFang SC';
                    $this->size = 15;
                    $this->color = '#BC9F59';
                    $this->weight = 'light';
                    $this->type = self::TYPE_PARAGRAPH;
                    break;
                default:
                    throw new \TypeError("Unknown text block entered.");
            }
    }


    public static function getFontBlocks(array $types = []) {
        $blocks = [];
        foreach ($types as $type) {
            $blocks[] = new Font($type);
        }
        return $blocks;
    }
}