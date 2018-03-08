<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 11/28/2016
 * Time: 8:56 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeaFeaturedRepository")
 * @ORM\Table(name="tea_featured")
 */
class TeaFeatured
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $day;

    /**
     * @ORM\ManyToOne(targetEntity="Tea", inversedBy="featuredTeas")
     * @ORM\JoinColumn(name="tea_id", referencedColumnName="id")
     */
    protected $tea;

    /**
     * @ORM\OneToOne(targetEntity="TeaFeatured", cascade={"persist"})
     * @ORM\JoinColumn(name="next_tea_featured_id", referencedColumnName="id")
     */
    protected $nextTea;

    /**
     * @ORM\OneToOne(targetEntity="TeaFeatured")
     * @ORM\JoinColumn(name="previous_tea_featured_id", referencedColumnName="id")
     */
    protected $previousTea;

    /**
     * @return mixed
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param mixed $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }
    /**
     * @return mixed
     */
    public function getTea()
    {
        return $this->tea;
    }

    /**
     * @param mixed $tea
     */
    public function setTea($tea)
    {
        $this->tea = $tea;
    }

    /**
     * @return mixed
     */
    public function getNextTea()
    {
        return $this->nextTea;
    }

    /**
     * @param mixed $nextTea
     */
    public function setNextTea($nextTea)
    {
        $this->nextTea = $nextTea;
    }

    /**
     * @return mixed
     */
    public function getPreviousTea()
    {
        return $this->previousTea;
    }

    /**
     * @param mixed $previousTea
     */
    public function setPreviousTea($previousTea)
    {
        $this->previousTea = $previousTea;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}