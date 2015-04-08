<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 7/31/14
 * Time: 5:45 PM
 */

namespace ModelFramework\FieldTypesService\InputFilterConfig;

interface InputFilterConfigAwareInterface
{

    /**
     * @param array|InputFilterConfigInterface $inputFilter
     *
     * @return $this
     * @throws \Exception
     */
    public function setInputFilterConfig( $inputFilter );

    /**
     * @param array $config
     *
     * @return $this
     * @throws \Exception
     */
    public function parseInputFilterConfigArray( array $config );

    /**
     * @return InputFilterConfigInterface
     */
    public function getInputFilterConfig();

    /**
     * @return InputFilterConfigInterface
     * @throws \Exception
     */
    public function getInputFilterConfigVerify();
}
