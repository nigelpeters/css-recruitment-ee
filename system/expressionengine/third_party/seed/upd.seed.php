<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// include config file
include PATH_THIRD.'seed/config'.EXT;

/**
 * Seed Update Class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_upd {

	// --------------------------------------------------------------------
	// PROPERTIES
	// --------------------------------------------------------------------

	/**
	 * Version number
	 *
	 * @access      public
	 * @var         string
	 */
	public $version = SEED_VERSION;


	// --------------------------------------------------------------------
	// METHODS
	// --------------------------------------------------------------------

	/**
	 * Constructor: sets EE instance
	 *
	 * @access      public
	 * @return      null
	 */
	public function __construct()
	{
		if ( ! function_exists('ee') ) {
			function ee() {	return get_instance(); }
		}

		// Define the package path
		ee()->load->add_package_path(PATH_THIRD.'Seed');

		// Load libraries...
		ee()->load->library('Seed_model');

		// Load other models
		Seed_model::load_models();
	}

	// --------------------------------------------------------------------

	/**
	 * Install the module
	 *
	 * @access      public
	 * @return      bool
	 */
	public function install()
	{
		// --------------------------------------
		// Add row to modules table
		// --------------------------------------

		ee()->db->insert('modules', array(
			'module_name'    => SEED_CLASS_NAME,
			'module_version' => SEED_VERSION,
			'has_cp_backend' => 'y'
		));

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Uninstall the module
	 *
	 * @return	bool
	 */
	function uninstall()
	{
		// --------------------------------------
		// get module id
		// --------------------------------------

		$query = ee()->db->select('module_id')
		       ->from('modules')
		       ->where('module_name', SEED_CLASS_NAME)
		       ->get();


		// --------------------------------------
		// remove references from modules
		// --------------------------------------

		ee()->db->where('module_name', SEED_CLASS_NAME);
		ee()->db->delete('modules');

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update the module
	 *
	 * @return	bool
	 */
	function update($current = '')
	{
		// --------------------------------------
		// Same Version - nothing to do
		// --------------------------------------

		if( version_compare( $current, '1.0.3') ) 
		{
			// Remove the old bad record that was inserted in 0.9.1
			// in the exp_modules table
			ee()->db->where('module_name', 'SEED_CLASS_NAME')
							->delete('modules');
		}

		if ($current == '' OR version_compare($current, SEED_VERSION) === 0)
		{
			return FALSE;
		}

		// Returning TRUE updates db version number
		return TRUE;
	}

	// --------------------------------------------------------------------

} // End class

/* End of file upd.Seed.php */