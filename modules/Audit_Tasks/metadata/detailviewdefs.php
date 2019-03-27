<?php
$module_name = 'Audit_Tasks';
$viewdefs[$module_name] =
	array(
		'DetailView' =>
			array(
				'templateMeta' =>
					array(
						'form'       =>
							array(
								'buttons' =>
									array(),
							),
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
