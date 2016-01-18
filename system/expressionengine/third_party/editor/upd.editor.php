<?php if (!defined('BASEPATH')) die('No direct script access allowed');

// include config file
require_once dirname(dirname(__FILE__)).'/editor/config.php';

/**
 * Install / Uninstall and updates the modules
 *
 * @package			DevDemon_Editor
 * @author			DevDemon <http://www.devdemon.com> - Lead Developer @ Parscale Media
 * @copyright 		Copyright (c) 2007-2012 Parscale Media <http://www.parscale.com>
 * @license 		http://www.devdemon.com/license/
 * @link			http://www.devdemon.com/editor/
 * @see				http://expressionengine.com/user_guide/development/module_tutorial.html#update_file
 */
class Editor_upd
{
	/**
	 * Module version
	 *
	 * @var string
	 * @access public
	 */
	public $version		=	EDITOR_VERSION;

	/**
	 * Module Short Name
	 *
	 * @var string
	 * @access private
	 */
	private $module_name	=	EDITOR_CLASS_NAME;

	/**
	 * Has Control Panel Backend?
	 *
	 * @var string
	 * @access private
	 */
	private $has_cp_backend = 'y';

	/**
	 * Has Publish Fields?
	 *
	 * @var string
	 * @access private
	 */
	private $has_publish_fields = 'n';


	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}

	// ********************************************************************************* //

	/**
	 * Installs the module
	 *
	 * Installs the module, adding a record to the exp_modules table,
	 * creates and populates and necessary database tables,
	 * adds any necessary records to the exp_actions table,
	 * and if custom tabs are to be used, adds those fields to any saved publish layouts
	 *
	 * @access public
	 * @return boolean
	 **/
	public function install()
	{
		// Load dbforge
		$this->EE->load->dbforge();

		//----------------------------------------
		// EXP_MODULES
		//----------------------------------------
		$module = array(	'module_name' => ucfirst($this->module_name),
							'module_version' => $this->version,
							'has_cp_backend' => 'y',
							'has_publish_fields' => 'n' );

		$this->EE->db->insert('modules', $module);

		//----------------------------------------
		// EXP_EDITOR_CONFIGS
		//----------------------------------------
		$fields = array(
			'config_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'			=> array('type' => 'SMALLINT',	'unsigned' => TRUE,	'default' => 1),
			'config_label'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'config_settings'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('config_id', TRUE);
		$this->EE->dbforge->create_table('editor_configs', TRUE);

		//----------------------------------------
		// EXP_EDITOR_BUTTONS
		//----------------------------------------
		$fields = array(
			'id'				=> array('type' => 'INT',		'unsigned' => TRUE,	'auto_increment' => TRUE),
			'site_id'			=> array('type' => 'SMALLINT',	'unsigned' => TRUE,	'default' => 1),
			'config_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'lowvar_id'			=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'matrixcol_id'		=> array('type' => 'INT',		'unsigned' => TRUE,	'default' => 1),
			'button_class'		=> array('type' => 'VARCHAR',	'constraint' => 250, 'default' => ''),
			'button_settings'	=> array('type' => 'TEXT'),
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('id', TRUE);
		$this->EE->dbforge->add_key('button_class');
		$this->EE->dbforge->add_key('config_id');
		$this->EE->dbforge->create_table('editor_buttons', TRUE);

		//----------------------------------------
		// Actions
		//----------------------------------------
		$module = array( 'class' => ucfirst($this->module_name), 'method' => 'ACT_file_upload');
		$this->EE->db->insert('actions', $module);

		//----------------------------------------
		// EXP_MODULES
		// The settings column, Ellislab should have put this one in long ago.
		// No need for a seperate preferences table for each module.
		//----------------------------------------
		if ($this->EE->db->field_exists('settings', 'modules') == FALSE)
		{
			$this->EE->dbforge->add_column('modules', array('settings' => array('type' => 'TEXT') ) );
		}

		//----------------------------------------
		// Add default configs
		//----------------------------------------
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('1', '1', 'Minimum', 'YToyODp7czo3OiJidXR0b25zIjthOjc6e2k6MDtzOjEwOiJmb3JtYXR0aW5nIjtpOjE7czoxOiJ8IjtpOjI7czo0OiJib2xkIjtpOjM7czo2OiJpdGFsaWMiO2k6NDtzOjE6InwiO2k6NTtzOjQ6ImxpbmsiO2k6NjtzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MTQ6ImltYWdlX2Jyb3dzaW5nIjtzOjM6InllcyI7czoxMjoiaW1hZ2Vfc3ViZGlyIjtzOjM6InllcyI7czoyOiJzMyI7YTo0OntzOjQ6ImZpbGUiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czo1OiJpbWFnZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjE0OiJhd3NfYWNjZXNzX2tleSI7czowOiIiO3M6MTQ6ImF3c19zZWNyZXRfa2V5IjtzOjA6IiI7fXM6NjoiaGVpZ2h0IjtzOjM6IjIwMCI7czo5OiJkaXJlY3Rpb24iO3M6MzoibHRyIjtzOjc6InRvb2xiYXIiO3M6MzoieWVzIjtzOjY6InNvdXJjZSI7czozOiJ5ZXMiO3M6NToiZm9jdXMiO3M6Mjoibm8iO3M6MTA6ImF1dG9yZXNpemUiO3M6MzoieWVzIjtzOjU6ImZpeGVkIjtzOjI6Im5vIjtzOjEyOiJjb252ZXJ0bGlua3MiO3M6MzoieWVzIjtzOjExOiJjb252ZXJ0ZGl2cyI7czozOiJ5ZXMiO3M6Nzoib3ZlcmxheSI7czozOiJ5ZXMiO3M6MTM6Im9ic2VydmVpbWFnZXMiO3M6MzoieWVzIjtzOjk6InNob3J0Y3V0cyI7czozOiJ5ZXMiO3M6MzoiYWlyIjtzOjI6Im5vIjtzOjM6Ind5bSI7czoyOiJubyI7czo4OiJwcm90b2NvbCI7czozOiJ5ZXMiO3M6MTg6ImFsbG93ZWR0YWdzX29wdGlvbiI7czo3OiJkZWZhdWx0IjtzOjExOiJhbGxvd2VkdGFncyI7czowOiIiO3M6MTQ6ImZvcm1hdHRpbmd0YWdzIjthOjc6e2k6MDtzOjE6InAiO2k6MTtzOjEwOiJibG9ja3F1b3RlIjtpOjI7czozOiJwcmUiO2k6MztzOjI6ImgxIjtpOjQ7czoyOiJoMiI7aTo1O3M6MjoiaDMiO2k6NjtzOjI6Img0Ijt9czo4OiJsYW5ndWFnZSI7czoyOiJlbiI7czo4OiJjc3NfZmlsZSI7czowOiIiO3M6NzoicGx1Z2lucyI7YTowOnt9fQ==')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('2', '1', 'Standard', 'YToyODp7czo3OiJidXR0b25zIjthOjE0OntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo5OiJ1bmRlcmxpbmUiO2k6NztzOjc6ImRlbGV0ZWQiO2k6ODtzOjE6InwiO2k6OTtzOjQ6ImxpbmsiO2k6MTA7czo0OiJmaWxlIjtpOjExO3M6NToiaW1hZ2UiO2k6MTI7czo1OiJ2aWRlbyI7aToxMztzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MTQ6ImltYWdlX2Jyb3dzaW5nIjtzOjM6InllcyI7czoxMjoiaW1hZ2Vfc3ViZGlyIjtzOjM6InllcyI7czoyOiJzMyI7YTo0OntzOjQ6ImZpbGUiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czo1OiJpbWFnZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjE0OiJhd3NfYWNjZXNzX2tleSI7czowOiIiO3M6MTQ6ImF3c19zZWNyZXRfa2V5IjtzOjA6IiI7fXM6NjoiaGVpZ2h0IjtzOjM6IjIwMCI7czo5OiJkaXJlY3Rpb24iO3M6MzoibHRyIjtzOjc6InRvb2xiYXIiO3M6MzoieWVzIjtzOjY6InNvdXJjZSI7czozOiJ5ZXMiO3M6NToiZm9jdXMiO3M6Mjoibm8iO3M6MTA6ImF1dG9yZXNpemUiO3M6MzoieWVzIjtzOjU6ImZpeGVkIjtzOjI6Im5vIjtzOjEyOiJjb252ZXJ0bGlua3MiO3M6MzoieWVzIjtzOjExOiJjb252ZXJ0ZGl2cyI7czozOiJ5ZXMiO3M6Nzoib3ZlcmxheSI7czozOiJ5ZXMiO3M6MTM6Im9ic2VydmVpbWFnZXMiO3M6MzoieWVzIjtzOjk6InNob3J0Y3V0cyI7czozOiJ5ZXMiO3M6MzoiYWlyIjtzOjI6Im5vIjtzOjM6Ind5bSI7czoyOiJubyI7czo4OiJwcm90b2NvbCI7czozOiJ5ZXMiO3M6MTg6ImFsbG93ZWR0YWdzX29wdGlvbiI7czo3OiJkZWZhdWx0IjtzOjExOiJhbGxvd2VkdGFncyI7czowOiIiO3M6MTQ6ImZvcm1hdHRpbmd0YWdzIjthOjc6e2k6MDtzOjE6InAiO2k6MTtzOjEwOiJibG9ja3F1b3RlIjtpOjI7czozOiJwcmUiO2k6MztzOjI6ImgxIjtpOjQ7czoyOiJoMiI7aTo1O3M6MjoiaDMiO2k6NjtzOjI6Img0Ijt9czo4OiJsYW5ndWFnZSI7czoyOiJlbiI7czo4OiJjc3NfZmlsZSI7czowOiIiO3M6NzoicGx1Z2lucyI7YTowOnt9fQ==')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('3', '1', 'Full', 'YToyODp7czo3OiJidXR0b25zIjthOjI4OntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo5OiJ1bmRlcmxpbmUiO2k6NztzOjc6ImRlbGV0ZWQiO2k6ODtzOjE6InwiO2k6OTtzOjEzOiJ1bm9yZGVyZWRsaXN0IjtpOjEwO3M6MTE6Im9yZGVyZWRsaXN0IjtpOjExO3M6Nzoib3V0ZGVudCI7aToxMjtzOjY6ImluZGVudCI7aToxMztzOjE6InwiO2k6MTQ7czo0OiJsaW5rIjtpOjE1O3M6NToiaW1hZ2UiO2k6MTY7czo1OiJ2aWRlbyI7aToxNztzOjQ6ImZpbGUiO2k6MTg7czo1OiJ0YWJsZSI7aToxOTtzOjE6InwiO2k6MjA7czo5OiJhbGlnbmxlZnQiO2k6MjE7czoxMToiYWxpZ25jZW50ZXIiO2k6MjI7czoxMDoiYWxpZ25yaWdodCI7aToyMztzOjc6Imp1c3RpZnkiO2k6MjQ7czoxOiJ8IjtpOjI1O3M6MTQ6Imhvcml6b250YWxydWxlIjtpOjI2O3M6MTE6InBhc3RlX3BsYWluIjtpOjI3O3M6MToifCI7fXM6MTQ6InVwbG9hZF9zZXJ2aWNlIjtzOjU6ImxvY2FsIjtzOjIwOiJmaWxlX3VwbG9hZF9sb2NhdGlvbiI7czoxOiIwIjtzOjIxOiJpbWFnZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoxNDoiaW1hZ2VfYnJvd3NpbmciO3M6MzoieWVzIjtzOjEyOiJpbWFnZV9zdWJkaXIiO3M6MzoieWVzIjtzOjI6InMzIjthOjQ6e3M6NDoiZmlsZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjU6ImltYWdlIjthOjE6e3M6NjoiYnVja2V0IjtzOjA6IiI7fXM6MTQ6ImF3c19hY2Nlc3Nfa2V5IjtzOjA6IiI7czoxNDoiYXdzX3NlY3JldF9rZXkiO3M6MDoiIjt9czo2OiJoZWlnaHQiO3M6MzoiMjAwIjtzOjk6ImRpcmVjdGlvbiI7czozOiJsdHIiO3M6NzoidG9vbGJhciI7czozOiJ5ZXMiO3M6Njoic291cmNlIjtzOjM6InllcyI7czo1OiJmb2N1cyI7czoyOiJubyI7czoxMDoiYXV0b3Jlc2l6ZSI7czozOiJ5ZXMiO3M6NToiZml4ZWQiO3M6Mjoibm8iO3M6MTI6ImNvbnZlcnRsaW5rcyI7czozOiJ5ZXMiO3M6MTE6ImNvbnZlcnRkaXZzIjtzOjM6InllcyI7czo3OiJvdmVybGF5IjtzOjM6InllcyI7czoxMzoib2JzZXJ2ZWltYWdlcyI7czozOiJ5ZXMiO3M6OToic2hvcnRjdXRzIjtzOjM6InllcyI7czozOiJhaXIiO3M6Mjoibm8iO3M6Mzoid3ltIjtzOjI6Im5vIjtzOjg6InByb3RvY29sIjtzOjM6InllcyI7czoxODoiYWxsb3dlZHRhZ3Nfb3B0aW9uIjtzOjc6ImRlZmF1bHQiO3M6MTE6ImFsbG93ZWR0YWdzIjtzOjA6IiI7czoxNDoiZm9ybWF0dGluZ3RhZ3MiO2E6Nzp7aTowO3M6MToicCI7aToxO3M6MTA6ImJsb2NrcXVvdGUiO2k6MjtzOjM6InByZSI7aTozO3M6MjoiaDEiO2k6NDtzOjI6ImgyIjtpOjU7czoyOiJoMyI7aTo2O3M6MjoiaDQiO31zOjg6Imxhbmd1YWdlIjtzOjI6ImVuIjtzOjg6ImNzc19maWxlIjtzOjA6IiI7czo3OiJwbHVnaW5zIjthOjA6e319')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('4', '1', 'Full (Visual Mode)', 'YToyODp7czo3OiJidXR0b25zIjthOjI4OntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo5OiJ1bmRlcmxpbmUiO2k6NztzOjc6ImRlbGV0ZWQiO2k6ODtzOjE6InwiO2k6OTtzOjEzOiJ1bm9yZGVyZWRsaXN0IjtpOjEwO3M6MTE6Im9yZGVyZWRsaXN0IjtpOjExO3M6Nzoib3V0ZGVudCI7aToxMjtzOjY6ImluZGVudCI7aToxMztzOjE6InwiO2k6MTQ7czo0OiJsaW5rIjtpOjE1O3M6NToiaW1hZ2UiO2k6MTY7czo1OiJ2aWRlbyI7aToxNztzOjQ6ImZpbGUiO2k6MTg7czo1OiJ0YWJsZSI7aToxOTtzOjE6InwiO2k6MjA7czo5OiJhbGlnbmxlZnQiO2k6MjE7czoxMToiYWxpZ25jZW50ZXIiO2k6MjI7czoxMDoiYWxpZ25yaWdodCI7aToyMztzOjc6Imp1c3RpZnkiO2k6MjQ7czoxOiJ8IjtpOjI1O3M6MTQ6Imhvcml6b250YWxydWxlIjtpOjI2O3M6MTE6InBhc3RlX3BsYWluIjtpOjI3O3M6MToifCI7fXM6MTQ6InVwbG9hZF9zZXJ2aWNlIjtzOjU6ImxvY2FsIjtzOjIwOiJmaWxlX3VwbG9hZF9sb2NhdGlvbiI7czoxOiIwIjtzOjIxOiJpbWFnZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoxNDoiaW1hZ2VfYnJvd3NpbmciO3M6MzoieWVzIjtzOjEyOiJpbWFnZV9zdWJkaXIiO3M6MzoieWVzIjtzOjI6InMzIjthOjQ6e3M6NDoiZmlsZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjU6ImltYWdlIjthOjE6e3M6NjoiYnVja2V0IjtzOjA6IiI7fXM6MTQ6ImF3c19hY2Nlc3Nfa2V5IjtzOjA6IiI7czoxNDoiYXdzX3NlY3JldF9rZXkiO3M6MDoiIjt9czo2OiJoZWlnaHQiO3M6MzoiMjAwIjtzOjk6ImRpcmVjdGlvbiI7czozOiJsdHIiO3M6NzoidG9vbGJhciI7czozOiJ5ZXMiO3M6Njoic291cmNlIjtzOjM6InllcyI7czo1OiJmb2N1cyI7czoyOiJubyI7czoxMDoiYXV0b3Jlc2l6ZSI7czozOiJ5ZXMiO3M6NToiZml4ZWQiO3M6Mjoibm8iO3M6MTI6ImNvbnZlcnRsaW5rcyI7czozOiJ5ZXMiO3M6MTE6ImNvbnZlcnRkaXZzIjtzOjM6InllcyI7czo3OiJvdmVybGF5IjtzOjM6InllcyI7czoxMzoib2JzZXJ2ZWltYWdlcyI7czozOiJ5ZXMiO3M6OToic2hvcnRjdXRzIjtzOjM6InllcyI7czozOiJhaXIiO3M6Mjoibm8iO3M6Mzoid3ltIjtzOjM6InllcyI7czo4OiJwcm90b2NvbCI7czozOiJ5ZXMiO3M6MTg6ImFsbG93ZWR0YWdzX29wdGlvbiI7czo3OiJkZWZhdWx0IjtzOjExOiJhbGxvd2VkdGFncyI7czowOiIiO3M6MTQ6ImZvcm1hdHRpbmd0YWdzIjthOjc6e2k6MDtzOjE6InAiO2k6MTtzOjEwOiJibG9ja3F1b3RlIjtpOjI7czozOiJwcmUiO2k6MztzOjI6ImgxIjtpOjQ7czoyOiJoMiI7aTo1O3M6MjoiaDMiO2k6NjtzOjI6Img0Ijt9czo4OiJsYW5ndWFnZSI7czoyOiJlbiI7czo4OiJjc3NfZmlsZSI7czowOiIiO3M6NzoicGx1Z2lucyI7YTowOnt9fQ==')");
		$this->EE->db->query("INSERT INTO exp_editor_configs VALUES ('5', '1', 'Standard (Visual Mode)', 'YToyODp7czo3OiJidXR0b25zIjthOjE0OntpOjA7czo0OiJodG1sIjtpOjE7czoxOiJ8IjtpOjI7czoxMDoiZm9ybWF0dGluZyI7aTozO3M6MToifCI7aTo0O3M6NDoiYm9sZCI7aTo1O3M6NjoiaXRhbGljIjtpOjY7czo5OiJ1bmRlcmxpbmUiO2k6NztzOjc6ImRlbGV0ZWQiO2k6ODtzOjE6InwiO2k6OTtzOjQ6ImxpbmsiO2k6MTA7czo0OiJmaWxlIjtpOjExO3M6NToiaW1hZ2UiO2k6MTI7czo1OiJ2aWRlbyI7aToxMztzOjE6InwiO31zOjE0OiJ1cGxvYWRfc2VydmljZSI7czo1OiJsb2NhbCI7czoyMDoiZmlsZV91cGxvYWRfbG9jYXRpb24iO3M6MToiMCI7czoyMToiaW1hZ2VfdXBsb2FkX2xvY2F0aW9uIjtzOjE6IjAiO3M6MTQ6ImltYWdlX2Jyb3dzaW5nIjtzOjM6InllcyI7czoxMjoiaW1hZ2Vfc3ViZGlyIjtzOjM6InllcyI7czoyOiJzMyI7YTo0OntzOjQ6ImZpbGUiO2E6MTp7czo2OiJidWNrZXQiO3M6MDoiIjt9czo1OiJpbWFnZSI7YToxOntzOjY6ImJ1Y2tldCI7czowOiIiO31zOjE0OiJhd3NfYWNjZXNzX2tleSI7czowOiIiO3M6MTQ6ImF3c19zZWNyZXRfa2V5IjtzOjA6IiI7fXM6NjoiaGVpZ2h0IjtzOjM6IjIwMCI7czo5OiJkaXJlY3Rpb24iO3M6MzoibHRyIjtzOjc6InRvb2xiYXIiO3M6MzoieWVzIjtzOjY6InNvdXJjZSI7czozOiJ5ZXMiO3M6NToiZm9jdXMiO3M6Mjoibm8iO3M6MTA6ImF1dG9yZXNpemUiO3M6MzoieWVzIjtzOjU6ImZpeGVkIjtzOjI6Im5vIjtzOjEyOiJjb252ZXJ0bGlua3MiO3M6MzoieWVzIjtzOjExOiJjb252ZXJ0ZGl2cyI7czozOiJ5ZXMiO3M6Nzoib3ZlcmxheSI7czozOiJ5ZXMiO3M6MTM6Im9ic2VydmVpbWFnZXMiO3M6MzoieWVzIjtzOjk6InNob3J0Y3V0cyI7czozOiJ5ZXMiO3M6MzoiYWlyIjtzOjI6Im5vIjtzOjM6Ind5bSI7czozOiJ5ZXMiO3M6ODoicHJvdG9jb2wiO3M6MzoieWVzIjtzOjE4OiJhbGxvd2VkdGFnc19vcHRpb24iO3M6NzoiZGVmYXVsdCI7czoxMToiYWxsb3dlZHRhZ3MiO3M6MDoiIjtzOjE0OiJmb3JtYXR0aW5ndGFncyI7YTo3OntpOjA7czoxOiJwIjtpOjE7czoxMDoiYmxvY2txdW90ZSI7aToyO3M6MzoicHJlIjtpOjM7czoyOiJoMSI7aTo0O3M6MjoiaDIiO2k6NTtzOjI6ImgzIjtpOjY7czoyOiJoNCI7fXM6ODoibGFuZ3VhZ2UiO3M6MjoiZW4iO3M6ODoiY3NzX2ZpbGUiO3M6MDoiIjtzOjc6InBsdWdpbnMiO2E6MDp7fX0=');");

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Uninstalls the module
	 *
	 * @access public
	 * @return Boolean FALSE if uninstall failed, TRUE if it was successful
	 **/
	function uninstall()
	{
		// Load dbforge
		$this->EE->load->dbforge();

		$this->EE->dbforge->drop_table('editor_configs');
		$this->EE->dbforge->drop_table('editor_buttons');

		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->delete('modules');
		$this->EE->db->where('class', ucfirst($this->module_name));
		$this->EE->db->delete('actions');

		return TRUE;
	}

	// ********************************************************************************* //

	/**
	 * Updates the module
	 *
	 * This function is checked on any visit to the module's control panel,
	 * and compares the current version number in the file to
	 * the recorded version in the database.
	 * This allows you to easily make database or
	 * other changes as new versions of the module come out.
	 *
	 * @access public
	 * @return Boolean FALSE if no update is necessary, TRUE if it is.
	 **/
	public function update($current = '')
	{
		// Are they the same?
		if ($current >= $this->version)
		{
			return FALSE;
		}

		$this->EE->load->dbforge();

		$current = str_replace('.', '', $current);

		// Two Digits? (needs to be 3)
		if (strlen($current) == 2) $current .= '0';

		$update_dir = PATH_THIRD.strtolower($this->module_name).'/updates/';

		// Does our folder exist?
		if (@is_dir($update_dir) === TRUE)
		{
			// Loop over all files
			$files = @scandir($update_dir);

			if (is_array($files) == TRUE)
			{
				foreach ($files as $file)
				{
					if ($file == '.' OR $file == '..' OR strtolower($file) == '.ds_store') continue;

					// Get the version number
					$ver = substr($file, 0, -4);

					// We only want greater ones
					if ($current >= $ver) continue;

					require $update_dir . $file;
					$class = 'EditorUpdate_' . $ver;
					$UPD = new $class();
					$UPD->do_update();
				}
			}
		}

		// Upgrade The Module
		$this->EE->db->set('module_version', $this->version);
		$this->EE->db->where('module_name', ucfirst($this->module_name));
		$this->EE->db->update('exp_modules');

		return TRUE;
	}

} // END CLASS

/* End of file upd.editor.php */
/* Location: ./system/expressionengine/third_party/editor/upd.editor.php */
