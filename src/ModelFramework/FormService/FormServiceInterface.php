<?php
/**
 * Class FormServiceInterface
 * @package ModelFramework\FormService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\FormService;

use ModelFramework\DataModel\DataModelInterface;

interface FormServiceInterface
{
    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function get(DataModelInterface $model, $mode, array $fields = []);

    /**
     * @param DataModelInterface $model
     * @param string             $mode
     * @param array              $fields
     *
     * @return $this
     * @throws \Exception
     */
    public function getForm(DataModelInterface $model, $mode, array $fields = []);
}
