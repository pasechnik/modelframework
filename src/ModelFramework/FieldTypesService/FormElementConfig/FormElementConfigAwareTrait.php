<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\FieldTypesService\FormElementConfig;

trait FormElementConfigAwareTrait
{

    /**
     * @var FormElementConfigInterface
     */
    private $_formElementConfig = null;

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function parseFormElementArray( array $config )
    {
        $formElementConfig = new FormElementConfig();
        $formElementConfig->exchangeArray( $config );

        return $formElementConfig;
    }

    /**
     * @param array|FormElementConfigInterface $formElement
     *
     * @return $this
     * @throws \Exception
     */
    public function setFormElementConfig( $formElement )
    {
        if ($formElement instanceof FormElementConfigInterface) {
            $this->_formElementConfig = $formElement;
        } elseif (is_array( $formElement )) {
            $this->_formElementConfig =
                $this->parseFormElementArray( $formElement );
        } else {
            throw new \Exception( "Wrong config type for 'setFormElementConfig()' in ",
                get_class( $this ) );
        }

        return $this;
    }

    /**
     * @return FormElementConfigInterface
     */
    public function getFormElementConfig()
    {
        return $this->_formElementConfig;
    }

    /**
     * @return FormElementConfigInterface
     * @throws \Exception
     */
    public function getFormElementConfigVerify()
    {
        $formElementConfig = $this->getFormElementConfig();
        if ($formElementConfig == null
            || !$formElementConfig instanceof FormElementConfigInterface
        ) {
            throw new \Exception( 'FormElementConfig is not set in '
                                  . get_class( $this ) );
        }

        return $formElementConfig;

    }

}
