<?php
/**
 * Class ModelConfigParserService
 *
 * @package ModelFramework\ModelConfigParserService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\ModelService\ModelConfigParserService;

use ModelFramework\ConfigService\ConfigServiceAwareInterface;
use ModelFramework\ConfigService\ConfigServiceAwareTrait;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareInterface;
use ModelFramework\FieldTypesService\FieldTypesServiceAwareTrait;
use ModelFramework\ModelService\ModelConfig\ModelConfig;
use ModelFramework\Utility\Arr;

class ModelConfigParser0Service
    implements ModelConfigParserServiceInterface,
               FieldTypesServiceAwareInterface, ConfigServiceAwareInterface
{

    use FieldTypesServiceAwareTrait, ConfigServiceAwareTrait;

    /**
     * @param string $modelName
     *
     * @return array
     * @throws \Exception
     */
    public function getModelConfig($modelName)
    {
        $cd = $this->getConfigServiceVerify()
            ->getByObject($modelName, new ModelConfig());

        if ($cd == null) {
            throw new \Exception('Please fill ModelConfig for the ' . $modelName
                . '. I can\'t work on');
        }

        return $this->pullModelConfig($cd);
    }

    /**
     * @param ModelConfig $cm
     *
     * @return array
     */
    public function pullModelConfig(ModelConfig $cm)
    {
        $start_config = [
            'fields'    => [],
            'joins'     => [],
            //            'unique'       => [ ],
            'adapter'   => $cm->adapter,
            'model'     => $cm->model,
            'label'     => $cm->label,
            'table'     => $cm->table,
            'fieldsets' => [],
            'unique'    => $cm->unique,
        ];

        foreach ($cm->groups as $_grp => $_fls) {
            if (is_numeric($_grp)) {
                $_grp   = $_fls;
                $_label = $cm->model . ' information';
                if ($_grp == 'fields') {
                    $_baseFieldSet = true;
                } else {
                    $_baseFieldSet = false;
                }
                $_fls = [
                    'label'    => $_label,
                    'elements' => [],
                    'base'     => $_baseFieldSet,
                ];
            } else {
                $_fls ['elements'] = [];
                $_fls ['base']
                                   =
                    isset($_fls ['base']) && $_fls ['base'] == true;
            }
            $start_config ['fieldsets'] [$_grp] = $_fls;
        }
        $modelConfig = array_merge_recursive($start_config,
            $this->getUtilityFields($cm->model));
        foreach ($cm->fields as $field_name => $field_conf) {
            $modelConfig = array_merge_recursive($modelConfig,
                $this->createField($field_name, $field_conf));
        }

        return $modelConfig;
    }

    /**
     * @param string $name
     * @param array  $conf
     *
     * @return array
     */
    protected function createField($name, $conf)
    {

        $type                = $conf['type'];
        $_fieldconf          = $this->getField($type);

        $_fieldsets          = [];
        $_joins              = [];
        $_fieldconf['label'] = isset($conf['label']) ? $conf['label']
            : ucfirst($name);
        $_labels             = [];

        if (in_array($type, ['static_lookup', 'lookup'])) {
            $_sign       = '_';
            $_joinfields = [];
            $_i          = 0;
            $_fields     = [];
            foreach ($conf['fields'] as $_jfield => $_jlabel) {
                if ( !$_i++) {
                    $_fieldconf['alias'] = $name . $_sign . $_jfield;
                }
                $_fields[$name . $_sign . $_jfield]     = [
                    'type'     => 'alias',
                    'fieldtype'=> 'alias',
                    'datatype' => 'string',
                    'default'  => '',
                    'source'   => $name . '_id',
                    'label'    => $_jlabel,
                    'source'   => $name,
                    'group'    => isset($conf['group']) ? $conf['group']
                        : 'fields',
                ];
                $_labels[$name . $_sign . $_jfield]     = $_jlabel;
                $_joinfields[$name . $_sign . $_jfield] = $_jfield;
                if (isset($conf['group'])) {
                    $_fieldsets[$conf['group']]['elements'][$name . $_sign
                    . $_jfield]
                                         = $_jlabel;
                    $_fieldconf['group'] = $conf['group'];
                }
            }
            $_joins[]               = [
                'model'  => $conf['model'],
                'on'     => [$name . '_id' => '_id'],
                'fields' => $_joinfields,
                'type'   => $type,
            ];
            $_fieldconf['source']   = $name;
            $_fieldconf['default']  = isset($conf['default']) ? $conf['default']
                : '';
            $_fields[$name . '_id'] = $_fieldconf;
            $_labels[$name . '_id'] = $_jlabel;
            $name .= '_id';
        } else {
            if (isset($conf['group'])) {
                $_fieldsets[$conf['group']]['elements'][$name]
                                     = $_fieldconf['label'];
                $_fieldconf['group'] = $conf['group'];
            }
            //FIXME this does not work for lookup fields, only for source fields. Need update.
            $_fieldconf['default'] = isset($conf['default']) ? $conf['default']
                : '';
            $_fieldconf['source']  = $name;
            $_fields               = [$name => $_fieldconf];
            $_labels               = [$name => $_fieldconf['label']];

            $_utility = $this->getFieldPart($conf['type'], 'utility');
            if (count($_utility)) {
                $_fields = array_merge($_fields, $_utility);
            }
        }
        $_infilter = $this->getInputFilter($type);
        if (isset($conf['required'])) {
            $_infilter['required'] = true;
        }
        $_infilter['name'] = $name;
        $_filters          = [$name => $_infilter];

        $result            = [
            'labels'    => $_labels,
            'fields'    => $_fields,
            'filters'   => $_filters,
            'joins'     => $_joins,
            'fieldsets' => $_fieldsets,
        ];

        return $result;
    }

    /**
     * Returns array with all registered models names
     *
     * @return array
     */
    public function getAllModelNames()
    {
        $models = [
            'User',
            'Mail',
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
        ];

        return $models;
    }


    /**
     * @param $model
     *
     * @return array
     * @throws \Exception
     */
    public function getAvailableIndexes($model)
    {
        $modelConfig = $this->getModelConfig($model);

        $indexes = [];
        foreach ($modelConfig['fields'] as $_key => $_field) {
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
