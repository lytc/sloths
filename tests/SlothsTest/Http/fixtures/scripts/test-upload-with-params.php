<?php

echo $_POST['foo'] . '-' . filesize($_FILES[0]['tmp_name']);