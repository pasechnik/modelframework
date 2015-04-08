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
class ListLiteObserver
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

        foreach ($model as $row){
            $result['selectlinks'][]=[
                        'route'       => 'common',
                        'label'       => $row->title,
                        'routeparams' => [
                            'view' => $viewConfig->params['view'],
                            'data' => $subject->getParams()->fromRoute('data')],
                       'queryparams' => [
                           'id'   => $subject->getParams()->fromRoute('id'),
                           'template'=>$row->_id,
                           ],
            ];
        }
        $subject->setData($result );
    }
}
