<?php

namespace Wepo\Controller;

use ModelFramework\GatewayService\MongoGateway;
use Wepo\Lib\WepoController;

class ImportController extends WepoController
{

    private $_dir = '/www/wepo/db/data/';
    private $_tables = [];

    private function splitFileName($filename)
    {
        $shortName  = basename($filename);
        $collection = preg_replace('/_|\d+|\.csv|\.html/', '', $shortName);
        return [
            'file'       => $filename,
            'shortName'  => $shortName,
            'collection' => $collection,
            'extension'  => pathinfo($shortName, PATHINFO_EXTENSION),
        ];
    }

    private function loadCSV($filename)
    {
//      $reader = new \PHPExcel_Reader_CSV();
//        $reader = \PHPExcel_IOFactory::createReader('CSV');
        $reader = \PHPExcel_IOFactory::createReaderForFile($filename);
        if (pathinfo(basename($filename), PATHINFO_EXTENSION) == 'csv') {
            $reader->setInputEncoding('UTF-8');
            $reader->setDelimiter(',');
            $reader->setEnclosure('"');
            $reader->setLineEnding("\r\n");
            $reader->setSheetIndex(0);
        }
        $reader->setReadDataOnly(true);

        $excel = $reader->load($filename);
        $data  = $excel->getActiveSheet()->toArray();
        if (pathinfo(basename($filename), PATHINFO_EXTENSION) == 'html') {
            array_shift($data);
        }

        return $data;
    }

    private function loadHTML($filename)
    {
        $dom = new \DOMDocument();
        $dom->loadHTMLFile($filename);
        $xml    = simplexml_import_dom($dom);
        $i      = 0;
        $data   = [];
        $fields = [];
        foreach ($xml->body->table->tr as $Row) {
            $rowData = ['keys' => [], 'nums' => []];
            for ($j = 0; $j < count($Row->td); $j++) {
                if ( !$i) {

                    $fields[$j] = $Row->td[$j]->p->__toString();
                } else {
                    $rowData['keys'][$fields[$j]]
                                         = $Row->td[$j]->p->__toString();
                    $rowData['nums'][$j] = $Row->td[$j]->p->__toString();
                }
            }

            if ($i > 1) {
                $data[] = $rowData;
            }

            $i++;
        }

        return $data;
    }

    private function parseHtml($f)
    {
        $dbAdapter = $this->getServiceLocator()->get('wepo_zoho');
        $folders   = new MongoGateway('folders', $dbAdapter);
        $bookmarks = new MongoGateway('bookmarks', $dbAdapter);

        $folders->drop();
        $bookmarks->drop();

        $dom = new \DOMDocument();
        $dom->loadHTMLFile($f['file'], LIBXML_PARSEHUGE);
        $xml = simplexml_import_dom($dom);
//        $dom->saveHTMLFile($f['file'].'_b');
//        prn($xml->body->dl);
        foreach ($xml->body->dl->dt as $a) {

            $folders->insert(['title'=>$a->h3->__toString() ]);
//            prn($a);
//            break;
        }

        foreach ($xml->body->dl->dl as $a) {

            prn($a);
            break;
        }
        return;

        $i      = 0;
        $data   = [];
        $fields = [];
        foreach ($xml->body->table->tr as $Row) {
            $rowData = ['keys' => [], 'nums' => []];
            for ($j = 0; $j < count($Row->td); $j++) {
                if ( !$i) {

                    $fields[$j] = $Row->td[$j]->p->__toString();
                } else {
                    $rowData['keys'][$fields[$j]]
                                         = $Row->td[$j]->p->__toString();
                    $rowData['nums'][$j] = $Row->td[$j]->p->__toString();
                }
            }

            if ($i > 1) {
                $data[] = $rowData;
            }

            $i++;
        }

        return $data;
    }

    private function loadXML($filename)
    {
//        $doc = new \DOMDocument();
//        $doc->load($filename);
        $xml       = simplexml_load_file($filename);
        $worksheet = $xml->xpath('ss:Worksheet')[0];
        $i         = 0;
        $data      = [];
        $fields    = [];
        foreach ($worksheet->Table->Row as $Row) {
            $rowData = ['keys' => [], 'nums' => []];
            for ($j = 0; $j < count($Row->Cell); $j++) {
                if ( !$i) {
                    $fields[$j] = $Row->Cell[$j]->Data->__toString();
                } else {
                    $rowData['keys'][$fields[$j]]
                                         = $Row->Cell[$j]->Data->__toString();
                    $rowData['nums'][$j] = $Row->Cell[$j]->Data->__toString();
                }
            }

            if ($i > 1) {
                $data[] = $rowData;
            }

            $i++;
        }

        return $data;
    }

    private function loadXLS($filename)
    {
//      $reader = new \PHPExcel_Reader_CSV();
        $reader = \PHPExcel_IOFactory::createReaderForFile($filename);

        $reader->setReadDataOnly(true);

        $excel = $reader->load($filename);

        $results = $excel->getActiveSheet()->toArray();
        $row     = 1;

        return $results;
    }

