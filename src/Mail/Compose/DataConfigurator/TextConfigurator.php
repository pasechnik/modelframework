<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 8/8/14
 * Time: 6:41 PM
 */

namespace Mail\Compose\DataConfigurator;

class TextConfigurator extends BaseConfigurator
{
    /**
     * @param  mixed $tagData
     * @return mixed
     */
    public function configure($tagData)
    {
        if (is_array($tagData)) {
            $textArray = $tagData;
            $text = '';
            $devide = '
            ';
            foreach ($textArray as $textPart) {
                $text = $text.$devide.$textPart;
            }
            $tagData = $text;
        }

        return $tagData;
    }
}
