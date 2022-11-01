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




/* 
 * Automatic theme updates from the GitHub repository
 * Care of https://gist.github.com/slfrsn/a75b2b9ef7074e22ce3b. modified by me
 */ 

add_filter('pre_set_site_transient_update_themes', 'cso_child_automatic_GitHub_updates', 100, 1);

function cso_child_automatic_GitHub_updates($data) {
  // Theme information
  $theme   = get_stylesheet(); // Folder name of the current theme
  $current = wp_get_theme()->get('Version'); // Get the version of the current theme
  // GitHub information
  $user = 'BeechAgency'; // The GitHub username hosting the repository
  $repo = 'cso-master-child-all-saints'; // Repository name as it appears in the URL
  // Get the latest release tag from the repository. The User-Agent header must be sent, as per
  // GitHub's API documentation: https://developer.github.com/v3/#user-agent-required
  $file = json_decode(file_get_contents('https://api.github.com/repos/'.$user.'/'.$repo.'/releases/latest', false,
      	stream_context_create(
			['http' => ['header' => "User-Agent: ".$user."\r\n"],
			'ssl' => ["verify_peer"=>false, "verify_peer_name"=>false]]
		)
  ));
  if($file) {
	$update = filter_var($file->tag_name, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    // Only return a response if the new version number is higher than the current version
    if($update > $current) {
  	  $data->response[$theme] = array(
	      'theme'       => $theme,
	      // Strip the version number of any non-alpha characters (excluding the period)
	      // This way you can still use tags like v1.1 or ver1.1 if desired
	      'new_version' => $update,
	      'url'         => 'https://github.com/'.$user.'/'.$repo,
	      'package'     => $file->zipball_url,
      );
    }
  }
  return $data;
}
