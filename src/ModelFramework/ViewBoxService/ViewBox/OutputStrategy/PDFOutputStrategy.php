<?php

namespace ModelFramework\ViewBoxService\ViewBox\OutputStrategy;

use ModelFramework\ViewBoxService\ViewBox\ViewBoxAwareTrait;

/**
 * Strategy for output PDF file
 * Class PDFOutStrategy
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
class PDFOutputStrategy
    implements OutputStrategyInterface
{
    use ViewBoxAwareTrait;

    /**
     * Generate output data
     * @return mixed
     */
    public function output()
    {
        $data=$this->getViewBoxVerify()->getData();
        $pdf= $this->getViewBoxVerify()->getPDFServiceVerify();

       // return $pdf->getPDFtoSave('pdf/order_.twig',$data);
        //echo $data[ 'template' ];exit;
        return $pdf->getPDFtoSave('pdf/wepo/'.$data[ 'template' ],$data);
        return $pdf->getPDFtoSave($data[ 'template' ],$data);
    }
}
