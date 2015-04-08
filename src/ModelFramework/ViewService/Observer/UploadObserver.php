<?php
/**
 * Class UploadObserver
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

class UploadObserver implements \SplObserver, ConfigAwareInterface, SubjectAwareInterface
{
    use ConfigAwareTrait, SubjectAwareTrait;

    private $_aclModel = null;
    private $_model = null;

    /**
     * @param \SplSubject|View $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);
        $files = $subject->getParams()->fromFiles();
        if (count($files)) {
            $fileService = $subject->getFilesystemServiceVerify();
            $subject->getLogicServiceVerify()->setParams($subject->getParams());

            $dataModel = $this->initModel();

            if ($dataModel->__isset('avatar'))
            {
//                            prn($dataModel);
                $oldAvatar =$dataModel->avatar();
                $oldAvatar = dirname($fileService->setDestenation($oldAvatar, true, lcfirst($dataModel->getModelName()))) . '/'.$oldAvatar;
            }

            foreach ($files as $_group => $_filefields) {
                foreach ($_filefields as $_fieldname => $_file) {
                    if (isset($dataModel->$_fieldname) && !$_file['error']) {
                        $realname = $_fieldname . '_real_name';
                        $size = $_fieldname . '_size';
                        $extension = $_fieldname . '_extension';
                        if ($_fieldname != 'avatar') {
                            $dataModel->$_fieldname = $fileService->saveFile($_file['name'], $_file['tmp_name']);
                        } else {
                            if (substr($_file['type'], 0, 5) != 'image') {
                                break;
                            }
                            $dataModel->$_fieldname =
                                basename($fileService->saveFile($_file['name'], $_file['tmp_name'], true,
                                    lcfirst($dataModel->getModelName())));
                            /* Delete old avatar */
                            if ($oldAvatar) {
                                $fileService->deleteFile($oldAvatar);
                            }
                        }
                        if (isset($dataModel->$size)) {
                            $dataModel->$size = (string)(round((float)$_file['size'] / 1048576, 2)) . ' MB';
                        }
                        if (isset($dataModel->$realname)) {
                            $dataModel->$realname = basename($_file['name']);
                        }
                        if (isset($dataModel->$extension)) {
                            $dataModel->$extension = $fileService->getFileExtension($_file['name']);
                        }
                        if (isset($dataModel->filesystem)) {
                            $dataModel->filesystem = $fileService->getFilesystem();
                        }
                    }
                }
            }
            $model = $this->setModel($dataModel);
        }
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
        $subject = $this->getSubject();
        $viewConfig = $subject->getViewConfigVerify();
        $query =
            $subject->getQueryServiceVerify()
                ->get($viewConfig->query)
                ->setParams($subject->getParams())
                ->process();

        $data = $subject->getData();
        if (isset($data['model']) && $data['model'] instanceof DataModelInterface) {
            $model = $data['model'];
        } else {
            if ($viewConfig->mode == 'insert') {
                $model = $subject->getGateway()->model();
//                $model = $query->setDefaults( $model );
            } elseif ($viewConfig->mode == 'update') {
                $model = $subject->getGateway()->findOne($query->getWhere());
                if ($model == null) {
                    throw new \Exception('Data is not accessible');
                }
            } else {
                throw new \Exception("Wrong mode  '" . $viewConfig->mode . "' in  " . $viewConfig->key .
                    ' View Config for the ' . get_class());
            }
        }

        if ($model instanceof AclDataModel) {
            $this->_aclModel = $model;
            $this->_model = $this->_aclModel->getDataModel();
        }

        $subject->getLogicServiceVerify()->get('setDefaults', $model->getModelName())->trigger($this->_model);

        return $this->_model;
    }

    public function setModel(DataModelInterface $model)
    {
        if ($this->_aclModel !== null && $this->_aclModel instanceof AclDataModel) {
            $this->_aclModel->setDataModel($model);
            $model = $this->_aclModel;
        }

        $this->getSubject()->setData(['model' => $model]);

        return $model;
    }
}
