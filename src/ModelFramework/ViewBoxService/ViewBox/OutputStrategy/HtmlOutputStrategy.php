<?php
namespace ModelFramework\ViewBoxService\ViewBox\OutputStrategy;

use ModelFramework\ViewBoxService\ViewBox\ViewBoxAwareTrait;
use Zend\View\Model\ViewModel as ZendViewModel;

/**
 * Strategy for output PDF file
 * Class PDFOutStrategy
 * @package ModelFramework\ViewBoxService\Output\Strategy
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
class HtmlOutputStrategy
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
        $viewModel = new ZendViewModel( $data );

        return $viewModel->setTemplate( $data[ 'template' ] );
    }

}
