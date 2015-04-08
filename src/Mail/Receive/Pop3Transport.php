<?php

namespace Mail\Receive;

/**
 * Description of POP3Mail
 *
 * Fetching emails via POP3 protocol
 *
 * @author KSV
 */
class Pop3Transport extends BaseTransport
{
    public function fetchFolders()
    {
        return;
    }

    //return all letters (send and inbox)
    public function fetchAll($exceptProtocolUids = [ ])
    {
        parent::openTransport();

        $resUids = parent::fetchAll($exceptProtocolUids);

        parent::closeTransport();

        return $resUids;
    }
}
