<?php
	//
	// foundation_application.php
	//
	class foundation_application
	{
		public function DISPLAY()
		{
			try
			{
				global $app;
				//
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
				foreach($app->get_ini() as $setting=>$value)
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
						$form_radio->add_token(''
					}
					else
					{
						// Not boolean
						//
						$form_field->add_token('SETTING', $setting);
						$form_field->add_token('VALUE', $value);
					} // if (is_bool($value))
					//
					$main->append_snippet('FORM_FIELDS', $form_field);
				} // foreach($app->get_ini() as $setting=>$value)
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
			}
			catch (Throwable $e)
			{
				throw new foundation_fault('Could not save', '', $e);
			} // try
		} // SAVE()
	} // foundation_install
?>