<?php
	//
	// phocus_ini.php
	//
	class phocus_ini
	{
		const INI_OPTIONAL='OPTIONAL';
		//
		//
		private $ini;
		private $ini_path;
		//
		//
		public function __construct($application_name)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==1)
				{
					throw new phocus_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				if (is_string($application_name)!==TRUE)
				{
					throw new phocus_fault('Parameter is not string.  It is ['.gettype($application_name).']', origin());
				} // if (is_string($application_name)!==TRUE)
				//
				//
				/////////////////////
				// Make ini object //
				/////////////////////
				//
				$this->ini_path=$this->ini_path($application_name);
				//
				$this->ini=$this->ini_load($this->ini_path);
			}
			catch(Throwable $e)
			{
				throw new phocus_fault('Cannot make ini', origin(), $e);
			} // try
		} // __construct()
		//
		//
		private function ini_load($application_ini_path)
		{
			try
			{
				// Does the ini file exist?
				if (file_exists($application_ini_path)!==TRUE)
				{
					// No, ini does not exist
					//
					// Let's try to get the installation sample ini
					$template=new phocus_template('phocus_application.ini', phocus_template::CORE);
					//
					$template->add_token('DATABASE_HOST', '');
					$template->add_token('DATABASE_USER', '');
					$template->add_token('DATABASE_PASSWORD', '');
					$template->add_token('DATABASE_NAME', '');
					$template->add_token('SMTP_HOST', '');
					$template->add_token('SMTP_USER', '');
					$template->add_token('SMTP_PASSWORD', '');
					$template->add_token('SMTP_FROM_ADDRESS', '');
					$template->add_token('SMTP_FROM_NAME', '');
					//
					$data=$template->render();
					file_save($application_ini_path, $data);
				} // if (file_exists($application_ini_path)!==TRUE)
				//
				// Does the ini file exist now?
				if (file_exists($application_ini_path)!==TRUE)
				{
					// No, ini file is missing
					throw new phocus_fault('Missing ini file', $application_ini_path);
				}
				else
				{
					// Yes, exists
					$ini=@parse_ini_file($application_ini_path, FALSE, INI_SCANNER_TYPED);
					//
					// Did it parse?
					if ($ini===FALSE)
					{
						// No, did not parse
						throw new phocus_fault('Could not parse ini', $application_ini_path);
					} // if ($ini===FALSE)
				} // if (file_exists($application_ini_path)!==TRUE)
				//
				return $ini;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not load ini', origin(), $e);
			} // try
		} // ini_load()
		//
		//
		private function ini_path($application_name)
		{
			try
			{
				$application_ini_path='../'.$application_name.'.ini';
				//
				return $application_ini_path;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot make application ini path', origin(), $e);
			} // try
		} //  ini_path()
		//
		//
		public function get_ini()
		{
			try
			{
				$arg_count=func_num_args();
				switch ($arg_count)
				{
					case 0: {
						$result=$this->ini;
					break; }
					//
					case 1: {
						$needle=func_get_arg(0);
						//
						confirm_string($needle);
						confirm_array_element($needle, $this->ini);
						//
						$result=$this->ini[$needle];
					break; }
					//
					case 2: {
						$needle=func_get_arg(0);
						$optional=func_get_arg(1);
						//
						confirm_string($needle);
						confirm_string($optional);
						//
						if ($optional!==phocus_ini::INI_OPTIONAL)
						{
							throw new phocus_fault('Unknown option', $optional);
						} // if ($optional!==phocus_ini::INI_OPTIONAL)
						//
						if (array_key_exists($needle, $this->ini)!==TRUE)
						{
							$result=NULL;
						}
						else
						{
							$result=$this->ini[$needle];
						} // if (array_key_exists($needle, $this->ini)!==TRUE)
					break; }
					//
					default: {
						throw new phocus_fault('Invalid argument count', $arg_count);
					break; }
				} // switch ($arg_count)
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot return ini', origin(), $e);
			} // try
		} // get_ini()
		//
		//
		public function get_ini_path()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==0)
				{
					throw new phocus_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==0)
				//
				//
				return $this->ini_path;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get ini path', origin(), $e);
			} // try
		} // get_ini_path()
	} // phocus_ini
?>
