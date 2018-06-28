
			<!--表单筛选 begin-->
			<div class="filter-oper">
				<div class="lArea">
					<span class="fl">申请时间：</span>
					<input class="Wdate vcontent start_time fl" id="startDate" type="text" value="<?php if(empty($search['start'])){ echo date('Y-m-d', strtotime( '-1 month' ));}else{ echo $search['start'];}?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'endDate\',{y:-1})&&\'2015\'}',maxDate:'#F{$dp.$D(\'endDate\')||\'%y-%M-%d\';}',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_from"/>
				        <span class="fl mlr10">至</span>
				        <input class="Wdate vcontent end_time fl" id="endDate" type="text" value="<?php if(empty($search['end'])){ echo date('Y-m-d');}else{ echo $search['end'];} ?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'startDate\');}',maxDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_to"/>
				        <input type="text" class="search-name vcontent" name="uname" value="<?php echo $search['uname'];?>" placeholder="输入用户名查询" c-placeholder="输入用户名查询">
				        <input type="hidden" class="vcontent" name="orderBy" value="<?php echo $search['orderBy'];?>">
			        <a href="javascript:;" class="btn-ss btn-specail submit">查询</a>
				</div>
				<div class="rArea"><a href="javascript:;" class="btn-ss btn-main addRebate">添加下线</a></div>
			</div>
			<table class="mod-tableA">
				<colgroup>
                    <col width="80">
                    <col width="180">
                    <col width="160">
                    <col width="310">
                    <col width="150">
                    <col width="100">
                </colgroup>
				<thead>
					<tr>
						<th>序号</th>
						<th>用户名</th>
						<th>申请时间</th>
						<th>返点比例</th>
						<th><span class="fn-sx-sort <?php if ($search['orderBy'] === 'desc') {echo 'sx-sort-b-current';} elseif ($search['orderBy'] === 'asc') {echo 'sx-sort-t-current';}?>">用户销量(元)<i class="sx-sort-t"></i><i class="sx-sort-b"></i></span></th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($lists as $key => $value):?>
					<tr>
						<td class="fcw"><?php echo (($cpage - 1) * 10 + ($key + 1));?></td>
						<td><?php echo $value['uname'];?></td>
						<td class="fcw"><?php echo $value['created'];?></td>
						<?php 
							$rebate_odds = json_decode($value['rebate_odds'], true);
							arsort($rebate_odds);
							$oddStr = '';
							foreach ($rebate_odds as $lid => $val)
							{
								$oddStr .= $this->betType[$lid] . ":<b>" . $val . "%</b>；";
							}
							$oddStr = mb_substr($oddStr, 0, -1);
						?>
						<td><div class="rebates-prop"><span class="bubble-tip" tiptext="<?php echo $oddStr;?>"><?php echo $oddStr;?></span></div></td>
						<td class="main-color-s">+<?php echo number_format(ParseUnit($value['total_sale'], 1 ), 2); ?></td>
						<td><a href="javascript:;" class="setRebate" data-val="<?php echo $value['uid'];?>">设置比例</a></td>
					</tr>
					<?php endforeach;?>
				</tbody>
			</table>
			<!-- pagination -->
			<?php echo $pagestr;?>
			<!-- pagination end -->
<script>
(function(){		 
	$('.rebates-prop .bubble-tip').mouseenter(function(){
        $.bubble({
            target:this,
            position: 'b',
            align: 'c',
            autoClose: false,
            content: $(this).attr('tiptext'),
            width:'300px'
        })
    }).mouseleave(function(){
            $('.bubble').hide();
        });
})();
</script>