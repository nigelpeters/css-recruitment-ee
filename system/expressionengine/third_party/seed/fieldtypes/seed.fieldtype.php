<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Abstract Field class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
abstract class Seed_fieldtype extends Seed_model
{
	public $title = 'default';
	
	public $settings = array(
		array(
			'name' => 'amount_off',
			'short_name' => 'amount_off',
			'note' => 'amount_off_note',
			'type' => 'text'
		)
	);


	public function initialize($plugin_settings = array())
	{
		if (is_array($plugin_settings))
		{
			$this->plugin_settings = $plugin_settings;
		}
		
		$this->type = get_class($this);
		
		return $this;
	}

	public function handle_post( $plugins, $settings = array(), $input_base = '' )
	{
		return array();
	}

	public function post_save( $entry_id, $data, $field ) 
	{
		return;
	}
}
