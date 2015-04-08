<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 13:07
 */

namespace ModelFramework\FormService\FormConfigParser;

use ModelFramework\AclService\AclConfig\AclConfigAwareInterface;
use ModelFramework\AclService\AclConfig\AclConfigAwareTrait;
use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModelAwareInterface;
use ModelFramework\DataModel\DataModelAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareInterface;
use ModelFramework\FormService\FormConfig\ParsedFormConfigAwareTrait;
use ModelFramework\FormService\FormConfigParser\Observer\ButtonObserver;
use ModelFramework\FormService\FormConfigParser\Observer\FieldsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\GroupsObserver;
use ModelFramework\FormService\FormConfigParser\Observer\InitObserver;
use ModelFramework\FormService\FormConfigParser\Observer\SaUrlObserver;
use ModelFramework\FormService\LimitFieldsAwareInterface;
use ModelFramework\FormService\LimitFieldsAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfigAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareInterface;
use ModelFramework\QueryService\QueryServiceAwareTrait;
use ModelFramework\Utility\SplSubject\SplSubjectTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfigAwareTrait;

class FormConfigParser
    implements \SplSubject, ParsedFormConfigAwareInterface,
               FieldTypesServiceAwareInterface, ModelConfigAwareInterface,
               AclConfigAwareInterface, LimitFieldsAwareInterface,
               QueryServiceAwareInterface, GatewayServiceAwareInterface,
               ConfigServiceAwareInterface, DataModelAwareInterface
{

    use SplSubjectTrait, ParsedFormConfigAwareTrait,
        FieldTypesServiceAwareTrait, ModelConfigAwareTrait, AclConfigAwareTrait,
        LimitFieldsAwareTrait, QueryServiceAwareTrait, GatewayServiceAwareTrait,
        ConfigServiceAwareTrait, DataModelAwareTrait;

    private $allowed_observers = [ ];

    public function init()
    {
        $this->setParsedFormConfig();
//        prn( 'Form Service -> init()', $this->getParsedFormConfig() );
        $this->attach( new InitObserver() );
        $this->attach( new GroupsObserver() );
        $this->attach( new SaUrlObserver() );
        $this->attach( new ButtonObserver() );
        $this->attach( new FieldsObserver() );
        foreach (
            $this->getModelConfigVerify()->observers as $observer =>
            $obConfig
        ) {
            if (is_numeric( $observer )) {
                $observer = $obConfig;
                $obConfig = null;
            }
            if (!in_array( $observer, $this->allowed_observers )) {
                throw new \Exception( $observer . ' is not allowed in ' .
                                      get_class( $this ) );
            }
            $observerClassName
                  = 'ModelFramework\FormService\FormConfigParer\Observer\\'
                    . $observer;
            $_obs = new $observerClassName();
            if (!empty( $obConfig ) && $_obs instanceof ConfigAwareInterface) {
                $_obs->setRootConfig( $obConfig );
            }
            $this->attach( $_obs );
        }

        return $this;
    }

}
