<?php

namespace ModelFramework\DataModel;

interface DataModelAwareInterface
{
    /**
     * @param DataModelInterface $dataModel
     *
     * @return $this
     */
    public function setDataModel(DataModelInterface $dataModel);

    /**
     * @return DataModelInterface
     */
    public function getDataModel();

    /**
     * @return DataModelInterface
     * @throws \Exception
     */
    public function getDataModelVerify();
}
