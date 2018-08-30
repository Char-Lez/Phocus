<?php
	//
	// phocus_application.php 
	//
	class phocus_application
	{
		const STRICT=1;
		const UNIVERSAL_PERMISSION='*';
		//
		private $ini;
		private $modules;
		private $require_authentication;
		private $permission;
		//
		//
		public function __construct($ini)
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
				confirm_object($ini, 'phocus_ini');
				//
				//
				$this->ini=$ini;
				//
				$this->load_modules();
				//
				$this->initialize();
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not make application', origin(), $e);
			} // try
		} // __construct()
		//
		//
		public function DATABASE_TEST()
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
				// Try to do something with the database
				$tables=query_fetch_all_unique('show tables;');
				//
				$main=new phocus_template('phocus_database_test_success.tem', phocus_template::CORE);
				//
				$result=$main->render();
				//
				return $result;
			}
			catch (Thorwable $e)
			{
				throw new phocus_fault('Could not test database', origin(), $e);
			} // try
		} // DATABASE_TEST()
		//
		//
		public function DISPLAY()
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
				$main=new phocus_template('phocus_configuration.tem', phocus_template::CORE);
				//
				// Is show_debug true?
				if ($this->ini->get_ini('show_debug')===TRUE)
				{
					// Yes, true
					$show_debug_true='CHECKED';
					$show_debug_false='';
				}
				else
				{
					// No, not true
					$show_debug_true='';
					$show_debug_false='CHECKED';
				} // if ($this->ini->get_ini('show_debug')===TRUE)
				//
				// Is strict true?
				if ($this->ini->get_ini('strict')===TRUE)
				{
					// Yes, true
					$strict_true='CHECKED';
					$strict_false='';
				}
				else
				{
					// No, not true
					$strict_true='';
					$strict_false='CHECKED';
				} // if ($this->ini->get_ini('strict')===TRUE)
				//
				// Are database settings present?
				if (($this->ini->get_ini('database_host')!=='') && 
						($this->ini->get_ini('database_user')!=='') && 
						($this->ini->get_ini('database_password')!=='') && 
						($this->ini->get_ini('database_name')!==''))
				{
					// Yes, present
					$database_test=new phocus_template('phocus_configuration_database_test.snip', phocus_template::CORE);
					$main->add_snippet('DATABASE_TEST', $database_test);
				}
				else
				{
					// No, something is missing
					$main->add_token('DATABASE_TEST', '');
				} // if [Database settings are present]
				//
				// Is smtp_debug true?
				if ($this->ini->get_ini('SMTP_debug')===TRUE)
				{
					// Yes, true
					$smtp_debug_true='CHECKED';
					$smtp_debug_false='';
				}
				else
				{
					// No, not true
					$smtp_debug_true='';
					$smtp_debug_false='CHECKED';
				} // if ($this->ini->get_ini('SMTP_debug')===TRUE)
				//
				// Are smtp settings present?
				if (($this->ini->get_ini('SMTP_host')!=='') && 
						($this->ini->get_ini('SMTP_user')!=='') && 
						($this->ini->get_ini('SMTP_password')!=='') && 
						($this->ini->get_ini('SMTP_from_address')!=='') && 
						($this->ini->get_ini('SMTP_from_name')!==''))
				{
					// Yes, present
					$smtp_test=new phocus_template('phocus_configuration_smtp_test.snip', phocus_template::CORE);
					$main->add_snippet('SMTP_TEST', $smtp_test);
				}
				else
				{
					// No, something is missing
					$main->add_token('SMTP_TEST', '');
				} // if [Database settings are present]
				//
				$main->add_token('PHOCUS_APPICATION_CLASS', application_name().'.php');
				$main->add_token('SHOW_DEBUG_TRUE', $show_debug_true);
				$main->add_token('SHOW_DEBUG_FALSE', $show_debug_false);
				$main->add_token('STRICT_TRUE', $strict_true);
				$main->add_token('STRICT_FALSE', $strict_false);
				$main->add_token('DATABASE_HOST', $this->ini->get_ini('database_host'));
				$main->add_token('DATABASE_USER', $this->ini->get_ini('database_user'));
				$main->add_token('DATABASE_PASSWORD', $this->ini->get_ini('database_password'));
				$main->add_token('DATABASE_NAME', $this->ini->get_ini('database_name'));
				$main->add_token('SMTP_HOST', $this->ini->get_ini('SMTP_host'));
				$main->add_token('SMTP_USER', $this->ini->get_ini('SMTP_user'));
				$main->add_token('SMTP_PASSWORD', $this->ini->get_ini('SMTP_password'));
				$main->add_token('SMTP_FROM_ADDRESS', $this->ini->get_ini('SMTP_from_address'));
				$main->add_token('SMTP_FROM_NAME', $this->ini->get_ini('SMTP_from_name'));
				$main->add_token('SMTP_DEBUG_TRUE', $smtp_debug_true);
				$main->add_token('SMTP_DEBUG_FALSE', $smtp_debug_false);
				//
				return $main->render();
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not display', origin(), $e);
			} // try
		} // DISPLAY()
		//
		//
		protected function has_permission($target)
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
				////////////////////////
				// Confirm data types //
				////////////////////////
				//
				confirm_string($target);
				//
				//
				///////////////////////
				// Check permissions //
				///////////////////////
				//
				if ((in_array($target, $this->permission)===TRUE) || (in_array($target, phocus_application::UNIVERSAL_PERMISSION)===TRUE))
				{
					$result=TRUE;
				}
				else
				{
					$result=FALSE;
				} // if ((in_array($target, $this->permission)===TRUE) || (in_array($target, phocus_application::UNIVERSAL_PERMISSION)===TRUE))
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault("Cannot verify permission [$target]", origin(), $e);
			} // try
		} // has_permission()
		//
		//
		private function load_modules()
		{
			try
			{
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not load modules', origin(), $e);
			} // try
		} // load_modules
		//
		//
		/**
		*<h1>authenticate</h1>
		*
		* Attempts to authenticate a user for access
		*
		* First it looks for 'phocus_session' in parameter, $_COOKIE, $_POST, $_GET
		* If it finds 'phocus_session' it checks to see if it is valid
		* - If the session is valid, it sets permissions, and is done
		* - If the session is invalid, it forces an authentication page to be displayed
		* If not found it looks for 'user' and 'password' in patameter, $_COOKIE, $_POST, $_GET
		* - If it finds both then it attempts to check the password
		* - - If the password is valid, it sets permissions, adds the session, and sets the cookie and is done
		* - - If the password is invalid it forces an authentication page to be displayed
		*
		* It is possible that the cookie object could have a 'phocus_session', and a 'user' and 'password' combination
		* Or that the parameter, post or get might have some or all of those leading to an ambigious situation.
		* user/password pairs will always take precident, causing the current session to be invalidated and a new one created, if it validates
		* and the hierachy of parameter, cookie, post then get shall also be applied
		*
		* Thus a user/password pair in the $_GET will take predicent over a phocus_session in $_GET, but not over
		* a phocus_session in parameter, $_COOKIE or $_POST, and so on.  A user/password in the parameters is the highest priority
		*
		* Parameter user/password
		* Parameter phocus_session
		* $_COOKIE user/password
		* $_COOKIE phocus_session
		* $_POST user/password
		* $_POST phocus_session
		* $_GET user/password
		* $_GET phocus_session
		*
		* @returns boolean
		*/
		protected function authenticate()
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
						// Nothing in the parameters
						//
						// Looks for user/password pair in $_COOKIE
						if ((array_key_exists('user', $_COOKIE)===TRUE) && (array_key_exists('password', $_COOKIE)===TRUE))
						{
							// user/password is in the $_COOKIE
							$user=$_COOKIE['user'];
							$password=$_COOKIE['password'];
							$method=1;
						}
						else
						{
							// user/password pair is not in the $_COOKIE
							// Is phocus_session in the $_COOKIE?
							if (array_key_exists('phocus_session', $_COOKIE)===TRUE)
							{
								// Yes, phocus_session is in the $_COOKIE
								$phocus_session=$_COOKIE['phocus_session'];
								$method=2;
							}
							else
							{
								// No, phocus_session is not in the $_COOKIE
								//
								// Is it in $_POST?								
								if ((array_key_exists('user', $_POST)===TRUE) && (array_key_exists('password', $_POST)===TRUE))
								{
									// user/password is in the $_POST
									$user=$_POST['user'];
									$password=$_POST['password'];
									$method=1;
								}
								else
								{
									// user/password pair is not in the $_POST
									// Is phocus_session in the $_POST?
									if (array_key_exists('phocus_session', $_POST)===TRUE)
									{
										// Yes, phocus_session is in the $_POST
										$phocus_session=$_POST['phocus_session'];
										$method=2;
									}
									else
									{
										// No, phocus_session is not in the $_POST
										//
										// Is it in $_GET?								
										if ((array_key_exists('user', $_GET)===TRUE) && (array_key_exists('password', $_GET)===TRUE))
										{
											// user/password is in the $_POST
											$user=$_GET['user'];
											$password=$_GET['password'];
											$method=1;
										}
										else
										{
											// user/password pair is not in the $_GET
											// Is phocus_session in the $_GET?
											if (array_key_exists('phocus_session', $_GET)===TRUE)
											{
												// Yes, phocus_session is in the $_GET
												$phocus_session=$_GET['phocus_session'];
												$method=2;
											}
											else
											{
												// No, phocus_session is not in the $_POST
												//
												throw new phocus_fault('No authentication information present', origin());
											} // if (array_key_exists('phocus_session', $_GET)===TRUE)
										} // if ((array_key_exists('user', $_GET)===TRUE) && (array_key_exists('password', $_GET)===TRUE))
									} // if (array_key_exists('phocus_session', $_POST)===TRUE)
								} // if ((array_key_exists('user', $_POST)===TRUE) && (array_key_exists('password', $_POST)===TRUE))
							} // if (array_key_exists('phocus_session', $_COOKIE)===TRUE)
						} // if ((array_key_exists('user', $_COOKIE)===TRUE) && (array_key_exists('password', $_COOKIE)===TRUE)) 
					break; }
					//
					case 1: {
						// This has to be a phocus_session
						//
						$phocus_session=func_get_arg(0);
						confirm_string($phocus_session);
						$method=2;
					break; }
					//
					case 2: {
						// This has to be a user/password pair
						//
						$user=func_get_arg(0);
						$password=func_get_arg(1);
						confirm_string($user);
						confirm_string($password);
						$method=1;
					break; }
					//
					default: {
						throw new phocus_fault("Invalid parameters [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				////////////
				//
				////////////
				//
				switch ($method)
				{
					case 1: {
						// Using user/password
						$password_hash=password_hash($password, PASSWORD_DEFAULT);
						if ($password_hash===FALSE)
						{
							throw new phocus_fault('Could not process password', origin());
						}
						else
						{
						} // if ($password_hash===FALSE)
					break; }
					//
					case 2: {
						// Using phocus_session
					break; }
				} // switch ($method)
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot authenticate', origin(), $e);
			} // try
		} // authenticate()
		//
		//
		protected function initialize()
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
				///////////////////////////
				// Initialize the object //
				///////////////////////////
				//
				$this->require_authentication=FALSE;
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not load modules', origin(), $e);
			} // try
		} // initialize
		//
		//
		protected function permission_add($permission)
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
				////////////////////////
				// Confirm data types //
				////////////////////////
				//
				confirm_string($permission);
				//
				//
				////////////////////
				// Add permission //
				////////////////////
				//
				$this->permission[]=$permission;
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not add permission', $permission, $e);
			} // try
		} // permission_add()
		//
		//
		/**
		*<h1>Sets/Gets the require_authentication condition</h1>
		*
		* When require_authentication is TRUE then the application should complete
		* authentication functions before proceeding to anything else
		*
		* FORM 1: Get the contition
		*         Returns the current condition without changing anything
		*
		* FORM 2: Set the condition
		*         Sets the condition, then returns whatever was set
		*/
		protected function require_authentication()
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
						// Form 1, get the condition
						// No computation needed here
					break; }
					//
					case 1: {
						// Form 2, set the condition
						confirm_boolean($condition);
						//
						$this->condition=$condition;
					break; }
					//
					default: {
						throw new phocus_fault("Invalid argument count [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				$result=$this->condition;
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot set require_authentication', origin(), $e);
			} // try
		} // require_authentication()
		//
		//
		/**
		* <h1>Render</h1>
		* Generates rendered output.  Rendering should handle all substitutions
		*
		* FORM 1:
		* 	No arguments, will look for command in post, get or cookie, in that order
		*   Or, if finding no command, will use 'DISPLAY' as default
		*
		* FORM 2:
		*		command [string], an explicitly declared command
		*
		*/
		function render()
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
						$command='';
					break; }
					//
					case 1: {
						$command=func_get_arg(0);
						confirm_string($command);
					break; }
					//
					default: {
						throw new phocus_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				////////////////////////////
				// Render the application //
				////////////////////////////
				//
				// Do we require authentication?
				if ($this->require_authentication()===TRUE)
				{
					$this->authenticate();
				} // if ($this->require_authentication()===TRUE)
				//
				$this->permission_add(phocus_application::UNIVERSAL_PERMISSION);
				//
				// Is there a declared command?
				if ($command!=='')
				{
					// Yes, declared command
					// Do nothing more
				}
				else
				{
					// No, no command declared
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
				} // if ($command!=='')
				//
				// Does the command exist?
				if (method_exists($this, $command)!==TRUE)
				{
					// No, command is missing
					throw new phocus_fault('Invalid command', $command);
				}
				else
				{
					// Yes, command exists
					$response=$this->$command();
				} // if (method_exists($application, $command)!==TRUE)
				//
				return $response;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot render', origin(), $e);
			} // try
		} // render()
		//
		//
		public function SAVE()
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
				confirm_post_element('show_debug');
				confirm_post_element('strict');
				confirm_post_element('database_host');
				confirm_post_element('database_user');
				confirm_post_element('database_password');
				confirm_post_element('database_name');
				confirm_post_element('SMTP_host');
				confirm_post_element('SMTP_user');
				confirm_post_element('SMTP_password');
				confirm_post_element('SMTP_from_address');
				confirm_post_element('SMTP_from_name');
				confirm_post_element('SMTP_debug');
				//
				//
				/////////////////////////////////////
				// Save the new configuration file //
				/////////////////////////////////////
				//
				$main=new phocus_template('phocus_application.ini', phocus_template::CORE);
				//
				$main->add_token('SHOW_DEBUG', $_POST['show_debug']);
				$main->add_token('STRICT', $_POST['strict']);
				$main->add_token('DATABASE_HOST', $_POST['database_host']);
				$main->add_token('DATABASE_USER', $_POST['database_user']);
				$main->add_token('DATABASE_PASSWORD', $_POST['database_password']);
				$main->add_token('DATABASE_NAME', $_POST['database_name']);
				$main->add_token('SMTP_HOST', $_POST['SMTP_host']);
				$main->add_token('SMTP_USER', $_POST['SMTP_user']);
				$main->add_token('SMTP_PASSWORD', $_POST['SMTP_password']);
				$main->add_token('SMTP_FROM_ADDRESS', $_POST['SMTP_from_address']);
				$main->add_token('SMTP_FROM_NAME', $_POST['SMTP_from_name']);
				$main->add_token('SMTP_DEBUG', $_POST['SMTP_debug']);
				//
				$content=$main->render();
				//
				file_save($this->ini->get_ini_path(), $content);
				//
				// Reload the new ini
				$application_name=application_name();
				$ini=new phocus_ini($application_name);
				$this->ini=$ini;
				//
				return $this->render('DISPLAY');
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not save', origin(), $e);
			} // try
		} // SAVE()
		//
		//
		public function SMTP_TEST()
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
				$main=new phocus_template('phocus_smtp_test.tem', phocus_template::CORE);
				$email=new phocus_template('phocus_smtp_test_email.snip', phocus_template::CORE);
				$subject=new phocus_template('phocus_smtp_test_email_subject.bit', phocus_template::CORE);
				$body=new phocus_template('phocus_smtp_test_email_body.snip', phocus_template::CORE);
				//
				$subject->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				$body->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				$email->add_snippet('SUBJECT', $subject);
				$email->add_snippet('BODY', $body);
				$email->add_token('SMTP_FROM_ADDRESS', $this->ini->get_ini('SMTP_from_address'));
				$email->add_token('SMTP_FROM_NAME', $this->ini->get_ini('SMTP_from_name'));
				//
				$main->add_token('PHOCUS_APPICATION_CLASS', application_name().'.php');
				$main->add_snippet('EMAIL', $email);
				$main->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				$result=$main->render();
				//
				return $result;
			}
			catch (Thorwable $e)
			{
				throw new phocus_fault('Could not test STMP', origin(), $e);
			} // try
		} // SMTP_TEST()
		//
		//
		public function SMTP_SEND_EMAIL()
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
				confirm_post_element('to_test_address');
				//
				//
				$main=new phocus_template('phocus_smtp_test_success.tem', phocus_template::CORE);
				$subject=new phocus_template('phocus_smtp_test_email_subject.bit', phocus_template::CORE);
				$body=new phocus_template('phocus_smtp_test_email_body.snip', phocus_template::CORE);
				//
				$subject->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				$body->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				my_mail($_POST['to_test_address'], $this->ini->get_ini('SMTP_from_name'), $subject->render(), $body->render());
				//
				$result=$main->render();
				//
				return $result;
			}
			catch (Thorwable $e)
			{
				throw new phocus_fault('Could not send STMP test', origin(), $e);
			} // try
		} // SMTP_SEND_EMAIL()
	} // phocus_install
?>