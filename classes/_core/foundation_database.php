<?php
	//
	// database.php
	//
	class foundation_database
	{
		private $connection;
		private $db;
		private $row;
		private $select;
		private $SQL;
		//
		public function __construct($host, $user, $password, $name)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 4);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($host);
				confirm_string($user);
				confirm_string($password);
				confirm_string($name);
				//
				//
				/////////////////////////////
				// Connect to the database //
				/////////////////////////////
				//
				$this->connection=@mysqli_connect($host, $user, $password);
				if ($this->connection===FALSE)
				{
					throw new foundation_fault('Could not connect to database', @mysqli_connect_error().' err:'.@mysqli_connect_errno());
				} // if ($this->connection===FALSE)
				//
				$this->db=@mysqli_select_db($this->connection, $name);
				if ($this->db===FALSE)
				{
					throw new foundation_fault('Could not select database', @mysqli_error());
				} // if ($this->db===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not create database', '', $e);
			} // try
		} // __construct()
		//
		//
		public function close()
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
				$close=@mysqli_close($this->connection);
				//
				if ($close===FALSE)
				{
					throw new foundation_fault('Close failed', mysqli_error($this->connection));
				} // if ($close===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not close database', '', $e);
			} //try
		} // close()
		//
		//
		public function fetch()
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
				/////////////////
				// Fetch a row //
				/////////////////
				//
				$this->row=@mysqli_fetch_assoc($this->select);
				//
				return $this->row;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get row', '', $e);
			} // try
		} // fetch()
		//
		//
		public function row_count()
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
				//////////////////////////
				// Return the row count //
				//////////////////////////
				//
				return @mysqli_num_rows($this->select);
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not count rows', '', $e);
			} // try
		} // row_count()
		//
		//
		public function query($SQL, $tokens)
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
						$SQL=func_get_arg(0);
						$tokens=array();
					break; }
					//
					case 2: {
						$SQL=func_get_arg(0);
						$tokens=func_get_arg(1);
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($SQL);
				confirm_array($tokens);
				//
				//
				foreach ($tokens as $id=>$value)
				{
					if ((is_string($value)===FALSE) && (is_numeric($value)===FALSE))
					{
						throw new foundation_fault("Invalid value type [$id]=>[".gettype($value).']', origin());
					} // ((is_string($value)===FALSE) && (is_numeric($value)===FALSE))
				} // foreach ($tokens as $id=>$value)
				//
				//
				///////////////////////
				// Process the query //
				///////////////////////
				//
				foreach ($tokens as $id=>$value)
				{
					$target="#{$id}#";
					$safe=@mysqli_real_escape_string($this->connection, $value);
					//
					$SQL=str_replace($target, $safe, $SQL);
				} // for ($a=0; $a<$arg_count; $a++)
				//
				$this->SQL=$SQL;
				//
				$this->select=@mysqli_query($this->connection, $this->SQL);
				if ($this->select===FALSE)
				{
					throw new foundation_fault ('Could not do query', $this->SQL);
				} // if ($this->select===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not query database', '', $e);
			} // try
		} // query()
	} // foundation_database
?>