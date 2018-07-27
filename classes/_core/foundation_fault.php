<?php
	//
	// foundation_fault.php
	//
	class foundation_fault extends Exception
	{
		private $data;
		protected $file;
		protected $line;
		protected $message;
		protected $previous;
		//
		//
		/**
		* <h1>constructor</h1>
		* Validates all the data, and if ok, creates a fault object
		*
		* @param $message [string] A human readable message
		* @param $data [string | numeric] Technical data to augment the message.  I.E. Types, values, fault codes
		* @param $previous [object::fault | NULL] Link back to previous fault object to trace fault chain
		*
		* @return	void
		*
		* @exception Throws either an Exception or another fault object
		*/
		public function __construct($message, $data, $previous=NULL)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$argument_count=func_num_args();
				switch ($argument_count)
				{
					case 0:
					case 1: {
						throw new foundation_fault("insufficient arguments [$argument_count]", origin());
					break; }
					//
					case 2: 
					case 3: {
						// Correct number of arguments.  Proceed.
					break; }
					//
					default: {
						throw new foundation_fault("too many arguments [$argument_count]", origin());
					break; }
				} // switch ($argument_count)
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				// Is $previous NULL, or object::error?  (YES, CHECK THIS ONE FIRST AS IT MAY BE USED BELOW)
				if (is_null($previous)===FALSE)
				{
					// Not null
					// Is $previous an object?
					if (is_object($previous)===FALSE)
					{
						// Not an object
						$type=gettype($previous);
						throw new foundation_fault("previous is not an object: [$type]", origin());
					}
					else
					{
						// Yes, an object
						$class=get_class($previous);
						// Is it the right class?
						if (($class!=='foundation_fault') && ($class!=='Exception') && ($class!=='Error'))
						{
							// Not the right class
							throw new foundation_fault("previous is not class foundation_fault, Exception, or Error: [$class]", origin());
						} // if (($class!=='fault') && ($class!=='Exception') && ($class!=='Error'))
					} // if (is_object($previous)===FALSE)
				} // if (is_null($previous)===FALSE)
				//
				// Is $message a string?
				if (is_string($message)===FALSE)
				{
					// Not a string
					$type=gettype($message);
					throw new foundation_fault("message is not a string: [$type]", origin());
				} // if (is_string($message)===FALSE)
				//
				// Is $data a string or numeric?
				if ((is_string($data)===FALSE) && (is_numeric($data)===FALSE))
				{
					// Not a string or numeric
					$type=gettype($data);
					throw new foundation_fault("data is not a string or numeric: [$type]", origin());
				} // if ((is_string($data)===FALSE) && (is_numeric($data)===FALSE))
				//
				// All the data is present and the types are correct
				//
				//
				///////////////////////////////
				// Initialize the properties //
				///////////////////////////////
				//
				// Get the file and line that called this contructor from the backtrace
				//
				$backtrace=debug_backtrace();
				//
				// $backtrace should ALWAYS be a array.  But I've seen some things...
				// I'm going to throw Exception, not fault, to avoid the infinite loop possibility
				if (array_key_exists(0, $backtrace)===FALSE)
				{
					throw new Exception('backtrace incomplete', __LINE__, $previous);
				}
				if (is_array($backtrace[0])===FALSE)
				{
					throw new Exception('backtrace missing element 0', __LINE__, $previous);
				}
				if (array_key_exists('file', $backtrace[0])===FALSE)
				{
					throw new Exception("backtrace missing 'file' element", __LINE__, $previous);
				}
				if (array_key_exists('line', $backtrace[0])===FALSE)
				{
					throw new Exception("backtrace missing 'line' element", __LINE__, $previous);
				}
				$this->file=$backtrace[0]['file'];
				$this->line=$backtrace[0]['line'];
				$this->message=$message;
				$this->data=$data;
				$this->previous=$previous;
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not create fault object', __LINE__, $e);
			} // try
		} // __construct()
		//
		//
		public function as_string()
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
				$files=array();
				$lines=array();
				$messages=array();
				$datas=array();  // Yeah, I know, data is plural
				//
				$file_max=0;
				$line_max=0;
				$message_max=0;
				$data_max=0;
				//
				$link=$this;
				$counter=0;
				//
				while ($link!==NULL)
				{
					if (get_class($link)==='foundation_fault')
					{
						$files[$counter]=$link->get_file();
						$lines[$counter]=$link->get_line();
						$messages[$counter]=$link->get_message();
						$datas[$counter]=$link->get_data();
						$link=$link->get_previous();
					}
					else
					{
						$files[$counter]=$link->getFile();
						$lines[$counter]=$link->getLine();
						$messages[$counter]=$link->getMessage();
						$datas[$counter]='::'.get_class($link).'::';
						$link=$link->getPrevious();
					}
					//
					if (strlen($files[$counter])>$file_max)
					{
						$file_max=strlen($files[$counter]);
					} // if (strlen($files[$counter])>$file_max)
					//
					if (strlen($lines[$counter])>$line_max)
					{
						$line_max=strlen($lines[$counter]);
					} // if (strlen($lines[$counter])>$line_max)
					//
					if (strlen($messages[$counter])>$message_max)
					{
						$message_max=strlen($messages[$counter]);
					} // if (strlen($messages[$counter])>$message_max)
					//
					//
					$counter++;
				} // while ($link!==NULL)
				//
				$result='';
				//
				foreach ($files as $idx=>$file)
				{
					$file_formatted=str_pad($file, $file_max);
					$line_formatted=str_pad($lines[$idx], $line_max);
					$message_formatted=str_pad('"'.$messages[$idx].'"', $message_max+2);
					$data_formatted=$datas[$idx];
					//
					$result="$file_formatted @ $line_formatted $message_formatted [$data_formatted]\n".$result;
				} // foreach ($files as $idx=>$file)
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not display as string', '', $e);
			} // try
		} // as_string()
		//
		//
		public function get_data()
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
				return $this->data;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not return data', __LINE__, $e);
			} // try
		} // get_data()
		//
		//
		public function get_file()
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
				return $this->file;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not return file', __LINE__, $e);
			} // try
		} // get_file()
		//
		//
		public function get_line()
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
				return $this->line;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not return line', __LINE__, $e);
			} // try
		} // get_line()
		//
		//
		public function get_message()
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
				return $this->message;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not return message', __LINE__, $e);
			} // try
		} // get_message()
		//
		//
		public function get_previous()
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
				return $this->previous;
			}
			catch (Throwable $e)
			{
				throw new Exception('Could not return previous', __LINE__, $e);
			} // try
		} // get_previous()
		//
		//
		public function __toString()
		{
			try
			{
				return $this->as_string();
			}
			catch (Throwable $e)
			{
				throw new Exception('Cannot make fault into string', __LINE__, $e);
			} // try
		} // __toString()
	} // foundation_fault
	
?>