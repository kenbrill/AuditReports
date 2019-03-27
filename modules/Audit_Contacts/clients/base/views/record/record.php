<?php
$module_name = 'Audit_Contacts';
$viewdefs[$module_name] =
	array(
		'base' =>
			array(
				'view' =>
					array(
						'record' =>
							array(
								'buttons' =>
									array(
										0 =>
											array(
												'type'      => 'button',
												'name'      => 'cancel_button',
												'label'     => 'LBL_CANCEL_BUTTON_LABEL',
												'css_class' => 'btn-invisible btn-link',
												'showOn'    => 'edit',
												'events'    =>
													array(
														'click' => 'button:cancel_button:click',
													),
											),
										3 =>
											array(
												'name' => 'sidebar_toggle',
												'type' => 'sidebartoggle',
											),
									),
								'panels'  =>
									array(
										0 =>
											array(
												'name'   => 'panel_header',
												'label'  => 'LBL_RECORD_HEADER',
												'header' => true,
												'fields' =>
													array(
														0 =>
															array(
																'name'          => 'picture',
																'type'          => 'avatar',
																'width'         => 42,
																'height'        => 42,
																'dismiss_label' => true,
																'readonly'      => true,
															),
														1 => 'field_name',
														2 =>
															array(
																'name'          => 'favorite',
																'label'         => 'LBL_FAVORITE',
																'type'          => 'favorite',
																'readonly'      => true,
																'dismiss_label' => true,
															),
														3 =>
															array(
																'name'          => 'follow',
																'label'         => 'LBL_FOLLOW',
																'type'          => 'follow',
																'readonly'      => true,
																'dismiss_label' => true,
															),
													),
											),
									),
							),
					),
			),
	);
