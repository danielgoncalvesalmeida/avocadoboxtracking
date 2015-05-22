<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Saltgrain for SHA or MD5 whatever (40 chars lenght)
$config['salt'] = 'kdd9234ur736hJzefA8lSkjdKHGskLorqDH34098';

// The company name
$config['appcompany'] = 'Avocado';

// The application name
$config['appname'] = 'Boxtracking';

$config['use_https'] = FALSE;

// Default results per page
$config['results_per_page_default'] = 25;

/*
 	========================================

 	LOCALES
 	
 	========================================
*/

// Date format 
$config['format_date_human'] = 'j/m/Y';
// Date/Time format 
$config['format_datetime_human'] = 'j/m/Y G:h';
// Date/Time format 
$config['format_datetimeseconds_human'] = 'j/m/Y G:h:s';



/*
 	========================================

 	ASSETS MANAGEMENT
 	
 	========================================
*/


// Assets version -> force cache refresh from browser
$config['assets_version'] = '20150319001';

// Do not add as first character a '/' it is provided by the base_url and media servers
$config['assets_path'] = 'assets/';

// General purpose directory for images that are not requiered to be secure
$config['assets_img_path'] = 'assets/img/';



/*
 	========================================

 	SITE PERFORMANCE
 	
 	========================================
*/

// Use http multi channel (performance)
// Use empty array if not wanted
$config['media_servers'] = array();
//$config['media_servers'] = array('http://media1.dinokid.local/','http://media2.dinokid.local/','http://media3.dinokid.local/');