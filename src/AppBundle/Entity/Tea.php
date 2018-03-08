<?php
/**
 * Stores and displays teas
 */

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Symfony\Component\HttpFoundation\File\File;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Overtrue\Pinyin\Pinyin;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TeaRepository")
 * @ORM\Table(name="tea", indexes={@Index(name="tea_search_idx", columns={"name"})})
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 * @Algolia\Index(
 *     perEnvironment=false,
 *     searchableAttributes = {"name", "tea_type", "name_pinyin", "tea_type_pinyin"},
 *     customRanking = {"asc(name)", "desc(viewCount)" }
 * )
 */
class Tea
{
    private $index = true;

    function __toString()
    {
        return $this->name;
    }

    public function newProperty()
    {
        $loader = new Twig_Loader_Filesystem(array('/vagrant/app/resources/views'));
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('tea/note_summary.html.twig');
        return $template->render(array('name' => 'Fabien'));
    }

    /**
     * @Algolia\Attribute(algoliaName="tea_type")
     */
    public function getTeaTypeName() {
        return $this->getTeaType()->getName();
    }

    /**
     * @Algolia\Attribute(algoliaName="name_pinyin")
     */
    public function getTeaTypeNameInPinyin() {
        $pinyin = new Pinyin();
        return $pinyin->permalink($this->getTeaType()->getName(), '');
    }

    /**
     * @Algolia\Attribute(algoliaName="tea_type_pinyin")
     */
    public function getTeaNameInPinyin() {
        $pinyin = new Pinyin();
        return $pinyin->permalink($this->getName(), '');
    }

    /**
     * @Algolia\IndexIf
     */
    public function indexTea() {
        return $this->isPublished() && $this->index;
    }

    /**
     * Owner constructor.
     */
    public function __construct()
    {
    }

    /**
     * @ORM\PreUpdate
     */
    public function updateTeaFields(PreUpdateEventArgs $event) {
        if ($event->hasChangedField('published')) {
            $this->publishedAt = $this->published
                ? DateTime::createFromFormat('Y-m-d H:m:s', date('Y-m-d H:m:s', time()))
                : null;
        }
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:m:s', date('Y-m-d H:m:s', time()));
    }

    /**
     * @ORM\PrePersist
     */
    public function setTeaFields() {
        $this->updatedAt = DateTime::createFromFormat('Y-m-d H:m:s', date('Y-m-d H:m:s', time()));
        if ($this->published) {
            $this->publishedAt = ($this->published && empty($this->publishedAt))
                ? DateTime::createFromFormat('Y-m-d H:m:s', date('Y-m-d H:m:s', time()))
                : $this->publishedAt;
        }
    }

    /**
     * @ORM\Id
     * @Algolia\Attribute(algoliaName="id")
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="TeaType", inversedBy="teas")
     * @ORM\JoinColumn(name="tea_type_id", referencedColumnName="id")
     */
    protected $teaType;


    /**
     * @ORM\Column(type="string")
     * @Algolia\Attribute(algoliaName="name")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="teas")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $history;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $waterTitle = '冲泡方法';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $water;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $storageTitle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $storage;

    /**
     * @ORM\OneToMany(targetEntity="Note", mappedBy="tea", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $notes;

    /**
     * @ORM\OneToMany(targetEntity="TeaFeatured", mappedBy="tea", cascade={"remove"})
     */
    protected $featuredTeas;

