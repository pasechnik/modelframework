<?php
/**
 * Class PDFServiceAwareInterface
 * @package ModelFramework\PDFService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
namespace ModelFramework\PDFService;

interface PDFServiceAwareInterface
{
    /**
     * @param PDFServiceInterface $pdfService
     *
     * @return $this
     */
    public function setPDFService(PDFServiceInterface $pdfService);

    /**
     * @return PDFServiceInterface
     */
    public function getPDFService();

    /**
     * @return PDFServiceInterface
     * @throws \Exception
     */
    public function getPDFServiceVerify();
}
