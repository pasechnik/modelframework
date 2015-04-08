<?php

namespace ModelFramework\ViewBoxService\ViewBox;

use ModelFramework\PDFService\PDFServiceAwareInterface;
use Zend\View\Model\ViewModel as ZendViewModel;

interface ViewBoxInterface extends PDFServiceAwareInterface
{

    public function setRedirect( ZendViewModel $redirect );

    public function getRedirect();

    public function hasRedirect();

    public function getData();

    public function setData( array $data );

    public function clearData();

    public function setDataFields();

    public function process();

    /**
     * @return ZendViewModel
     */
    public function output();

}
