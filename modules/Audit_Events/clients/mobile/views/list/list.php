<?php
$module_name = 'Audit_Accounts';
$viewdefs[$module_name] =
	array(
		'mobile' =>
			array(
				'view' =>
					array(
						'list' =>
							array(
								'panels' =>
									array(
										0 =>
											array(
												'label'  => 'LBL_PANEL_DEFAULT',
												'fields' =>
													array(
														0 =>
															array(
																'name'    => 'name',
																'label'   => 'LBL_NAME',
																'default' => true,
																'enabled' => true,
																'link'    => true,
															),
														1 =>
															array(
																'name'    => 'assigned_user_name',
																'label'   => 'LBL_ASSIGNED_TO_NAME',
																'default' => true,
																'enabled' => true,
																'link'    => true,
															),
													),
											),
									),
							),
					),
			),
	);
