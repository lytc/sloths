<?php

namespace Lazy\Db\Model;

class ArrayCollection extends Collection
{
    /**
     * @var array
     */
    protected $models = [];

    /**
     * @param array $rows
     * @param string $modelClassName
     */
    public function __construct(array $rows, $modelClassName)
    {
        $this->modelClassName = $modelClassName;

        foreach ($rows as $row) {
            $this->models[] = new $modelClassName($row, $this);
        }
    }
}