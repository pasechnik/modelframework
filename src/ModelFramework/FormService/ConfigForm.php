<?php
/**
 * Class ConfigForm
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

class ConfigForm
{
    public $_id = '';
    public $name = '';
    public $group = '';
    public $type = '';
    public $options = [ ];
    public $attributes = [ ];
    public $fieldsets = [ ];
    public $fieldsets_configs = [ ];
    public $elements = [ ];
    public $filters = [ ];
    public $validationGroup = [ ];

    /**
     * @param array $data
     *
     * @return $this
     */
    public function exchangeArray($data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->$key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}
