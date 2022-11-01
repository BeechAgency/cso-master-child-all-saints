<?php
/**
 * beechnut functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package beechnut
 */


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
