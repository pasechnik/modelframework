<?php
/**
 * Class PDFObserver
 * @package ModelFramework\ModelViewService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ViewService\Observer;

use ModelFramework\ConfigService\ConfigAwareInterface;
use ModelFramework\ConfigService\ConfigAwareTrait;
use ModelFramework\AclService\AclDataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareInterface;
use ModelFramework\Utility\SplSubject\SubjectAwareTrait;
use ModelFramework\ViewService\View;
use Wepo\Model\Status;

//use \ZendService\Amazon\Exception\ExceptionInterface as AmazonException;
//use \ZendService\Amazon\S3\S3;




class PDFObserver implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{
    use ConfigAwareTrait, SubjectAwareTrait;

    private $_aclModel = null;
    private $_model = null;


    /**
     * Select data from DB, generate PDF file, create new document
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $dataModel = $this->initModel();
        $dataModel->document_extension ='pdf';

        $viewConfig = $subject->getViewConfigVerify();
        $query
            = $subject->getQueryServiceVerify()
            ->get($viewConfig->query)
            ->setParams($subject->getParams())
            ->process();
        $subject->setData($query->getData());
        $model  = $subject->getGatewayServiceVerify()->get('Order')->findOne($query->getWhere());
        if ( !$model) {
            throw new \Exception('Data not found');
        }

        $variable[$model->getModelName()] = $model->toArray();

        $order=$model->toArray();
        $model_tpl  = $subject->getGatewayServiceVerify()->get('TemplatePDF')->findOne(['_id'=>$_GET['template']]);
        if ( !$model_tpl) {
            throw new \Exception('Data of template not found');
        }
        $dataModel->title=$order['title'];
        $dataModel->document_name = $model_tpl->title. ' - '.$order['title'];
        $dataModel->description  = $order['subject'].' ('.$model_tpl->title .')';
        $dataModel->document_real_name='document.pdf';
        $dataModel->owner_id=$order['owner_id'];
        $dataModel->creator_id=$order['creator_id'];
        $dataModel->patient_id=$order['patient_id'];


        $query
            = $subject->getQueryServiceVerify()
            ->get('OrderDetail.list')
            ->setParams($subject->getParams())
            ->process();
        $subject->setData($query->getData());
        $model  = $subject->getGatewayServiceVerify()->get('OrderDetail')->find($query->getWhere());
        $order['products']=$model->toArray();


        $variable['OrderDetail'] = $model->toArray();

        $variable['Card'] = $subject->getGatewayServiceVerify()
            ->get('CardPatient')
            ->findOne([
                '_id'=>$order['payment_card_id'],

                ]);

          /* Generate PDF*/
        $PDFService = $subject->getPDFServiceVerify();
        $pdf = $PDFService->getPDFtoSave($model_tpl->body,$variable);

        $dataModel->document_size=(string) (round((float) strlen($pdf) / 131072, 2)).' MB';

        /* Store PDF*/
        $fileService = $subject->getFilesystemServiceVerify();
        $dataModel->filesystem=$fileService->getFilesystem();
        $dataModel->document =
             $fileService->saveStringToFile($model_tpl->model_title.'.pdf',$pdf, false );

        /* Save to DB */
        $model = $this->setModel($dataModel);
        $subject->getGatewayServiceVerify()
            ->get('Document')->save($dataModel);
        $subject->getLogicServiceVerify()->get('postinsert', 'Document')->trigger($this->_model);
        $url = $subject->getParams()->getController()->url()
            ->fromRoute('common', [
            'data' => 'document',
            'view' => 'view',
            'id'=>$model->id,
        ]);
        $subject->setRedirect($subject->refresh(
            'Document data was successfully created',
            $url  ));

        return;
    }

    public function getModel()
    {
        if ($this->_aclModel !== null) {
            return $this->_aclModel;
        }

        return $this->_model;
    }

    public function getModelData()
    {
        return $this->_model;
    }

    public function initModel()
    {
        $subject    = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $query      =
            $subject->getQueryServiceVerify()
                ->get($viewConfig->query)
                ->setParams($subject->getParams())
                ->process();

        $data = $subject->getData();
        if (isset($data[ 'model' ]) && $data[ 'model' ] instanceof DataModelInterface) {
            $model = $data[ 'model' ];
        } else {
            if ($viewConfig->mode == 'pdf') {
                $model = $subject->getGateway()->model();
            } elseif ($viewConfig->mode == 'update') {
                $model = $subject->getGateway()->findOne($query->getWhere());
                if ($model == null) {
                    throw new \Exception('Data is not accessible');
                }
            } else {
                throw new \Exception("Wrong mode  '".$viewConfig->mode."' in  ".$viewConfig->key.
                    ' View Config for the '.get_class());
            }
        }

        if ($model instanceof AclDataModel) {
            $this->_aclModel = $model;
            $this->_model    = $this->_aclModel->getDataModel();
        }

        $subject->getLogicServiceVerify()->get('preinsert', 'Document')->trigger($this->_model);

        return $this->_model;
    }

    public function setModel(DataModelInterface $model)
    {
        if ($this->_aclModel !== null && $this->_aclModel instanceof AclDataModel) {
            $this->_aclModel->setDataModel($model);
            $model = $this->_aclModel;
        }

        $this->getSubject()->setData([ 'model' => $model ]);

        return $model;
    }
}