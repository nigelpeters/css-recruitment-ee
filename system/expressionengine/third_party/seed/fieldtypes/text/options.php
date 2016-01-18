<?php if( $is_unknown === TRUE ) : ?>

<tr>
	<td colspan="2" style="padding : 0">
		<div class="alert info">
			This fieldtype is unknown and will be treated as a <strong>text</strong> field
		</div>
	</td>
</tr>

<?php endif; ?>


<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?>_generated">
    <th scope="row">
    	Populate Field Values with
    </th>
    <td>
    	<label for="seed_field_<?=$channel_id?>_<?=$field_id?>_values">
    		<select style="width:50%" name="seed_field_<?=$channel_id?>_<?=$field_id?>_values" id="seed_field_<?=$channel_id?>_<?=$field_id?>_values" rel="seed_field_<?=$channel_id?>_<?=$field_id?>" class="field_sub_option_select">
    			<option value="generated">Generated Dummy Text</option>
    			<option value="specific">Specific Text from a Set</option>
    			<option value="sequence">Sequential Text</option>
    		</select>
    	</label>

    </td>
</tr>


<tr class="even generated seed_field_<?=$channel_id?>_<?=$field_id?> field_sub_option" style="display:none">
	<th scope="row">
		Minimum word count
	</th>
	<td>
		<label>
			<input style="width : 30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_from" name="seed_field_<?=$channel_id?>_<?=$field_id?>_from" value="3"/>
			Words
		</label>
	</td>
</tr>


<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?> generated field_sub_option" style="display:none">
	<th scope="row">
		Maximum word count
	</th>
	<td>
		<label>
			<input style="width:30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_to" name="seed_field_<?=$channel_id?>_<?=$field_id?>_to" value="6"/>
			Words
		</label>
	</td>
</tr>



<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?> generated field_sub_option" style="display:none">
	<th scope="row">
		Maximum character count
	</th>
	<td>
		<label>
			<input style="width:30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_max" name="seed_field_<?=$channel_id?>_<?=$field_id?>_max" value="<?=$field['field_maxl']?>"/>
			Characters
		</label>
	</td>
</tr>



<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?> generated field_sub_option" style="display:none">
	<th scope="row">
		Text Case

	</th>
	<td>
		<label for="seed_field_<?=$channel_id?>_<?=$field_id?>_case">
    		<select style="width:50%" name="seed_field_<?=$channel_id?>_<?=$field_id?>_case" id="seed_field_<?=$channel_id?>_<?=$field_id?>_case">
    			<option value="ucwords">Uppercase Words</option>
    			<option value="ucfirst">Uppercase first word</option>
    			<option value="lowercase">lowercase</option>
    			<option value="uppercase">UPPERCASE</option>
    		</select>
    	</label>
	</td>
</tr>


<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?> specific field_sub_option" style="display:none">
	<th scope="row">
		Possible Values
		<span class="help_text">Place each option on a newline. Seed will randomly select on line for each entry it populates</span>
	</th>
	<td>
		<label for="seed_field_<?=$channel_id?>_<?=$field_id?>_set">
    		<textarea name="seed_field_<?=$channel_id?>_<?=$field_id?>_set" id="seed_field_<?=$channel_id?>_<?=$field_id?>_set"></textarea>
    	</label>
	</td>
</tr>



<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?> sequence field_sub_option" style="display:none">
	<th scope="row">
		Sequence
		<span class="help_text">
			The marker <strong>{#}</strong> will be replaced with the current seed count.<br/> ie. 'Example 1', 'Example 2', 'Example 3'
		</span>
	</th>
	<td>
		<label for="seed_field_<?=$channel_id?>_<?=$field_id?>_set">
    		<input type="text" style="width:50%"  name="seed_field_<?=$channel_id?>_<?=$field_id?>_sequence" id="seed_field_<?=$channel_id?>_<?=$field_id?>_sequence" placeholder="Example {#}"/>
    	</label>
	</td>
</tr>