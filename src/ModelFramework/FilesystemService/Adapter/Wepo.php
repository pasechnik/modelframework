<?php

namespace ModelFramework\FilesystemService\Adapter;

use League\Flysystem\Config;
use SplFileInfo;
use FilesystemIterator;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use League\Flysystem\Util;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use Zend\Http\Client;
use Zend\Http\Client\Adapter\Curl;
use Zend\Http\Client\Exception;
use Zend\Json\Json;


class Wepo extends AbstractAdapter
{
    protected static $permissions = [
        'public'  => 0744,
        'private' => 0700,
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
    public function __construct(\ModelFramework\AuthService\AuthService $auth, $key, $api_url)
    {
        $this->client = new Client();
        $this->api_url = $api_url;
        $timestamp = time();
        $company_id = (string)$auth->getMainUser()->company_id;
        $user_id = $auth->getUser()->_id;
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
        $adapter->setOptions([
            'curloptions' => [
                CURLOPT_POST           => 1,
                CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
                CURLOPT_USERPWD        => "username:password",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_SSL_VERIFYHOST => FALSE,
            ]
        ]);

        $this->client->setAdapter($adapter);

    }

    /**
     * Ensure the root directory exists.
     *
     * @param   string $root root directory path
     * @return  string  real path to root
     */
    protected function ensureDirectory($root)
    {
        if (is_dir($root) === false) {
            mkdir($root, 0755, true);
        }

        return realpath($root);
    }

    /**
     * Check whether a file is present
     *
     * @param   string $path
     * @return  boolean
     */
    public function has($path)
    {
        $location = $this->applyPathPrefix($path);
        //   return true;
        return is_file($location);
    }

    /**
     * Write a file
     *
     * @param $path
     * @param $contents
     * @param null $config
     * @return array|bool
     */
    public function write($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $config = Util::ensureConfig($config);
        $this->ensureDirectory(dirname($location));

        $this->client->setMethod('POST');
        $this->client->setUri($this->api_url);
        $this->client->setParameterPOST(array_merge($this->auth_param,
            ['filename' => $path,
            ]));


        $this->client
            ->setHeaders(
                ['wp_path' => $path]
            );

        file_put_contents('tmp', $contents);
        $this->client->setFileUpload('tmp', 'form');

        $response = $this->client->send();

        $path = json_decode($response->getContent())->file_id;

        $type = 'wepo_fs';
        $result = compact('contents', 'type', 'size', 'path');

        if ($visibility = $config->get('visibility')) {
            $result['visibility'] = $visibility;
            //$this->setVisibility($path, $visibility);
        }

        return $result;
    }

    /**
     * Write using a stream
     *
     * @param $path
     * @param $resource
     * @param null $config
     * @return array|bool
     */
    public function writeStream($path, $resource, Config $config)
    {

        $username = 'admin';
        $password = '12345678';

        $auth = base64_encode($username . ':' . $password);

        $this->client->setUri('http://192.168.10.47/api/wstest/upload/fg');
        // $this->client->setUri('http://files.local/api/v2/fs');
        $this->client->setMethod('POST');
        $headers = [
            'Accept'        => '*/*',
            'Cache-Control' => 'no-cache',
            'Authorization' => 'Basic ' . $auth,
            'X-File-Name'   => 'todo.txt',
            // 'Content-Type'  => 'application/x-www-form-urlencoded',
            'Content-Type'  => 'application/x-www-form-urlencoded',
            // 'Content-Length'=>'50',
        ];
        $this->client->setHeaders($headers);
        file_put_contents('tmp', $resource);
//        $this->client->setFileUpload('todo.txt', 'todo_txt', $resource);

        $text = 'this is some plain text';
        $this->client->setFileUpload('some_text.txt', 'some_text_txt', $text, 'text/plain');

        prn($this->client->send()->getContent());


        exit;

        $location = $this->applyPathPrefix($path);
        $config = Util::ensureConfig($config);
        $this->ensureDirectory(dirname($location));

        $this->client->setMethod('POST');
        $this->client->setUri($this->api_url);
        $this->client->setParameterPOST(array_merge($this->auth_param,
            ['filename' => $path,
            ]));


        $this->client
            ->setHeaders(
                ['wp_path' => $path]
            );

        file_put_contents('tmp', $resource);
        $this->client->setFileUpload('tmp', 'form');

        $response = $this->client->send();

        $path = json_decode($response->getContent())->file_id;

        $type = 'wepo_fs';
        $result = compact('contents', 'type', 'size', 'path');

        if ($visibility = $config->get('visibility')) {
            $result['visibility'] = $visibility;

        }

        return $result;
    }

    /**
     * Get a read-stream for a file
     *
     * @param $path
     * @return array|bool
     */
    public function readStream($path)
    {
        $headers = [
            'User-Agent' => 'testing/1.0',
            'Accept'     => 'application/json',
            'X-Foo'      => ['Bar', 'Baz'],
            'custom'     => 'cust',
        ];
        $stream = \GuzzleHttp\Stream\Stream::factory('contents...');

        $client = new \GuzzleHttp\Client(['headers' => $headers]);
        $resource = fopen('a.gif', 'r');
        $request = $client->put($this->api_url . 'upload', ['body' => $resource]);

        prn($client, $request);
        echo $request->getBody();


        exit;
        $location = $this->applyPathPrefix($path);

        $this->client->setMethod('PUT');
        $this->client->setUri($this->api_url . 'upload');
        $this->client->setParameterPost(array_merge($this->auth_param,
            ['location' => $location,

            ]));
//        $this->client
//            //->setHeaders(['path: /usr/local....'])
//            ->setFileUpload('todo.txt','r')
//            ->setRawData(fopen('todo.txt','r'));

        $fp = fopen('todo.txt', "r");

        $curl = $this->client->getAdapter();
        $curl->setCurlOption(CURLOPT_PUT, 1)
            ->setCurlOption(CURLOPT_RETURNTRANSFER, 1)
            ->setCurlOption(CURLOPT_INFILE, $fp)
            ->setCurlOption(CURLOPT_INFILESIZE, filesize('todo.txt'));


        //  prn($curl->setOutputStream($fp));


        $response = $this->client->send();
        prn($response->getContent(), json_decode($response->getContent()));
        exit;


    }

    /**
     * Update a file using a stream
     *
     * @param   string $path
     * @param   resource $resource
     * @param   mixed $config Config object or visibility setting
     * @return  array|bool
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->writeStream($path, $resource, $config);
    }

    /**
     * Update a file
     *
     * @param   string $path
     * @param   string $contents
     * @param   mixed $config Config object or visibility setting
     * @return  array|bool
     */
    public function update($path, $contents, Config $config)
    {
        $location = $this->applyPathPrefix($path);
        $mimetype = Util::guessMimeType($path, $contents);

        if (($size = file_put_contents($location, $contents, LOCK_EX)) === false) {
            return false;
        }

        return compact('path', 'size', 'contents', 'mimetype');
    }

    /**
     * Read a file
     *
     * @param   string $path
     * @return  array|bool
     */
    public function read($path)
    {
        $location = $this->applyPathPrefix($path);
        $contents = file_get_contents($location);

        if ($contents === false) {
            return false;
        }

        return compact('contents', 'path');
    }

    /**
     * Rename a file
     *
     * @param $path
     * @param $newpath
     * @return bool
     */
    public function rename($path, $newpath)
    {
        $location = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $parentDirectory = $this->applyPathPrefix(Util::dirname($newpath));
        $this->ensureDirectory($parentDirectory);

        return rename($location, $destination);
    }

    /**
     * Copy a file
     *
     * @param $path
     * @param $newpath
     * @return bool
     */
    public function copy($path, $newpath)
    {
        $location = $this->applyPathPrefix($path);
        $destination = $this->applyPathPrefix($newpath);
        $this->ensureDirectory(dirname($destination));

        return copy($location, $destination);
    }

    /**
     * Delete a file
     *
     * @param $path
     * @return bool
     */
    public function delete($path)
    {
        $location = $this->applyPathPrefix($path);

        return unlink($location);
    }

    /**
     * List contents of a directory
     *
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $result = [];
        $location = $this->applyPathPrefix($directory) . $this->pathSeparator;

        if (!is_dir($location)) {
            return [];
        }

        $iterator = $recursive ? $this->getRecursiveDirectoryIterator($location) : $this->getDirectoryIterator($location);

        foreach ($iterator as $file) {
            $path = $this->getFilePath($file);
            if (preg_match('#(^|/)\.{1,2}$#', $path)) continue;
            $result[] = $this->normalizeFileInfo($file);
        }

        return $result;
    }

    /**
     * Get the metadata of a file
     *
     * @param $path
     * @return array
     */
    public function getMetadata($path)
    {
        $location = $this->applyPathPrefix($path);
        $info = new SplFileInfo($location);

        return $this->normalizeFileInfo($info);
    }

    /**
     * Get the size of a file
     *
     * @param $path
     * @return array
     */
    public function getSize($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the mimetype of a file
     *
     * @param $path
     * @return array
     */
    public function getMimetype($path)
    {
        $location = $this->applyPathPrefix($path);
        $finfo = new Finfo(FILEINFO_MIME_TYPE);

        return ['mimetype' => $finfo->file($location)];
    }

    /**
     * Get the timestamp of a file
     *
     * @param $path
     * @return array
     */
    public function getTimestamp($path)
    {
        return $this->getMetadata($path);
    }

    /**
     * Get the visibility of a file
     *
     * @param $path
     * @return array|void
     */
    public function getVisibility($path)
    {
        $location = $this->applyPathPrefix($path);
        clearstatcache(false, $location);
        $permissions = octdec(substr(sprintf('%o', fileperms($location)), -4));
        $visibility = $permissions & 0044 ? AdapterInterface::VISIBILITY_PUBLIC : AdapterInterface::VISIBILITY_PRIVATE;

        return compact('visibility');
    }

    /**
     * Set the visibility of a file
     *
     * @param $path
     * @param $visibility
     * @return array|void
     */
    public function setVisibility($path, $visibility)
    {
        $location = $this->applyPathPrefix($path);
        chmod($location, static::$permissions[$visibility]);

        return compact('visibility');
    }

    /**
     * Create a directory
     *
     * @param   string $dirname directory name
     * @param   array|Config $options
     *
     * @return  bool
     */
    public function createDir($dirname, Config $config)
    {
        $location = $this->applyPathPrefix($dirname);

        if (!is_dir($location)) {
            mkdir($location, 0777, true);
        }

        return ['path' => $dirname, 'type' => 'dir'];
    }

    /**
     * Delete a directory
     *
     * @param $dirname
     * @return bool
     */
    public function deleteDir($dirname)
    {
        $location = $this->applyPathPrefix($dirname);

        if (!is_dir($location)) {
            return false;
        }

        $contents = $this->listContents($dirname, true);
        $contents = array_reverse($contents);

        foreach ($contents as $file) {
            if ($file['type'] === 'file') {
                unlink($this->applyPathPrefix($file['path']));
            } else {
                rmdir($this->applyPathPrefix($file['path']));
            }
        }

        return rmdir($location);
    }

    /**
     * Normalize the file info
     *
     * @param SplFileInfo $file
     * @return array
     */
    protected function normalizeFileInfo(SplFileInfo $file)
    {
        $normalized = [
            'type'      => $file->getType(),
            'path'      => $this->getFilePath($file),
            'timestamp' => $file->getMTime()
        ];

        if ($normalized['type'] === 'file') {
            $normalized['size'] = $file->getSize();
        }

        return $normalized;
    }

    /**
     * Get the normalized path from a SplFileInfo object
     *
     * @param   SplFileInfo $file
     * @return  string
     */
    protected function getFilePath(SplFileInfo $file)
    {
        $path = $file->getPathname();
        $path = $this->removePathPrefix($path);

        return trim($path, '\\/');
    }

    /**
     * @param $path
     * @return RecursiveIteratorIterator
     */
    protected function getRecursiveDirectoryIterator($path)
    {
        $directory = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST);

        return $iterator;
    }

    /**
     * @param $path
     * @return DirectoryIterator
     */
    protected function getDirectoryIterator($path)
    {
        $iterator = new DirectoryIterator($path);

        return $iterator;
    }

}
