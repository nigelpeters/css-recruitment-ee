<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Fieldtype Matrix class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_fieldtype_matrix extends Seed_fieldtype
{
	public $title = 'matrix';
	
	public $settings = array(
		array(
			'name' 			=> 'from',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default'		=> 1
		),		
		array(
			'name' 			=> 'to',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default' 		=> 3
		)
	);
	private $channels;
	private $statuses;
	private $authors;
	private $ids_used = array();
	private $plugins;
	private static $row_data;

	public function get_settings( $field_id = 0 )
	{
		$return = array();

		if( $field_id == 0 ) return $return;
		if( !isset( ee()->api_channel_fields->settings[ $field_id ] ) ) return $return;


		$settings = ee()->api_channel_fields->settings[ $field_id ]['field_settings'];
		$settings = unserialize( base64_decode( $settings ) );

		if( !is_array( $settings ) ) return array();

		return $settings;
	}


	public function handle_post( $plugins, $field = array(), $input_base = '' )
	{
		$this->plugins = $plugins; 
		$extra = array();

		$settings = unserialize( base64_decode( $field['field_settings'] ) );

		$cells = ee()->seed_plugins->$field['field_type']->get_cell_types( $settings );

		//die('<pre>'.print_R($field,1).print_R($cells,1));
		// Loop over the cells and get any passed values
		foreach( $cells as $cell ) 
		{
			// Each cell needs to get it's post values handled
			$base = $field['field_id'] . '_cell_' . $cell['col_id'];
			$options = array();
			// Now we need the specific plugin settings
			foreach( $this->plugins[ $cell['col_type'] ]['settings'] as $setting )
			{
				// Build the field_name
				$passed_input_name = $input_base . $base . '_' . $setting['name'];
				$value = ee()->input->post( $passed_input_name );

				if( $setting['required'] === TRUE AND $value == '' )
				{
					$this->errors[] = lang('seed_error_missing_required_value');
				}

				$options[ $setting['name'] ] = $value;
				if( isset( $setting['count'] ) ) $options['count'] = $setting['count'];
			}

			$extra[ $cell['col_id'] ]['cell'] = $cell;
			$extra[ $cell['col_id'] ]['options'] = $options;

		}

		return $extra;

	}

	public function get_cell_types( $field = array() )
	{
		// We need to query the matrix_cols table to get the cell data

		$cells = ee()->db->where_in('col_id', $field['col_ids'] )
								->order_by('col_order', 'asc')
								->get('matrix_cols')
								->result_array();

		// We need to cleanup any cell types and revert unknowns and overridded items
		ee()->seed_channel_model = new Seed_channel_model();

		foreach( $cells as $key => $cell )
		{
			$cells[ $key ]['col_type'] = $this->_overload_cells( $cell['col_type']);
			// Decode the cell settings
			$cells[ $key ]['col_settings'] = unserialize( base64_decode( $cell['col_settings'] ) );
		}

		return $cells;
	}

	private function _overload_cells( $type )
	{
		if( array_key_exists( $type, ee()->seed_channel_model->overridden_fieldtypes ) )
		{
			$type = ee()->seed_channel_model->overridden_fieldtypes[ $type ];
		}

		if( !in_array( $type, ee()->seed_channel_model->known_fieldtypes ) )
		{
			$type = 'text';
		}

		return $type;
	}


	public function generate( $field = array() )
	{
		$ret = '';
		$this->row_data = array();

		// Generate some text within the bounds of the options
		if( $field['populate'] == 'sparse' )
		{
			// We don't want to always populate this. 
			if( rand( 1, 2 ) == 2 ) return $ret;
		}
			
		$settings = $this->get_settings( $field['field_id'] );

		// First figure out how many row we want
		$min = ( $field['from'] < $settings['min_rows'] ? $settings['min_rows'] : $field['from'] );
		$max = ( $field['to'] < $settings['max_rows'] ? $settings['max_rows'] : $field['to'] );

		$row_count = rand( $min, $max );

		// Anything to do?
		if( $row_count < 1 ) return $ret;

		$rows = array();

		for( $i=0; $i < $row_count; $i++)
		{
			$row = array();

			// Loop over the cells
			foreach( $field['extra'] as $cell_id => $cell )
			{
				// What type of cell is this?
				$type = $cell['cell']['col_type'];
				$cell_extra = array();

				// Do we have a way to generate content for this cell?

				// Is this cell required? 
				$cell_extra = $cell['options'];
				$cell_extra['populate'] = 'always';
				$cell_extra['field_id'] = $field['field_id'];
				$cell_extra['cell_id'] = $cell_id;

				$cell_data = ee()->seed_plugins->$type->generate( $cell_extra	 );

				$row[ $cell_id ] = $cell_data;
			}

			$rows[] = $row;
		}

		// Ok, we have to do this. 
		if( empty( $rows ) ) return $ret;

		$this->row_data = $rows;

		return '1';
	}

	public function post_save( $entry_id, $data, $field )
	{
		$this->_save_to_db( $entry_id, $this->row_data, $field);
	}

	private function _save_to_db( $entry_id, $rows, $field )
	{
		$row_order = 1;

		foreach( $rows as $row )
		{
			$data = array();
			
			$data['site_id'] = ee()->config->item('site_id');
			$data['field_id'] = $field['field_id'];
			$data['entry_id'] = $entry_id;

			$data['row_order'] = $row_order++;

			foreach( $row as $cell_id => $cell )
			{
				if( !is_array( $cell ) ) $data['col_id_'.$cell_id] = $cell;
				else
				{
					// this might be a playa field
					if( isset( $field['extra'][ $cell_id ]['cell']['col_type'] ) AND $field['extra'][ $cell_id ]['cell']['col_type'] == 'playa' )
					{
						$this->_save_rels( $cell );
					}
				}

				// Playa special handling
/*
				// save the changes
				$keywords = $this->_save_rels($selections, $data);

				// save the keywords in exp_matrix_data
				ee()->db->where('row_id', $this->settings['row_id'])
		             ->update('matrix_data', array($this->settings['col_name'] => $keywords));*/

			}

			// Insert to matrix_Data
			ee()->db->insert('matrix_data', $data);
		}

		return;
	}


	/**
	 * Save Relationships
	 */
	private function _save_rels($selections)
	{
		$r = '';

		$selections = current( $selections );

		if ($selections)
		{
			// -------------------------------------------
			//  Get child titles
			// -------------------------------------------

			$child_titles = array();

			$query = ee()->db->select('entry_id, title, url_title')
			                      ->where_in('entry_id', $selections)
			                      ->get('channel_titles')
			                      ->result();

			foreach ($query as $row)
			{
				$child_titles[$row->entry_id] = array($row->title, $row->url_title);
			}

			// -------------------------------------------
			//  Build new Playa data
			// -------------------------------------------

			foreach ($selections as $rel_order => $child_entry_id)
			{
				$batch_rel_data[] = array(
					'child_entry_id' => $child_entry_id,
					'rel_order'      => $rel_order
				);

				// add some keywords to $r
				$r .= ($r ? "\r" : '')
				    . '['.$child_entry_id.'] '.str_replace('\'', '', $child_titles[$child_entry_id][0]).' - '.$child_titles[$child_entry_id][1];
			}

			ee()->db->insert_batch('playa_relationships', $batch_rel_data);
		}

		return $r;
	}



	
}