<?php
/**
 * AR2 PostViews Class.
 * Adapted from WordPress Customize subpackage.
 *
 * @package AR2
 * @subpackage PostViews
 * @since 2.0
 */

final class AR2_PostViews {

	protected $zones = array();
	protected $sections = array();
	protected $display_types = array();
	
	/**
	 * Constructor.
	 * @since 2.0
	 */
	public function __construct() {
	
		require_once 'classes/postviews-section.php';
		require_once 'classes/postviews-zone.php';
		
		add_action( 'wp_loaded', array( $this, 'wp_loaded' ), 5 );
		
		add_action( 'ar2_postviews_register', array( $this, 'register_zones' ) );
		add_action( 'ar2_postviews_register', array( $this, 'register_sections' ) );
		
	}
	
	/**
	 * Registers zones and sections when WordPress is loaded.
	 * @since 2.0
	 */
	public function wp_loaded() {
	
		do_action( 'ar2_postviews_register', $this );
		
	}
	
	/**
	 * Adds a new zone.
	 * @since 2.0
	 */
	public function add_zone( $id, $args = array() ) {
	
		if ( is_a( $id, 'AR2_PostViews_Zone' ) )
			$zone = $id;
		else
			$zone = new AR2_PostViews_Zone( $this, $id, $args );
		
		$this->zones[ $zone->id ] = $zone;
		
	}
	
	/**
	 * Retrieves an existing zone as an AR2_PostViews_Zone object.
	 * @since 2.0
	 */
	public function get_zone( $id ) {
	
		if ( isset( $this->zones[ $id ] ) )
			return $this->zones[ $id ];
			
	}
	
	/**
	 * Renders an existing zone in HTML.
	 * @since 2.0
	 */
	public function render_zone( $id ) {
		if ( is_a ( $id, 'AR2_PostViews_Section' ) ) {
		
			$id->render();
		} else {
			if ( isset( $this->zones[ $id ] ) )
				$this->zones[ $id ]->render();		
		}
		
	}
	
	/**
	 * Removes an existing zone.
	 * @since 2.0
	 */
	public function remove_zone( $id ) {
	
		unset( $this->zones[ $id ] );
		
	}
	
	/**
	 * Adds a new post section.
	 * @since 2.0
	 */
	public function add_section( $id, $zone = '_void', $args = array() ) {
	
		if ( is_a( $id, 'AR2_PostViews_Section' ) )
			$section = $id;
		else
			$section = new AR2_PostViews_Section( $this, $id, $zone, $args );
		
		$this->sections[ $section->id ] = $section;
		$this->get_zone( $zone )->sections[] = &$this->sections[ $section->id ];
		
	}
	
	/**
	 * Retrieves an existing post section as an AR2_PostViews_Section object.
	 * @since 2.0
	 */
	public function get_section( $id ) {
	
		if ( isset( $this->sections[ $id ] ) )
			return $this->sections[ $id ];
			
	}
	
	/**
	 * Retrives existing post sections from a specified zone.
	 * @since 2.0
	 */
	public function get_sections_from_zone( $zone_id ) {
	
		$result = array();
		
		foreach ( $this->sections as $id => $obj ) {
			if ( $obj->zone->id == $zone_id )
				$result[ $id ] = $obj;
		}
		
		// Sort the sections based on priority.
		uasort( $result, array( $this, '_cmp_priority' ) );
		
		return $result;
		
	}
	
	/**
	 * Removes an existing post section.
	 * @since 2.0
	 */
	public function remove_section( $id ) {
	
		unset( $this->sections[ $id ] );
		
	}
	
	/**
	 * Renders an existing post section in HTML.
	 * @since 2.0
	 */
	public function render_section( $id ) {
		
		if ( is_a( $id, 'AR2_PostViews_Section' ) )
			$id->render();
		else if ( isset( $this->sections[ $id ] ) )
			$this->sections[ $id ]->render();
			
	}
	
	
	/**
	 * Registers a display type as an option for users.
	 * @since 2.0
	 */
	public function register_display_type( $id, $label ) {
		
		$this->display_types[ $id ] = $label;
		
	}
	
	
	/**
	 * Unregisters a display type as an option.
	 * @since 2.0
	 */
	public function unregister_display_type( $id ) {
		
		unset( $this->display_types[ $id ] );
		
	}
	
	
	/**
	 * Lists all registered display types as an array.
	 * @since 2.0
	 */
	public function list_display_types() {
		
		return $this->display_types;
		
	}
	
