<?php
require_once('ftp_sync.php');

ftp_sync::$ftp_host  = 'example.com';
ftp_sync::$ftp_user  = 'user';
ftp_sync::$ftp_pass  = '123';
ftp_sync::$ftp_port  = 21;
ftp_sync::$ftp_path  = 'reza/1/';
ftp_sync::$directory = '/home/reza/project/dash/';
ftp_sync::$debug     = true;

$result = ftp_sync::run();
if(!$result)
{
	print_r(ftp_sync::error());
}
?>