<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Seed Fieldtype Wygwam class
 *
 * @package         seed_ee_addon
 * @version         1.1.0
 * @author          Joel Bradbury ~ <joel@squarebit.co.uk>
 * @link            http://squarebit.co.uk/seed
 * @copyright       Copyright (c) 2012, Joel 
 */
class Seed_fieldtype_wygwam extends Seed_fieldtype
{
	public $title = 'wygwam';

	public $settings = array(
		array(
			'name' 			=> 'from',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default'		=> 2
		),		
		array(
			'name' 			=> 'to',
			'required' 		=> TRUE,
			'type' 			=> 'int',
			'default' 		=> 6
		),


		array(
			'name' 			=> 'markup_a',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_strong',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_em',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_u',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h1',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h2',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h3',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h4',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h5',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_h6',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_blockquote',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_ul',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		),
		array(
			'name' 			=> 'markup_ol',
			'required' 		=> FALSE,
			'type' 			=> 'str',
			'default' 		=> 'n'
		)

	);

	private $field = array();
	private $paragraphs = array();
	private $markers = array();

	private $frequency_settings = array(
			'anchor' => array(
				'len_min' 	=> 2,
				'len_max' 	=> 4,
				'per_p'		=> 5,
				'pre'		=> '<a href="#">',
				'post'		=> '</a>' ),
			'strong' => array(
				'len_min' 	=> 2,
				'len_max' 	=> 3,
				'per_p'		=> 5,
				'pre'		=> '<strong>',
				'post'		=> '</strong>' ),	
			'em' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 4,
				'pre'		=> '<em>',
				'post'		=> '</em>' ),	
			'u' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 8,
				'pre'		=> '<u>',
				'post'		=> '</u>' ),	
			'h1' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 10,
				'pre'		=> '<h1>',
				'post'		=> '</h1>' ) ,		
			'h2' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 6,
				'pre'		=> '<h2>',
				'post'		=> '</h2>' ) ,		
			'h3' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 6,
				'pre'		=> '<h3>',
				'post'		=> '</h3>' ),		
			'h4' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 10,
				'pre'		=> '<h4>',
				'post'		=> '</h4>' ),		
			'h5' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 12,
				'pre'		=> '<h5>',
				'post'		=> '</h5>' ),		
			'h6' => array(
				'len_min' 	=> 3,
				'len_max' 	=> 6,
				'per_p'		=> 12,
				'pre'		=> '<h6>',
				'post'		=> '</h6>' ),		
			'blockquote' => array(
				'len_min' 	=> 12,
				'len_max' 	=> 48,
				'per_p'		=> 6,
				'pre'		=> '<blockquote>',
				'post'		=> '</blockquote>' ),		
			'ol' => array(
				'len_min' 	=> 4,
				'len_max' 	=> 9,
				'per_p'		=> 8,
				'pre'		=> '<ol>',
				'post'		=> '</ol>',
				'sub_pre'	=> '<li>',
				'sub_post'	=> '</li>',
				'sub_min'	=> 3,
				'sub_max' 	=> 10 ),		
			'ul' => array(
				'len_min' 	=> 4,
				'len_max' 	=> 9,
				'per_p'		=> 8,
				'pre'		=> '<ul>',
				'post'		=> '</ul>',
				'sub_pre'	=> '<li>',
				'sub_post'	=> '</li>',
				'sub_min'	=> 3,
				'sub_max' 	=> 10)	
			);

	public function generate( $field = array() )
	{
		$ret = '';

		// Generate some text within the bounds of the options
		if( $field['populate'] == 'sparse' )
		{
			// We don't want to always populate this. 
			if( rand( 1, 2 ) == 1 ) $ret = $this->_generate_filler( $field );
		}
		else $ret = $this->_generate_filler( $field );

		return $ret;
	}



	private function _generate_filler( $field = array() )
	{
		$this->field = $field;

		$ret = '';

		$length = rand( $this->field['from'], $this->field['to'] );
	
		$ret = ee()->seed_generator_model->generate_paragraphs( $length );

		$paragraphs = explode( "\n\n", $ret );

		// Do we have any additional markup to drop in?
		$additional = $this->_check_additional();

		if( $additional === FALSE )
		{
			// Just wrap the paragraphs with <p> tags
			$ret = '<p>' . implode( $paragraphs,"</p>\n <p>" ) . '</p>';

			return $ret;
		}
		else
		{
			// We have more work to do
			$ret = $this->_generate_additional( $paragraphs, $additional );			
		}

		return $ret;
	}


	private function _generate_additional( $paragraphs = array(), $additional = array() )
	{
		if( empty( $paragraphs ) ) return '';
		if( empty( $additional ) ) return '<p>' . implode( $paragraphs,"</p>\n <p>" ) . '</p>';

		$this->paragraphs = $paragraphs;

		$c = count( $this->paragraphs );

		// From the list of possible additional elements, pick only a few
		shuffle($additional);
		$additional = array_slice( $additional, 0, $c );



		// Handle the inline elements first
		// anchors, em, strong, u
		if( in_array( 'markup_a', $additional ) ) $this->_generate_inline( 'anchor' );
		if( in_array( 'markup_strong', $additional ) ) $this->_generate_inline( 'strong' );
		if( in_array( 'markup_em', $additional ) ) $this->_generate_inline( 'em' );
		if( in_array( 'markup_u', $additional ) ) $this->_generate_inline( 'u' );

		// Now drop the line items into the content 
		$this->_populate_inline();

		// Non-inline items need to be outside the <p> tags, so add the p tags now
		$this->_add_p_tags();

		// Now move onto outside items
		if( in_array( 'markup_h1', $additional ) ) $this->_populate_outside( 'h1' );
		if( in_array( 'markup_h2', $additional ) ) $this->_populate_outside( 'h2' );
		if( in_array( 'markup_h3', $additional ) ) $this->_populate_outside( 'h3' );
		if( in_array( 'markup_h4', $additional ) ) $this->_populate_outside( 'h4' );
		if( in_array( 'markup_h5', $additional ) ) $this->_populate_outside( 'h5' );
		if( in_array( 'markup_h6', $additional ) ) $this->_populate_outside( 'h6' );
		if( in_array( 'markup_blockquote', $additional ) ) $this->_populate_outside( 'blockquote' );
		if( in_array( 'markup_ul', $additional ) ) $this->_populate_outside( 'ul' );
		if( in_array( 'markup_ol', $additional ) ) $this->_populate_outside( 'ol' );

		return implode( $this->paragraphs, "\n" );
	}


	private function _add_p_tags()
	{
		foreach( $this->paragraphs as $row => $paragraph )
		{
			$this->paragraphs[ $row ] = '<p>' . $paragraph . '</p>';
		}
	}

	private function _populate_inline()
	{	
		// Go paragraph by paragraph

		foreach( $this->paragraphs as $row => $paragraph )
		{
			// Do we have any markers for this paragraph?
			if( ! isset( $this->markers[ $row ] ) ) continue;

			// We need to loop over the markers and insert the content
			// Start at the end and work back to prevent collisions
			$total_length = strlen( $paragraph );

			$marker_count = count( $this->markers[ $row ] );

			$sorted = $this->markers[ $row ];

			rsort( $sorted );

			foreach( $sorted as $item )
			{
				// Cut the paragraph and stich it back together again
				$pre = substr( $paragraph, 0, $item['location'] );
				$post = substr( $paragraph, $item['location'] + 1 );

				$paragraph = $pre . $item['content'] . $post;
			}

			$this->paragraphs[ $row ] = $paragraph;
		}
	}

	private function _generate_inline( $type = 'anchor' )
	{
		// Get the settings for this type
		$per_p = $this->frequency_settings[ $type ]['per_p'];
		$len_min = $this->frequency_settings[ $type ]['len_min'];
		$len_max = $this->frequency_settings[ $type ]['len_max'];
		$pre = $this->frequency_settings[ $type ]['pre'];
		$post = $this->frequency_settings[ $type ]['post'];

		for( $i = 1; $i <= floor( count( $this->paragraphs ) / $per_p ); $i++ )
		{
			// Pick a paragraph from these
			$j = rand( 1, $per_p ) + ( $per_p * ( $i - 1 ) ) - 1;
			$tmp = $this->paragraphs[ $j ];

			// How long will this item be?
			$anchor_length = rand( $len_min, $len_max );

			// Now pick somewhere in this paragraph
			$pos = rand( 0, strlen( $tmp ) );

			// Find the nearest preceding word break
			$s = strrpos( $tmp, ' ', -(strlen( $tmp ) - $pos) );

			if( $s === FALSE )
			{
				// None match, go from the start
				$s = 0;
			}
			else
			{
				// move past the space to the start of the word
				$s++;
			}


			// Generate some content for this
			$content = ' '.$pre.ee()->seed_generator_model->generate_words( 150, rand( $len_min, $len_max) ). $post . ' ';

			// Now make a marker for this location
			// and generate what we'll be inserting here
			// We don't actually put the extra item in
			// until the end to prevent collisions 
			// between generated elements

			// NOTE : in the situation where this same point is selected by multiple items
			// only the last one will be used
			$this->markers[ $j ][ $s ] = array(
				'paragraph' => $j,
				'location'  => $s,
				'content'	=> $content);

		}

	}


	private function _populate_outside( $type = 'h3' )
	{
		// Get the settings for this type
		$per_p = $this->frequency_settings[ $type ]['per_p'];
		$len_min = $this->frequency_settings[ $type ]['len_min'];
		$len_max = $this->frequency_settings[ $type ]['len_max'];
		$pre = $this->frequency_settings[ $type ]['pre'];
		$post = $this->frequency_settings[ $type ]['post'];

		for( $i = 1; $i <= floor( count( $this->paragraphs ) / $per_p ); $i++ )
		{
			// Pick a paragraph from these
			$j = rand( 1, $per_p ) + ( $per_p * ( $i - 1 ) ) - 1;

			// Split the array at this point
			$head = array_slice( $this->paragraphs, 0, $j );
			$tail = array_slice( $this->paragraphs, $j );


			// If this is generation of ol or ul, we need to do extra work
			if( $type == 'ul' OR $type == 'ol' )
			{	
				$content = $pre . "\n";

				// How long will this list be?
				$list_length = rand( $this->frequency_settings[ $type ]['sub_min'], $this->frequency_settings[ $type ]['sub_max'] );
				$sub_pre = $this->frequency_settings[ $type ]['sub_pre'];
				$sub_post = $this->frequency_settings[ $type ]['sub_post'];

				for( $k = 1; $k <= $list_length; $k++ )
				{
					$content .= $sub_pre . ee()->seed_generator_model->generate_words( 150, rand( $len_min, $len_max) ) . $sub_post . "\n";
				}

				$content .= $post;
			}
			else
			{
				// Generate some content for this
				$content = $pre.ee()->seed_generator_model->generate_words( 250, rand( $len_min, $len_max) ). $post . ' ';
			}

			$this->paragraphs = array_merge( $head, array($content), $tail );
		}


	}



	private function _check_additional()
	{
		$has_additional = FALSE;
		$additional = array();

		foreach( $this->settings as $setting )
		{
			if( strpos( $setting['name'], 'markup' ) !== FALSE )
			{
				// Now check to see if the option was passed, and is set to 'y'
				if( isset( $this->field[ $setting['name'] ] ) AND $this->field[ $setting['name'] ] == 'y' )
				{
					$has_additional = TRUE;
					$additional[] = $setting['name'];
				}	
			}
		}

		if( ! $has_additional ) return FALSE;

		return $additional;
	}
}