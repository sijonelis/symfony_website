<?php
/**
 * Created by PhpStorm.
 * User: Vilkazz
 * Date: 2017-05-25
 * Time: 17:20
 */

namespace AppBundle\Services;


use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Overtrue\Flysystem\Qiniu\QiniuAdapter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Mapping\PropertyMappingFactory;
use Vich\UploaderBundle\Storage\AbstractStorage;

class QiniuUploader extends AbstractStorage
{
    /** @var  FileSystem flysystem */
    private $flysystem;
    private $domain;

    public function __construct(PropertyMappingFactory $factory, $accessKey, $secretKey, $bucket, $domain)
    {
        $this->domain = $domain;

        $adapter = new QiniuAdapter($accessKey, $secretKey, $bucket, $this->domain);

        $this->flysystem = new Filesystem($adapter, []);
        parent::__construct($factory);
    }

    /**
     * @param UploadedFile $file
     * @param string $pathRoot
     * @return array
     */
    public function uploadFile(UploadedFile $file, $pathRoot = 'usr', $originalName)
    {
        switch ($file->getClientMimeType()) {
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            default:
                //extension not supported in api
                return null;
        }
        //check if file size is valid
        if (filesize($file->getRealPath()) != $file->getSize())
            return null;

        $name = "$pathRoot/" . rand(10000000000, 99999999999) . ".$extension";
        try {
            $this->qiniuDelete($originalName);
        } catch (FileNotFoundException $e) {
        }
        $this->qiniuUpload($name, fopen($file->getRealPath(), 'r'));
        return [
            "uploaded" => 1,
            "fileName" => $name,
            "url" => $this->domain . '/' . $name,
        ];
    }


    /**
     * Do real upload.
     *
     * @param PropertyMapping $mapping
     * @param UploadedFile $file
     * @param string $dir
     * @param string $name
     *
     * @return bool
     */
    protected function doUpload(PropertyMapping $mapping, UploadedFile $file, ?string $dir, string $name)
    {
        $this->qiniuUpload('tea/' . $name, fopen($file->getPath() . '/' . $file->getFilename(), 'r'));
        return true;
    }

    private function qiniuUpload($name, $file)
    {
        $this->flysystem->writeStream($name, $file);
    }

    private function qiniuDelete($name)
    {
        $this->flysystem->delete($name);
    }

    /**
     * Do real remove.
     *
     * @param PropertyMapping $mapping
     * @param string $dir
     * @param string $name
     *
     * @return bool
     */
    protected function doRemove(PropertyMapping $mapping, ?string $dir, string $name) : bool
    {
//        $this->flysystem->delete('tea/' . $name);
        return true;
    }

    /**
     * Do resolve path.
     *
     * @param PropertyMapping $mapping The mapping representing the field
     * @param string $dir The directory in which the file is uploaded
     * @param string $name The file name
     * @param bool $relative Whether the path should be relative or absolute
     *
     * @return string
     */
    protected function doResolvePath(PropertyMapping $mapping, ?string $dir, string $name, ?bool $relative = false) : string
    {
        return '';
        // TODO: Implement doResolvePath() method.
    }
}