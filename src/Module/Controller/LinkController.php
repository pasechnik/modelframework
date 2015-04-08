<?php

namespace Wepo\Controller;

use Wepo\Lib\WepoController;
use ModelFramework\AuthService\AuthService as Auth;

class LinkController extends WepoController
{
    public function indexAction()
    {
        $bucketname = $this->params()->fromRoute('company');
        $accesstype = $this->params()->fromRoute('type');
        $filename   = $this->params()->fromRoute('file');
        $q          = $this->checkLink($filename, $bucketname, $accesstype);
        if (!$q) {
            return $this->showerror("Perhaps your file has not already exist or link has expired.", null, null,
                                     'Welcome to nowhere.');
        }

        return $q;
    }

    private function checkLink($filename, $bucketname, $accesstype)
    {
        $fs   = $this->getServiceLocator()->get('ModelFramework\FilesystemService');

        if ($bucketname == 'auto') {
            $bucketname = $fs->getBucket();
        }
        if ($accesstype == 'public') {
            $bool = $fs->getFileStream($filename, $bucketname, true);

            return $bool;
        } elseif ($accesstype == 'private') {
            try {
                $permission = $this->getPermission('data:Document');
            } catch (\Exception $ex) {
                return $this->showerror($ex->getMessage(), null, null, 'Permission denied');
            }
            if ($permission == Auth::OWN || $permission == Auth::ALL) {
                $fs   = $this->getServiceLocator()->get('ModelFramework\FilesystemService');
                $bool = $fs->getFileStream(basename($filename));

                return $bool;
            }
        } else {
            return false;
        }
    }
}
