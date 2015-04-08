<?php
/**
 * Class ConcatenationObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;

class SetAsDefaultObserver  implements \SplObserver, ConfigAwareInterface
{
    use ConfigAwareTrait;
    /**
     * @param \SplSubject|Logic $subject
     */
    public function update(\SplSubject $subject)
    {
        $model = $subject->getEventObject();
        $settings = $this->getRootConfig();
        if ($model->$settings['isdefault_field'] == 'true') {
            $gw = $subject->getGatewayServiceVerify()->get($model->getModelName());

            $searchQuery = [ ];
            foreach ($settings[ 'unique_fields' ] as $field) {
                $searchQuery[ $field ] = $model->$field;
            }

            $searchQuery['-_id'] = $model->_id;

            $otherModels = $gw->find($searchQuery);

            foreach ($otherModels as $othrModel) {
                $othrModel->$settings[ 'isdefault_field' ] = 'false';
                $gw->save($othrModel);
            }
        }
    }
}
