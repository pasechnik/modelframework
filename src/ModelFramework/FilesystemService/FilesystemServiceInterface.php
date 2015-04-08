<?php
/**
 * Class FileServiceInterface
 * @package ModelFramework\FileService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FilesystemService;

interface FilesystemServiceInterface
{
    public function saveFile($filename, $tmpname, $ispublic = false, $userdir = null);

//    private function setDestenation( $filename, $ispublic = FALSE, $userdir = null );

    public function getFileExtension($filename);
//
//    public function checkDestenation($filename, $ispublic = false, $userdir = null);
//
//    public function checkBucket($filename, $bucketname, $ispublic = false);
//
//    public function getFileStream($filename, $ispublic = false);

    public function downloadFile($filename, $filename,$ispublic = false);

    public function deleteFile($filename);

    public function getServerUrl();
}
