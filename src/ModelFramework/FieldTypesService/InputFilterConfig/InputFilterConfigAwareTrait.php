<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:48 PM
 */

namespace ModelFramework\FieldTypesService\InputFilterConfig;

trait InputFilterConfigAwareTrait
{

    /**
     * @var InputFilterConfigInterface
     */
    private $_inputFilterConfig = null;

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function parseInputFilterConfigArray( array $config )
    {
        $inputFilterConfig = new InputFilterConfig();
        $inputFilterConfig->exchangeArray( $config );

        return $inputFilterConfig;
    }

    /**
     * @param array|InputFilterConfigInterface $inputFilter
     *
     * @return $this
     * @throws \Exception
     */
    public function setInputFilterConfig( $inputFilter )
    {
        if ($inputFilter instanceof InputFilterConfigInterface) {
            $this->_inputFilterConfig = $inputFilter;
        } elseif (is_array( $inputFilter )) {
            $this->_inputFilterConfig =
                $this->parseInputFilterConfigArray( $inputFilter );
        } else {
            throw new \Exception( "Wrong config type for 'setInputFilterConfig()' in ",
                get_class( $this ) );
        }

        return $this;
    }

    /**
     * @return InputFilterConfigInterface
     */
    public function getInputFilterConfig()
    {
        return $this->_inputFilterConfig;
    }

    /**
     * @return InputFilterConfigInterface
     * @throws \Exception
     */
    public function getInputFilterConfigVerify()
    {
        $inputFilterConfig = $this->getInputFilterConfig();
        if ($inputFilterConfig == null
            || !$inputFilterConfig instanceof InputFilterConfigInterface
        ) {
            throw new \Exception( 'InputFilterConfig is not set in '
                                  . get_class( $this ) );
        }

        return $inputFilterConfig;
    }

}
