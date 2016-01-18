<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Generator Model class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_generator_model extends Seed_model {

    
    private static $seed_text = '';
    private static $table = array();
    private static $table_base = '';
    private static $text_base = 'lorem';
    private $order = 4;

    // --------------------------------------------------------------------
    // METHODS
    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @access      public
     * @return      void
     */
    function __construct()
    {
        // Call parent constructor
        parent::__construct();

        // Initialize this model
        $this->initialize(
            'seed_generator',
            'generator_id',
            array()
        );

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
        // Call parent install
        //parent::install();
    }


    public function initialize()
    {
        ee()->load->helper('file');

        $base = str_replace('{base}', static::$text_base, SEED_TEXT_SRC);

        $this->seed_text = read_file( $base );

        if( empty( $this->table ) OR $this->table_base != $base )
        {
            $this->table_base = $base;
            $this->table = $this->_generate_table( $this->order );
        }

    }

    public function set_base( $base = 'lorem')
    {
        static::$text_base = $base;
    }


    public function generate_words( $max_length, $length )
    {
        $this->initialize();

        $str = $this->_generate_text( $max_length, $this->table, $this->order );

        // Now trim it down to be the number of _words_ we want
        $return = '';
        $in_progress = TRUE;


        if( str_word_count( $str ) < $length ) return $str;

        $arr = array_slice( explode( ' ', $str ), 0, $length );
        $str = ucwords( implode( $arr, ' ') );

        return $str;
    }


    public function generate_paragraphs( $paragraphs )
    {
        
        $this->initialize();

        $str = $this->_generate_paragraphs( $paragraphs );

        return $str;
    }

    private function _generate_paragraphs( $paragraphs, $str_arr = array() )
    {
        $length = $paragraphs * rand(50, 125);

        $str = $this->_generate_text( $length, $this->table, $this->order );

        $arr = explode( "\n", $str );

        // Remove the empty elements
        foreach( $arr as $key => $row )
        {
            if( trim($row) == '' ) unset( $arr[$key] );
        }

        $str_arr = array_merge( $arr, $str_arr );

        $p_count = count( $str_arr );

        if( $p_count == $paragraphs )
        {
            return implode( $str_arr, "\n\n");
        }
        elseif( $p_count > $paragraphs ) 
        {
            // Trim it
            $str_arr = array_slice( $str_arr, 0, $paragraphs );
            $str = implode( $str_arr, "\n\n");
            return $str;
        }


        return $this->_generate_paragraphs( $paragraphs, $str_arr);
    }

    public function _generate_table( $look_forward )
    {
        $table = array();
        
        // now walk through the text and make the index table
        for ($i = 0; $i < strlen($this->seed_text); $i++) 
        {
            $char = substr($this->seed_text, $i, $look_forward);

            if (!isset($table[$char])) $table[$char] = array();
        }              
        
        // walk the array again and count the numbers
        for ($i = 0; $i < (strlen($this->seed_text) - $look_forward); $i++) 
        {
            $char_index = substr($this->seed_text, $i, $look_forward);
            $char_count = substr($this->seed_text, $i+$look_forward, $look_forward);
            
            if (isset($table[$char_index][$char_count])) {
                $table[$char_index][$char_count]++;
            } 
            else {
                $table[$char_index][$char_count] = 1;
            }                
        } 

        return $table;
    }

    public function _generate_text($length, $table, $look_forward) 
    {
        // get first character
        $char = array_rand($table);
        $o = $char;

        for ($i = 0; $i < ($length / $look_forward); $i++) {
            $newchar = $this->_return_weighted_char($table[$char]);            
            
            if ($newchar) {
                $char = $newchar;
                $o .= $newchar;
            } else {       
                $char = array_rand($table);
            }
        }
        
        return $o;
    }

    public function _return_weighted_char($array) 
    {
        if (!$array) return false;
        
        $total = array_sum($array);
        $rand  = mt_rand(1, $total);

        foreach ($array as $item => $weight) {
            if ($rand <= $weight) return $item;
            $rand -= $weight;
        }
    }

}