<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_seekers {
	
	public $return_data;
	
	// Constructor
	public function __construct()
	{
		$this->EE =& get_instance();

		$this->EE->load->library('Template');

		$this->EE->load->library('job_applications');
		$this->EE->load->library('job_cv');
		$this->EE->load->library('job_search');
		$this->EE->load->library('job_wishlists');
	}

/**
 * Job Application functions
 */

	// This creates the job app button
	// Template tag looks like
	// {exp:job_seekers:job_app_but job_id="{entry_id}"}
	
	public function job_app_but()
	{
		return $this->EE->job_applications->job_app_but();
	}
	
	// This creates the form which will process the final application form
	// It will have open and closing tags
	// {exp:job_seekers:job_app_form job_id="{entry_id}" job_contact="{author_id}"}
	// {/exp:job_seekers:job_app_form}
	
	public function job_app_form()
	{		
		return $this->EE->job_applications->job_app_form();
	}
	
	// Sends the job application notification to the job author
	
	public function job_app_process()
	{
		// Because the loads a page we dont need to return anything
		$this->EE->job_applications->job_app_process();
	}
	
	// Lists the jobs a user has already applied for
	
	public function job_app_list()
	{
		return $this->EE->job_applications->job_app_list();
	}

	// List the recruiters applications
	public function list_job_apps()
	{
		return $this->EE->job_applications->list_job_apps();
	}

/**
 * Job cv functions
 */

	public function load_cv_form()
	{
		return $this->EE->job_cv->load_form();
	}
	
	public function upload_cv()
	{
		echo $this->EE->job_cv->upload_cv();
	}

/**
 * Job Search functions
 */

	public function job_search_list()
	{
		return $this->EE->job_search->job_search_list();
	}

	// This function is loaded up when the path job-seekers/create-search
	// Tag {exp:job_seekers:job_search_form}
	
	public function job_search_form()
	{
		return $this->EE->job_search->job_search_form();
	}

	// This function is called by the ACT
	
	public function results()
	{
		// This function return entries
		return $this->EE->job_search->results();
	}

	// This function is loaded up when the path job-seekers/create-search
	// Tag {exp:job_seekers:seekers_search_form}
	
	public function seekers_search()
	{
		return $this->EE->job_search->seekers_search();
	}
	
	// This function is called by the ACT
	
	public function seekers_results()
	{
		// This function return entries
		return $this->EE->job_search->seekers_results();
	}
	 
/**
 * Job wishlists functions
 */

	// Generates the button form to add job to users wishlist
	// Template Tag needs to have a parameter 
	// {exp:job_seekers:wishlist_button job_id="{entry_id}"}
	
	public function wishlist_button()
	{
		return $this->EE->job_wishlists->wishlist_button();
	}
	
	// Rather than creating seperate funtions we shall combine add/remove to/from wishlist
	// This takes data from the submitted form
	
	public function wishlist_toggle()
	{
		// no return needed as page is reloaded
		$this->EE->job_wishlists->wishlist_toggle();		
	}

	public function show_wishlist()
	{
		return $this->EE->job_wishlists->show_wishlist();
	}

	/**
	* Dropdowns menus
	*/

		public function countrylist()
		{
			$country = $this->EE->TMPL->fetch_param('country');

			ee()->load->helper('form');

			 // Get the country list from the DB
			$this->EE->db->select('field_list_items');       
			$this->EE->db->from('exp_channel_fields');
			$this->EE->db->where('field_name', 'country');
			$this->EE->db->limit(1);    

			$q = $this->EE->db->get();

			$options = array();

			foreach (explode("\n", trim($q->row('field_list_items'))) as $v)
			{
				$v = trim($v);
		   		//array_push($options, $v);
				$options[$v] = $v;
			}

			 // Add list to the array

			$js = 'class="form-control"';

			return form_dropdown('country', $options, $country, $js);

		}

		public function regionlist()
		{
			$country = $this->EE->TMPL->fetch_param('country');
			$fname = "region";

			ee()->load->helper('form');

			 // Get the country list from the DB
			$this->EE->db->select('field_list_items');       
			$this->EE->db->from('exp_channel_fields');
			$this->EE->db->where('field_name', 'region');
			$this->EE->db->limit(1);    

			$q = $this->EE->db->get();

			$options = array();

			$options[] = "Any";

			foreach (explode("\n", trim($q->row('field_list_items'))) as $v)
			{
				$v = trim($v);
		   		//array_push($options, $v);
				$options[$v] = $v;
			}

			 // Add list to the array

			$js = 'class="form-control"';

			return form_dropdown($fname, $options, $country, $js);

		}
		
		public function contractlist()
		{
			$contract_type = $this->EE->TMPL->fetch_param('contract_type');

			ee()->load->helper('form');

			 // Get the country list from the DB
			$this->EE->db->select('field_list_items');       
			$this->EE->db->from('exp_channel_fields');
			$this->EE->db->where('field_name', 'contract_type');
			$this->EE->db->limit(1);    

			$q = $this->EE->db->get();

			$options = array();

			$options[] = "Any";

			foreach (explode("\n", trim($q->row('field_list_items'))) as $v)
			{
				$v = trim($v);
		   		//array_push($options, $v);
				$options[$v] = $v;
			}

			 // Add list to the array

			$js = 'class="form-control"';

			return form_dropdown('contract_type', $options, $contract_type, $js);   	
		}
}

/* End of file mod.job_seekers.php */
/* Location: /system/expressionengine/third_party/job_seekers/mod.job_seekers.php */ 