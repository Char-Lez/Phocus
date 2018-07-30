<?php
	// index.php
	//
	// FUNCTIONS
	{
		/**
		* <h1>Class Autoloader</h1>
		* This loads class files as needed
		*
		* @param	class_name as string
		* @return	void
		*/
		function class_autoloader($class_name)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$argument_count=func_num_args();
				confirm_args($argument_count, 1);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($class_name);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				confirm_path_safe($class_name);
				//
				//
				/////////////////////////
				// Load the class file //
				/////////////////////////
				//
				switch ($class_name)
				{
					case 'foundation_application': 
					case 'foundation_database': 
					case 'foundation_ini': 
					case 'foundation_fault': 
					case 'foundation_template': 
					case 'foundation_system': {
						$sub='_core/';
					break; }
					//
					case 'PHPMailer':
					case 'SMTP': {
						$sub='_library/';
					break; }
					//
					default: {
						$sub='';
					break; }
				} // switch ($class_name)
				//
				$target_file='../classes/'.$sub.$class_name.'.php';
				$include=@include($target_file);
				if ($include===FALSE)
				{
					throw new Exception("Class file did not load: [$class_name]", __LINE__, NULL);
				} // if ($include===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new Exception("Could not load class file: [$class_name]", __LINE__, $e);
			} // try
		} // class_autoloader()
		//
		//
		/**
		* <h1>Applcation Class</h1>
		* Determines the proper application class name
		* Helps deal with missing classes which is likely the
		* case in new installations
		*
		* @param application_name [string]
		* @return string
		*/
		function application_class($application_name)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_string($application_name)!==TRUE)
				{
					throw new foundation_fault('Parameter is not string.  It is ['.gettype($application_name).']', origin());
				} // if (is_string($application_name)!==TRUE)
				//
				//
				$application_class_path='../classes/'.$application_name.'.php';
				if (file_exists($application_class_path)!==TRUE)
				{
					// No, application class file does not exist
					//
					// Drop to the generic application
					$application_class_name='foundation_application';
				}
				else
				{
					// Yes, exists
					$application_class_name=$application_name;
				} // if (file_exists($application_class_path)!==TRUE)
				//
				return $application_class_name;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot get application class', '', $e);
			} // try
		} // application_class()
		//
		//
		/**
		* <h1>Application name</h1>
		* Determines the application name based on the __FILE__
		*
		* @return string
		*/
		function application_name()
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==0)
				//
				//
				$pieces=explode('/', __FILE__);
				//
				$last=end($pieces);
				//
				$parts=explode('.', $last);
				//
				$name=array_shift($parts);
				//
				return $name;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get application name', '', $e);
			} // try
		} // application_name()
		//
		//
		/**
		* <h1>fetch a row from from the database</h1>
		* Will attempt to return a row from the database as an associative array
		*/
		function fetch()
		{
			global $database;
			//
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==0)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==0)
				//
				//
				/////////////////
				// Fetch a row //
				/////////////////
				//
				// Is there a database connection?
				if ($database===FALSE)
				{
					// No database connection
					throw new foundation_fault('No database connection', origin());
				} // if ($database===FALSE)
				//
				return $database->fetch();
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get row', '', $e);
			} // try
		} // fetch()
		//
		//
		/**
		* <h1>generic_HTML</h1>
		* Returns a generic HTML document for when all else has failed
		*
		* @param	$debug_info [string] = Debugging data to show, file names, line numbers, error messages, etc.
		*/
		function generic_HTML($debug_info='')
		{
			try
			{
				// Because this is called in the main error catch we're looser with
				// parameter checking
				//
				/////////////////////////////
				// Confirm parameter types //
				/////////////////////////////
				//
				if (is_string($debug_info)!==TRUE)
				{
					$debug_info='';
				}
				//
				//
				$result='<!--generic_HTML()-->
				<!DOCTYPE html>
				<html lang="en">
					<head>
						<meta charset="utf-8">
						<meta http-equiv="X-UA-Compatible" content="IE=edge">
						<meta name="viewport" content="width=device-width, initial-scale=1">
						<meta name="security" content="D417680A40DB39454E947F78337AA549">
						<title>Foundation - Technical Problem</title>
					</head>
					<body>
						<br>
						<center>
							<img src="./images/_core/logo.png" style="height:200px;" alt="Foundation Logo">
							<br>
							<br>
							<br>
							This Foundation application is experiencing a technical problem.
							<br>
							<br>
							Please try back soon.
						</center>
						<pre>
#DEBUG_INFO#
						</pre>
					</body>
				</html>
				<!--/generic_HTML()-->';
				//
				$result=str_replace('#DEBUG_INFO#', htmlspecialchars($debug_info), $result);
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not show page', '', $e);
			} // try
		} // generic_HTML()
		//
		//
		/**
		* <h1>Extract a line from a backtrace</h1>
		* Returns a formatted origin line from a backtrace
		*
		* @param $backtrace [array] = The backtrace array from PHP
		* @param line [int] = The line to extract, starting at 0
		*/
		function backtrace_origin($backtrace, $line)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==2)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==2)
				//
				//
				/////////////////////////////
				// Confirm parameter types //
				/////////////////////////////
				//
				if (is_array($backtrace)!==TRUE)
				{
					throw new foundation_fault('$backtrace is ['.gettype($backtrace).'] not array', origin());
				}
				//
				if (is_int($line)!==TRUE)
				{
					throw new foundation_fault('$line is ['.gettype($line).'] not int', origin());
				}
				//
				//
				/////////////////////////////////////////
				// Extract the line from the backtrace //
				/////////////////////////////////////////
				//
				if (array_key_exists($line, $backtrace)===FALSE)
				{
					throw new foundation_fault('Line not found', $line);
				}
				//
				if (is_array($backtrace[$line])===FALSE)
				{
					throw new foundation_fault('backtrace missing element '.$line, '');
				}
				//
				if (array_key_exists('file', $backtrace[$line])===FALSE)
				{
					throw new foundation_fault("backtrace missing 'file' element", '');
				}
				//
				if (array_key_exists('line', $backtrace[$line])===FALSE)
				{
					throw new foundation_fault("backtrace missing 'line' element", '');
				}
				//
				$origin=$backtrace[$line]['file'].' @ '.$backtrace[$line]['line'];
				//
				return $origin;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot get backtrace origin', '', $e);
			} // try
		} // backtrace_origin()
		//
		//
		/**
		* <h1>Confirm an element exists in $_GET</h1>
		*
		* @param $target [string]
		*/
		function confirm_get_element($target)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				if (is_string($target)!==TRUE)
				{
					throw new foundation_fault('Invalid target type ['.gettype($target).']', origin());
				}
				//
				//
				/////////////////////////////////////////////
				// Determine if get has the target element //
				/////////////////////////////////////////////
				// 
				if (array_key_exists($target, $_GET)!==TRUE)
				{
					throw new foundation_fault("\$_GET is missing [$target]", origin());
				} // if (array_key_exists($target, $_GET)!==TRUE)
				//
				return $_GET[$target];
			}
			catch (Throwable $e)
			{
				throw new fault ('GET does not have required data', '', $e);
			} // try
		} // confirm_get_element()
		//
		//
		/**
		* <h1>Confirm number of arguments</h1>
		* Use this to confirm that a function has the exact right number of arguments
		*
		* @param $total [int] = The number of arguments present
		* @param $target [int] = $ht number of arguments needed
		*/
		function confirm_args($arg_count, $target)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count2=func_num_args();
				if ($arg_count2!==2)
				{
					throw new foundation_fault("Invalid args [$arg_count2]", origin());
				} // if ($arg_count2!==2)
				//
				confirm_int($arg_count);
				confirm_int($target);
				//
				//
				//////////////////////
				// Confirm the args //
				//////////////////////
				//
				if ($arg_count!==$target)
				{
					throw new foundation_fault("Invalid arg count: $arg_count", origin());
				} // if ($arg_count!==$target)
				//
				return TRUE;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm arg count', '', $e);
			} // try
		} // confirm_args()
		//
		//
		/**
		* <h1>Confirm Array Element exists</h1>
		* Use this to prove an array has a given key
		*
		* @param $needle [string] = The key you're looking for
		* @param haystack [string] = The array to search
		*/
		function confirm_array_element($needle, $haystack)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==2)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				if ((is_string($needle)!==TRUE) && (is_int($needle)!==TRUE))
				{
					throw new foundation_fault('$needle is not string or int ['.gettype($needle).']', origin());
				}
				//
				if (is_array($haystack)!==TRUE)
				{
					throw new foundation_fault('$haystack is not array ['.gettype($haystack).']', origin());
				}
				//
				//
				///////////////////////////////////////////////
				// Determine if array has the target element //
				///////////////////////////////////////////////
				// 
				if (array_key_exists($needle, $haystack)!==TRUE)
				{
					throw new foundation_fault("array is missing [$needle]", origin());
				} // if (array_key_exists($needle, $haystack)!==TRUE)
				//
				return TRUE;
			}
			catch (Throwable $e)
			{
				throw new fault ('Array does not have required element', '', $e);
			} // try
		} // confirm_array_element()
		//
		//
		/**
		* <h1>Confirm $_POST Element exists</h1>
		* Use this to prove $_POST has a given element
		*
		* @param $target [string] = The key to look for
		*/
		function confirm_post_element($target)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				if (is_string($target)!==TRUE)
				{
					throw new foundation_fault('$target is not string ['.gettype($target).']', origin());
				}
				//
				//
				////////////////////////////////////////////////
				// Determine if $_POST has the target element //
				////////////////////////////////////////////////
				// 
				if (array_key_exists($target, $_POST)!==TRUE)
				{
					throw new foundation_fault("\$_POST is missing [$target]", origin());
				} // if (array_key_exists($target, $_POST)!==TRUE)
				//
				return $_POST[$target];
			}
			catch (Throwable $e)
			{
				throw new fault ('POST does not have required data', '', $e);
			} // try
		} // confirm_post_element()
		//
		//
		/**
		* <h1>Confirm Array</h1>
		* Use this to confirm that the given variable is an array
		*
		* @param $test_me [array]
		*/
		function confirm_array($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_array($test_me)!==TRUE)
				{
					throw new foundation_fault('Parameter is not array.  It is ['.gettype($test_me).']', origin());
				} // if (is_array($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm array', '', $e);
			} // try
		} // confirm_array()
		//
		//
		/**
		* <h1>Confirm Boolean</h1>
		* Use this to confirm that the given variable is a boolean
		*
		* @param $test_me [boolean]
		*/
		function confirm_boolean($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_bool($test_me)!==TRUE)
				{
					throw new foundation_fault('Parameter is not boolean.  It is ['.gettype($test_me).']', origin());
				} // if (is_bool($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm boolean', '', $e);
			} // try
		} // confirm_boolean()
		//
		//
		/**
		* <h1>Confirm Double</h1>
		* Use this to confirm that the given variable is a double
		*
		* @param $test_me [double]
		*/
		function confirm_double($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_double($test_me)!==TRUE)
				{
					throw new foundation_fault('Parameter is not double.  It is ['.gettype($test_me).']', origin());
				} // if (is_double($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm double', '', $e);
			} // try
		} // confirm_double()
		//
		//
		/**
		* <h1>Confirm Integer</h1>
		* Use this to confirm that the given variable is an integer
		*
		* @param $test_me [integer]
		*/
		function confirm_int($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_int($test_me)!==TRUE)
				{
					throw new foundation_fault('Parameter is not integer.  It is ['.gettype($test_me).']', origin());
				} // if (is_int($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm integer', '', $e);
			} // try
		} // confirm_string()
		//
		//
		/**
		* <h1>Confirm Mixed</h1>
		* Use this to confirm a variable is one of a given list of types
		*
		* FORM 1:
		*  @param $test_me [mixed]
		*  @param $valid [string] (case insensitive)
		*
		* FORM 2:
		*  @param $test_me [mixed]
		*  @param $valid [string] (case insensitive)
		*  @param $name [string] = Name of class or type of resource
		*
		* The allowed values for valid are:
		*  A: Array
		*  B: Boolean
		*  D: Double
		*  I: Integer
		*  N: Null
		*  O: Object
		*  R: Resource
		*  S: String
		*
		* $valid will take a form like 'ABO' or 'SR' or any combination of the allowed values, in any order
		*
		* NOTE: You can put in multiple instances of the same value for valid and it will only slow things down
		*/
		function confirm_mixed()
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
					case 2: {
						$test_me=func_get_arg(0);
						$valid=func_get_arg(1);
						$name='';
					break; }
					//
					case 3: {
						$test_me=func_get_arg(0);
						$valid=func_get_arg(1);
						$name=func_get_arg(2);
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				if (is_string($valid)!==TRUE)
				{
					throw new foundation_fault('$valid is not null.  It is ['.gettype($valid).']', origin());
				} // if (is_string($valid)!==TRUE)
				//
				if (is_string($name)!==TRUE)
				{
					throw new foundation_fault('$name is not null.  It is ['.gettype($name).']', origin());
				} // if (is_string($name)!==TRUE)
				//
				//
				$valid=strtoupper($valid);
				//
				$check=str_split($valid);
				foreach($check as $type)
				{
					if (strpos('ABDINORS', $type)===FALSE)
					{
						throw new foundation_fault("\$haystack has invalid type.  [$type]", origin());
					} // switch ($type)
				} // foreach($check as $type)
				//
				$found=FALSE;
				foreach($check as $type)
				{
					if ($found===FALSE)
					{
						switch ($type)
						{
							case 'A': {
								if (is_array($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							case 'B': {
								if (is_bool($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							case 'D': {
								if (is_double($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							case 'I': {
								if (is_int($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							case 'N': {
								if (is_null($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							case 'O': {
								if (is_object($test_me)===TRUE)
								{
									if ($name!=='')
									{
										$class=get_class($test_me);
										if ($class!==$name)
										{
											throw new foundation_fault("\$test_me is invalid class. expected [$class].  Found [$name]", origin());
										}
										else
										{
											return $test_me;
										} // if ($class!==$name)
									}
									else
									{
										return $test_me;
									} // if ($name!=='')
								}
							break; }
							//
							case 'R': {
								if (is_resource($test_me)===TRUE)
								{
									if ($name!=='')
									{
										$type=get_resource_type($test_me);
										if ($type!==$name)
										{
											throw new foundation_fault("\$test_me is invalid class. expected [$class].  Found [$type]", origin());
										}
										else
										{
											return $test_me;
										} // if ($type!==$name)
									}
									else
									{
										return $test_me;
									} // if ($name!=='')
								}
							break; }
							//
							case 'S': {
								if (is_string($test_me)===TRUE)
								{
									return $test_me;
								}
							break; }
							//
							default: {
								throw new foundation_fault("\$test_me has invalid type. expected [$valid].  Found [".gettype($test_me).']', origin());
							break; }
						} // switch ($type)
					} // if ($found===FALSE)
				} // foreach($check as $type)
				//
				throw new foundation_fault("\$test_me not [$valid].  Found [".gettype($test_me).']', origin());
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm mixed', '', $e);
			} // try
		} // confirm_mixed()
		//
		//
		/**
		* <h1>Confirm Null</h1>
		* Use this to confirm that the given variable is NULL
		*
		* @param $test_me [NULL]
		*/
		function confirm_null($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_null($test_me)!==TRUE)
				{
					throw new foundation_fault('Not null.  It is ['.gettype($test_me).']', origin());
				} // if (is_null($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm null', '', $e);
			} // try
		} // confirm_null()
		//
		//
		/**
		* <h1>Confirm Object</h1>
		* Use this to confirm that the given variable is an object
		*
		* FORM 1:
		*  @param $test_me [object]
		*
		* FORM 2:
		*  @param $test_me [object]
		*  @param $name [string] = Name of class to confirm
		*/
		function confirm_object()
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
					case 1: {
						$test_me=func_get_arg(0);
						$name='';
					break; }
					//
					case 2: {
						$test_me=func_get_arg(0);
						$name=func_get_arg(1);
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				if (is_string($name)!==TRUE)
				{
					throw new foundation_fault('$name is not a string.  It is ['.gettype($name).']', origin());
				} // if (is_string($name)!==TRUE)
				//
				//
				if (is_object($test_me)!==TRUE)
				{
					throw new foundation_fault('Parameter is not object.  It is ['.gettype($test_me).']', origin());
				}
				else
				{
					if ($name!=='')
					{
						$class=get_class($test_me);
						if ($class!==$name)
						{
							throw new foundation_fault("Parameter is incorrect class.  It is [$class]", origin());
						} // if ($class!==$name)
					} // if ($name!=='')
				} // if (is_object($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm object', '', $e);
			} // try
		} // confirm_object()
		//
		//
		/**
		* <h1>Confirm Resource</h1>
		* Use this to confirm that the given variable is a resource
		*
		* FORM 1:
		*  @param $test_me [resource]
		*
		* FORM 2:
		*  @param $test_me [resource]
		*  @param $name [string] = Type of resource to confirm
		*/
		function confirm_resource()
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
					case 1: {
						$test_me=func_get_arg(0);
						$name='';
					break; }
					//
					case 2: {
						$test_me=func_get_arg(0);
						$name=func_get_arg(1);
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				if (is_string($name)!==TRUE)
				{
					throw new foundation_fault('$name is not a string.  It is ['.gettype($name).']', origin());
				} // if (is_string($name)!==TRUE)
				//
				//
				if (is_resource($test_me)!==TRUE)
				{
					throw new foundation_fault('Not a resource.  It is ['.gettype($test_me).']', origin());
				}
				else
				{
					if ($name!=='')
					{
						$type=get_resource_type($test_me);
						if ($type!==$name)
						{
							throw new foundation_fault("Incorrect resource type.  expected [$name].  It is [$type]", origin());
						} // if ($type!==$name)
					} // if ($name!=='')
				} // if (is_resource($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm resource', '', $e);
			} // try
		} // confirm_resource()
		//
		//
		/**
		* <h1>Confirm Path Safe</h1>
		* Use this to confirm that the given file name does not contain any dangerous
		* file system characters
		*
		* @param $test_me [string]
		*/
		function confirm_path_safe($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_string($test_me)!==TRUE)
				{
					$backtrace=debug_backtrace();			
					//
					$origin=backtrace_origin($backtrace, 1);
					//
					throw new foundation_fault('Parameter is not string.  It is ['.gettype($test_me).']', $origin);
				} // if (is_string($test_me)!==TRUE)
				//
				if (strpos($test_me, '..')!==FALSE)
				{
					throw new foundation_fault('Invalid path', $test_me);
				} // if (strpos($test_me, '..')!==FALSE)
				//
				if (strpos($test_me, '/')!==FALSE)
				{
					throw new foundation_fault('Invalid path', $test_me);
				} // if (strpos($test_me, '/')!==FALSE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm path safe', '', $e);
			} // try
		} // confirm_path_safe()
		//
		//
		/**
		* <h1>Confirm String</h1>
		* Use this to confirm that the given variable is a string
		*
		* @param $test_me [string]
		*/
		function confirm_string($test_me)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				//
				if (is_string($test_me)!==TRUE)
				{
					$backtrace=debug_backtrace();			
					//
					$origin=backtrace_origin($backtrace, 1);
					//
					throw new foundation_fault('Parameter is not string.  It is ['.gettype($test_me).']', $origin);
				} // if (is_string($test_me)!==TRUE)
				//
				return $test_me;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not confirm string', '', $e);
			} // try
		} // confirm_string()
		//
		//
		/**
		* <h1>Do Base 64 Decode</h1>
		* Decodes a string in base64 and deals with errors
		* 
		* @param encoded [string]
		* @return string
		*/
		function do_base64_decode($encoded)
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				if (is_string($encoded)!==TRUE)
				{
					throw new foundation_fault('$encoded is not string.  It is ['.gettype($encoded).']', origin());
				} // if (is_string($test_me)!==TRUE)
				//
				//
				///////////////////
				// Do the decode //
				///////////////////
				//
				$decoded=@base64_decode($encoded);
				//
				if ($decoded===FALSE)
				{
					throw new foundation_fault('Base64 decode failed', $encoded);
				}
				//
				return $decoded;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot base64 decode', '', $e);
			} // try
		} // do_base64_decode()
		//
		//
		/**
		*
		*	Returns TRUE if success, or string with error message if fail
		*
		*/
		function my_mail($to, $to_name, $subject, $body)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==4)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==4)
				//
				if (is_string($to)!==TRUE)
				{
					throw new foundation_fault('to is not string ['.gettype($to).']', origin());
				}
				//
				if (is_string($to_name)!==TRUE)
				{
					throw new foundation_fault('to_name is not string ['.gettype($to_name).']', origin());
				}
				//
				if (is_string($subject)!==TRUE)
				{
					throw new foundation_fault('subject is not string ['.gettype($subject).']', origin());
				}
				//
				if (is_string($body)!==TRUE)
				{
					throw new foundation_fault('body is not string ['.gettype($body).']', origin());
				}
				//
				//
				///////////////
				// SEND MAIL //
				///////////////
				//
				$debug=get_ini('SMTP_debug');
				$host=get_ini('SMTP_host');
				$username=get_ini('SMTP_user');
				$password=get_ini('SMTP_password');
				$from_address=get_ini('SMTP_from_address');
				$from_name=get_ini('SMTP_from_name');
				//
				$mail=new PHPMailer(true);
				$mail->SMTPDebug=$debug;
				//
				//
				//$mail->IsSMTP(); // set mailer to use SMTP
				//$mail->Host="secure238.inmotionhosting.com";
				//$mail->SMTPAuth=true; // turn on SMTP authentication
				//$mail->Username="char-lez@davenolansociety.com"; // SMTP username
				//$mail->Password="eagleeagle"; // SMTP password
				//
				$mail->IsSMTP();
				$mail->Host=$host;
				$mail->SMTPAuth=true;
				$mail->Username=$username;
				$mail->Password=$password;
				//
				$mail->From=$from_address;
				$mail->FromName=$from_name;
				$mail->AddAddress($to, $to_name);
				//
				$mail->WordWrap=50; // set word wrap to 50 characters
				$mail->IsHTML(true); // set email format to HTML
				//
				$mail->Subject=$subject;
				$mail->Body=$body;
				$send=$mail->Send();
				//
				if ($send!==TRUE)
				{
					$send=$mail->ErrorInfo;
				}
				//
				return $send;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Error sending email', '', $e);
			} // try
		} // my_mail()
		//
		//
		/**
		* <h1>Origin</h1>
		* Use this to find where a specific call came from
		*
		*/
		function origin()
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
					$backtrace=debug_backtrace();
					//
					if (is_array($backtrace)!==TRUE)
					{
						$backtrace_serialize=serialize(is_array($backtrace));
						throw new foundation_fault('backtrace invalid', $backtrace_serialize);
					}
					//
					if (array_key_exists(0, $backtrace)===FALSE)
					{
						$backtrace_serialize=serialize($backtrace);
						throw new foundation_fault('backtrace incomplete', $backtrace_serialize);
					}
					//
					if (is_array($backtrace[0])===FALSE)
					{
						$backtrace_serialize=serialize($backtrace);
						throw new foundation_fault('backtrace missing element 1', $backtrace_serialize);
					}
					//
					if (array_key_exists('file', $backtrace[0])===FALSE)
					{
						$backtrace_serialize=serialize($backtrace);
						throw new foundation_fault("backtrace missing 'file' element", $backtrace_serialize);
					}
					//
					if (array_key_exists('line', $backtrace[0])===FALSE)
					{
						$backtrace_serialize=serialize($backtrace);
						throw new foundation_fault("backtrace missing 'line' element", $backtrace_serialize);
					}
					//
					$origin=$backtrace[0]['file'].' @ '.$backtrace[0]['line'];
					//
					throw new foundation_fault("Invalid args [$arg_count]", $origin);
				} // if ($arg_count!==0)
				//
				//
				//////////////////////////
				// Determine the origin //
				//////////////////////////
				//
				$backtrace=debug_backtrace();			
				//
				if (is_array($backtrace)!==TRUE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace invalid', $backtrace_serialize);
				}
				//
				if (array_key_exists(1, $backtrace)===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace incomplete', $backtrace_serialize);
				}
				//
				if (is_array($backtrace[1])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace missing element 1', $backtrace_serialize);
				}
				//
				if (array_key_exists('file', $backtrace[1])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault("backtrace missing 'file' element", $backtrace_serialize);
				}
				//
				if (array_key_exists('line', $backtrace[1])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault("backtrace missing 'line' element", $backtrace_serialize);
				}
				//
				//
				$result=$backtrace[1]['file'].' @ '.$backtrace[1]['line'];
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot get origin', '', $e);
			} // try
		} // origin()
		//
		//
		/**
		* <h1>query</h1>
		* This will execute an SQL query against the database
		*
		* FORM 1:
		*  $SQL [string]
		*
		* FORM 2:
		*  $SQL [string]
		*  {List of discrete parameters}
		*
		* FORM 3:
		*  $SQL [string]
		*  $data [array]
		*/
		function query()
		{
			global $database;
			//
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count===0)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count===0)
				//
				$SQL=func_get_arg(0);
				if (is_string($SQL)===FALSE)
				{
					throw new foundation_fault('SQL is not string ['.gettype($SQL).']', origin());
				} // if (is_string($SQL)===FALSE)
				//
				$form=3;
				//
				if ($arg_count===1)
				{
					$form=1;
				} // if ($arg_count===1)
				//
				if ($arg_count>1)
				{
					$args=func_get_arg(1);
					if (is_array($args)===TRUE)
					{
						if ($arg_count!==2)
						{
							throw new foundation_fault("Invalid args for FORM 2 [$arg_count]", origin());
						} // if ($arg_count!==2)
						//
						$form=2;
					} // if (is_array($param)===TRUE)
				} // if ($arg_count>1)
				//
				switch ($form)
				{
					case 1: {
						$args=array();
					break; }
					//
					case 2: {
						// args is already defined
						foreach ($args as $key=>$arg)
						{
							if ((is_string($arg)===FALSE) && (is_numeric($arg)===FALSE))
							{
								throw new foundation_fault("Invalid \$args[$key]=>".gettype($arg), origin());
							} // if ((is_string($args[$a])===FALSE) && (is_numeric($args[$a])===FALSE))
						} // foreach ($args as $key=>$arg)
					break; }
					//
					case 3: {
						$args=array();
						if ($arg_count>1)
						{
							for ($a=1; $a<$arg_count; $a++)
							{
								$args[$a]=func_get_arg($a);
								if ((is_string($args[$a])===FALSE) && (is_numeric($args[$a])===FALSE))
								{
									throw new foundation_fault("Invalid arg type [$a]=>".gettype($args[$a]), origin());
								} // if ((is_string($args[$a])===FALSE) && (is_numeric($args[$a])===FALSE))
							} // for ($a=1; $a<$arg_count; $a++)
						} // if ($arg_count>1)
					break; }
				} // switch ($form)
				//
				//
				///////////////////
				// Execute query //
				///////////////////
				//
				// Is there a database connection?
				if ($database===FALSE)
				{
					// No database connection
					// Connect to the database
					$database=new database(get_ini('database_host'), get_ini('database_user'), get_ini('database_password'), get_ini('database_name'));
				} // if ($database===FALSE)
				//
				$database->query($SQL, $args);
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not do query', '', $e);
			} // try
		} // query()
		//
		//
		/**
		* <h1>query fetch all</h1>
		* This will execute an SQL query against the database and return all the rows as an array
		*
		* FORM 1:
		*  $SQL [string]
		*
		* FORM 2:
		*  $SQL [string]
		*  $unique [string]
		*
		* FORM 3:
		*  $SQL [string]
		*  $unique [string]
		*  $data [array]
		*
		* FORM 4:
		*  $SQL [string]
		*  $unique [string]
		*  {List of discrete parameters}
		*/
		function query_fetch_all_unique()
		{
			global $database;
			//
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count<1)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count<1)
				//
				$SQL=func_get_arg(0);
				if (is_string($SQL)===FALSE)
				{
					throw new foundation_fault('SQL is not string ['.gettype($SQL).']', origin());
				} // if (is_string($SQL)===FALSE)
				//
				switch ($arg_count)
				{
					case 1: {
						$args=array();
						$form=1;
					break; }
					//
					case 2: {
						$unique=func_get_arg(1);
						$form=2;
					break; }
					//
					case 3: {
						$unique=func_get_arg(1);
						//
						$args=func_get_arg(2);
						if (is_array($args)===TRUE)
						{
							$form=3;
						}
						else
						{
							if ((is_string($args)===FALSE) && (is_numeric($args)===FALSE))
							{
								throw new foundation_fault("Invalid arg type=>".gettype($args), origin());
							}
							$value=$args;
							$args=array();
							$args[1]=$value;
							$form=4;
						} // if (is_array($args)===TRUE)
					break; }
					//
					default: {
						$unique=func_get_arg(1);
						$args=array();
						for ($a=2; $a<$arg_count; $a++)
						{
							$b=$a-1;
							$args[$b]=func_get_arg($a);
							if ((is_string($args[$b])===FALSE) && (is_numeric($args[$b])===FALSE))
							{
								throw new foundation_fault("Invalid arg type [$b]=>".gettype($args[$b]), origin());
							} // if ((is_string($args[$b])===FALSE) && (is_numeric($args[$b])===FALSE))
						} // for ($a=2; $a<$arg_count; $a++)
						$form=4;
					break; }
				} // switch ($arg_count)
				//
				if ($form!=1)
				{
					if (is_string($unique)===FALSE)
					{
						throw new foundation_fault('unique is not string ['.gettype($unique).']', origin());
					} // if (is_string($unique)===FALSE)
				} // if ($form!=1)
				//
				//
				///////////////////
				// Execute query //
				///////////////////
				//
				// Is there a database connection?
				if ($database===FALSE)
				{
					// No database connection
					// Connect to the database
					$database=new database(get_ini('database_host'), get_ini('database_user'), get_ini('database_password'), get_ini('database_name'));
				} // if ($database===FALSE)
				//
				$database->query($SQL, $args);
				$data=array();
				$row_count=row_count();
				if ($row_count>0)
				{
					for($a=1; $a<=$row_count; $a++)
					{
						$row=fetch();
						switch ($form)
						{
							case 1: {
								$data[]=$row;
							break; }
							//
							default: {
								$key=$row[$unique];
								unset($row[$unique]);
								$data[$key]=$row;
							break; }
						} // switch ($form)
					} // for($a=1; $a<=$row_count; $a++)
				} // if ($row_count>0)
				//
				return $data;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not do query', '', $e);
			} // try
		} // query_fetch_all_unique()
		//
		//
		/**
		* <h1>file read</h1>
		* Reads a file and handles errors
		*
		* @param $path [string]
		* @return string
		*/
		function file_read($path)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 1);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($path);
				//
				//
				///////////////////
				// Read the file //
				///////////////////
				//
				$data=@file_get_contents($path);
				// Did the file read?
				if ($data===FALSE)
				{
					// No, did not read
					throw new foundation_fault('File did not read', $path);
				} // if ($data===FALSE)
				//
				return $data;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not read file', '', $e);
			} // try
		} // file_read()
		//
		//
		/**
		* <h1>File Save</h1>
		* Saves data to a file, and handles errors
		*
		* @param $path [string]
		* @param $data [string]
		* @return void
		*/
		function file_save($path, $data)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 2);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($path);
				confirm_string($data);
				//
				//
				$saved=@file_put_contents($path, $data);
				// Did the file save?
				if ($saved===FALSE)
				{
					// No, did not save
					throw new foundation_fault('File not saved', $path);
				} // if ($saved===FALSE)
				//
				return TRUE;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not save file', '', $e);
			} // try
		} // file_save()
		//
		//
		/**
		* <h1>Row count</h1>
		* Returns the row count from the last query
		*/
		function row_count()
		{
			global $database;
			//
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count!==0)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==0)
				//
				//
				///////////////////////
				// Get the row count //
				///////////////////////
				//
				// Is there a database connection?
				if ($database===FALSE)
				{
					// No database connection
					throw new foundation_fault('No database connection', origin());
				} // if ($database===FALSE)
				//
				return $database->row_count();
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get row count', '', $e);
			} // try
		} // row_count()
		//
		//
		/**
		* <h1>Trace</h1>
		* Use this to output trace codes and formatted debugging data in your development code
		*
		* $message [string] = data to format
		*/
		function trace($message='')
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				if ($arg_count>1)
				{
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count>1)
				//
				//
				$type=gettype($message);
				switch ($type)
				{
					case 'array': {
						//$message_display="A:[".serialize($message)."]";
						$message_display="Array<br>\n";
						foreach($message as $k=>$v)
						{
							$message_display.="$k=>$v<br>\n";
						} // foreach($message as $k=>$v)
					break; }
					//
					case 'boolean': {
						if ($message===TRUE)
						{
							$value='TRUE';
						}
						else
						{
							$value='FALSE';
						}
						$message_display="B:[$value]";
					break; }
					//
					case 'double': {
						$message_display="D:[$message]";
					break; }
					//
					case 'float': {
						$message_display="F:[$message]";
					break; }
					//
					case 'integer': {
						$message_display="I:[$message]";
					break; }
					//
					case 'null': {
						$message_display="N:[]";
					break; }
					//
					case 'object': {
						try
						{
							$message_display="O:[".serialize($message)."]";
						}
						catch (Throwable $e)
						{
							$class=get_class($message);
							$message_display='?:[CANNOT DISPLAY OBJECT: $class]';
						} // try
					break; }
					//
					case 'resource': {
						try
						{
							$message_display="R:[".serialize($message)."]";
						}
						catch (Throwable $e)
						{
							$message_display='?:[CANNOT DISPLAY RESOURCE]';
						} // try
					break; }
					//
					case 'string': {
						if ($message==='')
						{
							$message_display='';
						}
						else
						{
							$len=strlen($message);
							$message_display="S:$len:[$message]";
						}
					break; }
					//
					default: {
						$message_display="?:[CANNOT DISPLAY MESSAGE ($type)]";
					break;}
				} // switch ($type)
				//
				//
				//
				$backtrace=debug_backtrace();			
				//
				if (is_array($backtrace)!==TRUE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace invalid', $backtrace_serialize);
				}
				//
				if (array_key_exists(0, $backtrace)===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace incomplete', $backtrace_serialize);
				}
				//
				if (is_array($backtrace[0])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault('backtrace missing element 0', $backtrace_serialize);
				}
				//
				if (array_key_exists('file', $backtrace[0])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault("backtrace missing 'file' element", $backtrace_serialize);
				}
				//
				if (array_key_exists('line', $backtrace[0])===FALSE)
				{
					$backtrace_serialize=serialize($backtrace);
					throw new foundation_fault("backtrace missing 'line' element", $backtrace_serialize);
				}
				//
				//
				$origin=$backtrace[0]['file'].' @ '.$backtrace[0]['line'];
				//
				print "$origin [$message_display] <br>\n";
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot trace', '', $e);
			} // try
		} // trace()
	}
	//
	//
	try
	{
		$show_debug=TRUE;
		$database=FALSE;
		//
		spl_autoload_register('class_autoloader');
		//
		$application_name=application_name();
		$ini=new foundation_ini($application_name);
		$show_debug=$ini->get_ini('show_debug', foundation_ini::INI_OPTIONAL);
$show_debug=TRUE;
		$application_class=application_class($application_name);
		$application=new $application_class($ini);
		$response=$application->render(foundation_application::STRICT);
	}
	catch (Throwable $e)
	{
		// Are we showing debug info?
		if ($show_debug===TRUE)
		{
			// Yes, show tghe debug info
			$debug_info=date('Y/m/d H:i:s', time())."\n";
			$debug_info.=$e->__toString();
		}
		else
		{
			// No, squelch the debug info
			$debug_info='';
		} // if ($show_debug===TRUE)
		//
		$response=generic_HTML($debug_info);
	} // try
	//
	print $response;
?>