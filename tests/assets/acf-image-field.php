<?php

if ( function_exists( 'acf_add_local_field_group' ) ):
	acf_add_local_field_group( [
		'key'                   => 'group_5ee13f982e210',
		'title'                 => 'Image Test',
		'fields'                => [
			[
				'key'               => 'field_5ee13f9dcdb6d',
				'label'             => 'Image',
				'name'              => 'image',
				'type'              => 'image',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'return_format'     => 'id',
				'preview_size'      => 'large',
				'library'           => 'all',
				'min_width'         => '',
				'min_height'        => '',
				'min_size'          => '',
				'max_width'         => '',
				'max_height'        => '',
				'max_size'          => '',
				'mime_types'        => '',
			],
		],
		'location'              => [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => 'post',
				],
			],
		],
		'menu_order'            => 0,
		'position'              => 'normal',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => '',
		'active'                => true,
		'description'           => '',
	] );
endif;
