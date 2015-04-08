<?php
/**
 * Class PDFServiceInterface
 * @package ModelFramework\PDFService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
namespace ModelFramework\PDFService;

interface PDFServiceInterface{

    //private function getPDFMarkup($template, $variables = [ ], $params = [ ]);

    public function saveAsPDF($template, $dir, $variables = [ ], $params = [ ]);

    public function getViewModel($template, $variables = [ ], $params = [ ]);

    public function getPDFtoSave($template, $variables = [ ], $params = [ ]);

    public function getPDFResponse($template, $variables = [ ], $params = [ ]);

}