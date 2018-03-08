<?php
/**
 * Created by PhpStorm.
 * User: baka3
 * Date: 11/7/2016
 * Time: 1:45 PM
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Twig_Environment;
use Twig_Loader_Filesystem;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\NoteRepository")
 * @ORM\Table(name="note")
 */
class Note
{
    /**
     * Image loader for EasyAdmin
     * @return string
     */
    public function teaImage()
    {
        $loader = new Twig_Loader_Filesystem(array('/vagrant/app/resources/views'));
        $twig = new Twig_Environment($loader);
        /** @var Tea $tea */
        $tea = $this->getTea();
        $imageUrl = $tea->getCoverImage();
        $template = $twig->loadTemplate('note/tea_image.html.twig');
        return $template->render(array('imageUrl' => $imageUrl));
    }

    /**
     * @return mixed
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * @param mixed $viewed
     */
    public function setViewed($viewed)
    {
        $this->viewed = $viewed;
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
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Tea
     */
    public function getTea(): Tea
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
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getNotifyStaff()
    {
        return $this->notifyStaff;
    }

    /**
     * @param mixed $notifyStaff
     */
    public function setNotifyStaff($notifyStaff)
    {
        $this->notifyStaff = $notifyStaff;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Tea", inversedBy="notes")
     * @ORM\JoinColumn(name="tea_id", referencedColumnName="id", nullable=false)
     */
    protected $tea;

    /**
     * @ORM\Column(type="text")
     */
    protected $note;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":false, "comment":"boolean flag whether this post should be highlighted on the admin"})
     */
    protected $notifyStaff = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $viewed = false;


    function __toString()
    {
        return "$this->user: " . $this->getTea()->getName();
    }
}