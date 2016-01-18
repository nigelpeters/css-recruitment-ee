<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
    'pi_name' => 'Hello World',
    'pi_version' => '1.0',
    'pi_author' => 'EE Recipes',
    'pi_author_url' => 'http://ee-recipes.com/',
    'pi_description'=> 'A dummy plugin',
    'pi_usage' => Hello_world::usage()
);

class Hello_world {

    public $return_data;
    
    public function __construct()
    {
        $this->EE =& get_instance();
        $this->return_data = "<p>Hello, world</p>";
    }
    
    public function display()
    {
	    $name = $this->EE->TMPL->fetch_param("name", "world");
	    return "<p>Hello, ".$name."</p>";
    }
    
    public static function usage()
    {
        ob_start();
?>
Documentation goes here...
<?php
        $buffer = ob_get_contents();
        ob_end_clean();
        return $buffer;
    }
}

/* End of file pi.hello_world.php */
/* Location: /system/expressionengine/third_party/hello_world/pi.hello_world.php */ 