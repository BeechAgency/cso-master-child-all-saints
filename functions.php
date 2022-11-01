<?php 

$GLOBALS['CHILD_THEME_COLORS'] = array(
	'white' => 'ffffff',
	'black' => '000000',
	'primary-dark' => '003B5A',
	'primary-light' => '0079A3',
	'secondary-dark' => 'D89700',
	'secondary-light' => 'FAEAC8',
	'warning' => 'C92D2D',
	'success' => '2DC98D'
);


add_action( 'wp_enqueue_scripts', 'enqueue_parent_styles' );

function enqueue_parent_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}


/* Default brand colors for MCE color picker */
function csomaster_mce4_options($init) {

	// Loop through THEME_COLORS and add them to the MCE color picker
	$THEME_COLORS = $GLOBALS['CHILD_THEME_COLORS'];

	$custom_colours = "";

	foreach($THEME_COLORS as $name => $hex) {
		$custom_colours .= "'$hex',' $name',";
	}

    // build colour grid default+custom colors
    $init['textcolor_map'] = '['.$custom_colours.']';

    // change the number of rows in the grid if the number of colors changes
    // 8 swatches per row
    $init['textcolor_rows'] = 1;

    return $init;
}
add_filter('tiny_mce_before_init', 'csomaster_mce4_options');
