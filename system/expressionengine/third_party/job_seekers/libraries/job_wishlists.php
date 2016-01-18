<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_wishlists {

	public $return_data;

	public function __construct()
	{
		$this->EE =& get_instance();	
	}
	
	// Generates the button form to add job to users wishlist
	// Template Tag needs to have a parameter 
	// {exp:job_seekers:wishlist_button job_id="{entry_id}"}
	
	public function wishlist_button()
	{
		// If user is logged in && is a job_seeker group
		if($member_id = $this->EE->session->userdata('member_id'))
		{
			// Get the user id
			// Get the job id param from template
			$job_id = ee()->TMPL->fetch_param('job_id');
			
			// Query database to see if the user already has this job in their wishlist
			$this->EE->db->select('id');
			$this->EE->db->where('job_id', $job_id);
			$this->EE->db->where('member_id', $member_id);
			$this->EE->db->from('job_wishlists');

			$result = $this->EE->db->get();
			
			// If there is an entry
			if($result->num_rows() > 0)
			{
				// Return a button that informs user that they have already added job to wishlist
				$button = "<button class='btn btn-csspink btn-block'><span class='glyphicon glyphicon-trash pull-right'></span>Remove from wishlist </button>";
			}
			else
			{
				$button = "<button class='btn btn-csslgreen btn-block'><span class='glyphicon glyphicon-star pull-right'></span>Add to your wishlist </button>";
			}					

			// Create parameters array for the form
			$hidden_fields = array(
				"job_id" => $job_id,
				'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'wishlist_toggle'),
				);
			
			// Params = job_id
			$form_data = array(
				"id" => $this->EE->TMPL->form_id,
				"class" => "eeform",
				"hidden_fields" => $hidden_fields
				);

			$tagdata = $this->EE->TMPL->tagdata;

			// Add data	array to the form_declaration
			$form = $this->EE->functions->form_declaration($form_data) . $tagdata . $button ;
			
			// include the button
			$form .= "</form>";

		}
		else
		{
			// Return a button which links to registration form
			$form = "<a class='btn btn-csslgreen btn-block' href='{path=\"job-seekers/login\"}'><span class='glyphicon glyphicon-star pull-right'></span>Add to your wishlist </a>";

		}

		return $form;
	}
	
	// Rather than creating seperate funtions we shall combine add/remove to/from wishlist
	// This takes data from the submitted form
	
	public function wishlist_toggle()
	{
		// Get user id
		$member_id = $this->EE->session->userdata('member_id');

		// Get the job id from the post data
		$job_id = $this->EE->input->post('job_id');

		// Create query to check if wish already exists
		$this->EE->db->select('id');
		$this->EE->db->where('job_id', $job_id);
		$this->EE->db->where('member_id', $member_id);
		$this->EE->db->from('job_wishlists');

		$result = $this->EE->db->get();
		
		// If wish exists
		if($result->num_rows() > 0)
		{
			// Remove the entry from the database
			$this->EE->db->where('job_id', $job_id);
			$this->EE->db->where('member_id', $member_id);
			$this->EE->db->delete('job_wishlists');

		}
		else
		{	
		
			// Create the data array
			$data = array(
				'job_id' => $job_id,
				'member_id' => $member_id,
				'date_added' => $this->EE->localize->now
				);

			// Insert the job into the wishlist
			$this->EE->db->insert('job_wishlists', $data);
		}

		// Return to where we came from
		$this->EE->functions->redirect($_SERVER['HTTP_REFERER']);
	}

	public function show_wishlist()
	{
		// Get the user id
		$member_id = $this->EE->session->userdata('member_id');

		// Get the fields list
		$fields = $this->EE->db->select('field_name, field_id, group_id')
					->from('channel_fields')
					->get();

		$fields = $fields->result();

		// Clean up the fields array
		$cleanfields = array();

		foreach ($fields as $field ) {
			$cleanfields[$field->field_id] = $field->field_name;
		}

		// Create a query that returns jobs related to the users id
		$this->EE->db->select('t.*, d.*, jw.date_added');
		$this->EE->db->from('channel_titles AS t, channel_data AS d, job_wishlists AS jw');
		$this->EE->db->where('jw.member_id', $member_id);
		$this->EE->db->where('jw.job_id = d.entry_id', NULL, FALSE);
		$this->EE->db->where('t.entry_id = d.entry_id', NULL, FALSE);
		$this->EE->db->order_by('jw.date_added', 'DESC');

		$result = $this->EE->db->get();
		
		// If there are entries
		if($result->num_rows() > 0)
		{
			// Loop through the results to create an array to be parsed
			// These results need to be worked through changing the field_id_* to the field_title
			$variables = array();

			foreach ($result->result() as $row)
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

			$path = "<table class='table'>";
			$path .= ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);
			$path .= "</table>";

			return $path;

		}	
		else
		{
			// Create a no results variable
			return $this->EE->TMPL->no_results;
		}	
		// Return the parsed variables data to the template

	}
}