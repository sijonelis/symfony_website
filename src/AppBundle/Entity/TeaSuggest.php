<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2018-02-11
 * Time: 23:30
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeaSuggestRepository")
 * @ORM\Table(name="tea_suggest")
 */
class TeaSuggest
{
    /**
     *  constructor.
     */
    public function __construct()
    {
        if(!isset($this->timestamp)) $this->timestamp = new \DateTime('now');
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $timestamp;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $suggestion;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $adminNote;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $user;

    /**
     * @ORM\Column(type="text", nullable=false, options={"default": false})
     */
    protected $isOpen;

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getSuggestion()
    {
        return $this->suggestion;
    }

    /**
     * @param mixed $suggestion
     */
    public function setSuggestion($suggestion): void
    {
        $this->suggestion = $suggestion;
    }

    /**
     * @return mixed
     */
    public function getAdminNote()
    {
        return $this->adminNote;
    }

    /**
     * @param mixed $adminNote
     */
    public function setAdminNote($adminNote): void
    {
        $this->adminNote = $adminNote;
    }

    /**
     * @return mixed
     */
    public function getisOpen()
    {
        return $this->isOpen;
    }

    /**
     * @param mixed $isOpen
     */
    public function setIsOpen($isOpen): void
    {
        $this->isOpen = $isOpen;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

}