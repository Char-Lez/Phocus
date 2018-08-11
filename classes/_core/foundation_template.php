<?php
	//
	// foundation_template.php
	//
	class foundation_template
	{
		const APPLICATION='application';
		const CORE='core';
		//
		private $content;
		private $file_path;
		private $snippet;
		private $token_value;
		//
		public function __construct($file_name, $core=foundation_template::APPLICATION)
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
						$file_name=func_get_arg(0);
						$set=foundation_template::APPLICATION;
						$sub=application_name().'/';
					break; }
					//
					case 2: {
						$file_name=func_get_arg(0);
						$set=func_get_arg(1);
						$sub='_core/';
					break; }
					//
					default: {
						throw new foundation_fault('Invalid argument count', $arg_count);
					break; }
				} // switch ($arg_count)
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($file_name);
				confirm_string($set);
				//
				//
				//////////////////
				// Sanity Check //
				//////////////////
				//
				confirm_path_safe($file_name);
				if (($set!==foundation_template::APPLICATION) && ($set!==foundation_template::CORE))
				{
					throw new foundation_fault('Unknown set', $set);
				}
				//
				//
				///////////////////////
				// Load the template //
				///////////////////////
				//
				$this->file_path='../templates/'.$sub.$file_name;
				$this->content=file_read($this->file_path);
				//
				//
				///////////////////////////
				// Initialize properties //
				///////////////////////////
				//
				$this->token_value=array();
				$this->snippet=array();
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not make template', origin(), $e);
			} // try
		} // __construct()
		//
		//
		public function add_snippet($token, $snippet)
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
				confirm_string($token);
				confirm_object($snippet, 'foundation_template');
				//
				//
				/////////////////////
				// Add the snippet //
				/////////////////////
				//
				$this->snippet[$token]=$snippet->render();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not add snippet', origin(), $e);
			} // try
		} // add_snippet()
		//
		//
		public function append_snippet($token, $snippet)
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
				confirm_string($token);
				confirm_object($snippet, 'foundation_template');
				//
				//
				/////////////////////
				// Add the snippet //
				/////////////////////
				//
				$this->snippet[$token].=$snippet->render();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not append snippet', origin(), $e);
			} // try
		} // append_snippet()
		//
		//
		public function add_token($token, $value)
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
				confirm_string($token);
				//
				if ((is_numeric($value)===FALSE) && (is_string($value)===FALSE) && ($value!==NULL))
				{
					throw new foundation_fault('value is not a valid type ['.gettype($value).']', origin());
				} // if ((is_numeric($value)===FALSE) && (is_string($value)===FALSE))
				//
				//
				///////////////////
				// Add the token //
				///////////////////
				//
				if ($value===NULL)
				{
					$value='';
				}
				//
				$this->token_value[$token]=$value;
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not add token', origin(), $e);
			} // try
		} // add_token()
		//
		//
		public function add_token_array($token_values)
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
				confirm_array($token_values);
				//
				foreach ($token_values as $token=>$value)
				{
					if ((is_numeric($value)===FALSE) && (is_string($value)===FALSE) && ($value!==NULL))
					{
						throw new foundation_fault('value is not a valid type ['.gettype($value).']', origin());
					} // if ((is_numeric($value)===FALSE) && (is_string($value)===FALSE))
					//
					if ($value===NULL)
					{
						$value='';
					}
					//
					$this->token_value[$token]=$value;
				} // foreach ($token_values as $token=>$value)
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not add token', origin(), $e);
			} // try
		} // add_token_array()
		//
		//
		public function clear()
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
				////////////////////
				// Clear snippets //
				////////////////////
				//
				$this->clear_snippets();
				$this->clear_tokens();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not clear', origin(), $e);
			} // try
		} // clear ()
		//
		//
		public function clear_snippets()
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
				////////////////////
				// Clear snippets //
				////////////////////
				//
				$this->snippet=array();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not clear snippets', origin(), $e);
			} // try
		} // clear_snippets()
		//
		//
		public function clear_tokens()
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
				//////////////////
				// Clear tokens //
				//////////////////
				//
				$this->token_value=array();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not clear tokens', origin(), $e);
			} // try
		} // clear_tokens()
		//
		//
		public function get_content()
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
				//////////////////
				// Clear tokens //
				//////////////////
				//
				return $this->content;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get $content', origin(), $e);
			} // try
		} // get_content()
		//
		//
		public function get_file_path()
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
				//////////////////
				// Clear tokens //
				//////////////////
				//
				return $this->file_path;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get $file_path', origin(), $e);
			} // try
		} // get_file_path()
		//
		//
		public function get_snippet()
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
				//////////////////
				// Clear tokens //
				//////////////////
				//
				return $this->snippet;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get $snippet', origin(), $e);
			} // try
		} // get_snippet()
		//
		//
		public function get_token_value()
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
				//////////////////
				// Clear tokens //
				//////////////////
				//
				return $this->token_value;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not get $token_value', origin(), $e);
			} // try
		} // get_token_value()
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
				/////////////////////////
				// Render the template //
				/////////////////////////
				//
				$pre_rendition=$this->render_substitutions($this->content);
				//
				// First, escape ##'s
				// Find a viable escape string
				$escape=chr(31); // ASCII Group Separator
				while (strpos($pre_rendition, $escape)!==FALSE)
				{
					$escape.=chr(31);
				} // while (strpos($pre_rendition, $escape)!==FALSE)
				//
				// Use the escape string
				$escaped_content=str_replace('##', $escape, $this->content);
				//
				$escaped_rendition=$this->render_substitutions($escaped_content);
				//
				$rendition=str_replace($escape, '##', $escaped_rendition);
				//
				return $rendition;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not render template', origin(), $e);
			} // try
		} // render()
		//
		//
		private function render_substitutions($rendition)
		{
			try
			{
				foreach ($this->snippet as $token=>$HTML)
				{
					$rendition=str_replace("#{$token}#", $HTML, $rendition);
				} // foreach ($token_value as $token=>$value)
				//
				foreach ($this->token_value as $token=>$value)
				{
					$rendition=str_replace("#{$token}#", htmlspecialchars($value), $rendition);
				} // foreach ($token_value as $token=>$value)
				//
				return $rendition;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not do substitutions', origin(), $e);
			} // try
		} // render_substitutions()
	} // template
?>