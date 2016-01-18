<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Seed MCP File 
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */

class Seed_mcp
{
	public $module_name;
	private $nocache;
	private $data;
	private $result;

	function __construct()
	{
		if ( ! function_exists('ee') ) {
			function ee() {	return get_instance(); }
		}

		$this->module_name = strtolower(str_replace('_mcp', '', get_class($this)));
		$this->base = str_replace( '&amp;D=', '&D=', BASE.'&C=addons_modules&M=show_module_cp&module=' . $this->module_name );


		$this->data['base_url'] = $this->base;
	
		$controls = array();
		ee()->cp->set_right_nav( $controls );

		// Load helper
		ee()->load->helper('Seed');
		
		// Load Seed base model
		ee()->load->library('Seed_model');

		// Load other models  
		Seed_model::load_models();		
	}
	// --------------------------------------------------------------------

	// --------------------------------------------------------------------

	/**
	 * New Seed page
	 *
	 * @access      public
	 * @return      string
	 */

	function index( $type = 'new', $message = array() )
	{	

		// --------------------------------------
		// Load some libraries
		// --------------------------------------

		ee()->load->library('javascript');

		// --------------------------------------
		// Load assets
		// --------------------------------------
		$this->_add_morphine();
		ee()->cp->load_package_css('seed'.$this->nocache);

		ee()->view->cp_page_title = lang('seed_module_name');


		$this->data['seed_entries_uri'] = $this->base . '&method=seed_entries';
		$this->data['seed_members_uri'] = $this->base . '&method=seed_members';

		return ee()->load->view('mcp_index', $this->data, TRUE);	
	}


	function seed_members( $type = 'new', $message = array() )
	{

		// --------------------------------------
		// Load some libraries
		// --------------------------------------

		ee()->load->library('javascript');

		// --------------------------------------
		// Load assets
		// --------------------------------------
		$this->_add_morphine();
		ee()->cp->load_package_css('seed'.$this->nocache);
		//ee()->cp->load_package_js('seed'.$this->nocache);

		ee()->view->cp_page_title = lang('seed_new_member_seed');
		ee()->cp->set_breadcrumb($this->base, lang('seed_module_name'));


		return ee()->load->view('mcp_members', $this->data, TRUE);
	}
	// --------------------------------------------------------------------

	/**
	 * New Seed page
	 *
	 * @access      public
	 * @return      string
	 */

	function seed_entries( $type = 'new', $message = array() )
	{	

		// --------------------------------------
		// Load some libraries
		// --------------------------------------

		ee()->load->library('javascript');

		// --------------------------------------
		// Load assets
		// --------------------------------------
		$this->_add_morphine();
		ee()->cp->load_package_css('seed'.$this->nocache);
		//ee()->cp->load_package_js('seed'.$this->nocache);


		ee()->view->cp_page_title = lang('seed_new_seed');
		ee()->cp->set_breadcrumb($this->base, lang('seed_module_name'));

		// Get the channel list
		$this->data['channels'] = array();
		$this->_get_channel_data();

		if( $type == 'error' )
		{
			$this->data['errors'] = $message;
		}


		if( $type == 'success' )
		{
			$this->data['success'] = $message;
		}
		
		$this->data[ 'type' ] = $type;

		return ee()->load->view('mcp_seed', $this->data, TRUE);
	
	}

	// --------------------------------------------------------------------

	/**
	 * Start Seed
	 *
	 * @access      public
	 * @return      void
	 */
	function start_seed()
	{
		// Check we've got a passed channel_id and seed count
		$channel_id = ee()->input->post('seed_channel');
		$seed_count = ee()->input->post('seed_count');
		$seed_text_base = ee()->input->post('seed_text_base');

		$errors = array();


		if( $channel_id == '' ) 
		{
			$errors[] = lang('seed_error_no_channel');
		}
		if( $seed_count == '' ) 
		{
			$errors[] = lang('seed_error_no_count');
		}
		if( !empty( $errors ) ) 
		{
			return $this->seed_entries( 'error', $errors );
		}

		if( $seed_count <= 0 ) 
		{
			$errors[] = lang('seed_error_count_not_positive');
		}

		if( !empty( $errors ) ) 
		{
			return $this->seed_entries( 'error', $errors );
		}

		// Basic checks in place. Throw this over to the seed model for the actual grunt work
		$return = ee()->seed_channel_model->seed();

		if( is_array( $return ) )
		{
			return $this->seed_entries( 'error', $return );
		}

		// Get the basic details for the channel

		$channel  = ee()->db->select('c.channel_id, c.channel_title')
					->from('channels c')
					->where('c.channel_id', $channel_id)
					->get()
					->row_array();

		$ret[] = str_replace( 
			 		array(	'%seed_count%',
			 				'%channel_name%', 
			 				'%channel_link%' ), 
			 		array( 	$seed_count,
			 				$channel['channel_title'],
			 				str_replace( '&amp;D=', '&D=', BASE.'&C=content_edit&channel_id='.$channel_id ) ),
			 		lang('seed_success_message'));

		return $this->seed_entries( 'success', $ret );

	}
	// --------------------------------------------------------------------

