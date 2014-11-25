<?php

return [
    'foo' => 'message foo',
    'bar' => 'message :name',
    'plural1' => [
        'foo :name',
        'bar :name',
    ],
    'plural2' => [
        0       => 'There are no apples',
        1       => 'There is one apple',
        '2..19' => 'There are :count apples',
        '20..'  => 'There are many apples'
    ]
];