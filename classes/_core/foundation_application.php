<?php
	//
	// foundation_application.php
	//
	class foundation_application
	{
		const INI_OPTIONAL = 1;
		//
		private $application_class_name;
		private $database;
		private $ini;
		//
		//
		public function __construct($configuration_file='')
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				switch ($arg_count)
				{
					case 0: {
						$configuration_file='foundation';
					break; }
					//
					case 1: {
						$configuration_file=func_get_args(0);
					break; }
					//
					default: {
						throw new foundation_fault('Invalid argument count', $arg_count);
					break;}
				} // switch ($arg_count)
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($configuration_file);
				//
				//
				//////////////////
				// Sanity Check //
				//////////////////
				//
				confirm_path_safe($configuration_file);
				//
				//
				///////////////////////////////
				// Construct the application //
				///////////////////////////////
				//
				$this->database=FALSE;
				//
				$target='../'.$configuration_file.'.ini';
				//
				// Does the ini file exist?
				if (file_exists($target)!==TRUE)
				{
					// No, ini file is missing
					throw new foundation_fault('Missing ini file', $configuration_file);
				}
				else
				{
					// Yes, exists
					$this->ini=@parse_ini_file($target, FALSE, INI_SCANNER_TYPED);
					//
					// Did it parse?
					if ($this->ini===FALSE)
					{
						// No, did not parse
						throw new foundation_fault('Could not parse ini', $configuration_file);
					}
					else
					{
						// Yes, parsed
						$this->application_class_name=$this->get_ini('application_class_name', foundation_application::INI_OPTIONAL);
						//
						// Does the application class name exist?
						if ($this->application_class_name===FALSE)
						{
							// No, missing
							$this->application_class_name='FOUNDATION';
						} // if ($this->application_class_name===FALSE)
					} // if ($this->ini===FALSE)
				} // if (file_exists($target)!==TRUE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot create new Foundation application', '', $e);
			} // try
		} // __construct()
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
						if ($optional!==foundation_application::INI_OPTIONAL)
						{
							throw new foundation_fault('Unknown option', $optional);
						} // if ($optional!==foundation_application::INI_OPTIONAL)
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
						throw new foundation_fault('Invalid argument count', $arg_count);
					break; }
				} // switch ($arg_count)
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot return ini', '', $e);
			} // try
		} // get_ini()
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
						// Nom missing GET command
						// Go with default command
						$command='DISPLAY';
					} // if (array_key_exists('command', $_GET)===TRUE)
				} // if (array_key_exists('command', $_POST)===TRUE)
				//
				// Create the application object
				$app=new $this->application_class_name;
				//
				// Does the command exist?
				if (method_exists($app, $command)!==TRUE)
				{
					// No, command is missing
					throw new foundation_fault("Invalid command [$command]", '');
				}
				else
				{
					// Ywa, command exists
					$response=$app->$command();
				} // if (method_exists($app, $command)!==TRUE)
				//
				//
				return $response;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot render', '', $e);
			} // try
		} // render()
	} // foundation_application
?>