	/**
	 * Module Settings page
	 *
	 * @access      public
	 * @return      string
	 */
	public function settings()
	{
		// --------------------------------------
		// Load some libraries
		// --------------------------------------

		ee()->load->library('javascript');

		ee()->view->cp_page_title = lang('settings');
		ee()->cp->set_breadcrumb($this->base, lang('seed_module_name'));

		$this->cached_vars['form_post_url'] = $this->base . '&method=save_settings';

		return ee()->load->view('settings', $this->cached_vars, TRUE);
	}





	public function save_settings()
	{
		$data = array();

		foreach( ee()->seed_example_model->attributes() as $attribute )
		{
			if( ee()->input->get_post( $attribute ) != '' )
			{
				$data[ $attribute ] = ee()->input->get_post( $attribute );
			}
		}

		ee()->seed_example_model->insert( $data );

        // ----------------------------------
        //  Redirect to Settings page with Message
        // ----------------------------------
        
        ee()->functions->redirect($this->base . '&method=settings&msg=preferences_updated');
        exit;

	}


	private function _add_morphine()
	{
		$theme_folder_url = ee()->config->item('theme_folder_url');

		if (substr($theme_folder_url, -1) != '/') {
			$theme_folder_url .= '/';
		}

		$theme_folder_url .= "third_party/seed/";

		ee()->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$theme_folder_url.'styles/screen.css" />');

		ee()->cp->add_to_head('<script type="text/javascript" charset="utf-8" src="'.$theme_folder_url.'scripts/compressed.js"></script>');
		ee()->cp->add_to_head('<script type="text/javascript" charset="utf-8" src="'.$theme_folder_url.'seed.js"></script>');


		// Add datepicker


		$date_fmt = ee()->session->userdata('time_format');
		$date_fmt = $date_fmt ? $date_fmt : ee()->config->item('time_format');

