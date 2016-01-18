<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Seeker_job_search {

	var $max_job_searches = 5;

	public function __construct()
	{
	
	}

	// Loads up current user searches
	// users can only have a max of 5 searches so we need to confirm this
		
	public function job_search_list()
	{
		// Get the user id
		
		// Create query to select existing user job searches
		
		// If results
				
			// Count the results if the number is less than $max_job_searches
			
				// Add a button to the template varibles
			
			// setup the varibles for the template
			
			// Then return the data to the template
			
		// else
		
			// Either create message "no results"
			
			// Or setup varibles and return to template
	}

	// This function is loaded up when the path job-seekers/create-search
	// Tag {exp:job_seekers:job_search_form}
	// users can only have a max of 5 searches so we need to confirm this
		
	public function job_search_form()
	{
		// Create the form parameters
		
		// Return the form declaration
	}
	
	// This function is called by the ACT
	// users can only have a max of 5 searches so we need to confirm this
	
	public function job_search_save()
	{
		// Get the user id
		
		// Merge post form data into data array
		
		// Insert data into the db
		
		// If successful return to main page
	}

	// This function is called by the ACT
	
	public function job_search_delete()
	{
		// Get the user id
		
		// Get the form data
	}
}