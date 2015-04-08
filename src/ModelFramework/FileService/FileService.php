<?php

namespace ModelFramework\FileService;

use Zend\Filter\Null;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;


class FileService implements FileServiceInterface
{
    private $service = null;
    private $httpClient = null;
    private $auth_param = [];
    private $api_url=null;

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager, $config=[])
    {

        $this->service = $serviceManager;
//        $this->httpClient = new Client();
//        $this->api_url=$config['api_url'];
//        $timestamp = time();
//        $auth = $this->service->get('ModelFramework\AuthService');
//        $company_id = (string)$auth->getMainUser()->company_id;
//        $user_id=$auth->getUser()->_id;
//        $login = $auth->getUser()->login;
//        $key = $config['key'];
//        $hash = md5($login . $company_id . $timestamp . $key);
//        $this->auth_param = ['timestamp' => $timestamp,
//                             'login'     => $login,
//                             'owner'     => $user_id,
//                             'bucket'    => $company_id,
//                             'hash'      => $hash,
//        ];
//
//        $adapter = new Curl();
//        $this->httpClient->setAdapter($adapter);
    }

    public function saveFile($filename, $tmpname, $ispublic = false, $userdir = null)
    {

        $this->httpClient->setMethod('POST');
        $this->httpClient->setUri($this->api_url);
        $this->httpClient->setParameterPOST(array_merge($this->auth_param,
            ['filename' => $filename,
             'ispublic' => $ispublic,
            ]));

        $this->httpClient->setFileUpload($tmpname, 'form');

        $response = $this->httpClient->send();

        if ($response->getStatusCode() !=200){
            throw new \Exception ( json_decode($response->getBody())->message);
        }

        return json_decode($response->getContent())->data->$filename;
    }

    /**
     * @param string $filename
     * @param string $string
     * @param bool $ispublic
     * @param null $userdir
     * @return bool|string
     */
    public function saveStramToFile($filename, $string, $ispublic = false, $userdir = null)
    {
        file_put_contents('tmp',$string);

        return $this->saveFile($filename,'tmp',$ispublic,$userdir);

        $this->httpClient->setMethod('POST');
        $this->httpClient->setUri($this->api_url);
        $this->httpClient->setParameterPOST(array_merge($this->auth_param,
            ['filename' => $filename,
             'ispublic' => $ispublic,
             'method'   => 'stream'
            ]));



        $response = $this->httpClient
        //    ->setStream(fopen('data://text/plain;base64,' . base64_encode($stream),'r'))
            ->send()
            ->setStatusCode(200);



        if ($response->getStatusCode() !=200){
            throw new \Exception ( json_decode($response->getBody())->message);
        }

        return json_decode($response)->data->$filename;
    }

    public function moveFile($from, $to)
    {
        if (!@rename($from, $to)) {
            return false;
        }

        return true;
    }

    public function setDestenation($filename, $ispublic = false, $userdir = null)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $companydirname = './public/' . (string)$auth->getMainUser()->company_id;
        } else {
            $companydirname = './upload/' . (string)$auth->getMainUser()->company_id;
        }
        if ($userdir == null) {
            $userdir = (string)$auth->getUser()->id();
        }
        if (!file_exists($companydirname)) {
            if (!mkdir($companydirname, 0777, true)) {
                return false;
            }
        }
        $userdirname = $companydirname . '/' . $userdir;
        if (!file_exists($userdirname)) {
            if (!mkdir($userdirname, 0777, true)) {
                return false;
            }
        }
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
            $userdir = (string)$auth->getUser()->id();
        }
        if ($ispublic) {
            $destenation = './public/' . (string)$auth->getMainUser()->company_id . '/' . $userdir . '/' . $filename;
        } else {
            $destenation = './upload/' . (string)$auth->getMainUser()->company_id . '/' . $userdir . '/' . $filename;
        }
        if (file_exists($destenation) && !empty($filename)) {
            return $destenation;
        }

        return false;
    }

    public function checkBucket($filename, $bucketname, $ispublic = false)
    {
        $auth = $this->service->get('ModelFramework\AuthService');
        if ($ispublic) {
            $destenation = './public/' . $bucketname . '/' . $filename;
        } else {
            $destenation =
                './upload/' . (string)$auth->getMainUser()->company_id . '/' . (string)$auth->getUser()->id() . '/' .
                $filename;
        }
        if (file_exists($destenation)) {
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

        $response = new \Zend\Http\Response\Stream();
        $headers = new \Zend\Http\Headers();

        $headers->addHeaderLine('Content-Type', 'application/octet-stream')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->addHeaderLine('Content-Length', filesize($destenation));

        $response->setHeaders($headers);

        $response->setStream($stream = fopen($destenation, 'r'));
        $response->setStatusCode(200);
//        prn($destenation,$headers->toString(),filesize($destenation),$response); exit;
        return $response;
    }

    public function downloadFile($filename, $ispublic = false, $userdir = null)
    {
        $destenation = $this->checkDestenation($filename, $ispublic);
        if (!$destenation) {
            return false;
        }

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

    public function deleteFile($filename)
    {
        $destenation = $this->checkDestenation($filename);
        if (!$destenation) {
            return false;
        }

        @unlink($destenation);

        return true;
    }

    public function getServerUrl()
    {
        return $this->service->get('ViewHelperManager')->get('serverUrl')->__invoke();
    }
}
