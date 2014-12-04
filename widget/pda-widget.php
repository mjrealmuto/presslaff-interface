<?php
	
	include_once();

	
	class Presslaff_Widget extends WP_Widget {
		
		function __construct( )
		{
			parent::__construct(
				'Presslaff_FP_Widget',
				__('Presslaff Widget', 'text_domain'),
				array( 'description' => __('Widget to display contest links on Front Page', 'text_domain')	
			);
			
			//set presslaff object
			
			//get contest slug
			
		}
		
		public function widget( $args, $instance )
		{
			
			
			
		}
		
		
		
		
	}