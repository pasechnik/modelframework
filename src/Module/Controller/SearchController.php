<?php

namespace Wepo\Controller;

use Wepo\Lib\WepoController;
use Wepo\Model\Status;

class SearchController extends WepoController
{
    public function indexAction()
    {
        $searchQuery = $this->getParam('q', '');
        $results     = [ ];
        foreach ([
                      'User', 'Mail', 'Lead', 'Contact', 'Client', 'Document', 'Product', 'Pricebook',
                      'PricebookDetail', 'Activity', 'Quote', 'QuoteDetail', 'Order', 'OrderDetail', 'Invoice',
                      'Payment',
                  ] as $model) {
            $widget              = $this->getListing($model, [
                'status_id' => [ Status::NEW_, Status::NORMAL ]
            ], 'widget');
            $widget[ 'actions' ] = [
                'view' => [
                    'route' => strtolower($model),
                    'id'    => 'id',
                ],
            ];
            if ($widget[ 'paginator' ]->getTotalItemCount()) {
                $results[ 'widgets' ][ $model ]            = $widget;
                $results[ 'widgets' ][ $model ][ 'model' ] = strtolower($model);
            }
        }

        $results[ 'search_query' ] = $searchQuery;
        $results[ 'user' ]         = $this->user();

//        prn($results['widgets']['Mail']['tableHandler']->getTransportName('5295fdf7c5b9f222acd3c752'));
//        prn($results);

        return $results;
    }

    public function setupAction()
    {
        foreach ([
                      'User', 'Mail', 'Lead', 'Patient', 'Account', 'Document', 'Product', 'Pricebook',
                      'PricebookDetail', 'Activity', 'Quote', 'QuoteDetail', 'Order', 'OrderDetail', 'Invoice', 'InvoiceDetail',
                      'Payment', 'EventLog', 'Doctor',
                  ] as $model) {
            $index = [ ];
            foreach ($this->getModelConfig($model)[ 'fields' ] as $_key => $_field) {
                if ($_field[ 'type' ] == 'field' && $_field[ 'datatype' ] == 'string' &&
                     substr($_key, -3) !== '_id'
                ) {
                    $index [ $_key ] = 'text';
                }
                if (($model == 'EventLog' || substr($model, -6) == "Detail") &&
                     $_field[ 'type' ] == 'alias' && $_field[ 'datatype' ] == 'string'
                ) {
                    $index [ $_key ] = 'text';
                }
            }

            $this->table($model)->ensureIndex($index);
        }
    }
}
