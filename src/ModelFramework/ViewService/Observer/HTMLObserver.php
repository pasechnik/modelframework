<?php
/**
 * Class ViewObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

class HTMLObserver
    implements \SplObserver
{

    public function update( \SplSubject $subject )
    {
        $viewConfig = $subject->getViewConfigVerify();

        $query =
            $subject->getQueryServiceVerify()
                    ->get( $viewConfig->query )
                    ->setParams( $subject->getParams() )
                    ->process();

        $subject->setData( $query->getData() );

        $result = [ ];
        $model  = $subject->getGatewayVerify()->findOne( $query->getWhere() );
        if (!$model) {
            $result[ 'data' ] =
                'Sorry, some error occured. We can\'t display message. Ask administrator';
        }
        else{
            $result[ 'data' ] = $model->text;
        }

//        $data = $subject->getData();


        $subject->setData( $result );
    }
}
