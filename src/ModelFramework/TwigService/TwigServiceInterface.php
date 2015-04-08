<?php
/**
 * Class TwigServiceInterface
 * @package ModelFramework\TwigService
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Artem Bondarenko a.bondarenko@cronagency.com
 */
namespace ModelFramework\TwigService;

interface TwigServiceInterface{


    public function getParseString($template, $variables = [ ], $params = [ ]);


}