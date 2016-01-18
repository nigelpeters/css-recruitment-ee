<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Fieldtype Playa class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_fieldtype_playa extends Seed_fieldtype
{
	public $title = 'playa';
	
	public $settings = array(
		array(
			'name' 			=> 'from',
			'required' 		=> FALSE,
			'type' 			=> 'int',
			'default'		=> 2
		),		
		array(
			'name' 			=> 'to',
			'required' 		=> FALSE,
			'type' 			=> 'int',
			'default' 		=> 6
		)
	);
	private $channels;
	private $statuses;
	private $authors;
	private $ids_used = array();

	public function get_settings( $field = array() )
	{
		$return = array();

		$field_id = $field['field_id'];
		$is_cell = FALSE;
		
		if( isset( $field['cell_id'] ) )
		{
			$cell_id = $field['cell_id'];
			$is_cell = TRUE;
		}

		if( !$is_cell ) 
		{
			if( $field_id == 0 ) return $return;
			if( !isset( ee()->api_channel_fields->settings[ $field_id ] ) ) return $return;


			$settings = ee()->api_channel_fields->settings[ $field_id ]['field_settings'];
		}
		else
		{
			if( $cell_id == 0 ) return $return;

			$cell = ee()->db->select('col_settings')
								->where('col_id', $cell_id )
								->get('matrix_cols')
								->row_array();

			if( empty( $cell ) ) return $return;

			$settings = $cell['col_settings'];		

		}
		$settings = unserialize( base64_decode( $settings ) );

		if( !is_array( $settings ) ) return array();

		return $settings;
	}


	public function generate( $field = array() )
	{
		$ret = array('selections' => array() );

		// Generate some text within the bounds of the options
		if( $field['populate'] == 'sparse' )
		{
			// We don't want to always populate this. 
			if( rand( 1, 2 ) == 2 ) return $ret;
		}

		$settings = $this->get_settings( $field );

		// Figure out the channels to select from
		$this->channels = array();
		if( isset( $settings['channels'] ) AND !empty( $settings['channels'] ) )
		{
			foreach( $settings['channels'] AS $channel ) 
			{
				if( $channel == 'current' ) $this->channels[] = $field['channel_id'];
				else $this->channels[] = $channel;
			}
		}

		$this->authors = array();
		if( isset( $settings['authors'] ) AND !empty( $settings['authors'] ) )
		{
			foreach( $settings['authors'] AS $author ) 
			{
				if( $author == 'current' ) $this->authors[] = ee()->session->userdata['member_id'];
				else $this->authors[] = $author;
			}
		}

		$this->statuses = array();
		if( isset( $settings['statuses'] ) AND !empty( $settings['statuses'] ) )
		{
			foreach( $settings['statuses'] AS $status ) 
			{
				$this->statuses[] = $status;
			}
		}


		$entries = array();

		if( $settings['multi'] == 'y' )
		{
			// We need multiple entries
			// Randomly decide how many
			$from = 3;
			$to = 6;
			if( isset( $field['from'] ) AND is_int( $field['from'] ) ) $from = $field['from']; 
			if( isset( $field['to'] ) AND is_int( $field['to'] ) ) $to = $field['to']; 

			$count = rand( $from, $to );

			for( $i=0; $i < $count; $i++ )
			{
				$entries[] = $this->_generate_single();
			}
		}		
		else
		{
			// Just the one, simple
			$entries[] = $this->_generate_single();
		}

		// Also return the values that get dropped into the channel_data field
		$ret = array('selections' => $this->_channel_data( $entries ));

		return $ret;
	}

	private function _channel_data( $entries = array() )
	{
		if( empty( $entries ) ) return '';

		foreach( $entries as $entry )
		{
			$ret[] = $entry['entry_id'];
		}

		return $ret;
	}

	private function _generate_single()
	{
		// Pick an entry id at random with the channel, author and status as limited

		ee()->db->select( array('entry_id','title') );

		if( !empty( $this->channels ) )
		{
			// Limit by channels
			ee()->db->where_in('channel_id', $this->channels );
		}

		if( !empty( $this->authors ) )
		{
			// Limit by authors
			ee()->db->where_in('author_id', $this->authors );
		}

		if( !empty( $this->statuses ) )
		{
			// Limit by statuses
			ee()->db->where_in('status', $this->statuses );
		}

		// Do we have a list of id's to avoid?
		if( !empty( $this->ids_used ) ) 
		{
			ee()->db->where_not_in('entry_id', $this->ids_used );
		}


		$row = ee()->db->order_by('entry_id', 'random')
						->get('channel_titles','1')
						->row_array();

		// Mark this id as used
		$this->ids_used[] = $row['entry_id'];

		return $row;
	}
}