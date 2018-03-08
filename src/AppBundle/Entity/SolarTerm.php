<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-07-21
 * Time: 20:45
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 * @ORM\Table(name="solar_term")
 */
class SolarTerm
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $dateFrom;

    /**
     * @ORM\Column(type="integer")
     */
    private $dateTo;

    /**
     * @ORM\OneToMany(targetEntity="Tea", mappedBy="solarTermFrom")
     */
    protected $teasFrom;
    /**
     * @ORM\OneToMany(targetEntity="Tea", mappedBy="solarTermTo")
     */
    protected $teasTo;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param mixed $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param mixed $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getTeasFrom()
    {
        return $this->teasFrom;
    }

    /**
     * @param mixed $teasFrom
     */
    public function setTeasFrom($teasFrom)
    {
        $this->teasFrom = $teasFrom;
    }

    /**
     * @return mixed
     */
    public function getTeasTo()
    {
        return $this->teasTo;
    }

    /**
     * @param mixed $teasTo
     */
    public function setTeasTo($teasTo)
    {
        $this->teasTo = $teasTo;
    }

    public function __toString()
    {
        return $this->getName();
    }
}