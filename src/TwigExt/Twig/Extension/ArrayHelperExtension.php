<?php

namespace TwigExt\Twig\Extension;

/**
 *
 *
 */
class ArrayHelperExtension extends \Twig_Extension
{
    /**
     */
    public function __construct()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'zfc_arrayHelper';
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $filters = array();

        $withoutMethod              = new \Twig_Filter_Method($this, 'without');
        $filters[ 'without' ] = $withoutMethod;

        $replaceKeyMethod              = new \Twig_Filter_Method($this, 'replaceKey');
        $filters[ 'replaceKey' ] = $replaceKeyMethod;

        $removeKeyMethod              = new \Twig_Filter_Method($this, 'removeKey');
        $filters[ 'removeKey' ] = $removeKeyMethod;

        return $filters;
    }

    /**
     * @param mixed $entries All entries.
     * @param mixed $without Entries to be removed.
     *
     * @return array Remaining entries of {@code $entries} after removing the entries of {@code $without}.
     */
    public function without($entries, $without)
    {
        if (!is_array($without)) {
            $without = array( $without );
        }

        return array_diff($this->convertToArray($entries), $without);
    }

    /**
     * @param mixed $entries All entries.
     * @param mixed $key     Key of the entry to be merged.
     * @param mixed $value   Value of the entry to be merged.
     *
     * @return array Entries of {@code $entries} merged with an entry built from {@code $key} and {@code $value}.
     */
    public function replaceKey($entries, $key, $value)
    {
        return array_merge($this->convertToArray($entries), array( $key => $value ));
    }

    /**
     * @param mixed $entries All entries.
     * @param mixed $key     Key of the entry to be removed.
     *
     * @return array Entries of {@code $entries} without the entry with key {@code $key}.
     */
    public function removeKey($entries, $key)
    {
        $result = $this->convertToArray($entries);
        $minus = array_flip($this->convertToArray($key));
        $result = array_diff_key($result, $minus);

        return $result;
    }

    protected function convertToArray($source)
    {
        if (is_array($source)) {
            return $source;
        }

        if ($source instanceof \Traversable) {
            return iterator_to_array($source, true);
        }

        throw new \InvalidArgumentException('The filter can be applied to arrays only.');
    }
}
