<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 18:21
 */

namespace ModelFramework\FormService\FormField;

use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FieldTypesService\FormElementConfig\FormElementConfigInterface;
use ModelFramework\FieldTypesService\InputFilterConfig\InputFilterConfigInterface;
use ModelFramework\FormService\FormField\Strategy\DefaultStrategy;
use ModelFramework\FormService\FormField\Strategy\FileStrategy;
use ModelFramework\FormService\FormField\Strategy\JLookupStrategy;
use ModelFramework\FormService\FormField\Strategy\LookupStrategy;
use ModelFramework\FormService\FormField\Strategy\FormFieldStrategyInterface;
use ModelFramework\FormService\FormField\Strategy\StaticLookupStrategy;
use ModelFramework\FormService\FormField\Strategy\TextStrategy;
use ModelFramework\FormService\LimitFieldsAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelField\FieldConfig\ParsedFieldConfigAwareTrait;
use ModelFramework\QueryService\QueryServiceAwareTrait;

class FormField
    implements FormFieldInterface, DataModelAwareInterface
{

    use ParsedFieldConfigAwareTrait, FieldTypesServiceAwareTrait,
        AclConfigAwareTrait, LimitFieldsAwareTrait, QueryServiceAwareTrait,
        GatewayServiceAwareTrait, ConfigServiceAwareTrait, DataModelAwareTrait;


    /**
     * @var string
     */
    private $name = '';

    /**
     * @var FormFieldStrategyInterface
     */
    private $strategy = null;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName( $name )
    {
        $this->name = $name;
        $this->getStrategy()->setName( $name );
        return $this;
    }

//    /**
//     * @return string
//     */
//    public function getName()
//    {
//        return $this->name;
//    }

    /**
     * @return string
     */
    public function getType()
    {
        /**/
        return $this->getStrategy()->getType();
    }

    /**
     * @param FormFieldStrategyInterface $strategy
     *
     * @return $this
     */
    protected function setStrategy( FormFieldStrategyInterface $strategy )
    {
        /**/
        $this->strategy = $strategy;
        return $this;
    }

    /**
     * @return FormFieldStrategyInterface
     */
    protected function getStrategy()
    {
        /**/
        return $this->strategy;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function chooseStrategy( $type )
    {
        switch ($type) {
            case 'lookup':
                $this->setStrategy( new LookupStrategy() );
                break;
            case 'static_lookup':
                $this->setStrategy( new StaticLookupStrategy() );
                break;
            case 'jlookup':
                $this->setStrategy( new JLookupStrategy() );
                break;
            case 'file':
                $this->setStrategy( new FileStrategy() );
                break;
//            case 'email':
//                $this->setStrategy( new EmailStrategy() );
//                break;
            case 'credit_card':
            case 'text':
                $this->setStrategy( new TextStrategy() );
                break;
            default:
                $this->setStrategy( new TextStrategy() );
        }
        return $this;
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setFieldConfig( $config )
    {
        $this->getStrategy()->setFieldConfig( $config );
        return $this;
    }


//    /**
//     * @return FieldConfig\FieldConfigInterface
//     */
//    public function getFieldConfig()
//    {
//        return $this->getStrategy()->getFieldConfig();
//    }

//    /**
//     * @return FieldConfig\FieldConfigInterface
//     */
//    public function getFieldConfigVerify()
//    {
//        return $this->getStrategy()->getFieldConfigVerify();
//    }

//    /**
//     * @param array $aConfig
//     *
//     * @return FieldConfig\FieldConfigInterface
//     * @throws \Exception
//     */
//    public function parseFieldConfigArray( array $aConfig )
//    {
//        return $this->getStrategy()->parseFieldConfigArray( $aConfig );
//    }

    /**
     * @param array|InputFilterConfigInterface $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setInputFilterConfig( $config )
    {
        /**/
        return $this->getStrategy()->setInputFilterConfig( $config );
    }

    /**
     * @param array|FormElementConfigInterface $config
     *
     * @return $this
     * @throws \Exception
     */
    public function setFormElementConfig( $config )
    {
        /**/
        return $this->getStrategy()->setFormElementConfig( $config );
    }

    /**
     * @return $this
     */
    public function init()
    {
        $this->setFormElementConfig(
            $this
                ->getFieldTypesServiceVerify()
                ->getFormElement( $this->getType() )
        );
        $this->setInputFilterConfig(
            $this
                ->getFieldTypesServiceVerify()
                ->getInputFilter( $this->getType() )
        );
        $this->getStrategy()->setQueryService( $this->getQueryServiceVerify() );
        $this->getStrategy()
             ->setGatewayService( $this->getGatewayServiceVerify() );
        $this->getStrategy()->setDataModel( $this->getDataModel() );
        $this->getStrategy()->setAclConfig( $this->getAclConfigVerify() );
        $this->getStrategy()
             ->setConfigService( $this->getConfigServiceVerify() );
        $this->getStrategy()->setLimitFields( $this->getLimitFields() );
        $this->getStrategy()->init();
        return $this;
    }


    /**
     * @return $this
     */
    public function parse()
    {
//        $config = $this->getStrategy()->parse();
        $this->addParsedConfig( $this->getStrategy()->parse() );
        return $this;
    }

}
