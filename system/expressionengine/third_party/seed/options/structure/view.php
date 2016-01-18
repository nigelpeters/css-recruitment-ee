

<tr class="odd seed_option_<?=$channel_id?>_structure">
    <th scope="row">
    	Create entries as Children of which existing pages?

        <br/>
        <br/>
        <span class="help_text">
            Seed will only create entries below top level pages, one level deep.
        </span>
    </th>
    <td>
    	<?php foreach( $option['pages']['clean'] as $page_id => $page_uri ) : ?>

			<label for="seed_option_<?=$channel_id?>_page_<?=$page_id?>" style="display:block">
	    		<input type="checkbox" value="<?=$page_id?>" name="seed_option_<?=$channel_id?>_structure[]" id="seed_option_<?=$channel_id?>_page_<?=$page_id?>"/> <?=$page_uri?>
	    	</label>

	    <?php endforeach; ?>
    	
    </td>	
</tr>
