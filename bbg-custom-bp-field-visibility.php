<?php

class BBG_CPBV {
	function __construct() {
		add_filter( 'bp_xprofile_get_visibility_levels', array( &$this, 'modify_levels' ) );
		add_filter( 'bp_xprofile_get_hidden_fields_for_user', array( &$this, 'define_hidden_fields' ), 10, 3 );
	}
	
	/**
	 * Here's where we add custom levels, or remove levels we don't want
	 *
	 * @param array $levels
	 * @return array $levels
	 */
	function modify_levels( $levels ) {
		// Remove the 'friends' level, if it exists
		if ( isset( $levels['friends'] ) ) {
			unset( $levels['friends'] );
		}
		
		// Add a new 'Admins Only' level
		if ( !isset( $levels['admins-only'] ) ) {
			$levels['admins-only'] = array(
				'id' => 'admins-only',
				'label' => __( 'Admins Only', 'textdomain' )
			);
		}
		
		return $levels;
	}
	
	/**
	 * Here is where we define the actual levels for custom levels, in this case Admins Only
	 *
	 * The technique is this: Pull up a list of the visibility levels set by the user. Loop
	 * through them, looking for fields that match one of our new levels (in this case, 'Admins
	 * Only'). In each of these cases, do our necessary Admins Only check - if the current
	 * user doesn't pass the test (because he's not a Super Admin) then the field id gets
	 * added to the list of hidden fields, which are then returned from the function.
	 */
     function define_hidden_fields( $hidden_fields, $displayed_user_id, $current_user_id ) {
		
		// Get the displayed user's visibility settings
		//$user_visibility_levels = bp_get_user_meta( $displayed_user_id, 'bp_xprofile_visibility_levels', true );
		$hidden_levels = array( 'admins-only' );
		$res = bp_xprofile_get_fields_by_visibility_levels( $displayed_user_id, $hidden_levels );
		if ( is_super_admin( $current_user_id ) ) {
			$res = array();
		}
		$hidden_fields = array_merge((array)$hidden_fields, (array)$res);
		return $hidden_fields;

	}
	
	
}
new BBG_CPBV;

?>
