<?php

class search_model extends CI_Model {

	var $job_desc;
	var $contract;
	var $region;
	var $salary_min;
	var $salary_max;
	var $cand_salary_min;
	var $cand_salary_max;
	var $salary_unit;
	var $salary_desc;

    function __construct(){
        parent::__construct();
    }
	
	function search($query_array, $limit = NULL, $start = NULL)
	{

        $this->cleanfields('5');
		
		// To search the title
		// SELECT title FROM exp_channel_titles WHERE title LIKE '%data%' AND channel_id = 10
		$this->db->select('ct.entry_id, ct.title, ct.status, ct.url_title, ct.entry_date, cp.cat_id, cats.cat_name, '.$this->job_desc.' AS job_description, '.$this->contract.' AS contract, '.$this->region.' AS region, '.$this->salary_min.' AS salary_min, '.$this->salary_max.' AS salary_max, '.$this->salary_unit.' AS salary_unit, '.$this->salary_desc.' AS salary_desc, ', FALSE);
		$this->db->from('exp_channel_titles as ct');

		if($query_array['title'] != "")
		{
			$this->db->like('title', $query_array['title']);
		}

		$this->db->where('ct.channel_id', '10');

		// Join the data table
		$this->db->join('exp_channel_data as cd', 'cd.entry_id = ct.entry_id', 'left');
		$this->db->join('exp_category_posts as cp', 'cp.entry_id = ct.entry_id', 'left');
		$this->db->join('exp_categories as cats', 'cats.cat_id = cp.cat_id', 'left');

		// For the sector
		if($query_array['sector'] != "")
		{
			$this->db->where('cp.cat_id', $query_array['sector']);
		}

		// For the contract type
		if($query_array['contract'] != "0")
		{
			$this->db->where($this->contract, $query_array['contract']);
		}

		// For the region
		if($query_array['region'] != "0")
		{
			$this->db->where($this->region, $query_array['region']);
		}

		// For the salary minimum
		if($query_array['salary_min'] != "")
		{
			$this->db->where($this->salary_min.' >=', $query_array['salary_min']);
		}

		// For the salary maximum
		if($query_array['salary_max'] != "")
		{
			$this->db->where($this->salary_max.' <=', $query_array['salary_max']);
		}

		$this->db->where('ct.status', 'open');
		$this->db->where('ct.status !=', 'closed');

		$date = $this->localize->now;

		$where = "ct.entry_date < ".$date." ";
		$where2 = "(ct.expiration_date = 0 OR ct.expiration_date > ".$date.") ";

		$this->db->where('ct.entry_date <', $date);

		$this->db->where("(ct.expiration_date = 0 OR ct.expiration_date > $date)");

		//$this->db->where('ct.expiration_date', 0);
		//$this->db->or_where('ct.expiration_date >', $date);

		if($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}

		$this->db->order_by('ct.entry_date', "desc");
		$this->db->order_by('ct.entry_id', "desc");

		$q = $this->db->get();


		// Return multiple rows
		if($q->num_rows() > 0)
		{
			return $q;
		}			
	}

	function seekers_search($query_array, $limit = NULL, $start = NULL)
	{

		$this->cleanfields('4');
		
		// To search the title
		// SELECT title FROM exp_channel_titles WHERE title LIKE '%data%' AND channel_id = 10
		$this->db->select('mem.member_id, ct.title, ct.url_title, ct.entry_date, ct.entry_id, '.$this->contract.' AS contract, '.$this->region.' AS region, '.$this->cand_salary_min.' AS salary_min, '.$this->cand_salary_max.' AS salary_max, ', FALSE);
		$this->db->from('exp_channel_titles as ct');
		//$this->db->like('title', $query_array['title']);
		$this->db->where('ct.channel_id', '11');

		// Join the data table
		$this->db->join('exp_channel_data as cd', 'cd.entry_id = ct.entry_id', 'left');
	
		$this->db->join('exp_members as mem', 'mem.member_id = ct.author_id', 'left');

		// For the sector
		if($query_array['sector'] != "")
		{
			$this->db->select(' cp.cat_id, cats.cat_name, ');
			$this->db->join('exp_category_posts as cp', 'cp.entry_id = ct.entry_id', 'left');
			$this->db->join('exp_categories as cats', 'cats.cat_id = cp.cat_id', 'left');
			$this->db->where('cp.cat_id', $query_array['sector']);
		}

		// For the contract type
		if($query_array['contract'] != "0")
		{
			$this->db->where($this->contract, $query_array['contract']);
		}

		// For the region
		if($query_array['region'] != "0")
		{
			$this->db->where($this->region, $query_array['region']);
		}

		// For the salary minimum
		if($query_array['candidate_salary_min'] != "")
		{
			$this->db->where($this->cand_salary_min.' >=', $query_array['candidate_salary_min']);
		}

		// For the salary maximum
		if($query_array['candidate_salary_max'] != "")
		{
			$this->db->where($this->cand_salary_max.' <=', $query_array['candidate_salary_max']);
		}

		if($limit != NULL)
		{
			$this->db->limit($limit, $start);
		}

		$this->db->order_by('ct.entry_id', "asc");

		$q = $this->db->get();

		// Return multiple rows
		if($q->num_rows() > 0)
		{
			return $q;
		}			
	}
	function cleanfields($group_id)
	{
		// Get the fields from the DB
		$this->db->select('field_id, field_name');
		$this->db->from('channel_fields');
		$this->db->where('group_id', $group_id);
		$fields = $this->db->get();

		// turn array into useable id's
		$cleanfields = array();

		foreach ($fields->result() as $field ) {
			$cleanfields[$field->field_name] = $field->field_id;
		}

		if($group_id == '5')
		{
			$this->job_desc = "cd.field_id_".$cleanfields['job_description'];
			$this->salary_min = "cd.field_id_".$cleanfields['salary_min'];
			$this->salary_max = "cd.field_id_".$cleanfields['salary_max'];
			$this->salary_unit = "cd.field_id_".$cleanfields['salary_unit'];
			$this->salary_desc = "cd.field_id_".$cleanfields['salary_description'];
			$this->contract = "cd.field_id_".$cleanfields['job_contract_type'];
			$this->region = "cd.field_id_".$cleanfields['region'];
		}
		else if($group_id == '4')
		{
			$this->cand_salary_min = "cd.field_id_".$cleanfields['candidate_salary_min'];
			$this->cand_salary_max = "cd.field_id_".$cleanfields['candidate_salary_min'];
			$this->contract = "cd.field_id_".$cleanfields['contract_type'];
			$this->region = "cd.field_id_".$cleanfields['county_region'];
		}	

	}
}