<?php

namespace ModelFramework\FilesystemService;

use \League\Flysystem\Filesystem;

class FilesystemService extends Filesystem implements FilesystemServiceInterface
{
    private $service = null;
    protected $filesystem='';

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager, $adapter,$filesystem)
    {
        parent::__construct($adapter);
        $this->service = $serviceManager;
        $this->filesystem = $filesystem;
    }

    /**
     * Get current filesystem
     * @return string
     */
    public function getFilesystem(){
        return $this->filesystem;
    }

    public function saveFile($filename, $tmpname, $ispublic = false, $userdir = null)
    {

        $destenation = $this->setDestenation($filename, $ispublic, $userdir);
        if (!$destenation || !$result = $this->writeStream($destenation, fopen($tmpname, 'r'))) {
            return false;
        }

        return $destenation;
    }

    /**
     * @param $filename
     * @param $stream
     * @param bool $ispublic
     * @param null $userdir
     * @return bool|string
     */
    public function saveStringToFile($filename, $string, $ispublic = false, $userdir = null)
    {
        $destenation = $this->setDestenation($filename, $ispublic, $userdir);
        if (!$destenation || !$f = $this->write($destenation, $string)) {
            return false;
        }

        return $destenation;
    }

    public function moveFile($from, $to)
    {
        if (!$this->rename($from, $to)) {
            return false;
        }

        return true;
    }

    public function setDestenation($filename, $ispublic = false, $userdir = null)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $companydirname = './public/upload/' . (string)$auth->getMainUser()->company_id;
        } else {
            $companydirname = './upload/' . (string)$auth->getMainUser()->company_id;
        }
        if ($userdir == null) {
            $userdir = (string)$auth->getUser()->id();
        }

        $userdirname = $companydirname . '/' . $userdir;
        $destenation = $userdirname . '/' . uniqid() . $filename;

        return $destenation;
    }

    public function getFileExtension($filename)
    {
        return strtolower(@pathinfo($filename)['extension']);
    }

    public function getBucket()
    {
        return $this->service->get('ModelFramework\AuthService')->getMainUser()->company_id;
    }

    public function checkDestenation($filename, $ispublic = false, $userdir = null)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($userdir == null) {
            $userdir = (string) $auth->getUser()->id();
        }
        if ($ispublic) {
            $destenation = './public/upload/'.(string) $auth->getMainUser()->company_id.'/'.$userdir.'/'.$filename;
        } else {
            $destenation = './upload/'.(string) $auth->getMainUser()->company_id.'/'.$userdir.'/'.$filename;
        }
        if ($this->has($destenation) && !empty($filename)) {
            return $destenation;
        }

        return false;
    }

    public function checkBucket($filename, $bucketname, $ispublic = false)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $destenation = './public/'.$bucketname.'/'.$filename;
        } else {
            $destenation =
                './upload/'.(string) $auth->getMainUser()->company_id.'/'.(string) $auth->getUser()->id().'/'.
                $filename;
        }

        if ($this->has($destenation)) {
            return $destenation;
        }

        return false;
    }

    public function getFileStream($filename, $bucketname = null, $ispublic = false)
    {
        if ($bucketname == null) {
            $bucketname = $this->getBucket();
        }
        $destenation = $this->checkBucket($filename, $bucketname, $ispublic);
        if (!$destenation) {
            return false;
        }

        return $this->downloadFile($destenation,$filename,$ispublic);

        $response = new \Zend\Http\Response\Stream();
        $headers  = new \Zend\Http\Headers();

        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
                ->addHeaderLine('Content-Disposition', 'attachment; filename="'.$filename.'"')
                ->addHeaderLine('Content-Length', filesize($destenation));

        $response->setHeaders($headers);

        $response->setStream($stream = fopen($destenation, 'r'));
        $response->setStatusCode(200);

        return $response;
    }

    public function downloadFile($destenation, $filename, $ispublic = false)
    {

        if (!$this->has($destenation)) {
            return false;
        }

        $content = $this->read($destenation);

        $response = new \Zend\Http\Response\Stream();
        $headers = new \Zend\Http\Headers();
        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->addHeaderLine('Content-Length', strlen($content));
        $response->setHeaders($headers);

        $response->setStream(fopen('data://text/plain;base64,' . base64_encode($content), 'r'));
        $response->setStatusCode(200);

        return $response;
    }

    public function deleteFile($destenation)
    {
        if (!$this->has($destenation)) {
            return false;
        }

        $this->delete($destenation);

        return true;
    }

    public function getServerUrl()
    {
        $config = $this->service->get('config');
        $defaultAdapter=$config['bsb_flysystem']['filesystems']['default']['adapter'];
        if(isset($config['bsb_flysystem']['adapters'][$defaultAdapter]['options']['public_url'])){
            return $config['bsb_flysystem']['adapters'][$defaultAdapter]['options']['public_url'];
        }

        return $this->service->get('ViewHelperManager')->get('serverUrl')->__invoke().'/link';
    }
}
