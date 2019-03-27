<?php
$module_name = 'Audit_Bugs';
$viewdefs[$module_name] =
	array(
		'EditView' =>
			array(
				'templateMeta' =>
					array(
						'maxColumns' => '2',
						'widths'     =>
							array(
								0 =>
									array(
										'label' => '10',
										'field' => '30',
									),
								1 =>
									array(
										'label' => '10',
										'field' => '30',
									),
							),
					),
				'panels'       =>
					array(
						'default' =>
							array(),
					),
			),
	);
