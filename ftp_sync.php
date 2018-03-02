<?php
require_once('ftp.php');

/**
* sync local directory to ftp remote directory
*/
class ftp_sync
{

	public static $ftp_host  = null;
	public static $ftp_user  = null;
	public static $ftp_pass  = null;
	public static $ftp_port  = null;
	public static $ftp_path  = null;
	public static $directory = null;
	public static $debug     = false;

	private static $master_pwd = null;


	public static function error($_msg)
	{
		echo $_msg;
		exit();
	}


	public static function run()
	{
		if(self::$debug)
		{
			ini_set('display_startup_errors', 'On');
			ini_set('error_reporting', 'E_ALL | E_STRICT');
			ini_set('track_errors', 'On');
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}

		$connect = ftp::connect(self::$ftp_host, self::$ftp_user, self::$ftp_pass, self::$ftp_port);

		if(!$connect)
		{
			return self::error('can not connect to server');
		}

		if(!@ftp::chdir(self::$ftp_path))
		{
			return self::error('can not chdir to remote server');
		}

		self::$master_pwd = ftp::pwd();

		if(!@chdir(self::$directory))
		{
			return self::error('can not chdir to ftp path');
		}

		self::clean(self::$master_pwd);

		ftp::chdir(self::$master_pwd);

		self::send(self::$directory);

		ftp::close();
	}


	private static function clean($_directory)
	{
		$list = ftp::nlist($_directory);

		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				if(@ftp::chdir($value))
				{
					self::clean($value);
					ftp::rmdir($value);
				}
				else
				{
					ftp::delete($value);
				}
			}
		}
	}


	private static function send($_directory)
	{
		chdir($_directory);

		$list = glob(getcwd(). "/*");
		if(is_array($list))
		{
			foreach ($list as $key => $value)
			{
				if(is_dir($value))
				{
					$folder = explode(DIRECTORY_SEPARATOR, $value);
					$folder = end($folder);
					$new_dir  = ftp::mkdir($folder);
					if($new_dir)
					{
						ftp::chdir($new_dir);
					}
					self::send($value);
					$current_pwd = ftp::pwd();
					$folder = explode(DIRECTORY_SEPARATOR, $current_pwd);
					array_pop($folder);
					$folder = implode(DIRECTORY_SEPARATOR, $folder);
					ftp::chdir($folder);
				}
				else
				{
					$file = explode(DIRECTORY_SEPARATOR, $value);
					$file = end($file);
					ftp::put(ftp::pwd(). '/'. $file, $value, FTP_ASCII);
				}
			}
		}
	}
}
?>