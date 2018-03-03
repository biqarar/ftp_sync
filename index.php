<?php
require_once('ftp_sync.php');

ftp_sync::$ftp_host  = '__YOUR FTP DOMAIN__';
ftp_sync::$ftp_user  = '__YOUR FTP USER__';
ftp_sync::$ftp_pass  = '__YOUR FTP PASSWORD__';
ftp_sync::$ftp_port  = 21;
ftp_sync::$ftp_path  = '__YOUR FTP PATH DIRECTORY__[reza/1/ FOR EXAMPLE]';
ftp_sync::$directory = '__YOUR LOCAL PATH DIRECTORY__[/home/reza/1/ FOR EXAMPLE]';
ftp_sync::$debug     = true;

$result = ftp_sync::run();
if(!$result)
{
	// show errors
	// print_r(ftp_sync::error());
}
?>