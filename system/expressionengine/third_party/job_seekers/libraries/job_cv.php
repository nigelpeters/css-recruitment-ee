<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Job_cv {


	// The table structure is: 

	public function __construct()
	{
		//load ee dependencies
		$this->EE =& get_instance();
		
	}

	// This will load the form to allow the cv file to be uploaded using the ACT function upload_cv()
	
	// If their is a cv already uploaded then this will be displayed by the ZOO plugin

	public function load_form()
	{
		// get the users id
		if($member_id = $this->EE->session->userdata('member_id'))
		{

			// Create parameters array for the form
			$hidden_fields = array(
				"member_id" => $member_id,
				'ACT' => $this->EE->functions->fetch_action_id('Job_seekers', 'upload_cv'),
				);
			
			// Params = job_id
			$form_data = array(
				"id" => "fileupload",
				"class" => $this->EE->TMPL->form_class,
				"hidden_fields" => $hidden_fields
				);

			$tagdata = $this->EE->TMPL->tagdata;

			// Add data	array to the form_declaration
			$form = $this->EE->functions->form_declaration($form_data) . $tagdata ;
					
			return $form;
		}
		else
		{
			return $this->EE->TMPL->no_results;
		}
	}
	
	public function upload_cv()
	{
		$this->EE->load->library('filemanager');
		$this->EE->load->helper('file');

		// Get form postdata
		// GET the member channel entry ID
		$entry_id = $this->EE->input->post('entry_id');

		// GET the member filename
		if($this->EE->input->post('member_cv') != '')
		{
			$member_cv = $this->EE->input->post('member_cv');
			$filename = explode($this->EE->functions->fetch_site_index().'job-cvs/', $member_cv);

			// Check IF there is a filename
			if(unlink($_SERVER['DOCUMENT_ROOT'].'job-cvs/'.$filename[1] ))
			{
				// then unlink the file from folder
				// Delete the file from the database where filename && currentuserid exists

				$this->EE->db->where('uploaded_by_member_id', $this->EE->session->userdata('member_id'));
				$this->EE->db->where('file_name', $filename[1]);
				$q = $this->EE->db->delete('exp_files');

				if(!$q){ die("Cant delete file"); }
			}
			else
			{
				echo "File not deleted: ".$_SERVER['DOCUMENT_ROOT'].'job-cvs/'.$filename[1];
			}
		}

		// Then upload the file
		if($fileinfo = $this->EE->filemanager->upload_file(2))
		{
			if (array_key_exists('error', $fileinfo))
			{
				$info = new StdClass;
				$info->name = $_FILES['userfile']['name'];
				$info->size = $_FILES['userfile']['size'];
				$info->error = $fileinfo['error'];

				$files[] = $info;

				echo json_encode(array("files" => $files));
			}
			else
			{
				$fn_array = explode('.', $fileinfo['file_name']);

				$ext = $fn_array[1];
				$fn = $this->EE->session->userdata('username')."_CV_".$this->EE->localize->now.".".$ext;

				$this->EE->filemanager->rename_file($fileinfo['file_id'], $fn);

				//rename ("/folder/file.ext", "/folder/newfile.ext");

				$info = array(
					'name' => $fn,
					'size' => $fileinfo['file_size'],
					'type' => $fileinfo['mime_type'],
					'url' => $fileinfo['rel_path']
				);

	            // Update the member channel entry with the new filename
				$data = array(
					'field_id_49' => '{filedir_2}'.$info['name']
					);

				$this->EE->db->where('entry_id', $entry_id);
				$q = $this->EE->db->update('exp_channel_data', $data);

	            // If successfull return the JSON
				if($q)
				{
					$files[] = $info;
					echo json_encode(array("files" => $files));   	
				}
			}
		}
		else
		{
			echo "File not uploaded";
		}
	}

	function delete_cv()
	{
		// Get the member entry id

		// Get the file id
	}

}