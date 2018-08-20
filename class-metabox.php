<?php

defined( 'ABSPATH' ) or exit();

include_once 'trait-wp-auto-hooks.php';

if ( !class_exists( 'wpMetabox' ) && trait_exists( 'wpAutoHooks' ) ) :
	
	abstract class wpMetabox {
		use wpAutoHooks;
		
		const TEMP_PLACEHOLDER = 'Please, extend %s %s in a %s class.';
		const DEFAULT_CONTEXT  = 'advanced';
		const CONTEXTS         = [ 'normal', self::DEFAULT_CONTEXT, 'side' ];
		const PRIORITIES       = [ 'high', 'core', 'default', 'low' ];
		
		const ID       = '';
		const TITLE    = self::TEMP_PLACEHOLDER;
		const SCREENS  = ['post'];
		const CONTEXT  = self::DEFAULT_CONTEXT;
		const PRIORITY = 'default';
		
		public static function get_priority() {
			$priority = 'default';
			
			if ( in_array( static::PRIORITY, static::PRIORITIES ) ) {
				$priority = static::PRIORITY;
			} elseif ( static::PRIORITY === 'lowest' ) {
				$priority = static::PRIORITIES[ count( static::PRIORITIES ) - 1 ];
			} elseif ( static::PRIORITY === 'highest' ) {
				$priority = static::PRIORITIES[0];
			}
			
			return $priority;
		}
		
		public static function add() {
			
			if ( !is_admin() ) { return; }
			
			self::static_connect();
			
			$_ = __( 'Please, extend %s %s in a %s class.', 'marale-wp-connections' );
		}
		
		public static function add_meta_box() {
			
			static $added = FALSE;
			if ( isset( $added[ static::class ] ) ) { return; }
			
			
			
			foreach ( static::SCREENS as $screen ) {
				add_meta_box(
						static::ID,    // Unique ID
						
						sprintf( 
								__( static::TITLE, 'marale-wp-connections' ), 
								self::class.'::TITLE', 
								__( 'constant', 'marale-wp-connections' ),
								static::class
							),          // Box title
						
						static::class . '::html',   // Content callback
						
						$screen,		// Post type
						
						!in_array( static::CONTEXT, static::CONTEXTS ) ? 
							static::DEFAULT_CONTEXT : static::CONTEXT,
						self::get_priority()
					);
			}
			
			$added[ static::class ] = TRUE;
		}
		
		public static function html() {
			echo sprintf( 
					__( self::TEMP_PLACEHOLDER, 'marale-wp-connections' ), 
					self::class . '::' . __FUNCTION__, 
					__( 'method', 'marale-wp-connections' ),
					static::class
				);
		}
		
		public static function add_meta_boxes_wpaction() {
			self::hook_check(__FUNCTION__);
			static::add_meta_box();
		}
	}
	
	abstract class wpDashboardMetabox extends wpMetabox {
		use wpAutoHooks;
		
		const DEFAULT_CONTEXT = 'normal';
		const CONTEXTS = [ self::DEFAULT_CONTEXT, 'side' ];
		const SCREENS  = ['dashboard'];
		
		public static function add() {
			
			if ( static::SCREENS !== self::SCREENS ) {
				parent::static_connect();
			}
			
			self::static_connect();
		}
		
		public static function wp_dashboard_setup_wpaction() {
			self::hook_check(__FUNCTION__);
			static::add_meta_box();
		}
	}	
	
endif;