<?php

namespace ModelFramework\Utility;

class Obj
{
    public static function create($class_name, $params = null)
    {
        if (!class_exists($class_name)) {
            return;
        }
        if ($params === null) {
            return new $class_name();
        }
        if (method_exists($class_name, '__construct') === false) {
            throw new \Exception('Constructor for the class '.$class_name.
                                  ' does not exist, you should not pass arguments to the constructor of this class!');
        }
        $refMethod = new \ReflectionMethod($class_name, '__construct');
        $re_args   = [ ];
        foreach ($refMethod->getParameters() as $param) {
            if (array_key_exists($param->getName(), $params)) {
                if ($param->isPassedByReference()) {
                    $re_args[ $param->getName() ] = & $params[ $param->getName() ];
                } else {
                    $re_args[ $param->getName() ] = $params[ $param->getName() ];
                }
            } elseif ($param->isOptional()) {
                $re_args[ $param->getName() ] = $param->getDefaultValue();
            }
        }
        $refClass = new \ReflectionClass($class_name);

        return $refClass->newInstanceArgs($re_args);
    }
}
