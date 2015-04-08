<?php

namespace ModelFramework\FilesystemService\Adapter;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Polyfill\NotSupportingVisibilityTrait;
use League\Flysystem\Adapter\Polyfill\StreamedCopyTrait;
use League\Flysystem\Adapter\Polyfill\StreamedTrait;
use League\Flysystem\Config;
use League\Flysystem\Util;
use LogicException;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Client\Exception;

class WepoAdapter extends AbstractAdapter
{
    use StreamedTrait;
    use StreamedCopyTrait;
    use NotSupportingVisibilityTrait;

    /**
     * @var array
     */
    protected static $resultMap = [
        '{DAV:}getcontentlength' => 'size',
        '{DAV:}getcontenttype' => 'mimetype',
        'content-length' => 'size',
        'content-type' => 'mimetype',
    ];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var array auth_param
     */
    protected $auth_param;

    /**
     * Constructor.
     *
     * @param AuthService $auth
     * @param string $api_url
     * @param string $key
     */
    public function __construct(\ModelFramework\AuthService\AuthService $auth,  $api_url, $key)
    {
        $this->client = new Client();
        $this->api_url=$api_url;
        $timestamp = time();
        $company_id = (string)$auth->getMainUser()->company_id;
        $user_id=$auth->getUser()->_id;
        $login = $auth->getUser()->login;
        $key = $key;
        $hash = md5($login . $company_id . $timestamp . $key);
        $this->auth_param = ['timestamp' => $timestamp,
                             'login'     => $login,
                             'owner'     => $user_id,
                             'bucket'    => $company_id,
                             'hash'      => $hash,
        ];

        $adapter = new Curl();
        $this->client->setAdapter($adapter);

    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
//        $location = $this->applyPathPrefix($path);
//
//        try {
//            $result = $this->client->propFind($location, [
//                '{DAV:}displayname',
//                '{DAV:}getcontentlength',
//                '{DAV:}getcontenttype',
//                '{DAV:}getlastmodified',
//            ]);
//
//            return $this->normalizeObject($result, $path);
//        } catch (Exception\FileNotFound $e) {
//            return false;
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
//        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
//        $location = $this->applyPathPrefix($path);
//
//        try {
//            $response = $this->client->request('GET', $location);
//
//            if ($response['statusCode'] !== 200) {
//                return false;
//            }
//
//            return array_merge([
//                'contents' => $response['body'],
//                'timestamp' => strtotime($response['headers']['last-modified']),
//                'path' => $path,
//            ], Util::map($response['headers'], static::$resultMap));
//        } catch (Exception\FileNotFound $e) {
//            return false;
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);




        prn($path, $contents, $config,$location,(dirname($location)));


        $this->client->setMethod('POST');
        $this->client->setUri($this->api_url);
        $this->client->setParameterPOST(array_merge($this->auth_param,
            ['filename' => $path,
            ]));




        file_put_contents('tmp',$contents);




        $this->client->setFileUpload('tmp', 'form');

        $response = $this->client->send();

        if ($response->getStatusCode() !=200){
            throw new \Exception ( json_decode($response->getBody())->message);
        }
        prn($response->getContent());
        return json_decode($response->getContent())->data->$filename;






        $location = $this->applyPathPrefix($path);
        $this->client->request('PUT', $location, $contents);

        $result = compact('path', 'contents');

        if ($config->get('visibility')) {
            throw new LogicException(__CLASS__.' does not support visibility settings.');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
//        return $this->write($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
//        $location = $this->applyPathPrefix($path);
//
//        try {
//            $response = $this->client->request('MOVE', '/'.ltrim($location, '/'), null, [
//                'Destination' => '/'.ltrim($newpath, '/'),
//            ]);
//
//            if ($response['statusCode'] >= 200 && $response['statusCode'] < 300) {
//                return true;
//            }
//        } catch (Exception\FileNotFound $e) {
//            // Would have returned false here, but would be redundant
//        }
//
//        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
//        $location = $this->applyPathPrefix($path);
//
//        try {
//            $this->client->request('DELETE', $location);
//
//            return true;
//        } catch (Exception\FileNotFound $e) {
//            return false;
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($path, Config $config)
    {
//        $location = $this->applyPathPrefix($path);
//        $response = $this->client->request('MKCOL', $location);
//
//        if ($response['statusCode'] !== 201) {
//            return false;
//        }
//
//        return compact('path') + ['type' => 'dir'];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
//        return $this->delete($dirname);
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
//        $location = $this->applyPathPrefix($directory);
//
//        $response = $this->client->propFind($location, [
//            '{DAV:}displayname',
//            '{DAV:}getcontentlength',
//            '{DAV:}getcontenttype',
//            '{DAV:}getlastmodified',
//        ], 1);
//
//        array_shift($response);
//
//        $result = [];
//
//        foreach ($response as $path => $object) {
//            $path = $this->removePathPrefix($path);
//            $object = $this->normalizeObject($object, $path);
//            $result[] = $object;
//
//            if ($recursive && $object['type'] === 'dir') {
//                $result = array_merge($result, $this->listContents($object['path'], true));
//            }
//        }
//
//        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
//        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
//        return $this->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
//        return $this->getMetadata($path);
    }


}
