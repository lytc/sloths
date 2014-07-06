<?php

return [
    'Accepted'              => 'Must be accepted',
    'NotAccepted'           => 'Must not be accepted',

    'After'                 => 'Must be a date after :0',
    'NotAfter'              => 'Must not be a date after :0',

    'Alnum'                 => 'Must contain only letters and numbers',
    'NotAlnum'              => 'Must not contain only letters and numbers',

    'Alpha'                 => 'Must contain only letters',
    'NotAlpha'              => 'Must not contain only letters',

    'Arr'                   => 'Must be an array',
    'NotArr'                => 'Must not be an array',

    'Before'                => 'Must be a date before :0',
    'NotBefore'             => 'Must not be a date before :0',

    'Between'               => 'Must be between :0 and :1',
    'NotBetween'            => 'Must not be between :0 and :1',

    'Blank'                 => 'Must be NULL, an empty string or an empty array',
    'NotBlank'              => 'Must not be NULL, an empty string or an empty array',

    'Bool'                  => 'Must be TRUE or FALSE',
    'NotBool'               => 'Must not be TRUE or FALSE',

    'Callback'              => 'Must be a valid callback',
    'NotCallback'           => 'Must not be a valid callback',

    'Contains'              => 'Must contain :0',
    'NotContains'           => 'Must not contain :0',

    'Date'                  => 'Must be a valid date',
    'NotDate'               => 'Must not be a date',

    'Digit'                 => 'Must contain only digits',
    'NotDigit'              => 'Must not contain only digits',

    'Divisible'             => 'Must be divisible by :0',
    'NotDivisible'          => 'Must not be divisible by :0',

    'Domain'                => 'Must be a valid domain name',
    'NotDomain'             => 'Must not be a domain name',

    'Email'                 => 'Must be a valid email address',
    'NotEmail'              => 'Must not be an email address',

    'EndWith'               => 'Must end with :0',
    'NotEndWith'            => 'Must not end with :0',

    'Equals' => [
        ''                  => 'Must be equals to :0',
        'NON_SCALAR'        => 'Must be equals to given :0'
    ],
    'NotEquals' => [
        ''                  => 'Must not be equals to :0',
        'NON_SCALAR'        => 'Must not be equals to given :0'
    ],

    'Even'                  => 'Must be an even number',
    'NotEven'               => 'Must not be an even number',

    'Float'                 => 'Must be a float number',
    'NotFloat'              => 'Must not be a float number',

    'GreaterThan' => [
        ''                  => 'Must be greater than :0',
        'NON_SCALAR'        => 'Must be greater than given :0'
    ],
    'NotGreaterThan' => [
        ''                  => 'Must not be greater than :0',
        'NON_SCALAR'        => 'Must not be greater than given :0'
    ],

    'GreaterThanOrEqual' => [
        ''                  => 'Must be greater than or equal to :0',
        'NON_SCALAR'        => 'Must be greater than or equal to given :0',
    ],
    'NotGreaterThanOrEqual' => [
        ''                  => 'Must not be greater than or equal to :0',
        'NON_SCALAR'        => 'Must not be greater than or equal to given :0',
    ],

    'HasAttribute'          => 'Must have an attribute :0',
    'NotHasAttribute'          => 'Must not have an attribute :0',

    'HasKey'                => 'Must have the key :0',
    'NotHasKey'             => 'Must not have the key :0',

    'InstOf'                => 'Must be an instanceof class :0',
    'NotInstOf'             => 'Must not be an instanceof class :0',

    'Int'                   => 'Must be a integer number',
    'NotInt'                => 'Must not be a integer number',

    'Ip'                    => 'Must be an IP address',
    'NotIp'                 => 'Must not be an IP address',

    'LeapYear'              => 'Must be a leap year',
    'NotLeapYear'           => 'Must not be a leap year',

    'LengthBetween'         => 'Must have length between :0 and :1',
    'NotLengthBetween'      => 'Must not have length between :0 and :1',

    'Length'                => 'Must have length is :0',
    'NotLength'             => 'Must not have length is :0',

    'LessThan' => [
        ''                  => 'Must be less than :0',
        'NON_SCALAR'        => 'Must be less than given :0',
    ],
    'NotLessThan' => [
        ''                  => 'Must not be less than :0',
        'NON_SCALAR'        => 'Must not be less than given :0',
    ],

    'LessThanOrEqual' => [
        ''                  => 'Must be less than or equal to :0',
        'NON_SCALAR'        => 'Must be less than or equal to given :0',
    ],
    'NotLessThanOrEqual' => [
        ''                  => 'Must not be less than or equal to :0',
        'NON_SCALAR'        => 'Must not be less than or equal to given :0',
    ],

    'Lower'                 => 'Must be lowercase',
    'NotLower'              => 'Must not be lowercase',

    'Match'                 => 'Must match pattern :0',
    'NotMatch'              => 'Must not match pattern :0',

    'Max' => [
        ''                  => 'Must be less than :0',
        'INCLUSIVE'         => 'Must be less than or equal to :0'
    ],
    'NotMax' => [
        ''                  => 'Must not be less than :0',
        'INCLUSIVE'         => 'Must not be less than or equal to :0'
    ],

    'MaxLength'             => 'Must have length less than or equal to :0',
    'NotMaxLength'          => 'Must not have length less than or equal to :0',

    'Min' => [
        ''                  => 'Must be greater than :0',
        'INCLUSIVE'         => 'Must be greater than or equal to :0'
    ],
    'NotMin' => [
        ''                  => 'Must not be greater than :0',
        'INCLUSIVE'         => 'Must not be greater than or equal to :0'
    ],

    'MinLength'             => 'Must have length greater than or equal to :0',
    'NotMinLength'          => 'Must not have length greater than or equal to :0',

    'Negative'              => 'Must be greater than 0',
    'NotNegative'           => 'Must not be greater than 0',

    'Null'                  => 'Must be NULL',
    'NotNull'               => 'Must not be NULL',

    'Numeric'               => 'Must be numeric characters',
    'NotNumeric'            => 'Must not be numeric characters',

    'Object'                => 'Must be an object',
    'NotObject'             => 'Must not be an object',

    'Odd'                   => 'Must be an odd number',
    'NotOdd'                => 'Must not be an odd number',

    'Positive'              => 'Must be less than 0',
    'NotPositive'           => 'Must not be less than 0',

    'Regex'                 => 'Must be a regular expression pattern',
    'NotRegex'              => 'Must not be a regular expression pattern',

    'Required'              => 'Is required',
    'NotRequired'           => 'Is not required',

    'Same' => [
        ''                  => 'Must be the same of :0',
        'NON_SCALAR'        => 'Must be the same of given :0',
    ],
    'NotSame' => [
        ''                  => 'Must not be the same of :0',
        'NON_SCALAR'        => 'Must not be the same of given :0',
    ],

    'Scalar'                => 'Must be a number, string or boolean type',
    'NotScalar'             => 'Must not be a number, string and boolean type',

    'StartWith'             => 'Must be start with :0',
    'NotStartWith'          => 'Must not be start with :0',

    'String'                => 'Must be a string',
    'NotString'             => 'Must not be a string',

    'Upper'                 => 'Must be uppercase',
    'NotUpper'              => 'Must not be uppercase',

    'Url'                   => 'Must be a valid URL',
    'NotUrl'                => 'Must not be an URL',

    'Xdigit'                => 'Must contain only hexadecimal digits',
    'NotXdigit'             => 'Must not contain only hexadecimal digits',
];