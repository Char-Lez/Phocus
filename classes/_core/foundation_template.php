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
		private $token_value;
		private $snippet;
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
						$sub='';
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
				$target='../templates/'.$sub.$file_name;
				$this->content=@file_get_contents($target);
				if ($this->content===FALSE)
				{
					throw new foundation_fault('Could not load template', $target);
				} // if ($this->content===FALSE)
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
				throw new foundation_fault('Could not make template', '', $e);
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
				throw new foundation_fault('Could not add snippet', '', $e);
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
				throw new foundation_fault('Could not append snippet', '', $e);
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
				throw new foundation_fault('Could not add token', '', $e);
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
				throw new foundation_fault('Could not add token', '', $e);
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
				throw new foundation_fault('Could not clear', '', $e);
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
				throw new foundation_fault('Could not clear snippets', '', $e);
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
				throw new foundation_fault('Could not clear tokens', '', $e);
			} // try
		} // clear_tokens()
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
				$rendition=$this->content;
				//
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
				// All done
				//
				return $rendition;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not render template', '', $e);
			} // try
		} // render()
	} // template
?>