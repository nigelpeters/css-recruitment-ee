<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Css_helpers {

	function css_helpers()
	{
			$this->EE =& get_instance();
	}

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

	public function regionlist()
	{
		$country = $this->EE->TMPL->fetch_param('county_region');

		ee()->load->helper('form');

		 // Get the country list from the DB
		$this->EE->db->select('field_list_items');       
		$this->EE->db->from('exp_channel_fields');
		$this->EE->db->where('field_name', 'county_region');
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

		return form_dropdown('county_region', $options, $country, $js);

	}
}