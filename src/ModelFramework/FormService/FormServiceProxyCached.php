<?php
/**
 * Class FormServiceProxyCached
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\CacheService\CacheServiceAwareInterface;
use ModelFramework\CacheService\CacheServiceAwareTrait;
use ModelFramework\DataModel\DataModelInterface;

class FormServiceProxyCached implements FormServiceAwareInterface, CacheServiceAwareInterface, FormServiceInterface
{
    use CacheServiceAwareTrait, FormServiceAwareTrait;

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function get(DataModelInterface $model, $mode, array $fields = [])
    {
        return $this->getForm($model, $mode, $fields);
    }

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function getForm(DataModelInterface $model, $mode, array $fields = [])
    {
        return $this->getCacheService()->getCachedObjMethod($this->getFormService(), 'getForm', [ $model, $mode, $fields ]);
    }
}
