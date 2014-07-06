<?php
echo $_SERVER['REQUEST_METHOD'] . '-' . strlen(file_get_contents('php://input'));