<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Fieldtype File class
 *
 * @package         seed_ee_addon
 * @version         0.8.1
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_fieldtype_file extends Seed_fieldtype
{
	public $title = 'file';
	
	public $settings = array(
		array(
			'name' 			=> 'values',
			'required' 		=> TRUE,
			'type' 			=> 'text',
			'default'		=> 'generated'
		),		
		array(
			'name' 			=> 'from',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default'		=> 2
		),		
		array(
			'name' 			=> 'to',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default' 		=> 6
		),
		array(
			'name' 			=> 'max',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default'		=> 126
		),
		array(
			'name' 			=> 'case',
			'required' 		=> TRUE,
			'type' 			=> 'text',
			'default'		=> 'ucwords'
		),
		array(
			'name' 			=> 'set',
			'required' 		=> FALSE,
			'type' 			=> 'text',
			'default'		=> ''
		)
	);

	public function generate( $field = array() )
	{
		$ret = '';

		// Generate some text within the bounds of the options
		if( $field['populate'] == 'sparse' )
		{
			// We don't want to always populate this. 
			if( rand( 1, 2 ) == 2 ) return '';
		}
			
		// What type of generation are we performing?
		if( $field['values'] == 'specific') 
		{
			// Only return values from a specific set of possible 
			$ret = $this->_generate_specific( $field );
		}
		else
		{
			// Go ahead and generate the text from a random source
			$ret = $this->_generate_filler( $field );
		}


		return $ret;
	}


	private function _generate_specific( $field = array() )
	{
		$ret = '';

		// Get the passed values from the specific field
		$possible = $field['set'];

		if( trim( $possible ) == '' ) return $ret;

		// Split the possible field by newlines
		$possible = explode( "\n", $possible );
		if( count( $possible ) < 1 ) return $ret;
		elseif( count( $possible ) == 1 ) return current( $possible );


		// Now pick one of the values at random
		$ret = $possible[ rand( 0, count( $possible ) - 1) ];

		return $ret;
	}

	private function _generate_filler( $field = array() )
	{

		$ret = '';

		$length = rand( $field['from'], $field['to'] );
	
		$ret = ee()->seed_generator_model->generate_words( $field['max'], $length );

		// Now do some case management 	
		switch( $field['case'] ) {
			case 'ucwords' :
				$ret = ucwords( $ret );
				break;
			case 'lowercase' :
				$ret = strtolower( $ret );
				break;
			case 'uppercase' : 
				$ret = strtoupper( $ret );
				break;
			case 'ucfirst' :
			default :
				$ret = ucfirst( $ret );
				break;
		}

		return $ret;
	}
}