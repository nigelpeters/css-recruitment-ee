<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Channel Model class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_channel_model extends Seed_model {

	private $errors;
	private $field_settings;

	public $default_text_base = 'lorem';
	public $known_text_bases = array( 	'lorem',
										'kant',
										'cupcake',
										'bacon',
										'bluth',
										'space',
										'zombie' );

	public $known_fieldtypes = array(	'text',
										'textarea',										
										'wygwam',
										'playa',
										'matrix', );

	public $known_options = array(		'status',
										'structure',
										'category' );

	public $overridden_fieldtypes = array( 'rte' => 'wygwam' );

	public $has_settings = array( 'playa', 'matrix' );

	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access      public
	 * @return      void
	 */
	function __construct()
	{
		// Call parent constructor
		parent::__construct();

		// Initialize this model
		$this->initialize(
			'seed_channel',
			'seed_id',
			array()
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Installs given table
	 *
	 * @access      public
	 * @return      void
	 */
	public function install()
	{
		// Call parent install
		//parent::install();
	}


	// --------------------------------------------------------------------

	/**
	 * Takes a direct form submission, validates it, then runs
	 *
	 * @access      public
	 * @return      void
	 */
	public function seed()
	{
		// Set some basic states
		$this->errors = array();

		// Get the basics about this
		$channel_id = ee()->input->post('seed_channel');
		$seed_count = ee()->input->post('seed_count');
		$seed_text_base = ee()->input->post('seed_text_base');

		if( !is_numeric( $seed_count ) ) $this->errors( lang('seed_count_not_numeric') );

		// Check the channel_id is valid
		$this->channel = $this->_get_details( $channel_id );
		$this->_get_field_plugins();

		// Get the seed text base, or default to lorem
		$seed_text_base = $this->_get_text_base($seed_text_base);

		// Check we can continue
		if( !empty( $this->errors ) ) return $this->errors();

		//Collect channel options
		$this->channel_options = array();
		foreach( $this->known_options as $option )
		{
			$input_base = 'seed_option_'.$channel_id.'_'.$option;
			$value = ee()->input->post( $input_base );

			if( $value != '' AND !empty( $value ) )	$this->channel_options[ $option ] = $value;
		}

		// Now collect passed field settings
		$this->field_options = array();

		foreach( $this->channel['fields'] as $field_id => $field ) 
		{
			$input_base = 'seed_field_'.$channel_id.'_';
			$populate = ee()->input->post( $input_base . $field_id );


			if( $populate == 'always' OR $populate == 'sparse' )
			{
				// We'll be populating this field. Get the settings
				// Title gets special treatment
				$this->field_options[ $field_id ] = $this->_get_field_options( $field_id, $input_base );
				$this->field_options[ $field_id ]['populate'] = $populate;
			}
		}

		// Check we can continue
		if( !empty( $this->errors ) ) return $this->errors;

		// Looks ok, go to generate
		$seed['channel_id'] 		= $channel_id;
		$seed['seed_count']			= $seed_count;
		$seed['field_options'] 		= $this->field_options;
		$seed['channel_options'] 	= $this->channel_options;
		$seed['seed_text_base']		= $seed_text_base;
		
		$results = $this->_generate( $seed );

		if( !empty( $this->errors ) ) return $this->errors;

		return TRUE;
	}


	private function _get_text_base($text_base)
	{	
		if( in_array($text_base, $this->known_text_bases) ) return $text_base;

		return $this->default_text_base;
	}


	private function _get_field_plugins()
	{
		// We use this to first filter down the list of needed fields 
		// so we can get the plugin list lazily 

		$plugin_list = array();

		foreach( $this->channel['fields'] as $field )
		{
			$field_type = $field['field_type'];

			// This field may be being overridden 
			if( array_key_exists( $field_type, $this->overridden_fieldtypes ) )
			{
				$field_type = $this->overridden_fieldtypes[ $field_type ];
			}

			$plugin_list[] = $field_type;
		}


		$plugin_list = array_unique( $plugin_list );

		$this->plugins = $this->get_plugins( $plugin_list );

		// Also get the full list of channel options
		$this->options = $this->get_options( $this->known_options );

		return;
	}


	private function _get_field_options( $field_id, $input_base = '' )
	{
		if( $input_base == '' ) 
		{
			$this->errors[] = lang('seed_error_field_settings_not_passed');
			return array();
		}

		// Get the field_type, the specific settings depend on this
		if( !isset( $this->channel['fields'][$field_id] ) ) 
		{
			$this->errors[] = lang('seed_error_invalid_field_id');
			return array();
		}

		$field = $this->channel['fields'][$field_id];

		if( !isset( $this->plugins[ $field['field_type'] ] ) )
		{
			// This field may be being overridden 
			if( array_key_exists( $field['field_type'], $this->overridden_fieldtypes ) )
			{
				$field['field_type'] = $this->overridden_fieldtypes[ $field['field_type'] ];
			}
			else
			{
				// Default unknown field types to text
				$field['field_type'] = 'text';
			}

		}

		$options = array();

		// Check the required fields were passed
		foreach( $this->plugins[ $field['field_type'] ]['settings'] as $setting )
		{
			// Build the field_name
			$passed_input_name = $input_base . $field_id . '_' . $setting['name'];
			$value = ee()->input->post( $passed_input_name );

			if( $setting['required'] === TRUE AND $value == '' )
			{
				$this->errors[] = lang('seed_error_missing_required_value');
			}

			$options[ $setting['name'] ] = $value;
			if( isset( $setting['count'] ) ) $options['count'] = $setting['count'];

		}

		// Pass this over to the seed fieldtype for additional handling if requried
		$extra = ee()->seed_plugins->$field['field_type']->handle_post( $this->plugins, $field, $input_base );

		$options['extra'] = $extra;
		$options['field_type'] = $field['field_type'];
		$options['field_name'] = $field['field_name'];


		return $options;
	}


	private function _get_details( $channel_id = 0 )
	{
		$channels = array();

		// --------------------------------------
		// Get channels and searchable fields
		// --------------------------------------

		$results  = ee()->db->select('c.channel_id, c.channel_title, f.*')
					->from('channels c')
					->join('channel_fields f', 'c.field_group = f.group_id', 'left')
					->where('c.site_id', '1')
					//->where('c.channel_id', $channel_id)
			       	->order_by('c.channel_title', 'asc')
			       	->order_by('f.field_order', 'asc')
					->get()
					->result_array();

		foreach( $results as $row )
		{
			// Remember channel title
			$channels['title'] = $row['channel_title'];

			// Add 'Title' to fields while we're here
			if ( ! isset($channels['fields']))
			{
				$channels['fields'][0] = array('field_label'=>lang('title'), 'field_name'=>'title', 'is_title' => TRUE, 'field_required'=>'y', 'field_maxl' => '100', 'field_type' => 'text' );
			}

			// Add custom fields to this channel
			$channels['fields'][$row['field_id']] = $row;
		}


		if( empty( $channels ) ) return FALSE;

		return $channels;	
	}



	private function _generate( $seed = array() )
	{
		if( empty( $seed ) ) return;

		//ee()->load->library('api/Api_channel_entries');
		// We have the seed. Go a head and generate

		ee()->load->library('api');
		ee()->api->instantiate('channel_entries');
		ee()->api->instantiate('channel_fields');


		$data['author_id'] 			= ee()->session->userdata('member_id');
		$data['entry_date'] 		= ee()->localize->now;

		$meta = array();

		// Setup the generator class with our chosen textbase
		ee()->seed_generator_model->set_base($seed['seed_text_base']);

		ee()->api_channel_fields->setup_entry_settings($seed['channel_id'], array());

		// Loop this for as many times as we need to create
		// as many entries from the input
		for( $i = 0; $i < $seed['seed_count']; $i++ )
		{
			foreach( $seed['channel_options'] as $option_name => $option_value )
			{	
				$value = ee()->seed_options->$option_name->generate( $option_value );

				if( $value !== FALSE ) $data[ $option_name ] = $value;
			}

			foreach( $seed['field_options'] as $field_id => $field ) 
			{
				// Field_id 0 is the title
				if( $field_id == 0 ) 
				{
					$field_name = 'title';
				}
				else
				{
					$field_name = 'field_id_'.$field_id;
				}

				$field['seed_count'] = $i;
				$field['field_id'] = $field_id;
				$field['channel_id'] = $seed['channel_id'];

				// Pass the generation over to the specific field type
				$data[ $field_name ] = ee()->seed_plugins->$field['field_type']->generate( $field );
			}


			if( ee()->api_channel_entries->submit_new_entry( $seed['channel_id'], $data ) === FALSE )
			{
				$this->errors = ee()->api_channel_entries->get_errors();
				return FALSE;
			}

			$entry_id = ee()->api_channel_entries->entry_id;


			// Now wrap up an post save data we need
			foreach( $seed['channel_options'] as $option_name => $option_value )
			{				
				ee()->seed_options->$option_name->post_save( $entry_id, $data, $option_value );
			}

			foreach( $seed['field_options'] as $field_id => $field ) 
			{

				$field['seed_count'] = $i;
				$field['field_id'] = $field_id;
				$field['channel_id'] = $seed['channel_id'];

				ee()->seed_plugins->$field['field_type']->post_save( $entry_id, $data, $field );				
			}

		}

		return TRUE;
	}


	public function get_field_view( $type = 'text', $channel_id, $field_id, $field, $cell = array() )
	{	
		$is_unknown = FALSE;
		$is_overridden = FALSE;
		$is_cell = FALSE;
		$has_post_save = FALSE;

		if( !empty( $cell ) ) $is_cell = TRUE;

		if( array_key_exists( $type, $this->overridden_fieldtypes ) )
		{
			$type = $this->overridden_fieldtypes[ $type ];
			$is_overridden = TRUE;
		}


		if( !in_array( $type, $this->known_fieldtypes ) )
		{
			$is_unknown = TRUE;
			$type = 'text';
		}

		$settings = array();



	
		if( !isset( ee()->seed_plugins ) ) 
		{
			// Get the settings only if we need them
			$this->channel = $this->_get_details( $channel_id );
			$this->_get_field_plugins();

			ee()->load->library('api');
			ee()->api->instantiate('channel_entries');
			ee()->api->instantiate('channel_fields');

			ee()->api_channel_fields->setup_entry_settings($channel_id, array());
		}

		if( in_array( $type, $this->has_settings ) )
		{
			// Get the settings for this fieldtype
			$settings = ee()->seed_plugins->$type->get_settings( $field_id );
		}

		$data = array( 'channel_id' 	=> $channel_id,
						'field_id' 		=> $field_id,
						'field' 		=> $field,
						'is_unknown' 	=> $is_unknown,
						'is_overridden' => $is_overridden,
						'settings' 		=> $settings,
						'is_cell'		=> $is_cell,
						'cell'			=> $cell );

		$view = ee()->load->view( '../fieldtypes/'.$type.'/options', $data, TRUE);
		
		return $view;

	}




	public function get_option_view( $type = '', $channel_id, $option )
	{	
		if( $type == '' ) return '';

		$data = array( 'channel_id' 	=> $channel_id,
						'option' 		=> $option,);

		$view = ee()->load->view( '../options/'.$type.'/view', $data, TRUE);
		
		return $view;

	}


} // End class

/* End of file Seed_project_model.php */