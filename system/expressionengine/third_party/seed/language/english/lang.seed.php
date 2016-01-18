<?php defined('BASEPATH') or exit('No direct script access allowed');

$lang = array(

	'seed_module_name'			=> 'Seed',
	'seed_module_description'	=> 'Quickly populate your channels with dummy entries',

	'seed_new_seed' 	=> 'New Seed',
	'seed_field' 		=> 'Field',
	'seed_populate'		=> 'Populate Options',
	'seed_field_values'	=> 'Field Values',
	'start_seed'		=> 'Start Seeding',


	// Errors
	'seed_no_channels_to_populate'	=> 'There are no channels on this site to populate',
	'seed_error_no_channel'			=> 'You must pass a channel to seed',
	'seed_error_no_count'			=> 'You must pass a number of entries to seed',
	'seed_error_count_not_positive'	=> 'Seed count needs to be a positive number',
	'seed_error_unknown_fieldtype'	=> 'Unknown fieldtype, fallback population failed',
	'seed_error_missing_required_value'	=> 'A required value was not passed',

	'missing_title'					=> 'No Title',

	// Successes
	'seed_success_message' 			=> 'Created <strong>%seed_count%</strong> entries within the <strong>%channel_name%</strong> channel. <a href="%channel_link%">View the entries</a>',

	'' => ''

);