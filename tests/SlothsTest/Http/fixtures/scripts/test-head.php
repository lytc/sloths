<?php

$response = $_GET + ['bar' => $_SERVER['HTTP_BAR']];

header('Response: ' . json_encode($response));