<?php

namespace ModelFramework\ModelService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\DataModel\DataModel;
use ModelFramework\DataModel\DataModelInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\GatewayService\GatewayServiceAwareInterface;
use ModelFramework\GatewayService\GatewayServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\ModelService\ModelConfig\ParsedModelConfig;
use ModelFramework\ModelService\ModelConfigParser\ModelConfigParser;
use ModelFramework\Utility\Arr;

/**
 * Class ModelService
 *
 * @package ModelFramework\ModelService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */
class ModelService
    implements ModelServiceInterface, ConfigServiceAwareInterface,
    FieldTypesServiceAwareInterface, GatewayServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, GatewayServiceAwareTrait, ConfigServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function get($modelName)
    {
        return $this->getModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    public function getModel($modelName)
    {
        return $this->createModel($modelName);
    }

    /**
     * @param string $modelName
     *
     * @return DataModelInterface
     */
    protected function createModel($modelName)
    {
        $parsedModelConfig = $this->getParsedModelConfig($modelName);
        $model             = new DataModel();
        $model->setParsedModelConfig($parsedModelConfig);
        return $model;
    }

    /**
     * @param string $model
     *
     * @return bool
     * @throws \Exception
     */
    public function makeIndexes($model)
    {
        if ($this->getGatewayService() === null) {
            return false;
        }
        $indexes = $this->getAvailableIndexes($model);
        $this->getGatewayServiceVerify()->get($model)->createIndexes($indexes);
        return [];
    }

    /**
     * @param string $modelName
     *
     * @return ModelConfig
     * @throws \Exception
     */
    public function getModelConfig($modelName)
    {
        $modelConfig = $this->getConfigServiceVerify()
            ->getByObject($modelName, new ModelConfig());
        if ($modelConfig == null) {
            throw new \Exception('Please fill ModelConfig for the ' . $modelName
                . '. I can\'t work on');
        }

        return $modelConfig;
    }

    /**
     * @param string $modelName
     *
     * @return ParsedModelConfig
     * @throws \Exception
     */
    public function getParsedModelConfig($modelName)
    {
        return $this->pullModelConfig(
            $this->getModelConfig($modelName)
        )->getParsedModelConfig();
    }

    /**
     * @param ModelConfig $modelConfig
     *
     * @return ParsedModelConfig
     */
    public function pullModelConfig(ModelConfig $modelConfig)
    {

        $modelConfigParser = new ModelConfigParser();
        $modelConfigParser->setFieldTypesService($this->getFieldTypesServiceVerify());
        $modelConfigParser->setModelConfig($modelConfig);
        $modelConfigParser->init()->notify();

        return $modelConfigParser;
    }

    /**
     * Returns array with all registered models names
     *
     * @return array
     */
    public function getAllModelNames($scope = null)
    {
        $models = [
            'custom' => [
                'Lead',
                'Patient',
                'Account',
                'Document',
                'Product',
                'Pricebook',
                'PricebookDetail',
                'Activity',
                'Quote',
                'QuoteDetail',
                'Order',
                'OrderDetail',
                'Invoice',
                'InvoiceDetail',
                'Payment',
                'EventLog',
                'Doctor',
                'CardPatient',
                'CardLead',
                'Note',
                'NoteLead',
                'NotePatient',
                'Call',
                'Task',
                'Event',
                'Vendor',
            ],
            'system' => [
                'User',
                'Mail',
                'Test',
                'EmailToMail',
                'Acl',
                'MainUser',
                'MainCompany',
                'MainDb',
                'User',
                'SaUrl',
                'Role',
                'Mail',
                'MailDetail',
                'MailReceiveSetting',
                'MailSendSetting',
                'Email0',
                'Email',
                'EventLog',
            ]
        ];

        if ($scope !== null && $scope === 'all') {
            $result = $models['custom'];
            $result = Arr::merge($result, $models['system']);
            return $result;
        }

        if ($scope == null || !isset($models[$scope])) {
            $result   = $models['custom'];
            $result[] = 'User';
            $result[] = 'Mail';
            return $result;
        }

        return $models[$scope];
    }


    /**
     * @param $model
     *
     * @return array
     * @throws \Exception
     */
    public function getAvailableIndexes($model)
    {
        $modelConfig = $this->getParsedModelConfig($model);

        $indexes = [];
        $i = 0;
        foreach ($modelConfig->fields as $_key => $_field) {
            if ($i++ >= 64){
                break;
            }
            if ($_key == '_id' || $_field['datatype'] == 'array'
                || $_field['type'] == 'source'
            ) {
                continue;
            }
            /**
             * if we want to index all text field in text search
             */
            /**
             * if ($_field['type'] == 'field' && $_field['datatype'] == 'string') {
             * $indexes = Arr::put2ArrayKey($indexes, 'text', [$_key => 'text']);
             * }
             */
            if ($_key == 'title') {
                $indexes = Arr::put2ArrayKey($indexes, 'text',
                    [$_key => 'text']);
            }
            /**
             * end
             */
            if ($_field['fieldtype'] == 'textarea') {
                continue;
            }

            $indexes = Arr::put2ArrayKey(
                $indexes,
                'idx_' . $_key,
                [$_key => 1]
            );

        }

        return $indexes;
    }
}
