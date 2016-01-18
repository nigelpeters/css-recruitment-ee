<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_applications {



	public function __construct()
	{
		$this->EE =& get_instance();		
	}
	
	// This creates the job app button
	// Template tag looks like
	// {exp:job_seekers:job_app_but job_id="{entry_id}"}
	
	public function job_app_but()
	{

		// If the user is logged in Get the user id
		if($member_id = $this->EE->session->userdata('member_id'))
		{
		
			// Get the job id from the params
			$job_id = ee()->TMPL->fetch_param('job_id');
			$job_link = $this->EE->TMPL->fetch_param('job_link');

			// query the database to see if user has already applied
			$this->EE->db->select('id');
			$this->EE->db->where('job_id', $job_id);
			$this->EE->db->where('member_id', $member_id);
			$this->EE->db->from('job_applications');

			$result = $this->EE->db->get();
			
			if($result->num_rows() > 0)	
			{
				// If user has applied
				// Return a button which tells user they have applied
				return "<a class='btn btn-cssgreen btn-block' disabled='disabled' href='#'><span class='glyphicon glyphicon-thumbs-up pull-right'></span>You've already applied </a>";
			}
			else
			{

				return "<a class='btn btn-cssgreen btn-block' href='".$job_link."'><span class='glyphicon glyphicon-pencil pull-right'></span>Apply for this job </a>";
			
			}
		}
		else
		{
				$job_link = ee()->functions->create_url('job-seekers/login');
				// Return a button which links to login form
				return "<a class='btn btn-cssgreen btn-block' href='".$job_link."'><span class='glyphicon glyphicon-pencil pull-right'></span>Apply for this job </a>";

		}							
	}
	
	// This creates the form which will process the final form
	// It will have open and closing tags
	// {exp:job_seekers:job_app_form job_id="{entry_id}" job_contact="{author_id}"}
	// {/exp:job_seekers:job_app_form}
	
	public function job_app_form()
	{		
		// Get the user id
		$member_id = $this->EE->session->userdata('member_id');

		// Get the entry id from template params
		$job_id = ee()->TMPL->fetch_param('job_id');
		
		// Run query to check if user has already applied
		$this->EE->db->select('id');
		$this->EE->db->where('job_id', $job_id);
		$this->EE->db->where('member_id', $member_id);
		$this->EE->db->from('job_applications');

		$result = $this->EE->db->get();
		
		if($result->num_rows() > 0)	
		{
			// If user has already applied
			// Redirect to thankyou page
			$this->EE->functions->redirect($this->EE->config->item('site_url').'job-seekers/profile');
		}
		else
		{	
		// Else
					
			// Get author details
			$job_contact = ee()->TMPL->fetch_param('job_contact');

			$this->EE->db->select('screen_name, member_id');
			$this->EE->db->where('member_id', $job_contact);
			$this->EE->db->from('exp_members');

			$author = $this->EE->db->get();

			$author_name = $author->row()->screen_name;
			$author_id = $author->row()->member_id;

			//Get the users synopsis is a bit long winded sooooo

			//Get the field id in case the db has changed for what ever reason
			$this->EE->db->select('field_id');
			$this->EE->db->where('field_name', 'synopsis');
			$this->EE->db->from('channel_fields');

			$fieldinfo = $this->EE->db->get();
			$fieldid = "field_id_".$fieldinfo->row()->field_id;

			// Get the member channel entry id
			$this->EE->db->select('entry_id');
			$this->EE->db->from('exp_members mem, exp_channel_titles tit');
			$this->EE->db->where('tit.author_id = mem.member_id');
			$this->EE->db->where('mem.member_id = '.$member_id);

			$query = $this->EE->db->get();
			$mem_entry_id = $query->row()->entry_id;

			// Now get the user synopsis field data
			$this->EE->db->select($fieldid.' AS synopsis');
			$this->EE->db->where('entry_id', $mem_entry_id);
			$this->EE->db->from('channel_data');

			$userinfo = $this->EE->db->get();

			$usersynopsis = $userinfo->row()->synopsis;

			// Create the message and add the authors name

			$message = "Dear " . $author_name . ",<br/><br/>";
			$message .= $usersynopsis . "<br/><br/>";
			
			$textarea = "<div class='form-group'>\n";
				  		
			$textarea .= "<textarea id='application_message'  class='form-control' rows='15' name='application_message'>";

	    	// Build an array to hold the form's hidden fields
			$hidden_fields = array(
				"job_id" => $job_id,
				"job_contact" => $job_contact,
				"author_id" => $author_id,
				'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'job_app_process')
				);

	    	// Build an array with the form data
			$form_data = array(
				"id" => $this->EE->TMPL->form_id,
				"class" => $this->EE->TMPL->form_class,
				"hidden_fields" => $hidden_fields
				);

	    	// Fetch contents of the tag pair, ie, the form contents
			$tagdata = $this->EE->TMPL->tagdata;

			$form = $this->EE->functions->form_declaration($form_data) . $tagdata . $textarea . $message;

			return $form;			
		}
	}
	
	public function job_app_process()
	{
		// Get the form data
		$job_id = $this->EE->input->post('job_id');
		$job_contact = $this->EE->input->post('job_contact');
		$author_id = $this->EE->input->post('author_id');
		
		// Get the user id
		$member_id = $this->EE->session->userdata('member_id');
		$application_date = $this->EE->localize->now;

		// Create the data array
		$data = array(
			"job_id" => $job_id,
			"author_id" => $author_id,
			"member_id" => $member_id,
			"date_applied" => $application_date,
			"application_message" => $this->EE->input->post('application_message')
			);

		// If insert
		if($this->EE->db->insert('job_applications', $data))
		{

			// Get author details
			$this->EE->db->select('member_id, screen_name, email');
			$this->EE->db->where('member_id', $job_contact);
			$this->EE->db->from('exp_members');

			$author = $this->EE->db->get();
			$authorinfo = $author->row();
			
			// Get the message and user info

			$this->EE->db->from('job_applications AS job');
			$this->EE->db->select('job.member_id, job.application_message, members.screen_name, members.email, channel_titles.title');
			$this->EE->db->join('members', 'members.member_id = job.member_id');
			$this->EE->db->join('channel_titles', 'channel_titles.entry_id = job.job_id');
			$this->EE->db->where('job.member_id', $member_id);
			$this->EE->db->where('job.job_id', $job_id);
			$query = $this->EE->db->get();
			$userinfo = $query->row();

			$data['userinfo'] = $userinfo;
			$data['authorinfo'] = $authorinfo;

			ee()->load->library('phpmailer/phpmailer');
			ee()->load->helper('text');

			$to = $authorinfo->email;
			$subject = 'You have received a job application';
			$body = $this->EE->load->view( 'email', $data, true );
			if($this->_sendemail($to, $subject, $body))
			{
				$this->EE->functions->redirect($this->EE->config->item('site_url').'job-seekers/success-application');
			}

		}
		else
		{
		// Else
		
			// log and error
			
			// and show error page
		}

	}
	
	// Lists the jobs a user has already applied for
	
	public function job_app_list()
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
		$this->EE->db->select('t.*, d.*, jw.date_applied');
		$this->EE->db->from('channel_titles AS t, channel_data AS d, job_applications AS jw');
		$this->EE->db->where('jw.member_id', $member_id);
		$this->EE->db->where('jw.job_id = d.entry_id', NULL, FALSE);
		$this->EE->db->where('t.entry_id = d.entry_id', NULL, FALSE);
		$this->EE->db->order_by('jw.date_applied', 'DESC');

		if(ee()->TMPL->fetch_param('limit') != "")
		{
			$this->EE->db->limit(ee()->TMPL->fetch_param('limit'));
		}

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

	// Tag {exp:job_seekers:list_job_apps limit="5"}
	public function list_job_apps()
	{
		// Get the user id
		$author_id = $this->EE->session->userdata('member_id');

		// Get the limit params
		$limit = ee()->TMPL->fetch_param('limit');

		// Query is to SELECT * Job applications from the database where they are the author and join the user details and the job entry details		
		$this->EE->db->select("ja.job_id, ja.author_id, ja.member_id, ct.title, ct.url_title, ja.date_applied, m.screen_name as jobseeker");
		$this->EE->db->from("exp_job_applications as ja");
		$this->EE->db->join("exp_channel_titles as ct", "ct.entry_id = ja.job_id");
		$this->EE->db->join("exp_members as m", "m.member_id = ja.member_id");
		$this->EE->db->where("ja.author_id", $author_id);
		if(strlen($limit))
		{
			$this->EE->db->limit($limit);
		}
		
		$result = $this->EE->db->get();

		if($result->num_rows() > 0)
		{
			$page = "<table class='table'>";
		  	$page .= "<thead><tr><th>Job Posting</th> <th>Applicant</th> <th>Date Applied</th></tr><thead/>";
			$page .= ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $result->result_array());
			$page .= "</table>";

			return $page;
		}
		else
		{
			return $this->EE->TMPL->no_results;
		}
	}

	private function _sendemail($to, $subject, $body)
	{
		$this->EE->load->library('phpmailer/phpmailer');
		$mail = new PHPMailer();

		$mail->IsSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'smtp.gmail.com';         			// Specify main and backup server
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'cssrecruitment2014@gmail.com';         // SMTP username
		$mail->Password = 'cssrecruitment@2014';              // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

		$mail->From = 'info@css-people.co.uk';
		$mail->FromName = 'CSS Recruitment';
		$mail->AddAddress($to, '');       // Add a recipient

		$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
		$mail->IsHTML(true);                                  // Set email format to HTML

		$mail->Subject = $subject;
		$mail->Body    = $body;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		if(!$mail->Send()) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
			exit;
		}
		else
		{
			return true;
		}
	}		
}