<?php
/**
 * Class AbstractObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;

class DebugObserver extends AbstractObserver
    implements ConfigAwareInterface
{
    use ConfigAwareTrait;

    public function processModel($model)
    {
        $logicConfig = $this->getSubject()->getlogicConfigVerify();
        $debugInfo   = [
            'Observer config' => $this->getRootConfig(),
            'Model'           => $model,
            'Config'          => $logicConfig,
        ];

        prn('Called with key "'.$logicConfig->key.'"', $debugInfo);
    }
}