	/**
	 * Helper function to compare two objects by priority.
	 * Taken from WordPress' WP_Customize class.
	 *
	 * @since 3.4.0
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 */
	private function _cmp_priority( $a, $b ) {
	
		$ap = $a->priority;
		$bp = $b->priority;

		if ( $ap == $bp )
			return 0;
		return ( $ap < $bp ) ? 1 : -1;
		
	}
	
	/**
	 * Registers default post sections.
	 * @since 2.0
	 */
	public function register_sections() {
		

		$this->add_section( 'slideshow', 'home', array (
			'label'		=> __( 'Slideshow', 'ar2' ),
			'title'		=> __( 'Slideshow', 'ar2' ),
			'type'		=> 'slideshow',
			'count'		=> 3,
			'priority'	=> 5,
			'display_types' => array( 'slideshow' ),
			'enabled'	=> true,
		) );
		
		$this->add_section( 'featured-posts-1', 'home', array (
			'label'		=> __( 'Featured Posts #1', 'ar2' ),
			'title'		=> __( 'Featured Posts', 'ar2' ),
			'type'		=> 'node',
			'count'		=> 3,
			'priority'	=> 4,
			'enabled'	=> true,
		) );
		
		$this->add_section( 'featured-posts-2', 'home', array (
			'label'		=> __( 'Featured Posts #2', 'ar2' ),
			'title'		=> __( "Editors' Picks", 'ar2' ),
			'type'		=> 'quick',
			'count'		=> 3,
			'priority'	=> 3,
			'enabled'	=> true,
		) );
		
		$this->add_section( 'news-posts', 'home', array (
			'label'				=> __( 'News Posts', 'ar2' ),
			'title'				=> __( 'Latest News', 'ar2' ),
			'type'				=> 'line',
			'use_query_posts'	=> true,
			'count'				=> get_option( 'posts_per_page' ),
			'priority'			=> 2,
			'enabled'			=> true,
		) );
		
	}
	 
	/**
	 * Register default zones.
	 * @since 2.0
	 */
	public function register_zones() {
		
		$this->add_zone( '_void', array (
			'show_in_theme_options' => false,
			'show_in_customize'		=> false,
			'_builtin'				=> false,
			'persistent'			=> null,
		) );
		
		$this->add_zone( 'home', array (
			'label'					=> __( 'Home', 'ar2' ),
			'description'			=> __( 'Handles all content being displayed in the front page (excluding the slideshow).', 'ar2' ),
			'show_in_theme_options'	=> true,
			'show_in_customize'		=> true,
			'_builtin'				=> true,
		) );
		
	}
	
}

/**
 * Helper function to render zones.
 * @since 2.0
 */
function ar2_render_zone( $id ) {

	global $ar2_postviews;
	$ar2_postviews->render_zone( $id );
	
}

/**
 * Helper function to render sections.
 * @since 2.0
 */
function ar2_render_section( $id ) {
	
	global $ar2_postviews;
	$ar2_postviews->render_section( $id );
	
}

/**
 * Mega-helper function to render posts, preferably archives.
 * @since 2.0
 */
function ar2_render_posts( $query = null, $args = array(), $show_nav = false ) {

	$_defaults = array (
		'type'				=> 'traditional',
		'count'				=> get_option( 'posts_per_page' ),
		'title'				=> null,
		'use_query_posts'	=> true,
		'enabled'			=> true,
		'persistent'		=> false,
	);
	
	$args = wp_parse_args( $args, $_defaults );

	$section = new AR2_PostViews_Section( null, 'archive-posts', null, $args );
	ar2_render_section( $section );

	if ( $show_nav && $section->query->max_num_pages > 1 )
		ar2_post_navigation();

}

global $ar2_postviews;
$ar2_postviews = new AR2_PostViews();
 
/* End of file ar2-postviews.php */
/* Location: ./library/classes/ar2-postviews.php */