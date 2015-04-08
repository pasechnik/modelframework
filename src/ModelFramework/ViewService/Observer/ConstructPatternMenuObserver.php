<?php
/**
 * Class ConvertObserver
 * @package ModelFramework\ViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */


namespace ModelFramework\ViewService\Observer;

/**
 * Build variable array to insert pattern in to template
 * Class ConstructPatternMenuObserver
 * @package ModelFramework\ViewService\Observer
 */
class ConstructPatternMenuObserver implements \SplObserver
{
    private $allow_models;

    public function update(\SplSubject $subject)
    {
        $this->allow_models[] = 'Account';
        $this->allow_models[] = 'Order';
        $this->allow_models[] = 'OrderDetail';
        $this->allow_models[] = 'Lead';
        $this->allow_models[] = 'Patient';
        $this->allow_models[] = 'User';


        $Recipient_id = $subject->getParam('recipient', 0);
        if ($Recipient_id) {
            $dataModel = $subject
                ->getGatewayServiceVerify()
                ->get('Email')
                ->findOne(['model_id' => $Recipient_id]);

            if($dataModel){
                $this->allow_models[] = ucfirst($dataModel->data);
            }
        }

        if (!is_array($this->allow_models)) {
            return;
        }
        foreach ($this->allow_models as $model) {

            $visibleFields['pattern_items'][$model] = $subject
                ->getAclServiceVerify()
                ->getVisibleFields($model);
        }

        /* Copy current recipient data to array 'Contacts' */
        if (isset($dataModel)){
            $visibleFields['pattern_items']['Contacts']=
                $visibleFields['pattern_items'][ucfirst($dataModel->data)];
        }

        $subject->setData($visibleFields);
    }
}
