<?php
	//
	// phocus_system.php
	//
	class phocus_system
	{
		const INI_OPTIONAL = 1;
		//
		//private $application_class_path;
		private $application_ini_path;
		private $application_name;
		private $ini;
		//
		//
		public function __construct($application_name='')
		{
			try
			{
trace("\$application_name=$application_name");
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				switch ($arg_count)
				{
					case 0: {
						$this->application_name=application_name();
					break; }
					//
					case 1: {
						$this->application_name=func_get_args(0);
					break; }
					//
					default: {
						throw new phocus_fault('Invalid argument count', $arg_count);
					break; }
				} // switch ($arg_count)
trace("\$this->application_name={$this->application_name}");
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($this->application_name);
				//
				//
				//////////////////
				// Sanity Check //
				//////////////////
				//
				confirm_path_safe($this->application_name);
				//
				//
				///////////////////////////////
				// Construct the application //
				///////////////////////////////
				//
				//$this->database=FALSE;
				//
				$this->application_ini_path='../'.$this->application_name.'.ini';
trace("\$this->application_ini_path={$this->application_ini_path}");
				//
				// Does the ini file exist?
				if (file_exists($this->application_ini_path)!==TRUE)
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
					file_save($this->application_ini_path, $data);
				} // if (file_exists($this->application_ini_path)!==TRUE)
				//
				// Does the ini file exist now?
				if (file_exists($this->application_ini_path)!==TRUE)
				{
					// No, ini file is missing
					throw new phocus_fault('Missing ini file', $this->application_ini_path);
				}
				else
				{
					// Yes, exists
					$this->ini=@parse_ini_file($this->application_ini_path, FALSE, INI_SCANNER_TYPED);
					//
					// Did it parse?
					if ($this->ini===FALSE)
					{
						// No, did not parse
						throw new phocus_fault('Could not parse ini', $this->application_ini_path);
					} // if ($this->ini===FALSE)
				} // if (file_exists($this->application_ini_path)!==TRUE)
				//
				// ALWAYS CHECK THIS AFTER THE INI FILE
				// Does the application class file exist?
				$application_class_path='../classes/'.$this->application_name.'.php';
				if (file_exists($application_class_path)!==TRUE)
				{
					// No, application class file does not exist
					//
					// Drop to the generic application
					$this->application_name='phocus_application';
				} // if (file_exists($application_class_path)!==TRUE)
trace("\$this->application_name={$this->application_name}");
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot launch Foundation System', '', $e);
			} // try
		} // __construct()
		//
		//
		/*
		public function get_application_class_path()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				return $this->application_class_path;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get application_class_path', $e);
			} // try
		} // get_application_class_path()
		*/
		//
		//
		/*
		public function get_database()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				return $this->database;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get database', $e);
			} // try
		} // get_database()
		*/
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
						$ini=func_get_arg(0);
						//
						confirm_string($ini);
						confirm_array_element($ini, $this->ini);
						//
						$result=$this->ini[$ini];
					break; }
					//
					case 2: {
						$ini=func_get_arg(0);
						$optional=func_get_arg(1);
						//
						confirm_string($ini);
						confirm_int($optional);
						//
						if ($optional!==phocus_system::INI_OPTIONAL)
						{
							throw new phocus_fault('Unknown option', $optional);
						} // if ($optional!==phocus_application::INI_OPTIONAL)
						//
						if (array_key_exists($ini, $this->ini)!==TRUE)
						{
							$result=NULL;
						}
						else
						{
							$result=$this->ini[$ini];
						} // if (array_key_exists($ini, $this->ini)!==TRUE)
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
				throw new phocus_fault('Cannot return ini', '', $e);
			} // try
		} // get_ini()
		//
		//
		public function get_application_ini_path()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				return $this->application_ini_path;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get application_ini_path', $e);
			} // try
		} // get_application_ini_path()
		//
		//
		public function render()
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 0);
				//
				//
				////////////////////////////
				// Render the application //
				////////////////////////////
				//
				// Is there a POST command?
				if (array_key_exists('command', $_POST)===TRUE)
				{
					// Yes, has POST command
					$command=$_POST['command'];
				}
				else
				{
					// No, missing POST command
					// Is there a GET command?
					if (array_key_exists('command', $_GET)===TRUE)
					{
						// Yes, has GET command
						$command=$_GET['command'];
					}
					else
					{
						// No, missing GET command
						// Is there a COOKIE command?
						if (array_key_exists('command', $_COOKIE)===TRUE)
						{
							// Yes, has a COOKIE command
							$command=$_COOKIE['command'];
						}
						else
						{
							// No, missing COOKIE command
							// Go with default command
							$command='DISPLAY';
						} // if (array_key_exists('command', $_COOKIE)===TRUE)
					} // if (array_key_exists('command', $_GET)===TRUE)
				} // if (array_key_exists('command', $_POST)===TRUE)
				//
				// Create the application object
				$application=new $this->application_name;
				//
				// Does the command exist?
				if (method_exists($application, $command)!==TRUE)
				{
					// No, command is missing
					throw new phocus_fault('Invalid command', $command);
				}
				else
				{
					// Ywa, command exists
					$response=$application->$command();
				} // if (method_exists($application, $command)!==TRUE)
				//
				//
				return $response;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot render', '', $e);
			} // try
		} // render()
	} // phocus_system
?>