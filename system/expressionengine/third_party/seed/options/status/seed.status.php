<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Option Status class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_option_status extends Seed_option
{
	public $title = 'status';

	public $settings = array(
		array(
			'name' 			=> 'from',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default'		=> 2
		)

	);

	public function generate( $options = array() ) 
	{
		if( empty( $options ) ) return FALSE;

		if( count( $options ) < 2 ) return current( $options );

		$ret = $options[ rand( 0, count( $options ) -1 ) ];

		return $ret;
	}

}