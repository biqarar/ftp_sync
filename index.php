<?php
require_once('ftp.php');

ini_set('display_errors'        , 'On');
ini_set('display_startup_errors', 'On');
ini_set('error_reporting'       , 'E_ALL | E_STRICT');
ini_set('track_errors'          , 'On');
ini_set('display_errors'        , 1);
error_reporting(E_ALL);

/**
*
*/
class ftp_sync
{

	public static $ftp_host  = 'example.com';
	public static $ftp_user  = 'user';
	public static $ftp_pass  = '123';
	public static $ftp_port  = 21;
	public static $ftp_path  = 'reza/1/2/3/';
	public static $directory = '/home/reza/project/ftp_sync_test/';


	public static function error($_msg, $_group)
	{
		return $_msg;
	}


	public static function run()
	{

		$connect = ftp::connect(self::$ftp_host, self::$ftp_user, self::$ftp_pass, self::$ftp_port);

		if(!$connect)
		{
			return self::error('can not connect to server');
		}

		if(!chdir(self::$directory))
		{
			return self::error('can not chdir to ftp path');
		}

		$path       = realpath(''). DIRECTORY_SEPARATOR;
		$fdirectory = new \RecursiveDirectoryIterator($path);
		$flattened  = new \RecursiveIteratorIterator($fdirectory);
		$files      = new \RegexIterator($flattened, "/.*/");
		$paths      = [];

		foreach($files as $file)
		{
			$file_name = $file->getFilename();
			if($file_name !== '.' && $file_name !== '..')
			{
				$file_name = $file->getPath() . DIRECTORY_SEPARATOR . $file_name;
				$paths[] = $file_name;
			}
		}

		$ftp_path_split = explode(DIRECTORY_SEPARATOR, self::$ftp_path);
		$ftp_path_split = array_filter($ftp_path_split);

		foreach ($ftp_path_split as $key => $value)
		{
			if(!@ftp::chdir($value. DIRECTORY_SEPARATOR))
			{
				ftp::mkdir($value);
				ftp::chdir($value);
			}
		}

		$get = ftp::get(ftp::pwd());
		var_dump($get);

		// ftp::rmdir(self::$ftp_path);
		// ftp::mkdir(self::$ftp_path);
		// ftp::chdir(self::$ftp_path);

		self::ftp_copy(self::$directory, self::$ftp_path);
	}


	public static function ftp_copy($_from, $_to)
	{
		$d = dir($_from);

	    while($file = $d->read())
	    {
	        if ($file != "." && $file != "..")
	        {
	            if (is_dir($_from."/".$file))
	            {
	                if (!@ftp::chdir($_to."/".$file))
	                {
	                	ftp::mkdir($_to."/".$file);
	                }
	            	self::ftp_copy($_from."/".$file, $_to."/".$file);
	            }
	            else
	            {
	            	$upload = ftp::put($_to."/".$file, $_from."/".$file, FTP_BINARY);
	            }
	        }
	    }
		$d->close();
	}

}

ftp_sync::run();
?>