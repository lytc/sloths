<?php
echo $_SERVER['REQUEST_METHOD'] . '-' . file_get_contents('php://input');