

<tr class="odd seed_option_<?=$channel_id?>_status">
    <th scope="row">
    	Possible Entry Statuses
        <br/>
        <br/>
    	<span class="help_text">If none selected, status will default to channel default</span>
    </th>
    <td>

    	<?php foreach( $option['values'] as $status ) : ?>
		
		<label for="seed_option_<?=$channel_id?>_statuses_<?=$status['status_id']?>" style="display:block">
    		<input type="checkbox" value="<?=$status['status']?>" name="seed_option_<?=$channel_id?>_status[]" id="seed_option_<?=$channel_id?>_statuses_<?=$status['status_id']?>"/> <?=$status['status']?>
    	</label>

	    <?php endforeach; ?>
    	
    </td>
</tr>
