<?php

namespace Sloths\Application\Service;

class Validator extends \Sloths\Validation\Validator implements ServiceInterface, \JsonSerializable
{
    use ServiceTrait;

    /**
     * @param array $chains
     * @return Validator
     */
    public function create(array $chains = null)
    {
        $validator = clone $this;
        $validator->reset();

        if ($chains) {
            $validator->addChains($chains);
        }

        return $validator;
    }

    public function jsonSerialize()
    {
        if ($this->fails()) {
            return ['success' => false, 'messages' => $this->getMessages()];
        }

        return ['success' => true];
    }
}