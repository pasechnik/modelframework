<?php

namespace ModelFramework\Utility;

class Arr
{

    /**
     * Gets value from array is it exists in it and checks for lowercase math of keys
     *
     * @param array|null $a
     * @param string     $key
     * @param null|mixed $default
     *
     * @return mixed
     */
    public static function getDoubtField($a, $key, $default = null)
    {
        $result = $default;

        if (isset($a[$key])) {
            //&& !empty( $a[ $key ] )

            $result = $a[$key];
        } elseif (strtolower($key) !== $key) {
            foreach (array_keys($a) as $_key) {
                if (strtolower($_key) == strtolower($key)) {
                    return $a[$_key];
                }
            }
        } else {
            $result = $default;
        }

        return $result;
    }

    /**
     * Adds $value Value to the $a if $value is not null
     *
     * @param $a
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public static function addNotNull($a, $key, $value)
    {
        if ($value !== null) {
            //          if ( !is_array( $a ) ) { $a = [ ]; }
            $a[$key] = $value;
        }

        return $a;
    }

    /**
     * Merge two arrays together.
     *
     * If an integer key exists in both arrays and preserveNumericKeys is false, the value
     * from the second array will be appended to the first array. If both values are arrays, they
     * are merged together, else the value of the second array overwrites the one of the first array.
     *
     * @param array $a
     * @param array $b
     * @param bool  $preserveNumericKeys
     *
     * @return array
     *
     */
    public static function merge($a, $b, $preserveNumericKeys = false)
    {
        return \Zend\Stdlib\ArrayUtils::merge(self::arr($a), self::arr($b),
            $preserveNumericKeys);
    }

    public static function arr($a)
    {
        if ($a === null) {
            $a = [];
        }
        if ( !is_array($a)) {
            $a = (array)$a;
        }
        return $a;
    }

    public static function put2ArrayKey(array $a, $key, $value)
    {
        if ( !isset($a[$key])) {
            $a[$key] = [];
        }
        if ( !is_array($a[$key])) {
            $a[$key] = (array)$a[$key];
        }

        if ($value instanceof \ArrayObject
            || $value instanceof \ArrayIterator
        ) {
            $value = $value->getArrayCopy();
        }
        if (is_array($value)) {
            foreach ($value as $_k => $_v) {
                $a[$key][$_k] = $_v;
            }
        } else {
            $a[$key][] = $value;
        }
        return $a;
    }

}
