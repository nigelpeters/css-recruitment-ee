

<tr class="even seed_field_<?=$channel_id?>_<?=$field_id?> field_sub_option">
	<th scope="row">
		Minimum row count
	</th>
	<td>
		<label>
			<input style="width : 30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_from" name="seed_field_<?=$channel_id?>_<?=$field_id?>_from" value="3"/>
			Rows
		</label>
	</td>
</tr>


<tr class="odd seed_field_<?=$channel_id?>_<?=$field_id?> generated field_sub_option">
	<th scope="row">
		Maximum row count
	</th>
	<td>
		<label>
			<input style="width:30%" type="number" id="seed_field_<?=$channel_id?>_<?=$field_id?>_to" name="seed_field_<?=$channel_id?>_<?=$field_id?>_to" value="6"/>
			Rows
		</label>
	</td>
</tr>

</tbody>
</table>
</div>


	<div class="tg seed_field_<?=$channel_id?>_<?=$field_id?>_options" style="margin-left : 10%;  <?php if( $field['field_required'] == 'n' ) : ?>display:none<?php endif; ?>">

		<h2>Cell Options</h2>
		<div class="alert info">Select options for the Matrix cells</div>

	</div>

	<div class="tg seed_field_<?=$channel_id?>_<?=$field_id?>_options" style="margin-left : 10%;  <?php if( $field['field_required'] == 'n' ) : ?>display:none<?php endif; ?>">

		<!-- Now we need to go through the sub-listed cells and init the specific fieldtypes if they're around -->


		<?php foreach( $field['cells'] as $cell ) : ?>
		<div style="display:block">
			<h3 style="background:#fff; border-top:3px double #849099; margin-top:-1px"><?=$cell['col_label']?> <code>[<?=$cell['col_name']?>]</code> <span class="help_text"><?=$cell['col_type']?></span></h3>
		</div>

		<table>
			<tbody>
				<!-- Cell type options -->
				<?php echo( $this->seed_channel_model->get_field_view( $cell['col_type'], $channel_id, $field['field_id'] .'_cell_'.$cell['col_id'], $field, $cell ) ); ?>

			</tbody>
		</table>

		<?php endforeach; ?>





