<?php

namespace Sloths\Application\Service;

use Sloths\Db\ConnectionManager;

class Database extends ConnectionManager implements ServiceInterface
{
    use ServiceTrait;
}