<div class="clearfix">
	<div class="pop-table">
		<table>
			<colgroup>
				<col width="96">
				<col width="150">
			</colgroup>
			<thead>
				<tr>
					<th>彩种</th>
					<th>返点比例</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>竞彩足球</td>
					<td>
						<select class="selectList w95" name="42">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['42'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>竞彩篮球</td>
					<td>
						<select class="selectList w95" name="43">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['43'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>双色球</td>
					<td>
						<select class="selectList w95" name="51">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['51'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>大乐透</td>
					<td>
						<select class="selectList w95" name="23529">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['23529'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>福彩3D</td>
					<td>
						<select class="selectList w95" name="52">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['52'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>七乐彩</td>
					<td>
						<select class="selectList w95" name="23528">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['23528'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>新11选5</td>
					<td>
						<select class="selectList w95" name="21407">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['21407'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>惊喜11选5</td>
					<td>
						<select class="selectList w95" name="21408">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['21408'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>老时时彩</td>
					<td>
						<select class="selectList w95" name="55">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['55'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>江西快三</td>
					<td>
						<select class="selectList w95" name="57">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['57'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr> 
			</tbody>
		</table>
	</div>
	<div class="pop-table">
		<table>
			<colgroup>
				<col width="96">
				<col width="150">
			</colgroup>
			<thead>
				<tr>
					<th>彩种</th>
					<th>返点比例</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>老11选5</td>
					<td>
						<select class="selectList w95" name="21406">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['21406'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>七星彩</td>
					<td>
						<select class="selectList w95" name="10022">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['10022'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>排列三</td>
					<td>
						<select class="selectList w95" name="33">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['33'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>排列五</td>
					<td>
						<select class="selectList w95" name="35">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['35'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>胜负彩</td>
					<td>
						<select class="selectList w95" name="11">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['11'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>任选九</td>
					<td>
						<select class="selectList w95" name="19">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['19'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>上海快三</td>
					<td>
						<select class="selectList w95" name="53">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['53'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>快乐扑克</td>
					<td>
						<select class="selectList w95" name="54">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['54'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>
				<tr>
					<td>吉林快三</td>
					<td>
						<select class="selectList w95" name="56">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['56'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr> 
				<tr>
					<td>乐11选5</td>
					<td>
						<select class="selectList w95" name="21421">
          				<?php foreach ($oddType as $key => $val): ?>
            			<option value="<?php echo $key; ?>" <?php if ($rebate_odds['21421'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?>%</option>
            			<?php endforeach; ?>
        				</select>
					</td>
				</tr>  
			</tbody>
		</table>
	</div>
</div>
<div class="main-color">
	<?php echo $tips;?>
</div>