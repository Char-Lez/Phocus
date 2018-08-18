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
		private $strict;
		private $token_value;
		//
		public function __construct($file_name, $core=foundation_template::APPLICATION)
		{
			try
			{
				global $ini;
				//
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
				$this->strict=$ini->get_ini('strict');
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
		public function add_token_radio($token_list, $value, $prefix)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 3);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_array($token_list);
				confirm_string($value);
				confirm_string($prefix);
				confirm_string($default);
				//
				//
				////////////////////
				// Add the tokens //
				////////////////////
				//
				$used=FALSE;
				$token_prefix=strtoupper($prefix).'_';
				foreach ($token_list as $token=>$v)
				{
					$token=$token_prefix.strtoupper($v);
					if ($value===$v)
					{
						$this->token_value[$token]='CHECKED';
						$used=TRUE;
					}
					else
					{
						$this->token_value[$token]='';
					} // if ($value===$v)
				} // foreach ($token_list as $token=>$v)
				//
				if ($used===FALSE)
				{
					$token=$token_prefix.strtoupper($defult);
					$this->token_value[$token]='SELECTED';
				} // if ($used===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not add tokens', origin(), $e);
			} // try
		} // add_token_radio()
		//
		//
		public function add_token_select($token_list, $value, $prefix, $default)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 3);
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_array($token_list);
				confirm_string($value);
				confirm_string($prefix);
				confirm_string($default);
				//
				//
				////////////////////
				// Add the tokens //
				////////////////////
				//
				$used=FALSE;
				$token_prefix=strtoupper($prefix).'_';
				foreach ($token_list as $token=>$v)
				{
					$token=$token_prefix.strtoupper($v);
					if ($value===$v)
					{
						$this->token_value[$token]='SELECTED';
						$used=TRUE;
					}
					else
					{
						$this->token_value[$token]='';
					} // if ($value===$v)
				} // foreach ($token_list as $token=>$v)
				//
				if ($used===FALSE)
				{
					$token=$token_prefix.strtoupper($defult);
					$this->token_value[$token]='SELECTED';
				} // if ($used===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not add tokens', origin(), $e);
			} // try
		} // add_token_select()
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
				$rendition=$this->content;
				//
				if ($this->strict===TRUE)
				{
					// strict mode
					//
					$unused=array();
					foreach ($this->snippet as $token=>$HTML)
					{
						$rendition2=str_replace("#{$token}#", $HTML, $rendition);
						//
						if ($rendition2==$rendition)
						{
							$unused[]='SNIPPET:'.$token;
						} // if ($rendition2==$rendition)
						//
						$rendition=$rendition2;
					} // foreach ($token_value as $token=>$value)
					//
					foreach ($this->token_value as $token=>$value)
					{
						$rendition2=str_replace("#{$token}#", htmlspecialchars($value), $rendition);
						//
						if ($rendition2==$rendition)
						{
							$unused[]='TOKEN:'.$token;
						} // if ($rendition2==$rendition)
						//
						$rendition=$rendition2;
					} // foreach ($token_value as $token=>$value)
					//
					// Any unused tokens?
					if (count($unused)!==0)
					{
						// Yes, unused tokens
						$dedupe=array_unique($unused);
						$display=implode(', ', $dedupe);
						throw new foundation_fault('Unused token found', $display);
					} // if (count($unused)!==0)
					//
					$matches=array();
					$match_count=preg_match_all("/#(.*?)#/i", $rendition, $matches);
					//
					if ($match_count===FALSE)
					{
						throw new foundation_fault('Token match failed', '');
					} // if ($match_count===FALSE)
					//
					if ($match_count!==0)
					{
						$dedupe=array_unique($matches[0]);
						$display=implode(', ', $dedupe);
						if ($match_count===1)
						{
							throw new foundation_fault('Unresolved token found', $display);
						}
						else
						{
							throw new foundation_fault('Unresolved tokens found', $display);
						} // if ($match_count===1)
					} // if ($match_count!==0)
				}
				else
				{
					// Permissive mode
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
				} // if ($this->strict===TRUE)
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
		/*
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
		*/
	} // template
?>