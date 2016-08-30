<?php
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require ROOT .'PositiveCode/ClassLoader.php';

(new PositiveCode\ClassLoader(ROOT))->register();

