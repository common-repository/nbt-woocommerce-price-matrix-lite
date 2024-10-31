<tr class="pm-row">
	<td class="pm-row-zero order pm-handle">
		<span><?php echo count($attributes) + 1;?></span>
	</td>

	<td class="pm-field">
		<div class="pm-input">
			<div class="pm-input-wrap">
				<select class="pm-attributes-field" name="pm_attr[]">
					<option value="<?php echo $new_attribute['slug'];?>" selected><?php echo $new_attribute['label'];?></option>
				</select>
			</div>
		</div>
	</td>
	<td class="pm-field">
		<div class="pm-input">
			<div class="pm-input-wrap">
				<select class="pm-direction-field" name="pm_direction[]">
					<?php foreach( NBT_Solutions_Price_Matrix::pm_direction() as $k => $v ) {?>
					<option value="<?php echo $k;?>"<?php if( isset($new_attribute['direction']) && $new_attribute['direction'] == $k ) { echo ' selected';}?>><?php echo $v;?></option>
					<?php }?>
				</select>
			</div>
		</div>
	</td>
	<td class="pm-row-zero">
		<a class="pm-icon -plus small" href="#" data-event="add-row" title="Add row"></a>
		<a class="pm-icon -minus small" href="#" data-event="remove-row" title="Remove row"></a>
	</td>
</tr>