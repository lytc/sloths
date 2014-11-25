<?php

namespace Sloths\Db\Table\Sql;

class Insert extends \Sloths\Db\Sql\Insert
{
    use SqlTrait;
    use SqlWriteTrait
    {
        run as protected traitRun;
    }

    /**
     * @param bool $returnsId
     * @return string
     */
    public function run($returnsId = true)
    {
        $result = $this->traitRun();

        if ($returnsId) {
            return $this->getConnection()->getLastInsertId();
        }

        return $result;
    }
}