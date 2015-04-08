<?php

namespace TwigExt\Twig\Extension;

/**
 *
 *
 */
class DateHelperExtension extends \Twig_Extension
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
        return 'zfc_dateHelper';
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $filters = array();

        $dateFormatMethod              = new \Twig_Filter_Method($this, 'dateFormat');
        $filters[ 'dateFormat' ] = $dateFormatMethod;

        return $filters;
    }

    /**
     * @param mixed $entries All entries.
     * @param mixed $without Entries to be removed.
     *
     * @return array Remaining entries of {@code $entries} after removing the entries of {@code $without}.
     */
    public function dateFormat($entries, $without)
    {
//        prn($entries, $without);

        return 'DateFormat';
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
