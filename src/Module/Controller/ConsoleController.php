<?php

namespace Wepo\Controller;

use Wepo\Lib\WepoController;
use Wepo\Model\Table;

class ConsoleController extends WepoController
{
    public function roboAction()
    {
        $registredActions = [
            'mail' => 'syncAllMails',
        ];

        $all = $this->params()->fromRoute('all', false);

        $result = [];

        if ($all) {
            foreach ($registredActions as $label => $action) {
                $result[$label] = $this->$action();
            }
        } else {
            $param = array_keys($this->params()->fromRoute(), 'true');
            $labels = array_keys($registredActions);
            $actions = array_intersect($labels, $param);
            foreach ($actions as $label) {
                $action = $registredActions[$label];
                $result[$label] = $this->$action();
            }
        }

        return $result;
    }

    private function syncAllMails()
    {
        $dbs   = $this->table('MainDb');
        $dbs = $dbs->fetchAll();
        $count = 0;
        prn($dbs->count());
        exit();
        foreach ($dbs as $db) {
            $connection = $this->getServiceLocator()->get('wepo_company')->getDriver()->getConnection();
            $connection->setConnectionParameters($db->getParamsArray());
            $this->getServiceLocator()->get('\Wepo\Lib\GatewayService')->clearTableCache();
            $users      = $this->table('User')->fetchAll();
            foreach ($users as $user) {
                $count[ $user->login ] = parent::syncMails($user);
            }
            $this->updateMailChains();
        }

        return $count;
    }
}
