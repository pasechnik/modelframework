<?php
/**
 * Class PDFServiceAwareTrait
 * @package ModelFramework\PDFService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
namespace ModelFramework\PDFService;

trait PDFServiceAwareTrait{
    /**
     * @var PDFServiceInterface
     */
    private $_pdfService = null;

    /**
     * @param PDFServiceInterface $pdfService
     *
     * @return $this
     */
    public function setPDFService(PDFServiceInterface $pdfService)
    {
        $this->_pdfService = $pdfService;
    }

    /**
     * @return PDFServiceInterface
     */
    public function getPDFService()
    {
        return $this->_pdfService;
    }

    /**
     * @return PDFServiceInterface
     * @throws \Exception
     */
    public function getPDFServiceVerify()
    {
        $_pdfService = $this->getPDFService();
        if ($_pdfService == null || !$_pdfService instanceof PDFServiceInterface) {
            throw new \Exception('PDFService does not set in the PDFServiceAware instance of '.
                get_class($this));
        }

        return $_pdfService;
    }

}