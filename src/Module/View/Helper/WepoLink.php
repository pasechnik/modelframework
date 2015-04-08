<?php

namespace Wepo\View\Helper;

use ModelFramework\FilesystemService\FilesystemService;
use Zend\View\Helper\AbstractHelper;

class WepoLink extends AbstractHelper
{
    private $fs = null;

    public function __construct(FilesystemService $fileservice)
    {
        $this->fs = $fileservice;
    }

    public function __invoke($key, $accesstype, $userdir = null)
    {
        if ($accesstype == 'public') {
            //            $destenation = $this ->fs->checkBucket($key->document_real_name, $bucket, true);
//            $url = str_replace("./public/", $this -> fs->  getServerUrl()."/link/".$accesstype."", $destenation);
        } elseif ($accesstype == 'private') {
            //            $destenation = $this ->fs->checkBucket($key->document_real_name, $bucket);
//            $url = str_replace("./upload/", $this -> fs->  getServerUrl()."/link/".$accesstype."/", $destenation);
        } elseif ($accesstype == 'avatar') {
            if (!isset($key->avatar)) {
                return;
            }
            $destenation = $this->fs->checkDestenation($key->avatar, true, lcfirst($key->getModelName()));

            if (!$destenation || empty($key->avatar)) {
                $url = $this->fs->getServerUrl()."/link/public/img/".$key->getModelName().'.jpg';
            } else {
                $url = str_replace("./public/", $this->fs->getServerUrl()."/link/public/", $destenation);
            }
        } else {
            return;
        }
        if ($url == "") {
            $url = 'File does not exist';
        }

        return $url;
    }
}
