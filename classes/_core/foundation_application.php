<?php
	//
	// foundation_application.php
	//
	class foundation_application
	{
		private $modules;
		//
		//
		public function __construct()
		{
			try
			{
				load_modules();
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not make application', '', $e);
			} // try
		} // __construct()
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
				$form_field=new foundation_template('foundation_configuration_setting.snip', foundation_template::CORE);
				$form_radio_group=new foundation_template('foundation_configuration_setting_radio_group.snip', foundation_template::CORE);
				$form_radio=new foundation_template('foundation_configuration_setting_radio.snip', foundation_template::CORE);
				//
				foreach(get_ini() as $setting=>$value)
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
				} // foreach(get_ini() as $setting=>$value)
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
				$main=new foundation_template('foundtion_application.ini');
				//
				$main->add_template('show_debug', $_POST['show_debug']);
				$main->add_template('database_host', $_POST['database_host']);
				$main->add_template('database_user', $_POST['database_user']);
				$main->add_template('database_password', $_POST['database_password']);
				$main->add_template('database_name', $_POST['database_name']);
				$main->add_template('SMTP_host', $_POST['SMTP_host']);
				$main->add_template('SMTP_user', $_POST['SMTP_user']);
				$main->add_template('SMTP_password', $_POST['SMTP_password']);
				$main->add_template('SMTP_from_address', $_POST['SMTP_from_address']);
				$main->add_template('SMTP_from_name', $_POST['SMTP_from_name']);
				$main->add_template('SMTP_debug', $_POST['SMTP_debug']);
				//
				$content=$main->render();
				//
				$ini_file=get_ini_file();
				//
				file_save($ini_file, $content);
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not save', '', $e);
			} // try
		} // SAVE()
	} // foundation_install
?>