$(function(){



	$('.field_sub_option_select').each(function(){

		var rel = $(this).attr('rel');
		var val = $(this).val();

		if( val == '' ) $('.field_sub_option.'+rel).hide();
		else $('.field_sub_option.'+rel+'.'+val).show();
	});



	$('.field_sub_option_select').change(function(){
		
		var rel = $(this).attr('rel');
		var val = $(this).val();

		$('.field_sub_option.'+rel).hide();

		if( val != '' ) $('.field_sub_option.'+rel+'.'+val).show();
	});



	$('#seed_channel').change( function() {

		var channel_id = $(this).attr('value');

		$('.seed_fields_channel').hide();
		$('#seed_fields_channel_'+channel_id).show();

	});

	$('.optional_field_populate_option').change( function() {

		var rel = $(this).attr('rel');
		var value = $(this).attr('value');

		if( value == 'empty' ) $( '#' + rel ).hide();
		else $( '#' + rel ).show();


	});


	$('.channel_advanced_options').change( function() {

		var rel = $(this).attr('rel');
		var value = $(this).attr('value');

		if( value == 'empty' ) $( '#' + rel ).hide();
		else $( '#' + rel ).show();
		
	});

});