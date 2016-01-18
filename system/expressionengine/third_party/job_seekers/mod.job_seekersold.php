<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_seekers {
	
	public $return_data;
	
	// Constructor
	public function __construct()
	{
		$this->EE =& get_instance();
	}

//Frontend job functions --------------------

	public function wishlist_button()
	{
		 //Get entry ID
		$entry_id = $this->EE->TMPL->fetch_param('entry_id');
		$member_id = $this->EE->session->userdata('member_id');

		// Get user ID and check against table "job_applications" if the user has already applied for this job

		$this->EE->db->select('id');       
		$this->EE->db->from('job_wishlists');
		$this->EE->db->where('member_id', $member_id);
		$this->EE->db->where('job_id', $entry_id);    

		$q = $this->EE->db->get();   

		if($q->num_rows() > 0)
		{
			// If TRUE return show a disabled button
			$button = "<button class='btn btn-csspink btn-block'><span class='glyphicon glyphicon-trash pull-right'></span>Remove from wishlist </button>";
		}
		else
		{
			$button = "<button class='btn btn-csslgreen btn-block'><span class='glyphicon glyphicon-star pull-right'></span>Add to your wishlist </button>";
		}
			// Create the form that we will use for the action
			// Vars needed: entry_id; member_id;
			// Build an array to hold the form's hidden fields
			$hidden_fields = array(
				"entry_id" => $entry_id,
				'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'toggle_wish'),
				);

			    // Build an array with the form data
			$form_data = array(
				"id" => $this->EE->TMPL->form_id,
				"class" => $this->EE->TMPL->form_class,
				"hidden_fields" => $hidden_fields
				);

			    // Fetch contents of the tag pair, ie, the form contents
			$tagdata = $this->EE->TMPL->tagdata;

			$form = $this->EE->functions->form_declaration($form_data) . $tagdata . $button . "</form>";

			return $form;

			// Return the form

	}

	// --------------------------------------------------------------------

	/**
	 * ACT: (un)add job to wishlist
	 *
	 * @access     public
	 * @return     void
	 */
	public function toggle_wish()
	{
		// --------------------------------------
		// Get entry id from post, member_id from session
		// --------------------------------------

		$entry_id = $this->EE->input->post('entry_id');
		$member_id = $this->EE->session->userdata('member_id');

		// --------------------------------------
		// if we have an entry_id and a member_id,
		// add/remove a record to/from job_wishlists table,
		// depending on whether a record already exists or not
		// --------------------------------------

		if ($entry_id && $member_id)
		{
			// Data to work with
			$data = array(
				'job_id'  => $entry_id,
				'member_id' => $member_id
				);

			// Liked or not?
			$this->EE->db->where('job_id', $entry_id);
			$this->EE->db->where('member_id', $member_id);
			$wished = $this->EE->db->get('job_wishlists');

			if ($wished->num_rows() > 0)
			{
				$this->EE->db->delete('job_wishlists', $data);
			}
			else
			{
				$data['date_added'] = $this->EE->localize->now;
				$this->EE->db->insert('job_wishlists', $data);
			}

			// Cater for Ajax requests
			if (AJAX_REQUEST)
			{
				die($wished ? '-1' : '1');
			}

		}

		// --------------------------------------
		// Go back to where you came from
		// --------------------------------------

		$this->EE->functions->redirect($_SERVER['HTTP_REFERER']);
	}



	public function wishlist_list()
	{
		// Get the member id

		// select all entries from the wishlist table


		// Needs some sort of convoluted left join!!!!

		// Select Job title FROM exp_channel_titles LEFT JOIN exp_job_wishlists.job_id = exp_channel_titles.entry_id WHERE exp_job_wishlists.member_id =  $member_id;

		//$this->EE->load->model('channel_entries_model');
		// $query = $this->EE->channel_entries_model->get_entry('33', NULL, FALSE);
		// $boobs = "You like the boobs";
		// return $boobs;
		
		$this->EE->db->from('channel_titles AS ct, channel_data AS cd, channels AS c');
		$this->EE->db->select('ct.*, cd.*, c.*');
		$this->EE->db->where('ct.entry_id = cd.entry_id', NULL, FALSE);
		$this->EE->db->where('c.channel_id = ct.channel_id', NULL, FALSE);
		$this->EE->db->join('job_wishlists', 'job_wishlists.job_id = cd.entry_id');
		$res = $this->EE->db->get();


		$fields = $this->EE->db->select('field_name, field_id')
					->from('channel_fields')
					//->where('group_id', 5)
					->get();

		$fields = $fields->result();

		// Clean up the fields array
		$cleanfields = array();

		foreach ($fields as $field ) {
			$cleanfields[$field->field_id] = $field->field_name;
		}


		$results = $res->result();
		$variables = array();

		foreach ($results as $row)
		{

			$variables_row = array();

			$variables_row['title'] = $row->title;

			foreach ($row as $key => $value) {


				if (strpos($key,'field_id_') !== false) {

				    $key_array = explode('field_id_', $key);

				    $new_row = array($cleanfields[$key_array[1]] => $value);
				    $variables_row = array_merge($new_row, $variables_row);
				}
				else
				{
					$new_row = array($key => $value);
					$variables_row = array_merge($new_row, $variables_row);
				}

			}

			$variables[] = $variables_row;
		
		}


		// These results need to be worked through changing the field_id_* to the field_title
		return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
	}

	// Job application functions


	public function application_button()
	{

		//Get entry ID
		$job_link = $this->EE->TMPL->fetch_param('job_link');
		$job_id = $this->EE->TMPL->fetch_param('job_id');
		$member_id = $this->EE->session->userdata('member_id');
		// $application_date = $this->EE->localize->now; 

		// Get user ID and check against table "job_applications" if the user has already applied for this job

		$this->EE->db->select('id');       
		$this->EE->db->from('job_applications');
		$this->EE->db->where('member_id', $member_id);
		$this->EE->db->where('job_id', $job_id);    

		$q = $this->EE->db->get();   

		if($q->num_rows() > 0)
		{
		 // If TRUE return show a disabled button
			return "<a class='btn btn-cssgreen btn-block' disabled='disabled' href='#'><span class='glyphicon glyphicon-thumbs-up pull-right'></span>You've already applied </a>";
		}
		else
		{
		 // Return the standard button
			return "<a class='btn btn-cssgreen btn-block' href='".$job_link."'><span class='glyphicon glyphicon-pencil pull-right'></span>Apply for this job </a>";
		}

	}

	public function application_form()
	{

		// Get the data from the template
		$member_id = $this->EE->session->userdata('member_id');
		$job_id = $this->EE->TMPL->fetch_param('job_id'); 
	
    	// Build an array to hold the form's hidden fields
		$hidden_fields = array(
			"job_id" => $job_id,
			'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'application_process'),
			);

    	// Build an array with the form data
		$form_data = array(
			"id" => $this->EE->TMPL->form_id,
			"class" => $this->EE->TMPL->form_class,
			"hidden_fields" => $hidden_fields
			);

    	// Fetch contents of the tag pair, ie, the form contents
		$tagdata = $this->EE->TMPL->tagdata;

		$form = $this->EE->functions->form_declaration($form_data) . $tagdata;

		return $form;			

	}

public function application_process()
{	
		$member_id = $this->EE->session->userdata('member_id');
		$application_date = $this->EE->localize->now;

		if($this->EE->input->post('job_id'))
		{
			// Process the data
			$data = array(
				"job_id" => $this->EE->input->post('job_id'),
				"member_id" => $member_id,
				"date_applied" => $application_date,
				"application_message" => $this->EE->input->post('application_message')
				);

			$this->EE->db->insert('job_applications', $data);
			
			// Say thankyou
			$this->EE->functions->redirect($_SERVER['HTTP_REFERER']);
		}	
}

//CVs Uploads --------------------

	public function cvform() {

    // Find the entry_id of the auction to add the form for
		$member_id = $this->EE->session->userdata('member_id');
		if( $member_id === FALSE ) {
			return "";
		}

    // Build an array to hold the form's hidden fields
		$hidden_fields = array(
			"member_id" => $member_id,
			'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'cvupload'),
			);

    // Build an array with the form data
		$form_data = array(
			"id" => "fileupload",
			"class" => $this->EE->TMPL->form_class,
			"hidden_fields" => $hidden_fields
			);

    // Fetch contents of the tag pair, ie, the form contents
		$tagdata = $this->EE->TMPL->tagdata;

		$form = $this->EE->functions->form_declaration($form_data) . $tagdata;

		return $form;
	} 

	public function cvupload()
	{
		$this->EE->load->library('filemanager');
		$this->EE->load->helper('file');

		// GET the member channel entry ID
		$entry_id = $this->EE->input->post('entry_id');

		// GET the member filename
		if($this->EE->input->post('member_cv') != '')
		{
			$member_cv = $this->EE->input->post('member_cv');
			$filename = explode($this->EE->functions->fetch_site_index().'job-cvs/', $member_cv);

			// Check IF there is a filename
			if(unlink($_SERVER['DOCUMENT_ROOT'].'/css-recruitment-ee/job-cvs/'.$filename[1] ))
			{
				// then unlink the file from folder
				// Delete the file from the database where filename && currentuserid exists

				$this->EE->db->where('uploaded_by_member_id', $this->EE->session->userdata('member_id'));
				$this->EE->db->where('file_name', $filename[1]);
				$q = $this->EE->db->delete('exp_files');

				if(!$q){ die("Cant delete file"); }
			}
			else
			{
				echo "File not deleted: ".$_SERVER['DOCUMENT_ROOT'].'/css-recruitment-ee/job-cvs/'.$filename[1];
			}
		}
		// Then upload the file

		if($fileinfo = $this->EE->filemanager->upload_file(2))
		{
			$info->name = $fileinfo['file_name'];
			$info->size = $fileinfo['file_size'];
			$info->type = $fileinfo['file_type'];

            // I set this to original file since I did not create thumbs.  change to thumbnail directory if you do = $upload_path_url .'/thumbs' .$data['file_name']
			$info->deleteType = 'DELETE';
			$info->error = null;

            // Update the member channel entry with the new filename
			$data = array(
				'field_id_49' => '{filedir_2}'.$info->name
				);

			$this->EE->db->where('entry_id', $entry_id);
			$q = $this->EE->db->update('exp_channel_data', $data);

            // If successfull return the JSON
			if($q)
			{
				$files[] = $info;
				echo json_encode(array("files" => $files));   	
			}

		}

	}
//Job wishlists --------------------

//Profile form fields --------------------

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