<?php
/**
 * Entity to store user favourite tea along with the timestamp
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeaFavouriteRepository")
 * @ORM\Table(name="tea_favourite")
 */
class TeaFavourite
{
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
    function __toString()
    {
        return 'Favourite teas';
    }

    /**
     * Owner constructor.
     */
    public function __construct()
    {

    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tea", inversedBy="favouriteTeaUsers")
     * @ORM\JoinColumn(name="tea_id", referencedColumnName="id")
     */
    protected $tea;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="favouriteTeas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
}