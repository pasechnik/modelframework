<?php
/**
 * Class FormService
 *
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\AclService\AclConfig\AclConfig;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\AclService\AclServiceAwareInterface;
use ModelFramework\AclService\AclServiceAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfigParser\FormConfigParser;
use ModelFramework\FormService\FormConfigParser\Observer\AclObserver;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelServiceAwareInterface;
use ModelFramework\ModelService\ModelServiceAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;

class FormService
    implements FormServiceInterface, FieldTypesServiceAwareInterface,
               ConfigServiceAwareInterface, AclServiceAwareInterface,
               ModelServiceAwareInterface, LimitFieldsAwareInterface,
               QueryServiceAwareInterface, GatewayServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ConfigServiceAwareTrait,
        AclServiceAwareTrait, DataModelAwareTrait, ModelServiceAwareTrait,
        LimitFieldsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function get( DataModelInterface $model, $mode, array $fields = [ ] )
    {
        return $this->getForm( $model, $mode, $fields );
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function getForm(
        DataModelInterface $model,
        $mode,
        array $fields = [ ]
    ) {
        return $this->createForm( $model, $mode, $fields );
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function createForm(
        DataModelInterface $model,
        $mode,
        array $fields = [ ]
    ) {
        $parsedFormConfig = $this
            ->setDataModel( $model )
            ->setLimitFields( $fields )
            ->parse()->getParsedFormConfig();
        $form             = new DataForm();
        $form->setParsedFormConfig( $parsedFormConfig );

        return $form;
    }


    public function parse()
    {
        $formConfigParser = new FormConfigParser();
        $dataModel        = $this->getDataModelVerify();
        $modelName        = $dataModel->getModelName();
        $formConfigParser->setFieldTypesService( $this->getFieldTypesServiceVerify() );
        $formConfigParser->setQueryService( $this->getQueryServiceVerify() );
        $formConfigParser->setGatewayService( $this->getGatewayServiceVerify() );
        $formConfigParser->setConfigService( $this->getConfigServiceVerify() );
        $formConfigParser->setModelConfig(
            $this->getModelServiceVerify()->getModelConfig( $modelName )
        );
        $aclData = null;
        if ($dataModel instanceof AclDataModel) {
            /**
             * @var AclConfig $aclData
             */
            $aclData = $dataModel->getDataPermissions();
            $formConfigParser->setAclConfig( $aclData );
            $formConfigParser->setDataModel( $dataModel->getDataModel() );
        }
        else
        {
            $formConfigParser->setDataModel( $dataModel );
        }
        $formConfigParser->setLimitFields( $this->getLimitFields() );
        $formConfigParser->init()->notify();

        return $formConfigParser;
    }

}
