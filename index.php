<?php
require_once('ftp_sync.php');

ftp_sync::$ftp_host  = 'example.com';
ftp_sync::$ftp_user  = 'user';
ftp_sync::$ftp_pass  = '123';
ftp_sync::$ftp_port  = 21;
ftp_sync::$ftp_path  = 'reza/1/2/';
ftp_sync::$directory = '/home/reza/project/ftp_sync_test/';
ftp_sync::$debug     = true;

ftp_sync::run();

?>