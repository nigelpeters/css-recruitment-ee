<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed helper class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */

// --------------------------------------------------------------------

/**
 * Object to Array
 *
 * From a multi-dimensional object return a 
 * usable multi-dimensional array
 *
 * @param      array
 * @param 	   bool 
 * @return     array
 */
if ( ! function_exists('Seed_obj_to_array'))
{
	function Seed_obj_to_array($obj, $clean = FALSE, $convert = array() ) 
	{

	    if(is_object($obj)) $obj = (array) $obj;

	    if(is_array($obj)) {

	        $new = array();

	        foreach($obj as $key => $val) {

	        	if( $clean ) 
	        	{
		        	$key = str_replace( '-', '_', $key );

		        	if( isset( $convert[ $key ] ) ) $key = $convert[ $key ];
		        }

	            $new[$key] = Seed_obj_to_array($val, $clean);
	        }
	    }
	    else $new = $obj;

	    return $new;
	}
}

/**
 * Debug
 *
 * @param       mixed
 * @param       bool
 * @return      void
 */
if ( ! function_exists('dumper'))
{
	function dumper($var, $exit = TRUE)
	{
		echo '<pre>'.print_r($var, TRUE).'</pre>';

		if ($exit) exit;
	}
}


/**
 * Zebra table helper
 *
 * @param       bool
 * @return      string
 */
if ( ! function_exists('seed_row'))
{
	function seed_row($reset = FALSE)
	{
		static $i = 0;

		if ($reset) $i = 0;

		return (++$i % 2 ? 'odd' : 'even');
	}
}

// --------------------------------------------------------------

/* End of file Seed_helper.php */