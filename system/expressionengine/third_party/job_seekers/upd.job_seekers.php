<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_seekers_upd {
    
    public $version = '1.0';

    private $module_name = "Job_seekers";
    private $EE;
    
    // Constructor
    public function __construct()
    {
        $this->EE =& get_instance();
    }
    
    /**
     * Install the module
     *
     * @return boolean TRUE
     */
    public function install()
    {
        $mod_data = array(
            'module_name' => $this->module_name,
            'module_version' => $this->version,
            'has_cp_backend' => "y",
            'has_publish_fields' => 'n'
        );
        $this->EE->db->insert('modules', $mod_data);
        
		$this->EE->load->dbforge();
	    
	    $wishlists = array(
	        'id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'auto_increment'=> TRUE
	        ),
	        'member_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'job_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'date_added' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'default' => '0',
	            'null' => FALSE
	        )
	    );

	    $this->EE->dbforge->add_field($wishlists);
	    $this->EE->dbforge->add_key('id', TRUE);
	    $this->EE->dbforge->create_table('job_wishlists');

	    $applications = array(
	        'id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'auto_increment'=> TRUE
	        ),
	        'author_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'member_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'job_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'application_message' => array(
	            'type' => 'text'
	        ),
	        'date_applied' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'default' => '0',
	            'null' => FALSE
	        )
	    );
	    $this->EE->dbforge->add_field($applications);
	    $this->EE->dbforge->add_key('id', TRUE);
	    $this->EE->dbforge->create_table('job_applications');

	    $job_search = array(
	        'id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'auto_increment'=> TRUE
	        ),
	        'member_id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'null' => FALSE
	        ),
	        'get_query' => array(
	            'type' => 'mediumtext',
	            'null' => TRUE
	        ),
	        'search_date' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'default' => '0',
	            'null' => FALSE
	        )
	    );

	    $this->EE->dbforge->add_field($job_search);
	    $this->EE->dbforge->add_key('id', TRUE);
	    $this->EE->dbforge->create_table('job_search');

	    $job_seekers_search = array(
	        'id' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'auto_increment'=> TRUE
	        ),
	        'get_query' => array(
	            'type' => 'mediumtext',
	            'null' => TRUE
	        ),
	        'search_date' => array(
	            'type' => 'int',
	            'constraint' => '10',
	            'unsigned' => TRUE,
	            'default' => '0',
	            'null' => FALSE
	        )
	    );

	    $this->EE->dbforge->add_field($job_seekers_search);
	    $this->EE->dbforge->add_key('id', TRUE);
	    $this->EE->dbforge->create_table('job_seekers_search');
	    // CV Upload function
	    $data = array( 'class' => $this->module_name, 'method' => 'upload_cv' );
	    $this->EE->db->insert('actions', $data);

	    // Job wishlist function
	    $data = array( 'class' => $this->module_name, 'method' => 'wishlist_toggle' );
	    $this->EE->db->insert('actions', $data);

	    // Job application functions
	    $data = array( 'class' => $this->module_name, 'method' => 'job_app_form' );
	    $this->EE->db->insert('actions', $data);

	    $data = array( 'class' => $this->module_name, 'method' => 'job_app_process' );
	    $this->EE->db->insert('actions', $data);

	    // Job search functions
	    $data = array( 'class' => $this->module_name, 'method' => 'job_search_form' );
	    $this->EE->db->insert('actions', $data);

	    $data = array( 'class' => $this->module_name, 'method' => 'results' );
	    $this->EE->db->insert('actions', $data);

	    $data = array( 'class' => $this->module_name, 'method' => 'job_search_list' );
	    $this->EE->db->insert('actions', $data);

	    // Job seekers search functions
	    $data = array( 'class' => $this->module_name, 'method' => 'seekers_search' );
	    $this->EE->db->insert('actions', $data);

	    $data = array( 'class' => $this->module_name, 'method' => 'seekers_results' );
	    $this->EE->db->insert('actions', $data);

	    return TRUE;
    
    }
    
    /**
     * Uninstall the module
     *
     * @return boolean TRUE
     */
    public function uninstall()
    {
        $this->EE->db->select('module_id');
        $query = $this->EE->db->get_where('modules', 
            array( 'module_name' => $this->module_name )
        );
        
        $this->EE->db->where('module_id', $query->row('module_id'));
        $this->EE->db->delete('module_member_groups');
        
        $this->EE->db->where('module_name', $this->module_name);
        $this->EE->db->delete('modules');
        
        $this->EE->db->where('class', $this->module_name);
        $this->EE->db->delete('actions');
        
        $this->EE->db->where('class', $this->module_name.'_mcp');
        $this->EE->db->delete('actions');
        
        $this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('job_applications');
		$this->EE->dbforge->drop_table('job_wishlists');
		$this->EE->dbforge->drop_table('job_search');
		$this->EE->dbforge->drop_table('job_seekers_search');
        
        return TRUE;
    }
    
    /**
     * Update the module
     *
     * @return boolean
     */
    public function update($current = '')
    {
        if ($current == $this->version) {
            // No updates
            return FALSE;
        }
        
        return TRUE;
    }
    
}

/* End of file upd.job_seekers.php */
/* Location: /system/expressionengine/third_party/job_seekers/upd.job_seekers.php */