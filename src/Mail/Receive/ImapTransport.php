<?php

namespace Mail\Receive;

use RecursiveIteratorIterator;

/**
 * Description of IMAPMAil
 *
 * Fetching emails via IMAP protocols.
 *
 * @author KSV
 */
class ImapTransport extends BaseTransport
{
    protected $rootFolder = null;

    //
    protected function fetchFolders()
    {
        throw new Exception('not implemented');
    }

    //fetch mails from the biggest folder
    public function fetchAll($exceptProtocolUids = [ ], $type = null)
    {
        parent::openTransport();

        $resUids = parent::fetchAll($exceptProtocolUids, $type);

        parent::closeTransport();

        return $resUids;
    }

    protected function prepareExceptUids($exceptProtocolUids)
    {
        foreach ($exceptProtocolUids as $key => $pUid) {
            //            prn($mainFolder == substr( $pUid, 0, strlen( $mainFolder ) ));
            if ($this->rootFolder == substr($pUid, 0, strlen($this->rootFolder))) {
                $exceptProtocolUids[ $key ] = substr($pUid, strlen($this->rootFolder));
            } else {
                unset($exceptProtocolUids[ $key ]);
            }
        }
        return $exceptProtocolUids;
    }

    protected function findRootFolder()
    {
        $folders      = new \RecursiveIteratorIterator($this->transport->getFolders(), \RecursiveIteratorIterator::SELF_FIRST);
        $biggestCount = 0;
        foreach ($folders as $folder) {
            try {
                if ($folder->isSelectable()) {
                    $this->transport->selectFolder($folder->getGlobalName());
                    //                    prn($folder->getGlobalName());
                    $newBiggestCount = count($this->transport->getUniqueId());
                    //                    prn($newBiggestCount);
                    if ($biggestCount < $newBiggestCount) {
                        $biggestCount       = $newBiggestCount;
                        $this->rootFolder = $folder;
                    }
                }
            } catch (\Exception $ex) {
            }
        }

        if (isset($this->rootFolder) && ($biggestCount > 0)) {
            try {
                $this->transport->selectFolder($this->rootFolder->getGlobalName());
            } catch (\Exception $ex) {
            }
        } else {
            return array();
        }
    }

    protected function getSettingUid($uid)
    {
        return [ $this->setting->id() => $this->rootFolder.$uid ];
    }
}
