<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?>_min">
	<th scope="row">
        Minimum paragraph count
    </th>
    <td>
        <input style="width : 10%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_from" name="seed_field_<?=$channel_id?>_<?=$field_id?>_from" value="3"/> Paragraphs
    </td>
</tr>

<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?>_min">
	<th scope="row">
        Maximum paragraph count
    </th>
    <td>
        <input style="width : 10%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_to" name="seed_field_<?=$channel_id?>_<?=$field_id?>_to" value="6"/> Paragraphs
    </td>
</tr>


<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?>_min">
    <th scope="row">
        Include Additional Elements
        <br/>
        <br/>
        <span class="help_text">
            Extra markup can be added to the generated text. From these elements extra markup will be added to the field during text generation. <br/>Depending on the length of the text generated only some of the selected elements may be used.
        </span>
    </th>
    <td>


        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_a" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_a" value="y" checked="checked"/> links
        </label>

        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_strong" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_strong" value="y" checked="checked"/> strong
        </label>
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_em" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_em" value="y"/> em
        </label>

        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_u" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_u" value="y"/> underline
        </label>


        <br/>

        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h1" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h1" value="y"/> h1
        </label>


        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h2" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h2" value="y"/> h2
        </label>

        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h3" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h3" value="y"/> h3
        </label>
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h4" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h4" value="y"/> h4
        </label>
      
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h5" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h5" value="y"/> h5
        </label>
      
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h6" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_h6" value="y"/> h6
        </label>
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_blockquote" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_blockquote" value="y"/> blockquote
        </label>
        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_ul" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_ul" value="y"/> ul > li
        </label>

        <label style="display:block">
            <input type="checkbox" id="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_ol" name="seed_field_<?=$channel_id?>_<?=$field_id?>_markup_ol" value="y"/> ol > li
        </label>

      

    </td>
</tr>


