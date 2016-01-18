<?php

Class Job_search {
	
	function __construct()
	{
		// Load up EE dependences
		$this->EE =& get_instance();
	}

	// This is to load the form
	// is can be done from the front end

	// On the search form there are 7 fields to search
	// Title : Sector : Job Type (Contract, Perm, Temp, Part time) : Region : Salary Min : Salary Max : Salary Unit
	// {exp:job_seekers:job_search_form(}
	public function job_search_form()
	{
		// Get the post data
		$tag_vars = array();
	    $tag_vars[0] = array(
			"title" => $this->EE->input->post('title'),
			"sector" => $this->EE->input->post('sector'),
			"contract" => $this->EE->input->post('contract_type'),
			"region" => $this->EE->input->post('region'),
			"salary_min" => $this->EE->input->post('salary_min'),
			"salary_max" => $this->EE->input->post('salary_max')
		);

		if($this->EE->input->post('process') == "1")
		{	
			// Start form validation here
			// use CI validation library to check submission
			$this->EE->load->helper('form');
			$this->EE->load->library('form_validation');
			//$this->EE->form_validation->set_rules('title', 'Job title', 'required|min_length[5]');
			$this->EE->form_validation->set_rules('title', 'Job title', 'min_length[5]');
			$this->EE->form_validation->set_rules('salary_min', 'Salary minimum', 'numeric');
			$this->EE->form_validation->set_rules('salary_max', 'Salary maximum', 'numeric');
			
			if($this->EE->form_validation->run() == FALSE)
			{
				$form_errors = array();
					foreach (array('title', 'salary_min', 'salary_max') as $field_name)
					{
						$field_error = form_error($field_name);
						if ($field_error)
		                {
		                    $form_errors[] = $field_error;
		                    $tag_vars[0]['error:'.$field_name] = $field_error;
		                }
		            }
	
				if ($this->EE->TMPL->fetch_param('error_handling') != 'inline')
				{
					return $this->EE->output->show_user_error(FALSE, $form_errors);
				}
			}
			else
			{
				// We save the data as a get query in the db
				$this->EE->db->insert('job_search', array(
					'get_query' => http_build_query($tag_vars[0]),
					'search_date' => $this->EE->localize->now
					));
				
				$query_id = $this->EE->db->insert_id();

				$this->results($query_id);

				/** ----------------------------------------
				/**  Redirect to search results page
				/** ----------------------------------------*/

				$path = reduce_double_slashes(
					ee()->functions->create_url('job-seekers/results'.'/'.$query_id.'/')
				);

				return ee()->functions->redirect($path);					
			}
		}

    	// Build an array with the form data
		$form_data = array(
			"id" => "form",
			"action" => $this->EE->functions->fetch_current_uri()
			);

		if($this->EE->TMPL->fetch_param('class') != "")
		{
			$form_data['class'] = $this->EE->TMPL->fetch_param('class');
		}

		$form = $this->EE->functions->form_declaration($form_data);
		$form .= $this->EE->TMPL->parse_variables(ee()->TMPL->tagdata, $tag_vars);

		// Return form
		return $form;			
	}

	public function search_jobs()
	{

		// Get teh post data s
			// We save the data as a get query in the db
			$this->EE->db->insert('job_search', array(
				'get_query' => http_build_query($searcharray),
				'search_date' => $this->EE->localize->now
				));
			
			$query_id = $this->EE->db->insert_id();

			$this->results($query_id);

	}

	public function results()
	{

		// Basically based on the get data we need to build a DB query using simple conditional statements
		$this->_load_query(ee()->uri->segment(3), 'job_search');

		// Load Pagination Object
		$this->EE->load->library('pagination');
		$this->EE->load->model('search_model');
	
		$query_array = array(
			"title" => $this->EE->input->get('title'),
			"sector" => $this->EE->input->get('sector'),
			"contract" => $this->EE->input->get('contract'),
			"region" => $this->EE->input->get('region'),
			"salary_min" => $this->EE->input->get('salary_min'),
			"salary_max" => $this->EE->input->get('salary_max')
		);

		$limit = 7;
		$page = (ee()->uri->segment(4)) ? ee()->uri->segment(4) : 0;
		$tagdata = NULL;

		if($result = $this->EE->search_model->search($query_array, $limit, $page))
		{

			$count = $this->EE->search_model->search($query_array);
			$data = array();

			foreach($result->result_array() as $row)
			{
				$data[] = $row;
			}

			$tagdata = ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data);

			$config = array();
			$config['base_url'] = ee()->functions->create_url('job-seekers/results/'.ee()->uri->segment(3).'/');
			$config['uri_segment'] = 4;
			$config['total_rows'] = $count->num_rows();
			$config['per_page'] = $limit;			 
			$config['num_links'] = 5;
			$config['full_tag_open'] = '<ul class="pagination csspagigreen">';
			$config['full_tag_close'] = '</ul>';
			$config['first_link'] = 'First';
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_link'] = 'Last';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['next_link'] = '&gt;';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_link'] = '&lt;';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';

			$this->EE->pagination->initialize($config);
			$data['paginate'] = $this->EE->pagination->create_links();

			$tagdata .= $data['paginate'];

			return $tagdata;
		}
		else
		{
			// load no result
			return $this->EE->TMPL->no_results();
		}

	}

	// {exp:job_seekers:seekers_search_form(}
	public function seekers_search()
	{
		// Get the post data
		$tag_vars = array();
	    $tag_vars[0] = array(
			"sector" => $this->EE->input->post('sector'),
			"contract" => $this->EE->input->post('contract_type'),
			"region" => $this->EE->input->post('region'),
			"salary_min" => $this->EE->input->post('salary_min'),
			"salary_max" => $this->EE->input->post('salary_max')
		);

		if($this->EE->input->post('process') == "1")
		{	
			// Start form validation here
			// use CI validation library to check submission
			$this->EE->load->helper('form');
			$this->EE->load->library('form_validation');
			$this->EE->form_validation->set_rules('salary_min', 'Salary minimum', 'numeric');
			$this->EE->form_validation->set_rules('salary_max', 'Salary maximum', 'numeric');
			
			if($this->EE->form_validation->run() == FALSE)
			{
				$form_errors = array();
					foreach (array('salary_min', 'salary_max') as $field_name)
					{
						$field_error = form_error($field_name);
						if ($field_error)
		                {
		                    $form_errors[] = $field_error;
		                    $tag_vars[0]['error:'.$field_name] = $field_error;
		                }
		            }
	
				if ($this->EE->TMPL->fetch_param('error_handling') != 'inline')
				{
					return $this->EE->output->show_user_error(FALSE, $form_errors);
				}
			}
			else
			{
				// We save the data as a get query in the db
				$this->EE->db->insert('job_seekers_search', array(
					'get_query' => http_build_query($tag_vars[0]),
					'search_date' => $this->EE->localize->now
					));
				
				$query_id = $this->EE->db->insert_id();

				$this->results($query_id);

				/** ----------------------------------------
				/**  Redirect to search results page
				/** ----------------------------------------*/

				$path = reduce_double_slashes(
					ee()->functions->create_url('job-staff/results'.'/'.$query_id.'/')
				);

				return ee()->functions->redirect($path);					
			}
		}

    	// Build an array with the form data
		$form_data = array(
			"id" => "form",
			"action" => $this->EE->functions->fetch_current_uri()
			);

		if($this->EE->TMPL->fetch_param('class') != "")
		{
			$form_data['class'] = $this->EE->TMPL->fetch_param('class');
		}

		$form = $this->EE->functions->form_declaration($form_data);
		$form .= $this->EE->TMPL->parse_variables(ee()->TMPL->tagdata, $tag_vars);

		// Return form
		return $form;			
	}

		public function seekers_results()
		{

			// Basically based on the get data we need to build a DB query using simple conditional statements
			$this->_load_query(ee()->uri->segment(3), 'job_seekers_search');

			// Load Pagination Object
			$this->EE->load->library('pagination');
			$this->EE->load->model('search_model');
		
			$query_array = array(
				"sector" => $this->EE->input->get('sector'),
				"contract" => $this->EE->input->get('contract'),
				"region" => $this->EE->input->get('region'),
				"candidate_salary_min" => $this->EE->input->get('salary_min'),
				"candidate_salary_max" => $this->EE->input->get('salary_max')
			);

			$limit = 15;
			$page = (ee()->uri->segment(4)) ? ee()->uri->segment(4) : 0;
			$tagdata = NULL;

			if($result = $this->EE->search_model->seekers_search($query_array, $limit, $page))
			{

				$count = $this->EE->search_model->seekers_search($query_array);
				$data = array();

				foreach($result->result_array() as $row)
				{
					$data[] = $row;
				}

				$table = "<table class='table'>";
				$tablend = "</table>";

				$tagdata = $table . ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $data) . $tablend;

				$config = array();
				$config['base_url'] = ee()->functions->create_url('job-staff/results/'.ee()->uri->segment(3).'/');
				$config['uri_segment'] = 4;
				$config['total_rows'] = $count->num_rows();
				$config['per_page'] = $limit;			 
				$config['num_links'] = 5;
				$config['full_tag_open'] = '<ul class="pagination csspagigreen">';
				$config['full_tag_close'] = '</ul>';
				$config['first_link'] = 'First';
				$config['first_tag_open'] = '<li>';
				$config['first_tag_close'] = '</li>';
				$config['last_link'] = 'Last';
				$config['last_tag_open'] = '<li>';
				$config['last_tag_close'] = '</li>';
				$config['next_link'] = '&gt;';
				$config['next_tag_open'] = '<li>';
				$config['next_tag_close'] = '</li>';
				$config['prev_link'] = '&lt;';
				$config['prev_tag_open'] = '<li>';
				$config['prev_tag_close'] = '</li>';
				$config['cur_tag_open'] = '<li class="active"><a href="#">';
				$config['cur_tag_close'] = '</a></li>';
				$config['num_tag_open'] = '<li>';
				$config['num_tag_close'] = '</li>';

				$this->EE->pagination->initialize($config);
				$data['paginate'] = $this->EE->pagination->create_links();

				$tagdata .= $data['paginate'];

				return $tagdata;
			}
			else
			{
				// load no result
				return $this->EE->TMPL->no_results();
			}

		}
	private function _load_query($query_id, $table) {
				
		$rows = $this->EE->db->get_where($table, array('id' => $query_id))->result();

		if (isset($rows[0])) {
			parse_str($rows[0]->get_query, $_GET);		
		}
		
	}

	function my_form()
	{
	    // load default tag variables
	    $tag_vars = array();
	    $tag_vars[0] = array(
	        'first_name' => '',
	        'error:first_name' => '',
	        'last_name' => '',
	        'error:last_name' => ''
	    );

	    // handle a form submission
	    if ($this->EE->input->post('my_form_hidden') == '1')
	    {
	        // load POST data into tag
	        $tag_vars[0]['first_name'] = $this->EE->input->post('first_name', TRUE);
	        $tag_vars[0]['last_name'] = $this->EE->input->post('last_name', TRUE);

	        // use CI validation library to check submission
	        $this->EE->load->helper('form');
	        $this->EE->load->library('form_validation');
	        $this->EE->form_validation->set_rules('first_name', 'lang:first_name', 'required');
	        $this->EE->form_validation->set_rules('last_name', 'lang:first_name', 'required');

	        $valid_form = $this->EE->form_validation->run();
	        if ($valid_form)
	        {
	            // probably save something to database, then redirect
	        }
	        else
	        {
	            $form_errors = array();
	            foreach (array('first_name', 'last_name') as $field_name)
	            {
	                $field_error = form_error($field_name);
	                if ($field_error)
	                {
	                    $form_errors[] = $field_error;
	                    $tag_vars[0]['error:'.$field_name] = $field_error;
	                }
	            }

	            if ($this->EE->TMPL->fetch_param('error_handling') != 'inline')
	            {
	                // show default EE error page
	                return $this->EE->output->show_user_error(FALSE, $form_errors);
	            }
	        }
	    }

	    // parse and output tagdata
	    $out = $this->EE->functions->form_declaration(array(
	        'action' => $this->EE->functions->fetch_current_uri(),
	        'hidden_fields' => array('my_form_hidden')));
	    $out .= $this->EE->TMPL->parse_variables($tagdata, $tag_vars);
	    return $out.'</form>';
	}
}