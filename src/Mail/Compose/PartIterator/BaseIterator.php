<?php
/**
 * Created by PhpStorm.
 * User: ksv
 * Date: 7/17/14
 * Time: 9:16 PM
 */

namespace Mail\Compose\PartIterator;

class BaseIterator
{
    const IteratorAscOrder = 'ASC';
    const IteratorDescOrder = 'DESC';

    protected $partCount = 0;
    protected $order = 'asc';

    public function __construct($params)
    {
        $this->partCount = $params['count'];
        $this->order = $params['order'];
    }

    public function fetchData($mailParts)
    {
        if (!is_array($mailParts) && !$mailParts instanceof \Traversable) {
            throw new \Exception('Wrong mail parts configuration. Iterator cannot iterate non array');
        }

        if (isset($mailParts['header'])) {
            unset($mailParts['header']);
        }

        $parts = array_values($mailParts);

        $data = [];

        $order = $this->order == 'asc' ? 0 : 1;

        $count = count($parts);
        $forCount = $this->partCount ?: $count;

        if (($count - $forCount) < 0) {
            return [ null ];
        }

        for ($i = 0; $i < $forCount; $i++) {
            $index = abs($order*($count - 1) - $i);
            $data[] = $parts[$index]->getData();
        }

        return $data;
    }
}
