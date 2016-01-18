
<?php if( isset( $settings['multi'] ) AND $settings['multi'] == 'n' ) : ?>

<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?> field_sub_option">
	<th scope="row" colspan="2">

		No settings. <?php if( isset( $settings['channels'] ) ) : ?>Playa field set to relate a single entry from <?=count( $settings['channels'] )?> channel<?php if( count( $settings['channels'] ) > 1 ) : ?>s<?php endif;?>.<?php else : ?>Playa field is set to relate to a single entry from any channel.<?php endif; ?>

	</th>
</tr>

<?php else : ?>

<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?> field_sub_option">
	<th scope="row">
		Minimum related entry count
	</th>
	<td>
		<label>
			<input style="width : 30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_from" name="seed_field_<?=$channel_id?>_<?=$field_id?>_from" value="3"/>
			Entries
		</label>
	</td>
</tr>


<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?> generated field_sub_option">
	<th scope="row">
		Maximum related entry count
	</th>
	<td>
		<label>
			<input style="width:30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_to" name="seed_field_<?=$channel_id?>_<?=$field_id?>_to" value="6"/>
			Entries
		</label>
	</td>
</tr>

<?php endif; ?>


