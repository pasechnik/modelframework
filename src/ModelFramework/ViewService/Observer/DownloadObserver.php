<?php
/**
 * Class DownloadObserver
 * @package ModelFramework\ModelViewService
 * @author  Ilia Davydenko di.nekto@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class DownloadObserver extends AbstractObserver
{
    public function process($model)
    {
        $subject = $this->getSubject();
        $fs = $subject->getFilesystemServiceVerify();
        $response = $fs->downloadFile($model->document,$model->document_real_name);
        $subject->setResponse($response);
    }
}
