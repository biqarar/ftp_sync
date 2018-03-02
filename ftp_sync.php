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
	public static $errors    = [];
	public static $status    = true;

	private static $master_pwd = null;


	private static function set_error($_msg)
	{
		self::$status   = false;
		self::$errors[] = $_msg;
	}


	public static function error()
	{
		return self::$errors;
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

		if(!self::$ftp_host)  self::set_error("ftp_host not set");
		if(!self::$ftp_user)  self::set_error("ftp_user not set");
		if(!self::$ftp_pass)  self::set_error("ftp_pass not set");
		if(!self::$ftp_port)  self::set_error("ftp_port not set");
		if(!self::$ftp_path)  self::set_error("ftp_path not set");
		if(!self::$directory) self::set_error("directory not set");

		if(!self::$status) return false;

		$connect = ftp::connect(self::$ftp_host, self::$ftp_user, self::$ftp_pass, self::$ftp_port);

		if(!$connect)
		{
			return self::set_error('can not connect to server');
		}

		if(!@ftp::chdir(self::$ftp_path))
		{
			return self::set_error('can not chdir to remote server');
		}

		self::$master_pwd = ftp::pwd();

		if(!@chdir(self::$directory))
		{
			return self::set_error('can not chdir to ftp path');
		}

		if(!self::$status) return false;

		self::clean(self::$master_pwd);

		ftp::chdir(self::$master_pwd);

		self::send(self::$directory);

		ftp::close();

		return true;
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

		$list = glob(getcwd(). DIRECTORY_SEPARATOR. "*");
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
					ftp::put(ftp::pwd(). DIRECTORY_SEPARATOR. $file, $value, FTP_ASCII);
				}
			}
		}
	}
}
?>