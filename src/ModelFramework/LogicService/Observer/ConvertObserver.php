<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\DataMapping\DataMappingConfig\DataMappingConfig;
use ModelFramework\DataModel\DataModel;
use ModelFramework\LogicService\Logic;
use ModelFramework\Utility\Arr;

class ConvertObserver extends AbstractObserver implements ConfigAwareInterface
{

    use ConfigAwareTrait;

    private $_data = [ ];

    public function getData()
    {
        return $this->_data;
    }

    public function setData( array $data )
    {
        //        $this->_data = Arr::merge( $this->_data, $data );
        $this->_data = $data;
    }

    protected function clearData()
    {
        $this->_data = [ ];
    }

    public function processModel( $model )
    {
        /**
         * @var Logic $subject
         */
        $subject = $this->getSubject();
        $this->getRootConfig();
        $save = Arr::getDoubtField( $subject->getData(), 'save', false );
        $save = Arr::getDoubtField( $this->getRootConfig(), 'save', $save );
        $subject->setData( [ 'save' => $save ] );
        $data          = $this->getData();
        $convertConfig = $this->getConfig( $model->getModelName() );
        if ($convertConfig == null) {
            throw  new \Exception( 'Convert Config for ' .
                                   $model->getModelName() . ' is not set' );
        }
        $data[ $convertConfig->key ] = $model;
        $this->setData( $data );
        $this->processConfig( $convertConfig );
        $subject->setData( [ $this->getData() ] );
    }

    public function processConfig( $convertConfig )
    {
        /**
         * @var Logic $subject
         */
        $subject = $this->getSubject();
        $data    = $this->getData();

        /**
         * @var DataModel $model
         */
        $model = $data[ $convertConfig->key ];
        foreach ($convertConfig->targets as $target => $config) {
            $targetModel =
                $subject->getModelServiceVerify()->get( $config[ 'model' ] );
            foreach ($config[ 'fields' ] as $field => $value) {
                $targetModel->$field = $this->parse( $value );
            }
            $this->saveModel( $targetModel, 'insertconverted' );
            $data[ $target ] = $targetModel;
            $this->setData( $data );

            if (!isset( $config[ 'related' ] )) {
                continue;
            }

            $related = [ ];
            foreach ($config[ 'related' ] as $relatedName => $query) {
                $related[ $relatedName ] = [ ];
                $relatedConfig           = $this->getConfig( $relatedName );
                foreach ($query as $qField => $qValue) {
                    $query[ $qField ] = $this->parse( $qValue );
                }

                $rModel = $this->getData()[ $relatedConfig->key ];
                $gw     = $subject->getGatewayServiceVerify()
                                  ->get( $relatedConfig->model, $rModel );

                foreach ($gw->find( $query ) as $_rModel) {
                    $_d                        = $data;
                    $_d[ $relatedConfig->key ] = $_rModel;
                    $this->setData( $_d );
                    $this->processConfig( $relatedConfig );

                    $related[ $relatedName ][ ] =
                        array_diff_key( $this->getData(), $data );
                }
            }

            if (isset( $convertConfig->post )) {
                $model->merge( $convertConfig->post );
                $this->saveModel( $model, 'convert' );
            }

            $data[ 'related' ] = $related;

            $this->setData( $data );
        }
    }

    /**
     * @param DataModel $model
     *
     * @throws \Exception
     */
    public function saveModel( $model, $mode )
    {
        /**
         * @var Logic $subject
         */
        $subject = $this->getSubject();

        if (!Arr::getDoubtField( $subject->getData(), 'save', false )) {
            return;
        }

//        $mode = 'insert';
//        if ( $model->id() !== '' )
//        {
//            $mode = 'update';
//        }
        $subject->getLogicServiceVerify()->get( 'pre' . $mode,
            $model->getModelName() )
                ->trigger( $model );
        $subject->getGatewayServiceVerify()
                ->get( $model->getModelName(), $model )->save( $model );

        $subject->getLogicServiceVerify()->get( 'post' . $mode,
            $model->getModelName() )
                ->trigger( $model );
    }

    public function getConfig( $key )
    {
        /**
         * @var Logic $subject
         */
        $subject = $this->getSubject();

        $data          = $this->getData();
        $convertConfig =
            $subject->getConfigServiceVerify()
                    ->getByObject( $key, new DataMappingConfig() );

        if ($convertConfig !== null && !isset( $data[ $convertConfig->key ] )) {
            $data[ $convertConfig->key ] =
                $subject->getModelServiceVerify()->get( $convertConfig->model );
            $this->setData( $data );
        }

        return $convertConfig;
    }

    public function parse( $string )
    {
        if (strlen( $string ) && $string{0} == ':') {
            return $this->get( substr( $string, 1 ) );
        }

        return $string;
    }

    public function get( $string )
    {
        $data = $this->getData();

        list( $model, $field ) = explode( '.', $string );
        if (!isset( $data[ $model ] )) {
            return '';
        }

        return $data[ $model ]->$field;
    }
}
