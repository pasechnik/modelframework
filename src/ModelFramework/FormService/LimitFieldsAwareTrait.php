<?php
/**
 * Class LimitFieldsAwareTrait
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

trait LimitFieldsAwareTrait
{

    /**
     * @var array
     */
    private $_limitFields = null;

    /**
     * @param array $limitFields
     *
     * @return $this
     */
    public function setLimitFields( array $limitFields )
    {
        $this->_limitFields = $limitFields;

        return $this;
    }

    /**
     * @return array
     */
    public function getLimitFields()
    {
        return $this->_limitFields;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getLimitFieldsVerify()
    {
        $_limitFields = $this->getLimitFields();
        if ($_limitFields == null || !is_array( $_limitFields )) {
            throw new \Exception( 'LimitFields are not set in' .
                                  get_class( $this ) );
        }

        return $_limitFields;
    }
}
