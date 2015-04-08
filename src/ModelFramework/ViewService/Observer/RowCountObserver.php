<?php
/**
 * Class RowCountObserver
 *
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ViewService\View;
use ModelFramework\ViewService\ViewConfig\ViewConfig;

class RowCountObserver
    implements \SplObserver
{

    /**
     * @param View|\SplSubject $subject
     */
    public function update(\SplSubject $subject)
    {
        $viewConfig = $subject->getConfigServiceVerify()
            ->getByObject($subject->getName(), new ViewConfig());
        if ($viewConfig == null) {
            throw new \Exception('Please fill ViewConfig for the ' .
                $subject->getName() . '. I can\'t work on');
        }
        $result['rowcount'] = $subject->getParam('rowcount', $viewConfig->rows);
        if ($result['rowcount'] != $viewConfig->rows) {
            $viewConfig->rows                     = $result['rowcount'];
            $subject->getViewConfigVerify()->rows = $result['rowcount'];
            $subject->getConfigServiceVerify()->saveByObject($viewConfig);
        }
        $subject->setData($result);

    }
}
