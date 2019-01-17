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
		private $permission;
		private $require_authentication;
		protected $user_id;
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
				$this->permission=array();
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
		public function authenticate()
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
				confirm_args($arg_count, 0);
				//
				//
				///////////////////////
				// AUTHENTICATE USER //
				///////////////////////
				//
				$authenticate=FALSE;
				//
				// Is there an access_token existing for this application/user?
				if (array_key_exists($application_name, $_COOKIE))
				{
					// Yes, an access_token exists
					//
					// Is it in the database?
					$SQL="SELECT user_id FROM `access_session` WHERE access_token='#1#' AND application='#2#';";
					$access_token=query_one($SQL, $_COOKIE[$application_name], $application_name);
					if ($access_token===FALSE)
					{
						// No, not in the database
						//
						// Do nothimg more here
					}
					else
					{
						// Yes, in the database
						//
						$this->user_id=$access_token['user_id'];
						$authenticate=TRUE;
					} // if ($access_token===FALSE)
				}
				else
				{
					// No, an access_token does not exist
					//
					// Do nothing more here
				} // if (array_key_exists('access_token', $_COOKIE))
				//
				// Have we authenticated?
				if ($authenticate!==TRUE)
				{
					// No, we have not, proceed with other authentication steps
					//
          				// Does ID exist in $_POST?
          				if (array_key_exists('ID', $_POST)!==TRUE)
          				{
           					// No, ID is missing
          					// Does PASSWORD exist in $_POST?
            					if (array_key_exists('PASSWORD', $_POST)!==TRUE)
            					{
            						// No, PASSWORD is missing
            						// We will assume this is a new authentication request
            						$message='';
            					}
            					else
            					{
            						// Yes, PASSWORD is present
            						$message='MISSING ID';
            					} // if (array_key_exists('PASSWORD', $_POST)!==TRUE)
          				}
          				else
          				{
          					// ID exists
          					//
          					// Does PASSWORD exist in $_POST?
          					if (array_key_exists('PASSWORD', $_POST)!==TRUE)
          					{
          						// No, PASSWORD is missing
           						$message='Need password';
            					}
            					else
            					{
         						// Yes, PASSWORD is present
        						//
        						$message='Invalid Password';
        						//
       							$ID=strtoupper($_POST['ID']);
       							$authenticate=$this->authenticate_id_password($ID, $_POST['PASSWORD']);
       							//
       							// Did it authenticate?
      							if ($authenticate===TRUE)
      							{
      								// Yes, authenticated
       								//
       								$this->user_id=$ID;
       							}
       							else
       							{
       								// No, did not authenticate
       								//
       								// Do nothing more
       							} // if ($authenticate===TRUE)
       						} // if (array_key_exists('PASSWORD', $_POST)!==TRUE)
      					} // if (array_key_exists('ID', $_POST)!==TRUE)
				} // if ($authenticate!==TRUE)
				//
				// Did user authenticate?
				if ($authenticate!==TRUE)
				{
					// No, did not authenticate
					//
					// Do nothing more here
				}
				else
				{
					// Yes, authenticated
					//
					// Delete all existing access_tokens for this ID
					$SQL="DELETE from `access_session` WHERE user_id='#1#' AND application='#2#';";
					query($SQL, $this->user_id, $application_name);
					//
					// Create a new access token
					$access_token=random_bytes(64);
					//
					// Save the access token to the database for future validation
					$SQL="INSERT INTO `access_session` (application, user_id, access_token) VALUES ('#1#', '#2#', '#3#');";
					query($SQL, $application_name, $this->user_id, $access_token);
					//
					// Set the cookie
					setcookie($application_name, $access_token, time() + 86400, '/');
					$_COOKIE[$application_name]=$access_token;
				} // if ($authenticate!==TRUE)
				//
				return $authenticate;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Unable to authenticate', origin(), $e);
			} // try
		} // authenticate()
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
				if ((in_array($target, $this->permission)===TRUE) || (in_array(phocus_application::UNIVERSAL_PERMISSION, $this->permission)===TRUE))
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
		} // initialize()
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
		} // load_modules()
		//
		//
		protected function login()
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
				confirm_args($arg_count, 0);
				//
				$main=new phocus_template('phocus_login.tem', phocus_template::CORE);
				$main->add_token('message', $message);
				$main->add_token('APPLICATION_NAME', $application_name);
				$result=$main->render();
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not display login', origin(), $e);
		  	} // try
		} // login()
		//
		//
		public function logout()
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
				confirm_args($arg_count, 0);
				//
				//
				///////////////////
				// Do the logout //
				///////////////////
				//
				// Is there an acces_token?
				if (array_key_exists($application_name, $_COOKIE))
				{
				  // Yes, has an access_token
				  //
				  // Load the access_token
				  $SQL="SELECT user_id FROM `access_session` WHERE access_token='#1#' AND application='#2#';";
				  $access_data=query_one($SQL, $_COOKIE[$application_name], $application_name);
				  //
				  $user_id=$access_data['user_id'];
				  //
				  $SQL="DELETE from `access_session` WHERE user_id='#1#' AND application='#2#';";
				  query($SQL, $user_id, $application_name);
				  //
					setcookie($application_name, '', 0, '/');
				  //
				  $result=$this->login();
				  //
				  return $result;
				}
				else
				{
				  // No access_token
				  throw new phocus_fault('Cannot logout', 'No access token');
				} // if (array_key_exists($application_name, $_COOKIE))
		  	}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannotlogout', origin(), $e);
			} // try
		} // logout()
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
		protected function permission_set()
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
				/////////////////////
				// Set permissions //
				/////////////////////
				//
				$this->permission_add(phocus_application::UNIVERSAL_PERMISSION);
				//
				return;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Could not set permissions', $permission, $e);
			} // try
		} // permissions_set()
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
				$this->permission_set();
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
								// Try to get default_command
								$default_command=$ini->get_ini('defaut_command', phocus_ini::INI_OPTIONAL);
								// Is there a default command?
								if (is_null($default_command)===TRUE)
								{
									// No, default_command is not defined
									//
									// Use the Phocus default
									$command='DISPLAY';
								}
								else
								{
									// Yes, default_command is defined
									//
									$command=$default_command;
								} // if (is_null($default_command)===TRUE)
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
						$condition=func_get_arg(0);
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
		//
		//
		protected function user_id()
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
						// Form 1, get the user_id
						// No computation needed here
					break; }
					//
					case 1: {
						// Form 2, set the user_id
						$user_id=func_get_arg(0);
						confirm_string($user_id);
						//
						$this->user_id=$user_id;
					break; }
					//
					default: {
						throw new phocus_fault("Invalid argument count [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				$result=$this->user_id;
				//
				return $result;
			}
			catch (Throwable $e)
			{
				throw new phocus_fault('Cannot access user_id', origin(), $e);
			} // try
		} // user_id()
	} // phocus_install
?>
