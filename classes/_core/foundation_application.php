<?php
	//
	// foundation_application.php
	//
	class foundation_application
	{
		const STRICT=1;
		//
		private $ini;
		private $modules;
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
					throw new foundation_fault("Invalid args [$arg_count]", origin());
				} // if ($arg_count!==1)
				//
				confirm_object($ini, 'foundation_ini');
				//
				//
				$this->ini=$ini;
				//
				$this->load_modules();
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not make application', '', $e);
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
				$main=new foundation_template('foundation_database_test_success.tem', foundation_template::CORE);
				//
				$result=$main->render();
				//
				return $result;
			}
			catch (Thorwable $e)
			{
				throw new foundation_fault('Could not test database', '', $e);
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
				$main=new foundation_template('foundation_configuration.tem', foundation_template::CORE);
				/*
				$form_field=new foundation_template('foundation_configuration_setting.snip', foundation_template::CORE);
				$form_radio_group=new foundation_template('foundation_configuration_setting_radio_group.snip', foundation_template::CORE);
				$form_radio=new foundation_template('foundation_configuration_setting_radio.snip', foundation_template::CORE);
				//
				foreach($this->ini->get_ini() as $setting=>$value)
				{
					if (is_bool($value)===TRUE)
					{
						// It's boolean
						if ($value===TRUE)
						{
							$value='TRUE';
						}
						else
						{
							$value='FALSE';
						} // if ($value===TRUE)
						//
						$form_radio_group->add_token('SETTING', $setting);
						//
						// TRUE
						$form_radio->add_token('NAME', $setting);
						$form_radio->add_token('VALUE', 'TRUE');
						$form_radio->add_token('DISPLAY', 'True');
						if ($value===TRUE)
						{
							$form_radio->add_token('STATUS', 'CHECKED');
						}
						else
						{
							$form_radio->add_token('STATUS', '');
						} // if ($value===TRUE)
						//
						$form_radio_group->append_snippet('RADIO_BUTTONS', $form_radio);
						//
						// FALSE
						$form_radio->add_token('VALUE', 'FALSE');
						$form_radio->add_token('DISPLAY', 'False');
						if ($value===FALSE)
						{
							$form_radio->add_token('STATUS', '');
						}
						else
						{
							$form_radio->add_token('STATUS', 'CHECKED');
						} // if ($value===FALSE)
						//
						$form_radio_group->append_snippet('RADIO_BUTTONS', $form_radio);
						//
						$main->append_snippet('FORM_FIELDS', $form_radio_group);
						//
						$form_radio_group->clear();
					}
					else
					{
						// Not boolean
						//
						$form_field->add_token('SETTING', $setting);
						$form_field->add_token('VALUE', $value);
						//
						$main->append_snippet('FORM_FIELDS', $form_field);
					} // if (is_bool($value))
				} // foreach($this->ini->get_ini() as $setting=>$value)
				*/
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
				// Are database settings present?
				if (($this->ini->get_ini('database_host')!=='') && 
						($this->ini->get_ini('database_user')!=='') && 
						($this->ini->get_ini('database_password')!=='') && 
						($this->ini->get_ini('database_name')!==''))
				{
					// Yes, present
					$database_test=new foundation_template('foundation_configuration_database_test.snip', foundation_template::CORE);
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
					$smtp_test=new foundation_template('foundation_configuration_smtp_test.snip', foundation_template::CORE);
					$main->add_snippet('SMTP_TEST', $smtp_test);
				}
				else
				{
					// No, something is missing
					$main->add_token('SMTP_TEST', '');
				} // if [Database settings are present]
				//
				$main->add_token('FOUNDATION_APPICATION_CLASS', application_name().'.php');
				$main->add_token('SHOW_DEBUG_TRUE', $show_debug_true);
				$main->add_token('SHOW_DEBUG_FALSE', $show_debug_false);
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
				throw new foundation_fault('Could not display', '', $e);
			} // try
		} // DISPLAY()
		//
		//
		private function load_modules()
		{
			try
			{
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not load modules', '', $e);
			} // try
		} // load_modules
		//
		//
		/**
		* <h1>Render</h1>
		* Generates rendered output.  Rendering should handle all substitutions
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
						$strict=FALSE;
					break; }
					//
					case 1: {
						$arg=func_get_arg(0);
						if (is_string($arg)===TRUE)
						{
							$command=$arg;
							$strict=FALSE;
						}
						else
						{
							if (is_int($arg)===TRUE)
							{
								if ($arg===foundation_application::STRICT)
								{
									$command='';
									$strict=TRUE;
								}
								else
								{
									throw new foundation_fault('Invalid option', $arg);
								} // if ($arg===foundation_appliction::STRICT)
							} // if (is_int($arg)===TRUE)
						} // if (is_string($arg)===TRUE)
					break; }
					//
					case 2: {
						$command=func_get_arg(0);
						confirm_string($command);
						$strict=func_get_arg(1);
						confirm_int($strict);
						if ($strict!==foundation_application::STRICT)
						{
							throw new foundation_fault('Invalid option', $strict);
						} // if ($strict!==foundation_application::STRICT)
					break; }
					//
					default: {
						throw new foundation_fault("Invalid args [$arg_count]", origin());
					break; }
				} // switch ($arg_count)
				//
				//
				////////////////////////////
				// Render the application //
				////////////////////////////
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
					throw new foundation_fault('Invalid command', $command);
				}
				else
				{
					// Yes, command exists
					$response=$this->$command();
				} // if (method_exists($application, $command)!==TRUE)
				//
				// Are we running in strict mode?
				if ($strict===TRUE)
				{
					// Yes, strict mode
					$matches=array();
					$match_count=preg_match_all("/#(.*?)#/i", $response, $matches);
					//
					if ($match_count===FALSE)
					{
						throw new foundation_fault('Token match failed', '');
					} // if ($match_count===FALSE)
					//
					if ($match_count!==0)
					{
						$display=implode(', ', $matches[0]);
						if ($match_count===1)
						{
							throw new foundation_fault('Unresolved token found', $display);
						}
						else
						{
							throw new foundation_fault('Unresolved tokens found', $display);
						} // if ($match_count===1)
					} // if ($match_count!==0)
				} // if ($strict===TRUE)
				//
				//
				return $response;
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Cannot render', '', $e);
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
				$main=new foundation_template('foundation_application.ini', foundation_template::CORE);
				//
//trace($_POST);
				$main->add_token('SHOW_DEBUG', $_POST['show_debug']);
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
				$ini=new foundation_ini($application_name);
				$this->ini=$ini;
				//
				return $this->render('DISPLAY');
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not save', '', $e);
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
				$main=new foundation_template('foundation_smtp_test.tem', foundation_template::CORE);
				$email=new foundation_template('foundation_smtp_test_email.snip', foundation_template::CORE);
				$subject=new foundation_template('foundation_smtp_test_email_subject.bit', foundation_template::CORE);
				$body=new foundation_template('foundation_smtp_test_email_body.snip', foundation_template::CORE);
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
				$main->add_token('FOUNDATION_APPICATION_CLASS', application_name().'.php');
				$main->add_snippet('EMAIL', $email);
				$main->add_token('DOMAIN', $_SERVER['HTTP_HOST']);
				//
				$result=$main->render();
				//
				return $result;
			}
			catch (Thorwable $e)
			{
				throw new foundation_fault('Could not test STMP', '', $e);
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
				$main=new foundation_template('foundation_smtp_test_success.tem', foundation_template::CORE);
				$subject=new foundation_template('foundation_smtp_test_email_subject.bit', foundation_template::CORE);
				$body=new foundation_template('foundation_smtp_test_email_body.snip', foundation_template::CORE);
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
				throw new foundation_fault('Could not send STMP test', '', $e);
			} // try
		} // SMTP_SEND_EMAIL()
	} // foundation_install
?>