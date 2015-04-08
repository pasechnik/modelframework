<?php
/**
 * Class LimitFieldsAwareInterface
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

interface LimitFieldsAwareInterface
{

    /**
     * @param array $limitFields
     *
     * @return $this
     */
    public function setLimitFields( array $limitFields );

    /**
     * @return array
     */
    public function getLimitFields();

    /**
     * @return array
     * @throws \Exception
     */
    public function getLimitFieldsVerify();
}
