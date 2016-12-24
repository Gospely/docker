<?php
@header ( "Content-Type:text/html; charset=utf-8" );
define ( 'SCR', 'aCloud_index' );
define ( 'ACLOUD_INDEX', 1 );
define ( "ACLOUD_PATH", dirname ( __FILE__ ) );
require_once ACLOUD_PATH . '/aCloud.php';
$router = new ACloud_Router ();
$router->run ();