    /**
     * @ORM\OneToMany(targetEntity="TeaFavourite", mappedBy="tea", cascade={"remove"})
     */
    protected $favouriteTeaUsers;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="SolarTerm", inversedBy="teasFrom")
     * @ORM\JoinColumn(name="solar_term_from_id", referencedColumnName="id")
     */
    protected $solarTermFrom;

    /**
     * @ORM\ManyToOne(targetEntity="SolarTerm", inversedBy="teasTo")
     * @ORM\JoinColumn(name="solar_term_to_id", referencedColumnName="id")
     */
    protected $solarTermTo;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published;

    /**
     * @Algolia\Attribute(algoliaName="cover_image")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $coverImage;

    /**
     * @Algolia\Attribute(algoliaName="view_count")
     * @ORM\Column(type="integer", nullable=false, options={"default":0})
     */
    protected $viewCount = 0;

    /**
     * @Vich\UploadableField(mapping="tea_image", fileNameProperty="coverImage")
     * @var File
     */
    private $coverImageFile;

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
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
    public function getTeaType()
    {
        return $this->teaType;
    }

    /**
     * @param mixed $teaType
     */
    public function setTeaType($teaType)
    {
        $this->teaType = $teaType;
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
     * @return mixed
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param mixed $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @param mixed $publishedAt
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * @param mixed $viewCount
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    /**
     * @var Image[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Image", cascade={"persist"})
     * @ORM\JoinTable(name="images",
     *      joinColumns={@ORM\JoinColumn(name="tea_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", unique=true)}
     * )
     */
//    private $images;

    /**
     * Get images
     *
     * @return Image[]|ArrayCollection
     */
//    public function getImages()
//    {
//        return $this->images;
//    }

    public function setCoverImageFile(File $image = null)
    {
        $this->coverImageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getCoverImageFile()
    {
        return $this->coverImageFile;
    }

    public function setCoverImage($coverImage)
    {
        $this->coverImage = $coverImage;
    }

    public function getCoverImage()
    {
        return $this->coverImage;
    }

    /**
     * @return mixed
     */
    public function getFavouriteTeas()
    {
        return $this->favouriteTeas;
    }

    /**
     * @param mixed $favouriteTeas
     */
    public function setFavouriteTeas($favouriteTeas)
    {
        $this->favouriteTeas = $favouriteTeas;
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
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param mixed $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * @return mixed
     */
    public function getWater()
    {
        return $this->water;
    }

    /**
     * @param mixed $water
     */
    public function setWater($water)
    {
        $this->water = $water;
    }

    /**
     * @return mixed
     */
    public function getFavouriteTeaUsers()
    {
        return $this->favouriteTeaUsers;
    }

    /**
     * @param mixed $favouriteTeaUsers
     */
    public function setFavouriteTeaUsers($favouriteTeaUsers)
    {
        $this->favouriteTeaUsers = $favouriteTeaUsers;
    }

    public function setIndex(bool $index) {
        $this->index = $index;
    }

    /**
     * @return mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param mixed $storage
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * @return mixed
     */
    public function getFeaturedTeas()
    {
        return $this->featuredTeas;
    }

    /**
     * @param mixed $featuredTeas
     */
    public function setFeaturedTeas($featuredTeas)
    {
        $this->featuredTeas = $featuredTeas;
    }

    /**
     * @return mixed
     */
    public function getSolarTermFrom()
    {
        return $this->solarTermFrom;
    }

    /**
     * @param mixed $solarTermFrom
     */
    public function setSolarTermFrom($solarTermFrom)
    {
        $this->solarTermFrom = $solarTermFrom;
    }

    /**
     * @return mixed
     */
    public function getSolarTermTo()
    {
        return $this->solarTermTo;
    }

    /**
     * @param mixed $solarTermTo
     */
    public function setSolarTermTo($solarTermTo)
    {
        $this->solarTermTo = $solarTermTo;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getWaterTitle(): string
    {
        return $this->waterTitle;
    }

    /**
     * @param string $waterTitle
     */
    public function setWaterTitle(string $waterTitle)
    {
        $this->waterTitle = $waterTitle;
    }

    /**
     * @return mixed
     */
    public function getStorageTitle()
    {
        return $this->storageTitle;
    }

    /**
     * @param mixed $storageTitle
     */
    public function setStorageTitle($storageTitle)
    {
        $this->storageTitle = $storageTitle;
    }
}