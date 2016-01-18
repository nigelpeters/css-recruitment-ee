<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine Page History Plugin
 *
 * @package		Page History
 * @subpackage		Plugins
 * @category		Plugins
 * @author		David Hyland
 * @link			http://www.dhyland.com
 */

$plugin_info = array(
				'pi_name'			=> 'Page History',
				'pi_version'		=> '1.0.4',
				'pi_author'		=> 'David Hyland',
				'pi_author_url'	=> 'http://www.dhyland.com',
				'pi_description'	=> 'Returns last pages visited',
				'pi_usage'		=> Page_history::usage()
			);


class Page_history {  
		
	
	/**
	  *  Constructor
	  */
	function Page_history()
	{
		// make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------
	
	
	/**
	  *  Get page from session tracker
	  */  
	 function get()
	{
		$site_url = ($this->EE->TMPL->fetch_param('site_url')) ? $this->EE->config->item('site_url') : '';
		$page = $this->EE->TMPL->fetch_param('page');
		if ($page=='' || !is_numeric($page))
		{
			$page = 1; // last url
		}
		return $this->EE->session->tracker[$page];
			
	}
	/* END */
	
		
// ----------------------------------------
//  Plugin Usage
// ----------------------------------------

// This function describes how the plugin is used.
//  Make sure and use output buffering

function usage()
{
ob_start(); 
?>

To get the current URL:
{exp:page_history:get page='0'}

To get the previous page:
{exp:page_history:get page='1'} 
OR simply:
{exp:page_history:get}

To get "two pages ago":
{exp:page_history:get page='2'}

And so on, up to a limit of 5 "pages ago"

The default returned URL structure is page relative, ie "folder/template/".

To return the full site URL (ie: http://domain.com/page) add the parameter site_url='yes', eg:
{exp:page_history:get page='1' site_url='yes'} 

Marvellous :)

<?php
$buffer = ob_get_contents();
	
ob_end_clean(); 

return $buffer;
}
/* END */


}
// END CLASS

/* End of file pi.page_history.php */
/* Location: ./system/expressionengine/third_party/page_history/pi.page_history.php */