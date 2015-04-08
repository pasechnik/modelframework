<?php
/**
 * Class AbstractObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;

abstract class AbstractConfigObserver extends AbstractObserver
    implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    abstract public function process($model, $key, $value);

    public function processModel($model)
    {
        foreach ($this->getRootConfig() as $key => $value) {
            if (is_numeric($key)) {
                $key   = $value;
                $value = '';
            }
            if (!isset($model->$key)) {
                throw new \Exception(
                    'Field '.$key.' does not exist in model '
                    .$model->getModelName()
                );
            }
            $this->process($model, $key, $value);
        }
    }
}
