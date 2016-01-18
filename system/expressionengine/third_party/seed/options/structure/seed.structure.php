<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Option Structure class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_option_structure extends Seed_option
{
	public $title = 'structure';

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
		return FALSE;
	}

	public function post_save( $entry_id, $data, $option ) 
	{
		if( empty( $option ) ) return;

		// Get the url title first
		$entry = ee()->db->select('url_title, channel_id')
								->from('channel_titles')
								->where('entry_id', $entry_id)
								->get()
								->row_array();

		if( !isset( $entry['url_title'] ) ) return;

		$url_title = $entry['url_title'];
		$channel_id = $entry['channel_id'];

		$parent_entry_id = 0;
		// Now figure where to drop this entry
		if( count( $option ) < 2 ) 
		{
			// Only one place we can drop it
			$parent_entry_id = current( $option );
		}	
		else
		{
			// we have to pick a parent
			$parent_entry_id = $option[ rand( 0, count( $option ) - 1 ) ];
		}

		// Let structure do the heavy lifting
		require_once PATH_THIRD.'structure/sql.structure.php';
		$structure_sql = new Sql_structure();

		$channel_type = $structure_sql->get_channel_type($channel_id);
		
		// If the current channel is not assigned as any sort of Structure channel, then stop
		if ($channel_type == 'page' )
		{
			$site_pages = $structure_sql->get_site_pages();
				
			// get form fields
			$entry_data = array(
				'channel_id'	=> $channel_id,
				'entry_id'		=> $entry_id,
				'uri'			=> $url_title,
				'template_id'	=> $structure_sql->get_default_template($channel_id),
				'listing_cid'	=> 0,
				'hidden'		=> 'n'
			);

		
			$site_pages = $structure_sql->get_site_pages();
			if( !isset( $site_pages['uris'][ $parent_entry_id ] ) ) return;

			$entry_data['parent_id'] = $parent_entry_id;

			$parent_uri = $site_pages['uris'][ $parent_entry_id ] . '/';
			$entry_data['uri'] = $structure_sql->create_page_uri($parent_uri, $entry_data['uri']);
			
			require_once PATH_THIRD.'structure/mod.structure.php';
	        $structure_model = new Structure();

			$structure_model->set_data($entry_data);
		}

		// Cleanup
		unset( $structure_sql );
		unset( $structure_model );

		// We need to update the config->item('site_pages')
		// now other wise it won't register for the next loop
		$res = ee()->db->select('site_pages')
								->from('sites')
								->where('site_id', '1')
								->get()
								->row_array();

		ee()->config->set_item('site_pages', unserialize( base64_decode( $res['site_pages'] ) ) );

	}

}