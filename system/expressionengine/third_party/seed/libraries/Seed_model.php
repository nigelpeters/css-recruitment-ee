<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Load CI model if it doesn't exist
if ( ! class_exists('CI_model'))
{
	load_class('Model', 'core');
}

/**
 * Seed Base Model class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_model extends CI_Model {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	private $_table;

	private $_pk;

	private $_attributes = array();

	protected $EE;

	private $_params = array();


	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * PHP5 Constructor
	 *
	 * @return     void
	 */
	function __construct( )
	{
		// Call parent constructor
		parent::__construct();

		if ( ! function_exists('ee') ) {
			function ee() {	return get_instance(); }
		}
	}

	
	// --------------------------------------------------------------------

	/**
	 * Sets table, PK and attributes
	 *
	 * @access      protected
	 * @param       string    Table name
	 * @param       string    Primary Key name
	 * @param       array     Attributes
	 * @return      void
	 */
	protected function initialize($table, $pk, $attributes)
	{
		// Check table prefix
		$prefix = ee()->db->dbprefix;

		// Add prefix to table name if not there
		if (substr($table, 0, strlen($prefix)) != $prefix)
		{
			$table = $prefix.$table;
		}

		// Set the values
		$this->_table       = $table;
		$this->_pk          = $pk;
		$this->_attributes  = $attributes;
	}

	// --------------------------------------------------------------------

	/**
	 * Load models based on this main model
	 *
	 * @access      public
	 * @return      void
	 */
	public function load_models()
	{
		$EE =& get_instance();

		$EE->load->model("seed_channel_model");
		$EE->load->model("seed_generator_model");
	}

	// --------------------------------------------------------------------

	/**
	 * Return table name
	 *
	 * @access      public
	 * @return      string
	 */
	public function table()
	{
		return $this->_table;
	}

	// --------------------------------------------------------------------

	/**
	 * Return primary key
	 *
	 * @access      public
	 * @return      string
	 */
	public function pk()
	{
		return $this->_pk;
	}

	// --------------------------------------------------------------------

	/**
	 * Return array of attributes, sans PK
	 *
	 * @access      public
	 * @return      array
	 */
	public function attributes()
	{
		return array_keys($this->_attributes);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Check key is a valid attribute
	 *
	 * @access      public
	 * @return      array
	 */
	public function is_attribute( $key )
	{
		if( array_key_exists( $key, $this->_attributes ) ) return TRUE;

		elseif( $key == $this->_pk ) return TRUE;

		return FALSE;
	}



	// --------------------------------------------------------------------

	/**
	 * Return one record by primary key or attribute
	 *
	 * @access      public
	 * @param       int       id of the record to fetch
	 * @param       string    attribute to check
	 * @return      array
	 */
	public function get_one($id, $attr = FALSE)
	{
		if ($attr === FALSE) $attr = $this->_pk;

		return ee()->db->where($attr, $id)->get($this->_table)->row_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Return multiple records
	 *
	 * @access      public
	 * @return      array
	 */
	public function get_all()
	{
		return ee()->db->get($this->_table)->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Return multiple records' primary keys
	 *
	 * @access      public
	 * @return      array
	 */
	public function get_all_pk()
	{
		return ee()->db->select( $this->_pk )->get($this->_table)->result_array();
	}
	// --------------------------------------------------------------------

	/**
	 * Return an empty row for data initialisation
	 *
	 * @access      public
	 * @return      array
	 */
	public function empty_row()
	{
		$row = array_merge(array($this->_pk), $this->attributes());
		$row = array_combine($row, array_fill(0, count($row), ''));
		return $row;
	}

	// --------------------------------------------------------------------

	/**
	 * Insert record into DB
	 *
	 * @access      public
	 * @param       array     data to insert
	 * @return      int
	 */
	public function insert($data = array())
	{
		if (empty($data))
		{
			// loop through attributes to get posted data
			foreach ($this->attributes() AS $attr)
			{
				if (($val = ee()->input->post($attr)) !== FALSE)
				{
					$data[$attr] = $val;
				}
			}
		}
		else
		{
			// Check our passed data, and drop any non attributes
			foreach( $data as $key => $val )
			{
				if( is_array( $val ) ) unset( $data[ $key ] );
				elseif( ! $this->is_attribute( $key ) ) unset( $data[ $key ] );

			}
		}

		// Insert data and return inserted id
		ee()->db->insert($this->_table, $data);
		return ee()->db->insert_id();
	}

	// --------------------------------------------------------------------


	/**
	 * Update record into DB
	 *
	 * @access      public
	 * @param       array     data to insert
	 * @return      int
	 */
	public function update($id, $data = array())
	{
		if (empty($data))
		{
			// loop through attributes to get posted data
			foreach ($this->attributes() AS $attr)
			{
				if (($val = ee()->input->post($attr)) !== FALSE)
				{
					$data[$attr] = $val;
				}
			}
		}
		else
		{
			// Check our passed data, and drop any non attributes
			foreach( $data as $key => $val )
			{
				if( is_array( $val ) ) unset( $data[ $key ] );
				elseif( ! $this->is_attribute( $key ) ) unset( $data[ $key ] );

			}
		}

		// Insert data and return inserted id
		ee()->db->update($this->_table, $data, "{$this->_pk} = '{$id}'");
	}

	// --------------------------------------------------------------------

	/**
	 * Update record into DB
	 *
	 * @access      public
	 * @param       array     data to insert
	 * @return      int
	 */
	public function insert_update($id, $data = array())
	{
		if (empty($data))
		{
			// loop through attributes to get posted data
			foreach ($this->attributes() AS $attr)
			{
				if (($val = ee()->input->post($attr)) !== FALSE)
				{
					$data[$attr] = $val;
				}
			}
		}
		else
		{
			// Check our passed data, and drop any non attributes
			foreach( $data as $key => $val )
			{
				if( is_array( $val ) ) unset( $data[ $key ] );
				elseif( ! $this->is_attribute( $key ) ) unset( $data[ $key ] );
			}
		}

		$sql = ee()->db->insert_string($this->_table, $data);


		$sql .= " ON DUPLICATE KEY UPDATE ";

		$temp = array();

		foreach( $data as $key => $row ) 
		{
			$temp[] = " `" . $key . "` = '" . ee()->db->escape_str( $row ) . "'";
		}

		$sql .= implode( ", ", $temp );

		// Insert data and return inserted id
		ee()->db->query( $sql );
	}

	// --------------------------------------------------------------------

	/**
	 * Delete record
	 *
	 * @access      public
	 * @param       array     data to insert
	 * @param       string    optional attribute to delete records by
	 * @return      void
	 */
	public function delete($id, $attr = FALSE)
	{
		if ( ! is_array($id))
		{
			$id = array($id);
		}

		if ($attr === FALSE) $attr = $this->_pk;

		ee()->db->where_in($attr, $id)->delete($this->_table);
	}

	// --------------------------------------------------------------------

	/**
	 * Installs given table
	 *
	 * @access      public
	 * @return      void
	 */
	public function install()
	{
		// Begin composing SQL query
		$sql = "CREATE TABLE IF NOT EXISTS {$this->_table} ( ";

		// Add primary key -- is it an array?
		if (is_array($this->_pk))
		{
			foreach ($this->_pk AS $key)
			{
				$sql .= "{$key} int(10) unsigned NOT NULL, ";
			}
		}
		else
		{
			$sql .= "{$this->_pk} int(10) unsigned NOT NULL, ";
		}

		// add other attributes
		foreach ($this->_attributes AS $attr => $props)
		{
			$sql .= "{$attr} {$props}, ";
		}

		// Set PK
		$sql .= "PRIMARY KEY (".implode(',', (array) $this->_pk)."))";

		// Execute query
		ee()->db->query($sql);
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstalls given table
	 *
	 * @access      public
	 * @return      void
	 */
	public function uninstall()
	{
		ee()->db->query("DROP TABLE IF EXISTS {$this->_table}");
	}

	// --------------------------------------------------------------------

	public function get_plugins( $plugin_list )
	{
		ee()->load->helper(array('file'));
		
		$plugins = array();
		ee()->seed_plugins = new stdClass();

		require_once SEED_FIELD_PLUGIN_PATH . 'seed.fieldtype.php';
	
		foreach( $plugin_list as $type ) 
		{
			if( trim($type) != '') $paths[] = SEED_FIELD_PLUGIN_PATH . '/' . $type;
		}

		$found_plugins = array();

		foreach ($paths as $i => $path)
		{
			if ( ! is_dir($path))
			{
				continue;
			}
			
			foreach (get_filenames($path, TRUE) as $file)
			{
				$class = basename($file, EXT);

				if (strpos($class, 'seed.') !== 0 || strpos($class, '~') !== FALSE)
				{
					continue;
				}

				$class = substr( $class, 5 );
				
				$plugin = $this->create_child( $class );

				ee()->seed_plugins->$class = $plugin;

				$plugins[ $class ] = get_object_vars($plugin);
			}
		}
		
		return $plugins;
	}

	// --------------------------------------------------------------------

	public function get_plugin( $plugin_name )
	{
		return $this->get_plugins( array( $plugin_name ) );
	}


	public static function create_child($path)
	{
		$class = 'Seed_fieldtype_' . $path;

		require_once SEED_FIELD_PLUGIN_PATH . $path . '/seed.'. $path . EXT;

		$child = new $class;
				
		$child->initialize();
		
		return $child;
	}


	// --------------------------------------------------------------------

	public function get_options( $options_list )
	{
		ee()->load->helper(array('file'));

		$options = array();
		ee()->seed_options = new stdClass();
		
		require_once SEED_OPTION_PLUGIN_PATH . 'seed.option.php';
	
		foreach( $options_list as $type ) 
		{
			if( trim($type) != '') $paths[] = SEED_OPTION_PLUGIN_PATH . '/' . $type;
		}

		$found_options = array();

		foreach ($paths as $i => $path)
		{
			if ( ! is_dir($path))
			{
				continue;
			}
			
			foreach (get_filenames($path, TRUE) as $file)
			{
				$class = basename($file, EXT);

				if (strpos($class, 'seed.') !== 0 || strpos($class, '~') !== FALSE)
				{
					continue;
				}

				$class = substr( $class, 5 );
				
				$option = $this->create_option( $class );

				ee()->seed_options->$class = $option;

				$options[ $class ] = get_object_vars($option);
			}
		}
		
		return $options;
	}


	public static function create_option($path)
	{
		$class = 'Seed_option_' . $path;

		require_once SEED_OPTION_PLUGIN_PATH . $path . '/seed.'. $path . EXT;

		$child = new $class;
				
		$child->initialize();
		
		return $child;
	}

}
// End of file Seed_model.php