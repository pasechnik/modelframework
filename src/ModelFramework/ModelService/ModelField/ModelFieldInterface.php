<?php
/**
 * Created by PhpStorm.
 * User: vlad
 * Date: 27.01.15
 * Time: 19:31
 */

namespace ModelFramework\ModelService\ModelField;

interface ModelFieldInterface
{

    /**
     * @param string $type
     *
     * @return $this
     */
    public function chooseStrategy($type);

    /**
     * @return $this
     */
    public function init();

    /**
     * @return $this
     */
    public function parse();

}
