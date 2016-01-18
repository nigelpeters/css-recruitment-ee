<div id="seed_container" class="mor">


<?php if( $type == 'error' ) : ?>

	<div class="tg">
		<h2>Error</h2>
		<div class="alert info">
			There were <?php if( count( $errors ) > 1 ) :?>were some errors<?php else : ?>was an error<?php endif; ?> :
			<ul>
			<?php foreach( $errors as $error ) : ?>
				<li><?=$error?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>

<?php elseif( $type == 'success' ) : ?>

	<div class="tg">
		<h2>Success</h2>
		<div class="alert success">
			<?php foreach( $success as $msg ) : ?>
				<p><?=$msg?></p>
			<?php endforeach; ?>
		</div>

	</div>

<?php elseif ($channels): ?>

<form method="post" action="<?=$base_url?>&amp;method=start_seed">

	<input type="hidden" name="XID" value="<?=XID_SECURE_HASH?>" />

	<div class="tg">
		<h2>Start a new seed</h2>
		<table class="data" id="seed-new-seed">
	        <tbody>
	        	<tr class="<?=seed_row()?>">
					<td scope="row" style="width:30%">
						<label for="seed_channel">Channel to Seed</label>
					</td>
					<td>
						<select style="width:30%" id="seed_channel" name="seed_channel">
							<option value="">-</option>
							<?php foreach( $channels as $channel_id => $channel ) : ?>
								<option value="<?=$channel_id?>"><?=$channel['title']?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr class="<?=seed_row()?>" style="width:30%">
					<td scope="row">
						<label for="seed_count">Create How Many Entries?</label>
					</td>
					<td>
						<input style="width:30%" type="number" id="seed_count" name="seed_count" value="10"/>
					</td>
				</tr>

				<tr class="<?=seed_row()?>" style="width:30%">
					<td scope="row">
						<label for="seed_text_base">Text Generation Base</label>
					</td>
					<td>
						<select style="width:30%" id="seed_text_base" name="seed_text_base">
							<option value="lorem">Lorem Ipsum</option>
							<option value="kant">Bacon Ipsum</option>
							<option value="cupcake">Cupcake Ipsum</option>
							<option value="bluth">Bluth Ipsum</option>
							<option value="space">Space Ipsum</option>
							<option value="zombie">Zombie Ipsum</option>
						</select>
					</td>
				</tr>



			</tbody>
		</table>

	</div>



	
	<?php foreach( $channels as $channel_id => $channel ) : ?>	

	<div class="seed_fields_channel" id="seed_fields_channel_<?=$channel_id?>" style="display:none;">


		<div class="tg">
			<h2>Channel Options</h2>
			<div class="alert success">Select your channel specific options</div>
		</div>

		<!-- Standard Options First -->
		<?php $i = 0;
		$has_options = FALSE;

		foreach( $standard_options as $option ) : ?>

			<?php if( $option['visible'] === TRUE ) :	
			$i++; $has_options = TRUE; ?>

				<?php if( $i > 1 ) : ?> 
				<div class="tg" style="margin-top : -20px; margin-left : 5%">
				<?php else : ?>
				<div class="tg" style=" margin-left : 5%;">
				<?php endif; ?>

					<div style="display:block">
						<h3 style="background:#fff; border-top:3px double #849099; margin-top:-1px"><?=$option['option_label']?></h3>
					</div>


					<table class="data">
						<tbody>										
							
						<!-- Field type options -->
						<?php echo( $this->seed_channel_model->get_option_view( $option['option_type'], $channel_id, $option ) ); ?>

						</tbody>
					</table>

				</div>

			<?php endif; ?>

		<?php endforeach; 


		foreach( $channel['options'] as $option ) : ?>
		
			<?php if( $option['visible'] === TRUE ) :	
				$i++; $has_options = TRUE; ?>

				<?php if( $i > 1 ) : ?> 
				<div class="tg" style="margin-top : -20px; margin-left : 5%">
				<?php else : ?>
				<div class="tg" style=" margin-left : 5%;">
				<?php endif; ?>

					<div style="display:block">
						<h3 style="background:#fff; border-top:3px double #849099; margin-top:-1px"><?=$option['option_label']?></h3>
					</div>


					<table class="data">
						<tbody>										
							
						<!-- Field type options -->
						<?php echo( $this->seed_channel_model->get_option_view( $option['option_type'], $channel_id, $option ) ); ?>

						</tbody>
					</table>

				</div>

			<?php endif;

		endforeach; ?>

		<?php if( $has_options == FALSE ) : ?>

			<div class="tg" style=" margin-left : 5%">
				<div class="alert warning">
					There are no options available for this channel
				</div>
			</div>
		<?php endif; ?>

		<div class="tg">
			<h2>Fields</h2>
			<div class="alert success">Select your population options for this channel's fields.</div>
		</div>

		<?php foreach( $channel['fields'] as $field_id => $field ) : ?>

		<?php if( $field['field_label'] != 'title' ) : ?> 
		<div class="tg" style="margin-top : -20px; margin-left : 5%">
		<?php else : ?>
		<div class="tg" style=" margin-left : 5%;">
		<?php endif; ?>

			<div style="display:block">
				<h3 style="background:#fff; border-top:3px double #849099; margin-top:-1px"><?=$field['field_label']?> <code>[<?=$field['field_name']?>]</code> <span class="help_text"><?=$field['field_type']?><?php if( $field['field_required'] == 'y' ) : ?>, *Required Field*<?php endif; ?></span></h3>
			</div>

			<table class="data">
				<thead>
					<tr style="background-color :transparent">
						<th colspan="2">
						<?php if( $field['field_required'] == 'y' ) : ?>
							<label for="seed_field_<?=$channel_id?>_<?=$field_id?>">
								Populate Options : 
								<select style="width:30%" class="optional_field_populate_option" id="seed_field_<?=$channel_id?>_<?=$field_id?>" rel="seed_field_<?=$channel_id?>_<?=$field_id?>_options" name="seed_field_<?=$channel_id?>_<?=$field_id?>">
									<option value="always">Always Populate</option>
								</select>
							</label>
						<?php else : ?>
							<label for="seed_field_<?=$channel_id?>_<?=$field_id?>">
								Populate Options : 
								<select style="width:30%" class="optional_field_populate_option" id="seed_field_<?=$channel_id?>_<?=$field_id?>" rel="seed_field_<?=$channel_id?>_<?=$field_id?>_options" name="seed_field_<?=$channel_id?>_<?=$field_id?>">
									<option value="empty">Don't Populate</option>
									<option value="sparse">Populate Sparsely</option>
									<option value="always">Always Populate</option>
								</select>
							</label>
						<?php endif;?>
						</th>
					</tr>
				</thead>


				<tbody <?php if( $field['field_required'] == 'n' ) : ?>style="display:none"<?php endif; ?> id="seed_field_<?=$channel_id?>_<?=$field_id?>_options">

				<!-- Field type options -->
				<?php echo( $this->seed_channel_model->get_field_view( $field['field_type'], $channel_id, $field_id, $field ) ); ?>


				</tbody>

			</table>

		</div>

		<?php endforeach; ?>

	</div>
	<?php endforeach; ?>

	<p><input type="submit" class="submit" value="<?=lang('start_seed')?>" /></p>

</form>

<?php else : ?>

	<p><?=lang('seed_no_channels_to_populate')?></p>

<?php endif; ?>


</div>

