<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Fieldtype Textarea class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_fieldtype_textarea extends Seed_fieldtype
{
	public $title = 'textarea';

	public $settings = array(
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
		)
	);

	public function generate( $field = array() )
	{
		$ret = '';

		// Generate some text within the bounds of the options
		if( $field['populate'] == 'sparse' )
		{
			// We don't want to always populate this. 
			if( rand( 1, 2 ) == 1 ) $ret = $this->_generate_filler( $field );
		}
		else $ret = $this->_generate_filler( $field );

		return $ret;
	}



	private function _generate_filler( $field = array() )
	{

		$ret = '';

		$length = rand( $field['from'], $field['to'] );
	
		$ret = ee()->seed_generator_model->generate_paragraphs( $length );

		return $ret;
	}
}