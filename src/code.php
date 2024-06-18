<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$BASEDIR = explode('src', dirname(__FILE__))[0];
require $BASEDIR . 'src/settings.php';
require $BASEDIR . 'src/constants.php';
require $BASEDIR . 'src/functions.php';

session_start();
extract($_GET);

$subd = ($_SERVER["HTTP_HOST"] == 'www.misaludonline.cl' or $_SERVER["HTTP_HOST"] == 'misaludonline.cl') ? 'meeting/' : '';
$dmn = 'https://' . $_SERVER["HTTP_HOST"] . '/' . $subd;
$ref = 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

$log = true;
