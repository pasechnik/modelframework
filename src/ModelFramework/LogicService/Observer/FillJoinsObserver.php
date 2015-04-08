<?php
/**
 * Class FillJoinsObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\LogicService\Observer;

use ModelFramework\AclService\AclDataModel;
use ModelFramework\FormService\StaticDataConfig\StaticDataConfig;
use ModelFramework\LogicService\Logic;
use Zend\Db\ResultSet\ResultSetInterface;

class FillJoinsObserver
    implements \SplObserver
{

    /**
     * @param \SplSubject|Logic $subject
     */
    public function update(\SplSubject $subject)
    {
        $this->fillJoins($subject);
//        FIXME This is a place for debugging results of Observers
//        prn( 'Logic Service FillJoinsObserver' );
//        prn( $subject->getEventObject()->toArray() );
//        exit;
    }

    /**
     * @param \SplSubject|Logic $subject
     */
    protected function fillJoins($subject)
    {
        $models      = $subject->getEventObject();
        $modelConfig = $subject->getModelService()
            ->getParsedModelConfig($subject->getModelName());
        if ( !(is_array($models) || $models instanceof ResultSetInterface)) {
            $models = [$models];
        }

        $aModels = [];
        foreach ($models as $_k => $aclModel) {
            if ($aclModel instanceof AclDataModel) {
                $mymodel = $aclModel->getDataModel();
            } else {
                $mymodel = $aclModel;
            }

            foreach ($modelConfig->joins as $_k => $join) {
                if ($join['type'] == 'lookup') {
                    $this->fillLookup($join, $subject, $mymodel);
                } elseif ($join['type'] == 'static_lookup') {
                    $this->fillStaticLookup($join, $subject, $mymodel);
                }
            }

            $aModels[] = $mymodel->getArrayCopy();
        }
        if ($models instanceof ResultSetInterface) {
            $models->initialize($aModels);
        }
//        else
//        {
//            $models = $aModels;
//        }
    }

    protected function fillLookup($joinConf, $subject, $model)
    {
        $othergw = $subject->getGatewayServiceVerify()->get($joinConf['model']);
        foreach ($joinConf['on'] as $myfield => $otherfield) {
            $othermodel = $othergw->findOne([$otherfield => $model->$myfield]);
            if ($othermodel !== null) {
                foreach ($joinConf['fields'] as $myfield => $otherfield) {
                    $model->$myfield = $othermodel->$otherfield;
                }
            } else {
                foreach ($joinConf['fields'] as $myfield => $otherfield) {
                    unset($model->$myfield);
                }
            }
        }
    }

    protected function fillStaticLookup($joinConf, $subject, $model)
    {
        $othermodel = $subject->getLogicService()->getConfigService()
            ->get('StaticDataSource', $joinConf['model'],
                new StaticDataConfig())->options;
        foreach ($joinConf['on'] as $myfield => $otherfield) {
            if (isset($othermodel[$model->$myfield])) {
                $othermodel = $othermodel[$model->$myfield];
                foreach ($joinConf['fields'] as $myfield => $otherfield) {
                    $model->$myfield = $othermodel[$otherfield];
                }
            } else {
                foreach ($joinConf['fields'] as $myfield => $otherfield) {
                    unset($model->$myfield);
                }
            }
        }
//        exit;
    }
}
