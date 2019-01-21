<?php
	//
	// phocus_template.php
	//
	class phocus_template
	{
		const ALL='all';
		const APPLICATION='application';
		const CORE='core';
		const STRICT='strict';
		const PERMISSIVE='permissive';
		const SPECIFIC='specific';
		const UNIVERSAL='universal';
		//
		private $content;
		private $file_path;
		private $mode;
		private $snippet;
		private $token_value;
		//
		private static $universal_snippet=array();
		private static $universal_token_value=array();
		//
		public function __construct($file_name, $set=phocus_template::APPLICATION, $mode=phocus_template::STRICT)
		{
			try
			{
				global $application_name;
				//
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				//
				if (($arg_count<1) || ($arg_count>3))
				{
					throw new phocus_fault('Invalid argument count', $arg_count);
				}
				//
				//
				//////////////////////
				// Check data types //
				//////////////////////
				//
				confirm_string($file_name);
				confirm_string($set);
				confirm_string($mode);
				//
				//
				//////////////////
				// Sanity Check //
				//////////////////
				//
				confirm_path_safe($file_name);
				if (($set!==phocus_template::APPLICATION) && ($set!==phocus_template::CORE))
				{
					throw new phocus_fault('Unknown set', $set);
				}
				if (($mode!==phocus_template::STRICT) && ($mode!==phocus_template::PERMISSIVE))
				{
					throw new phocus_fault('Unknown mode', $mode);
				}
				//
				//
				// Determine subdirectory based on set
				if ($set===phocus_template::CORE)
				{
				  $sub='_core/';
				}
				else
				{
				  $sub=$application_name.'/';
				} // if ($set===phocus_template::CORE)
				//
				//
				///////////////////////
				// Load the template //
				///////////////////////
				//
				$this->file_path='../phocus_templates/'.$sub.$file_name;
				$this->content=file_read($this->file_path);
				//
				// Does the template have data?
				if (strlen($this->content)===0)
				{
					// No, it is empty
					//
					throw new phocus_fault('Empty template', $this->file_path);
				} // if (strlen($this->content)===0)
				//
				///////////////////////////
				// Initialize properties //
				///////////////////////////
				//
				$this->token_value=array();
				$this->snippet=array();
				$this->mode=$mode;
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not make template', origin(), $e);
			} // try
		} // __construct()
		//
		//
		public function add_snippet($token, $snippet, $collection=phocus_template::SPECIFIC)
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
				confirm_string($token);
				confirm_object($snippet, 'phocus_template');
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				/////////////////////
				// Add the snippet //
				/////////////////////
				//
				switch ($collection)
				{
					case 'specific': {
						$this->snippet[$token]=$snippet->render();
					break; }
					//
					case 'universal': {
						phocus_template::$universal_snippet[$token]=$snippet->render();
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add snippet', origin(), $e);
			} // try
		} // add_snippet()
		//
		//
		public function append_snippet($token, $snippet, $collection=phocus_template::SPECIFIC)
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
				confirm_string($token);
				confirm_object($snippet, 'phocus_template');
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				/////////////////////
				// Add the snippet //
				/////////////////////
				//
				switch ($collection)
				{
					case 'specific': {
						$this->snippet[$token].=$snippet->render();
					break; }
					//
					case 'universal': {
						phocus_template::$universal_snippet[$token].=$snippet->render();
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not append snippet', origin(), $e);
			} // try
		} // append_snippet()
		//
		//
		public function add_token($token, $value, $collection=phocus_template::SPECIFIC)
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
				confirm_string($token);
				confirm_mixed($value, 'NDIS');
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				///////////////////
				// Normalization //
				///////////////////
				//
				if ($value===NULL)
				{
					$value='';
				}
				//
				///////////////////
				// Add the token //
				///////////////////
				//
				switch ($collection)
				{
					case 'specific': {
						$this->token_value[$token]=$value;
					break; }
					//
					case 'universal': {
						phocus_template::$universal_token_value[$token]=$value;
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add token', origin(), $e);
			} // try
		} // add_token()
		//
		//
		public static function add_universal_token($token, $value)
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
				confirm_mixed($value, 'NDIS');
				//
				//
				///////////////////
				// Normalization //
				///////////////////
				//
				if ($value===NULL)
				{
					$value='';
				}
				//
				///////////////////
				// Add the token //
				///////////////////
				//
				phocus_template::$universal_token_value[$token]=$value; 
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add universal token', origin(), $e);
			} // try
		} // add_universal_token()
		//
		//
		public function add_token_array($token_values, $collection=phocus_template::SPECIFIC)
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
				confirm_array($token_values);
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				////////////////////
				// Add the tokens //
				////////////////////
				//
				foreach ($token_values as $token=>$value)
				{
					confirm_mixed($value, 'NDIS');
					//
					if ($value===NULL)
					{
						$value='';
					}
					//
					switch ($collection)
					{
						case 'specific': {
							$this->token_value[$token]=$value;
						break; }
						//
						case 'universal': {
							phocus_template::$universal_token_value[$token]=$value;
						break; }
						//
						default: {
							// This should not be possible
							//
							throw new phocus_fault('Unsupported collection', $collection);
						break; }
					} // switch ($collection)
				} // foreach ($token_values as $token=>$value)
				//
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add token', origin(), $e);
			} // try
		} // add_token_array()
		//
		//
		public function add_token_radio($token_list, $value, $prefix, $default, $collection=phocus_template::SPECIFIC)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 5);
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
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
						switch ($collection)
						{
							case 'specific': {
								$this->token_value[$token]='CHECKED';
							break; }
							//
							case 'universal': {
								phocus_template::$universal_token_value[$token]='CHECKED';
							break; }
							//
							default: {
								// This should not be possible
								//
								throw new phocus_fault('Unsupported collection', $collection);
							break; }
						} // switch ($collection)
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
					switch ($collection)
					{
						case 'specific': {
							$this->token_value[$token]='SELECTED';
						break; }
						//
						case 'universal': {
							phocus_template::$universal_token_value[$token]='SELECTED';
						break; }
						//
						default: {
							// This should not be possible
							//
							throw new phocus_fault('Unsupported collection', $collection);
						break; }
					} // switch ($collection)
				} // if ($used===FALSE)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add tokens', origin(), $e);
			} // try
		} // add_token_radio()
		//
		//
		public function add_token_select($token_list, $value, $prefix, $default, $collection=phocus_template::SPECIFIC)
		{
			try
			{
				//////////////////////////
				// Check argument count //
				//////////////////////////
				//
				$arg_count=func_num_args();
				confirm_args($arg_count, 5);
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
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
						switch ($collection)
						{
							case 'specific': {
								$this->token_value[$token]='SELECTED';
							break; }
							//
							case 'universal': {
								phocus_template::$universal_token_value[$token]='SELECTED';
							break; }
							//
							default: {
								// This should not be possible
								//
								throw new phocus_fault('Unsupported collection', $collection);
							break; }
						} // switch ($collection)
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
				throw new phocus_fault('Could not add tokens', origin(), $e);
			} // try
		} // add_token_select()
		//
		//
		public function clear($collection=phocus_template::SPECIFIC)
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				//
				//
				////////////////////
				// Clear snippets //
				////////////////////
				//
				switch ($collection)
				{
					case 'all': {
						$this->clear_snippets(phocus_template::ALL);
						$this->clear_tokens(phocus_template::ALL);
					break; }
					//
					case 'specific': {
						$this->clear_snippets(phocus_template::SPECIFIC);
						$this->clear_tokens(phocus_template::SPECIFIC);
					break; }
					//
					case 'universal': {
						$this->clear_snippets(phocus_template::UNIVERSAL);
						$this->clear_tokens(phocus_template::UNIVERSAL);
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not clear', origin(), $e);
			} // try
		} // clear ()
		//
		//
		public function clear_snippets($collection=phocus_template::ALL)
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				//
				//
				////////////////////
				// Clear snippets //
				////////////////////
				//
				switch ($collection)
				{
					case 'all': {
						$this->snippet=array();
						phocus_template::$universal_snippet=array();
					break; }
					//
					case 'specific': {
						$this->snippet=array();
					break; }
					//
					case 'universal': {
						phocus_template::$universal_snippet=array();
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not clear snippets', origin(), $e);
			} // try
		} // clear_snippets()
		//
		//
		public function clear_tokens($collection=phocus_template::ALL)
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL) && ($collection!==phocus_template::ALL))
				//
				//
				//////////////////
				// Clear tokens //
				//////////////////
				//
				switch ($collection)
				{
					case 'all': {
						$this->token_value=array();
						phocus_template::$universal_token_value=array();
					break; }
					//
					case 'specific': {
						$this->token_value=array();
					break; }
					//
					case 'universal': {
						phocus_template::$universal_token_value=array();
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not clear tokens', origin(), $e);
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
				throw new phocus_fault('Could not get $content', origin(), $e);
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
				throw new phocus_fault('Could not get $file_path', origin(), $e);
			} // try
		} // get_file_path()
		//
		//
		public function get_snippet($collection=phocus_template::SPECIFIC)
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				/////////////////////
				// Return snippets //
				/////////////////////
				//
				switch ($collection)
				{
					case 'specific': {
						return $this->snippet;
					break; }
					//
					case 'universal': {
						return phocus_template::$universal_snippet;
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get $snippet', origin(), $e);
			} // try
		} // get_snippet()
		//
		//
		public function get_token_value($collection=phocus_template::SPECIFIC)
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
				confirm_string($collection);
				//
				//
				//////////////////
				// Sanity check //
				//////////////////
				//
				if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				{
					throw new phocus_fault('Invalid collection', $collection);
				} // if (($collection!==phocus_template::SPECIFIC) && ($collection!==phocus_template::UNIVERSAL))
				//
				//
				///////////////////
				// Return tokens //
				///////////////////
				//
				switch ($collection)
				{
					case 'specific': {
						return $this->token_value;
					break; }
					//
					case 'universal': {
						return phocus_template::$universal_token_value;
					break; }
					//
					default: {
						// This should not be possible
						//
						throw new phocus_fault('Unsupported collection', $collection);
					break; }
				} // switch ($collection)
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not get $token_value', origin(), $e);
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
				if ($this->mode===phocus_template::STRICT)
				{
					// Strict mode
					//
					$unused=array();
					//
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
					foreach (phocus_template::$universal_snippet as $token=>$HTML)
					{
						$rendition2=str_replace("#{$token}#", $HTML, $rendition);
						//
						if ($rendition2==$rendition)
						{
							$unused[]='UNIVERSAL SNIPPET:'.$token;
						} // if ($rendition2==$rendition)
						//
						$rendition=$rendition2;
					} // foreach ($token_value as $token=>$value)
					//
					foreach (phocus_template::$universal_token_value as $token=>$value)
					{
						$rendition2=str_replace("#{$token}#", htmlspecialchars($value), $rendition);
						//
						if ($rendition2==$rendition)
						{
							$unused[]='UNIVERAL TOKEN:'.$token;
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
						throw new phocus_fault('Unused token found', $display);
					} // if (count($unused)!==0)
					//
					$matches=array();
					$match_count=preg_match_all("/#([a-zA-Z0-9_]*?)#/i", $rendition, $matches);
					//
					if ($match_count===FALSE)
					{
						throw new phocus_fault('Token match failed', '');
					} // if ($match_count===FALSE)
					//
					if ($match_count!==0)
					{
						$dedupe=array_unique($matches[0]);
						$display=implode(', ', $dedupe);
						if ($match_count===1)
						{
							throw new phocus_fault('Unresolved token found', $display);
						}
						else
						{
							throw new phocus_fault('Unresolved tokens found', $display);
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
					//
					foreach (phocus_template::$universal_snippet as $token=>$HTML)
					{
						$rendition=str_replace("#{$token}#", $HTML, $rendition);
					} // foreach ($token_value as $token=>$value)
					//
					foreach (phocus_template::$universal_token_value as $token=>$value)
					{
						$rendition=str_replace("#{$token}#", htmlspecialchars($value), $rendition);
					} // foreach ($token_value as $token=>$value)
				} // if ($this->mode===phocus_template::STRICT)
				//
				return $rendition;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not render template', origin(), $e);
			} // try
		} // render()
	} // template
?>
