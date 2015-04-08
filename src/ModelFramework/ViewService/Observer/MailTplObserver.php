<?php
/**
 * Class ListObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;

/**
 * Class ListLiteObserver
 * Get array from model with link params (for PDF)
 * Clone ListObserver w/o paginator
 * @package ModelFramework\ViewService\Observer
 */
class MailTplObserver
    implements \SplObserver
{

    /**
     * @param \SplSubject|View $subject
     */
    public function update( \SplSubject $subject )
    {

        $viewConfig = $subject->getViewConfigVerify();
        $query      =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();
        $subject->setData( $query->getData() );

        $model  = $subject->getGatewayServiceVerify()->get($viewConfig->model)->find($query->getWhere());
        if ( !$model->count()) {
            return;
        }

        foreach ($model as $key=> $row){
            $result['selectlinks'][]=[
                        'route'       => 'common',
                        'label'       => $row->title,
                        'folder_title'=> $row->folder_title,
                        'routeparams' => [
                            'view' => 'send',
                            'data' => 'mail',
                        ],
                       'queryparams' => [
                           'recipient'  => $subject->getParams()->fromRoute('id'),
                           'template'   => $row->_id,
                           ],
            ];
        }
        usort($result['selectlinks'], function($a, $b){
           return strnatcmp($a['folder_title'], $b['folder_title']);
        });
        $subject->setData($result);
    }
}
