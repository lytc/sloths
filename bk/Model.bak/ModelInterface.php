<?php

namespace Sloths\Db\Model;

interface ModelInterface
{
    const INT          = 'int';
    const INTEGER      = self::INT;
    const TINYINT      = 'tinyint';
    const SMALLINT     = 'smallint';
    const MEDIUMINT    = 'mediumint';
    const BIGINT       = 'bigint';
    const DOUBLE       = 'double';
    const REAL         = self::DOUBLE;
    const FLOAT        = 'float';
    const DECIMAL      = 'decimal';
    const NUMERIC      = self::DECIMAL;
    const CHAR         = 'char';
    const VARCHAR      = 'varchar';
    const BINARY       = 'binary';
    const VARBINARY    = 'varbinary';
    const DATE         = 'date';
    const TIME         = 'time';
    const DATETIME     = 'datetime';
    const TIMESTAMP    = 'timestamp';
    const YEAR         = 'year';
    const TINYBLOB     = 'tinyblob';
    const BLOB         = 'blob';
    const MEDIUMBLOB   = 'mediumblob';
    const LONGBLOB     = 'longblob';
    const TINYTEXT     = 'tinytext';
    const TEXT         = 'text';
    const MEDIUMTEXT   = 'mediumtext';
    const LONGTEXT     = 'longtext';
    const ENUM         = 'enum';
    const SET          = 'set';
    const BOOLEAN      = 'boolean';
}