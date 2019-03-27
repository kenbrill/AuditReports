<?php
$module_name = 'Audit_Accounts';
$viewdefs[$module_name] =
	array(
		'base' =>
			array(
				'view' =>
					array(
						'list' =>
							array(
								'panels'  =>
									array(
										0 =>
											array(
												'label'  => 'LBL_PANEL_1',
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
														2 =>
															array(
																'name'    => 'date_modified',
																'enabled' => true,
																'default' => true,
															),
														3 =>
															array(
																'name'    => 'date_entered',
																'enabled' => true,
																'default' => true,
															),
													),
											),
									),
								'orderBy' =>
									array(
										'field'     => 'date_modified',
										'direction' => 'desc',
									),
							),
					),
			),
	);