		ee()->cp->add_to_head('<style type="text/css">.hasDatepicker{background:#fff url('.ee()->config->item('theme_folder_url').'cp_themes/default/images/calendar_bg.gif) no-repeat 98% 2px;background-repeat:no-repeat;background-position:99%;}</style>');
		ee()->cp->add_to_head( trim('
			<script type="text/javascript">
				$.createDatepickerTime=function(){
					date = new Date();
					hours = date.getHours();
					minutes = date.getMinutes();
					suffix = "";
					format = "'.$date_fmt.'";
				
					if (minutes < 10) {
						minutes = "0" + minutes;
					}
				
					if (format == "us") {
						if (hours > 12) {
							hours -= 12;
							suffix = " PM";
						} else if (hours == 12) {
							suffix = " PM";
						} else {
							suffix = " AM";
						}
					}
				
					return " \'" + hours + ":" + minutes + suffix + "\'";
				}
			
				EE.date_obj_time = $.createDatepickerTime();
			</script>') );


		// Set up date js
	/*	ee()->javascript->output('
			$("#entry_date").datepicker({dateFormat: $.datepicker.W3C + date_obj_time, defaultDate: new Date('.( ee()->localize->set_localized_time( ee()->localize->now * 1000).')});
		') );*/


		ee()->cp->add_js_script(array(
			'ui'		=> 'datepicker'
		));

		ee()->javascript->compile();
		

	}


	public function _get_channel_data()
	{

		// --------------------------------------
		// Get channels and searchable fields
		// --------------------------------------

		$results  = ee()->db->select('c.channel_id, c.channel_title, f.*')
					->from('channels c')
					->join('channel_fields f', 'c.field_group = f.group_id', 'left')
					->where('c.site_id', '1')
			       	->order_by('c.channel_title', 'asc')
			       	->order_by('f.field_order', 'asc')
					->get()
					->result_array();

		
		foreach( $results as $row )
		{
			// Remember channel title
			$this->data['channels'][$row['channel_id']]['title'] = $row['channel_title'];

			// Add 'Title' to fields while we're here
			if ( ! isset($this->data['channels'][$row['channel_id']]['fields']))
			{
				$this->data['channels'][$row['channel_id']]['fields'][0] = array('field_label'=>lang('title'), 'field_name'=>'title', 'is_title' => TRUE, 'field_required'=>'y', 'field_maxl' => '255', 'field_type' => 'text' );
			}

			if( $row['field_id'] == '' ) continue;

			if( $row['field_type'] == 'matrix' )
			{
				ee()->seed_channel_model->get_plugin( 'matrix' );
				// Decode the settings
				$settings = unserialize( base64_decode( $row['field_settings'] ) );

				$row['cells'] = ee()->seed_plugins->$row['field_type']->get_cell_types( $settings );

				// Now unset that plugin so we're clean for later
				unset( ee()->seed_plugins );
			}

			// Add custom fields to this channel
			$this->data['channels'][$row['channel_id']]['fields'][$row['field_id']] = $row;
		}


		// --------------------------------------
		// Get standard channel options
		// --------------------------------------
		$this->_get_standard_options();

		// Prep for per channel options
		$this->_prep_channel_options();

		// --------------------------------------
		// Get per channel options
		// --------------------------------------
		foreach( $this->data['channels'] as $channel_id => $channel_info )
		{
			$this->_get_channel_options( $channel_id );

			// Now get any additionals
			// like structure
			$this->_get_channel_structure( $channel_id );
		}


	}

	private function _get_channel_structure( $channel_id )
	{
		if( !isset( $this->result['structure'] ) ) return;

		// Now check if this channel is structure enabled
		$structure = array();
		foreach( $this->result['structure'] as $row )
		{
			if( $row['channel_id'] == $channel_id ) 
			{
				$structure = $row;
			}
		}

		if( empty( $structure ) ) return;

		// We appear to have a structure page
		$option['option_label'] 	= 'Structure';
		$option['option_type'] 		= 'structure';
		$option['values']			= $structure;
		$option['visible'] 			= ( $structure['type'] == 'page' ? TRUE : FALSE );
		$option['pages']			= $this->result['structure_pages'];

		$this->data['channels'][ $channel_id ]['options'][] = $option;
	}

	private function _prep_channel_options()
	{
		// Get statuses, categories for all channels

		// Statuses
		$statuses = ee()->db->select('s.*, c.channel_id')
					->from('statuses s')
					->join('channels c', 's.group_id = c.status_group', 'left')
					->where('s.site_id', '1')
					->order_by('s.status_order','asc')	
					->get()
					->result_array();
		$this->result['statuses'] = $statuses;



		// Categories
		$categories = array();

	/*	$channel_groups = ee()->db->select('channel_id, cat_group')
							->from('channels')
							->where('site_id','1')
							->where('cat_group IS NOT NULL', null)
							->get()
							->result_array();

		if( !empty( $channel_groups ) )
		{
			// There is at least one channel with some cat groups
			$cat_groups = array();
			$cat_groups_by_channel = array();

			foreach( $channel_groups as $channel ) 
			{
				if( $channel['cat_group'] != '' ) 
				{
					$groups = explode( '|', $channel['cat_group'] );
					$cat_groups_by_channel[ $channel['channel_id'] ] = $groups;

					$cat_groups = array_merge( $cat_groups, $groups );
				}				
			}

			// Now get the categories for these groups
			$cat_groups_data = ee()->db->select('g.group_id, g.group_name, g.sort_order')
								->from('category_groups g')
								->where_in('g.group_id', $groups )
								->get()
								->result_array();



			ee()->load->library('api');
			ee()->api->instantiate('channel_categories');


			foreach( $cat_groups_data as $group )
			{
				// Fetch the category tree
				ee()->api_channel_categories->category_tree($group['group_id'], '', $group['sort_order']);

				$groups[ $group['group_id'] ] = ee()->api_channel_categories->categories;

			//	$categories['groups'][ $group['group_id'] ] = $group;
			}


			// Now drop them into their respective channels

			foreach( $cat_groups_by_channel as $channel_id => $channel_group )
			{
				foreach( $channel_group as $subgroup ) 
				{
					$categories[ $channel_id ]['groups'][ $subgroup ] = $groups[ $subgroup ];
				}

			}

		}
*/
		$this->result['categories'] = $categories;


		// See if structure is installed and active for any channels
		$has_structure = ee()->db->from('modules')
									->where('module_name', 'Structure')
									->get()
									->row_array();
		if( !empty( $has_structure ) ) 
		{
			// Get structure channels
			$structure = ee()->db->from('structure_channels')
										->where('type !=','unmanaged')
										->get()
										->result_array();
			$this->result['structure'] = $structure;

			// Get the site pages for display
			$pages = ee()->db->select('site_pages')
									->from('sites')
									->where('site_id', '1')
									->get()
									->row_array();

			$structure_data = ee()->db->select('entry_id')
									->from('structure')
									->where('parent_id', '0')
									->order_by('lft', 'asc')
									->get()
									->result_array();



			// Decode the pages
			$structure_pages = array();

			if( !empty( $pages ) ) $structure_pages = unserialize( base64_decode( $pages['site_pages'] ) );

			if( !is_array( $structure_pages ) ) $structure_pages = array();

			$structure_pages = current( $structure_pages );

			$clean_pages = array();
			// Clean up the pages, we only want top level items
			$top_pages = array();
			foreach( $structure_data as $row )
			{
				$top_pages[] = $row['entry_id'];
			}

			foreach( $structure_pages['uris'] as $page_id => $page_uri )
			{
				if( in_array( $page_id, $top_pages ) ) $clean_pages[ $page_id ] = $page_uri;
			}

			$structure_pages['clean'] = $clean_pages;
			
			$this->result['structure_pages'] = $structure_pages;
		}
		// Categories .. todo


	}

	private function _get_channel_options( $channel_id = 0 )
	{
		if( $channel_id == 0 ) return;

		// Get this channel's variable options
		// 		- statuses
		//		- categories
		
		$this->_get_channel_statuses( $channel_id );		
		$this->_get_channel_categories( $channel_id );


	}

	private function _get_channel_categories( $channel_id ) 
	{
		// Do we have categories for this channel?
		$channel_categories = array();

		if( isset( $this->result['categories'][ $channel_id ] ) )
		{
			$channel_categories = $this->result['categories'][ $channel_id ];
		}

	
		$categories['option_label'] 		= 'Categories';
		$categories['option_type']			= 'category';
		$categories['values']				= $channel_categories;	

		// Hide categories for now
		$categories['visible']				= FALSE; //( count($channel_categories) > 0 ? TRUE : FALSE );

		$this->data['channels'][ $channel_id ]['options'][] = $categories;
	}

	private function _get_channel_statuses( $channel_id ) 
	{
		// Do we have statuses for this channel?
		$channel_statuses = array();

		if( isset( $this->result['statuses'] ) ) 
		{
			foreach( $this->result['statuses'] as $status )
			{
				if( $status['channel_id'] == $channel_id )
				{
					$channel_statuses[] = $status;
				}
			}
		}

		$statuses['option_label'] 		= 'Statuses';
		$statuses['option_type']		= 'status';
		$statuses['values']				= $channel_statuses;
		$statuses['visible']			= ( count( $channel_statuses ) > 0 ? TRUE : FALSE );

		$this->data['channels'][ $channel_id ]['options'][] = $statuses;
	}

	private function _get_standard_options()
	{
		// Standard, non channel specific or varying options
		// 		- Start Date
		//		- End Date

		$options = array();

		$entry_date['option_label'] 	= 'Start Date';
		$entry_date['option_type'] 		= 'entry_date';
		$entry_date['inital_value']		= ee()->localize->now;
		$entry_date['visible']			= TRUE;
		$options[] = $entry_date;



		$expiry_date['option_label'] 	= 'Expiry Date';
		$expiry_date['option_type'] 	= 'expiry_date';
		$expiry_date['inital_value']	= 0;
		$expiry_date['visible']			= TRUE;
		$options[] = $expiry_date;


//		$this->data['standard_options'] = $options;
		$this->data['standard_options'] = array();

	}



	
}