    private function loadFile($filename)
    {
        $results = [];
        $info    = $this->splitFileName($filename);

        if (in_array($info['extension'], ['csv', 'html', 'xls', 'ods'])) {
            $results = $this->loadCSV($filename);
        } elseif ($info['extension'] == 'xml') {
            $results = $this->loadXML($filename);
        }

        return $results;
    }

    private function toString($a)
    {
        foreach ($a as $key => $value) {
            $a[$key] = '' . $value;
        }

        return $a;
    }

    private function makeHash($array, $headerKey = 0)
    {
        $result = [];
        $header = [];

        foreach (array_splice($array, $headerKey, 1)[0] as $k => $value) {
            if (null !== $value) {
                $header[] = preg_replace('/\./', '', $value);
            }
        }

        foreach ($array as $row) {
            $ara = array_combine($header,
                array_slice($row, 0, count($header)));

            $result[] = $this->toString($ara);
        }
        return $result;
    }

    private function saveToCollection($array, $collection, $db = 'wepo_zoho')
    {

        prn(array_slice($array, 0, 10));
        $dbAdapter = $this->getServiceLocator()->get($db);
        $gw        = new MongoGateway($collection, $dbAdapter);

        foreach ($array as $row) {
            $gw->insert($row);
        }
    }

    private function dropCollection($collection, $db = 'wepo_zoho')
    {
        $dbAdapter = $this->getServiceLocator()->get($db);
        $gw        = new MongoGateway($collection, $dbAdapter);
        $gw->drop();
    }

    private function scan($dir)
    {
        $results = [];
        foreach (
            glob($dir . '*.{csv,html,xml,ods,xls}', GLOB_BRACE) as
            $filename
        ) {
            $results[] = $this->splitFileName($filename);
        }

        return $results;
    }

    private function saveFile2Collection($fileItem)
    {
        $data
            = $this->makeHash($this->loadFile($fileItem['file']), 0);

        $this->saveToCollection($data, $fileItem['collection']);

        return [$fileItem['collection'] => $data];
    }


    function indexAction()
    {

        $results           = ['data' => []];
        $results['tables'] = $this->scan($this->_dir);

        $c = $this->params()->fromQuery('c', 0);
        $d = $this->params()->fromQuery('d', 0);
        $z = $this->params()->fromQuery('z', 0);
        if ('all' === $d) {
            $d = 0;
            prn('drop all');
            foreach ($results['tables'] as $fileItem) {
                $this->dropCollection($fileItem['collection']);
            }
        } else {
            $d = (int)$d;
        }

        if ($d > 0 && isset($results['tables'][$d - 1])) {
            $this->dropCollection($results['tables'][$d - 1]['collection']);
        }

        if ('all' === $c) {
            $c = 0;
            prn('convert all');
            foreach ($results['tables'] as $fileItem) {
                prn($fileItem);
                $this->saveFile2Collection($fileItem);
            }
        } else {
            $c = (int)$c;
        }

        if ($c > 0 && isset($results['tables'][$c - 1])) {
            $fileItem = $results['tables'][$c - 1];
            prn($fileItem['shortName']);
            $this->_tables[$fileItem['collection']] = 1;
            $results['data'] += $this->saveFile2Collection($fileItem);
        }

        if ($z == 31) {
            $fileItem = $results['tables'][$z - 1];
            $this->parseHtml($fileItem);
        }

        return $results;
    }

    /**
     * Remove \n\n and modify weight (x'y")
     * @param $filename_in
     * @param $filename_out
     * @param string $filename_tmp
     */
    public function preParceCSV($filename_in,$filename_out,$filename_tmp='tmp.csv'){

        $pattern="#\"(\d)[\\\'|\'|\.|\-|,]*\s*(\d+\.*\d*)[\\\'|\'|\"]+\,#si";
        $replacement = "\"\$1'\$2\"\"\",";


        $fo=fopen($filename_tmp,'wb');
        $fi=fopen($filename_in,'rb');
        while(!feof($fi)){
            $l=fread($fi,2000);
            $t=str_replace("\r\n","-r-r-n-n-",$l);
            $t=str_replace("\n","",$t);
            fwrite($fo,str_replace("-r-r-n-n-","\r\n",$t));
        }
        fclose($fo);
        fclose($fi);

        $fo=fopen($filename_out,'w');
        $fi=fopen($filename_tmp,'r');
        while(!feof($fi)){
            $l=fgets($fi);
            $t=str_replace(['\""',"\'",'\"""'],['""',"'",'"""'],$l);
            $t=preg_replace($pattern, $replacement,$t);
            fwrite($fo,$t);
        }
        fclose($fo);
        fclose($fi);
        unlink($filename_tmp);
    }

}
