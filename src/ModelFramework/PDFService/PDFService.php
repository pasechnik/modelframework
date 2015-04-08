<?php

namespace ModelFramework\PDFService;

// Use the session with Zend libraries

/**
 * Description of PDFService
 *
 * @author PROG-3
 */

use \LosPdf\View\Model\PdfModel;

class PDFService implements PDFServiceInterface
{
    private $service         = null;
    private $defaultFileName = 'file.pdf';

//    private $defaultDir =
//    private $params   = [
//        'paperSize'        => null,
//        'paperOrientation' => null,
//        'fileName'         => null
//    ];

    public function __construct(\Zend\ServiceManager\ServiceManager $serviceManager)
    {
        $this->service = $serviceManager;
//        $this -> renderer = $serviceManager -> get( 'ViewPDFRenderer' ) -> setHtmlRenderer( $serviceManager -> get( 'zfctwigviewtwigrenderer' ) );
    }

    private function getPDFMarkup($template, $variables = [ ], $params = [ ])
    {
        $pdf        = $this->getViewModel($template, $variables, $params);
        $twigRenderer = $this->service->get('zfctwigviewtwigrenderer');

        $pdfRenderer=$this->service->get('ViewPDFRenderer');

        $pdfRenderer->getEngine()->keep_table_proportions = true;
        $html  = $pdfRenderer->setRenderer($twigRenderer)->getRenderer()-> render($pdf);
        $pdfRenderer->getEngine()->WriteHTML($html);
        $markup = $pdfRenderer->getEngine()->Output('', 'S');
        return $markup;
    }

    private function getPDFMarkupFromString($template, $variables = [ ], $params = [ ])
    {
        $pdf        = $this->getViewModel($template, $variables, $params);
        $twigRenderer = $this->service->get('zfctwigviewtwigrenderer');
        $this->service->get('twigenvironment')->getLoader()->addLoader(new \Twig_Loader_String());

        $pdfRenderer=$this->service->get('ViewPDFRenderer');
        try{
        $pdfRenderer->getEngine()->keep_table_proportions = true;
        $html  = $pdfRenderer->setRenderer($twigRenderer)->getRenderer()-> render($pdf);
        $pdfRenderer->getEngine()->WriteHTML($html);
        $markup = $pdfRenderer->getEngine()->Output('','S');
        } catch ( \Exception $e ) {prn($e);}
        return $markup;
        exit;
    }

    public function saveAsPDF($template, $dir, $variables = [ ], $params = [ ])
    {
        $markup   = $this->getPDFMarkup($template, $variables, $params);
        $fileName = isset($params[ 'fileName' ]) ? $params[ 'fileName' ].'.pdf' : $this->defaultFileName;
        file_put_contents($dir.$fileName, $markup);

        return $dir.$fileName.'.pdf';
    }

    public function getViewModel($template, $variables = [ ], $params = [ ])
    {
        $PDFView = new PdfModel();

        foreach ($params as $key => $parametr) {
            $PDFView->setOption($key, $parametr);
        }
        $PDFView->setOption('basePath', 'public');
        $PDFView->setVariables($variables);
        $PDFView->setTemplate($template);

        return $PDFView;
    }

    public function getPDFtoSave($template, $variables = [ ], $params = [ ])
    {
        $markup   = $this->getPDFMarkupFromString($template, $variables, $params);
        $response = new \Zend\Http\Response();
        $response->setContent($markup);

        $headers  = new \Zend\Http\Headers();
        $filename = isset($params[ 'fileName' ]) ? 'filename='.$params[ 'fileName' ].'.pdf' : 'filename='.$this->defaultFileName;
        $headers->addHeaders(
            [
                'Content-Type'              => 'application/pdf',
                'Content-Disposition'       => 'attachment;'.$filename,
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length'            => strlen($markup),
                'Accept-Ranges'             => 'bytes',
            ]
        );
        $response->setHeaders($headers);

        return $response;
    }

    public function getPDFResponse($template, $variables = [ ], $params = [ ])
    {
        $markup   = $this->getPDFMarkup($template, $variables, $params);
        $response = new \Zend\Http\Response();
        $response->setContent($markup);

        $headers  = new \Zend\Http\Headers();
        $filename = isset($params[ 'fileName' ]) ? 'filename='.$params[ 'fileName' ].'.pdf' : 'filename='.$this->defaultFileName;
        $headers->addHeaders(
            [
                'Content-Type'              => 'application/pdf',
                'Content-Disposition'       => 'inline; '.$filename.'',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Length'            => strlen($markup),
                'Accept-Ranges'             => 'bytes',
            ]
        );
        $response->setHeaders($headers);

        return $response;
    }
}
