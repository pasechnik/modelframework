<?php
/**
 * Class LetterParamObserver
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

class LetterParamObserver extends AbstractObserver
{
    /**
     * @param \SplSubject|Query $subject
     *
     * @throws \Exception
     */
    public function update(\SplSubject $subject)
    {
        $this->setSubject($subject);

        $data = [
            'params' => [],
        ];

        $where = [ ];
        foreach ($this->getRootConfig() as $field => $param) {
            $letter = $subject->getParam($param, '');
            if (strlen($letter)) {
                $where[ $field ] =
                    new \MongoRegex('/^'.$letter.'/i');
                $data['queryparams'][$param] = $letter;

                $data['letter'] = $letter;
            }
        }

        $subject->setData($data);
        $subject->setWhere($where);
    }
}
