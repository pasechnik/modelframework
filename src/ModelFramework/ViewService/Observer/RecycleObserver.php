<?php
/**
 * Class RecycleObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;
use Wepo\Model\Status;

class RecycleObserver implements \SplObserver
{

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update( \SplSubject $subject )
    {
        $viewConfig        = $subject->getViewConfigVerify();
        $modelRoute        = strtolower( $viewConfig->model );
        $request           = $subject->getParams()->getController()->getRequest();
        $results           = [ ];
        $results[ 'view' ] = $subject->getParam( 'view' );
        $ids               = $request->getPost( 'checkedid', null );
        if (!is_array( $ids )) {
            $id = $subject->getParams()->fromRoute( 'id', 0 );
            if ($id) {
                $ids = array( $id );
            } else {
                $subject->setRedirect( $subject->getParams()->getController()->redirect()->toRoute( 'common',
                    [
                        'data' => $modelRoute,
                        'view' => $results[ 'view' ] ==
                                  'delete' ?
                            'list' :
                            'recyclelist'
                    ] ) );

                return;
            }
        }
        $results[ 'ids' ] = $ids;
        foreach ($ids as $id) {
            try {
                $results[ 'items' ][ $id ] = $subject->getGateway()->findOne( [ '_id' => $id ] );
            } catch ( \Exception $ex ) {
                $subject->setRedirect( $subject->refresh( 'Data is invalid ' .
                                                          $ex->getMessage(), $this->url()
                                                                                  ->fromRoute( 'common',
                                                                                      [
                                                                                          'data' => $modelRoute,
                                                                                          'view' => 'list'
                                                                                      ] ) ) );

                return;
            }
        }
        if ($request->isPost()) {
            $delyes = $request->getPost( 'delyes', null );
            $delno  = $request->getPost( 'delno', null );
            if ($delyes !== null) {
                $view = $subject->getViewConfigVerify()->mode;
                if (!in_array( $view, [ 'delete', 'clean', 'restore' ] )) {
                    throw new \Exception( 'Action is not allowed' );
                }
                $subject->getLogicServiceVerify()->get( 'pre' . $view, $viewConfig->model )
                        ->trigger( $results[ 'items' ] );
                $subject->getLogicServiceVerify()->get( $view, $viewConfig->model )->trigger( $results[ 'items' ] );
                $subject->getLogicServiceVerify()->get( 'post' . $view, $viewConfig->model )
                        ->trigger( $results[ 'items' ] );

                $url = $subject->getParams()->fromPost( 'saurl' )[ 'back' ];
                $output = null;
                if (isset($url[ 'query' ])){
                    parse_str( parse_url( $url )[ 'query' ], $output );
                }
                $temp = $subject->getGateway()->findOne( [ '_id' => $id ] );
                if ($temp) {
                    if (isset( $output[ 'back' ] ) &&
                        $temp->toArray()[ 'status_id' ] != Status::DELETED
                    ) {
                        $url = $subject->getSaUrlBack( $output[ 'back' ] );
                    }
                }
                if (!isset( $url ) || $view == 'clean') {
                    $url = $subject->getParams()->getController()->url()->fromRoute( 'common', [
                        'data' => $modelRoute,
                        'view' => $results[ 'view' ] == 'delete' ? 'list' : 'recyclelist'
                    ] );
                }
                $subject->setRedirect( $subject->refresh( ucfirst( $results[ 'view' ] ) . ' was successfull ',
                    $url ) );

                return;
            }
            if ($delno !== null) {
                $subject->setRedirect( $subject->getParams()->getController()->redirect()->toRoute( 'common', [
                    'data' => $modelRoute,
                    'view' => $results[ 'view' ] == 'delete' ? 'list' : 'recyclelist'
                ] ) );

                return;
            }
        }
        $subject->setData( $results );
    }
}
