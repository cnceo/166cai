<?php if(!$this->is_ajax):?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/rebates.min.css');?>" rel="stylesheet" />
<div class="wrap p-rebates">
	<div class="tab-nav">
		<ul>
			<li class="active"><a href="javascript:;"><span>推广收益</span></a></li>
			<?php if(empty($rebates['puid'])):?><li><a href="javascript:stab('subordinate','rebates/subordinate')"><span>我的下线</span></a></li><?php endif;?>
			<li class="rebates-notes"><a href="javascript:;">推广说明></a></li>
		</ul>
	</div>
	<div class="tab-content">
		<div class="tab-item" style="display:block;">
			<div class="rebates-led">
				<ul>
					<li>今日收益<i class="icon-font">&#xe618;</i><br><strong class="main-color-s"><?php echo number_format(ParseUnit($todayIncome, 1), 2);?></strong>元</li>
					<li>收入总计<i class="icon-font">&#xe626;</i><br><strong><?php echo number_format(ParseUnit($rebates['total_income'], 1 ), 2); ?></strong>元</li>
					<?php if(empty($rebates['puid'])):?><li class="rebates-led-last">
						推广链接<br><input type="text" id="pro_link" value="<?php echo $rebates['pro_link'];?>"><a href="javascript:;" class="btn-s btn-main" onClick="jsCopy();">复制链接</a>
						<p class="copy-callback" id="copy-callback" style="display:none;"><i class="icon-font">&#xe646;</i>复制链接成功</p>
					</li><?php endif;?>
				</ul>
			</div>

			<div class="mod-tab">
				<ul>
					<li class="current">
						<a href="javascript:;">返点明细</a>
					</li>
					<li>
						<a href="javascript:;">我的比例</a>
					</li>
				</ul>
			</div>
			<div class="mod-tab-con">
				<div class="mod-tab-item detail-form" style="display: block;">
					<!--表单筛选 begin-->
					<div class="filter-oper">
						<span class="fl">交易时间：</span>
						<input class="Wdate vcontent start_time fl" id="startDate" type="text" value="<?php echo date('Y-m-d', strtotime( '-1 month' ));?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'endDate\',{y:-1})&&\'2015\'}',maxDate:'#F{$dp.$D(\'endDate\')||\'%y-%M-%d\';}',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_from"/>
				        <span class="fl mlr10">至</span>
				        <input class="Wdate vcontent end_time fl" id="endDate" type="text" value="<?php echo date('Y-m-d'); ?>" onClick="WdatePicker({startDate:'%y-%M-%d',minDate:'#F{$dp.$D(\'startDate\');}',maxDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true});" style="width:100px" name="date_to"/>
				        <input type="text" class="search-name vcontent" name="userName" placeholder="输入用户名查询" c-placeholder="输入用户名查询">
				        <dl class="simu-select select-small">
				            <dt>
				            <span class='_scontent'>所有彩种</span><i class="arrow"></i>
				            <input type='hidden' name='lid' class="vcontent" value='all' >
				            </dt>
				            <dd class="select-opt">
				            	<div class="select-opt-in" data-name='lid'>
			                        <?php foreach( $betType as $key => $val ): ?>
			                        <a href="javascript:;" data-value="<?php echo $key; ?>"><?php echo $val; ?></a>
			                        <?php endforeach; ?>
					            </div>
				            </dd>
				        </dl>
				        <a href="javascript:;" class="btn-ss btn-specail submit">查询</a>
					</div>
					<!--表单筛选 end-->
					<div id='container-detail-form'>
				<?php endif;?>
					<!--表格 begin-->
					<table class="mod-tableA">
						<colgroup>
		                    <col width="110">
		                    <col width="140">
		                    <col width="220">
		                    <col width="140">
		                    <col width="200">
		                    <col width="170">
		                </colgroup>
						<thead>
							<tr>
								<th>彩种</th>
								<th>期号</th>
								<th>用户名</th>
								<th>消费金额(元)</th>
								<th>赚取收入(元)</th>
								<th>交易时间</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($lists as $value):?>
							<tr>
								<td><a href="/<?php echo BetCnName::getEgName($value['lid']);?>"  target = "_blank">
					<?php echo BetCnName::getCnName($value['lid']); ?>
				</a></td>
								<td><?php echo $value['issue'];?></td>
								<td><?php echo $value['userName'];?></td>
								<td><?php echo number_format(ParseUnit($value['money'], 1 ), 2); ?></td>
								<td class="main-color">+<?php echo number_format(ParseUnit($value['income'], 1 ), 2); ?></td>
								<td><?php echo $value['created'];?></td>
							</tr>
							<?php endforeach;?>
						</tbody>
						<tfoot>
							<tr>
								<td class="tal table-foot-td" colspan="5">
									<span class="mr20">收入笔数：<b class="font-color-s"><?php echo $count;?></b> 笔</span>
									<span>收入金额合计：<b class="main-color"><?php echo number_format(ParseUnit($totalMoney, 1), 2)?></b> 元</span>
								</td>
								<td class="tar table-page">
									<span class="mlr10">本页 <b><?php echo $cpnum;?></b> 条记录</span><span>共 <b><?php echo $pagenum;?></b> 页</span>
								</td>
							</tr>
						</tfoot>
					</table>
					<!--表格 end-->
					<!-- pagination -->
					<?php echo $pagestr;?>
					<!-- pagination end -->
					<?php if(!$this->is_ajax):?>
					</div>
				</div>
				<div class="mod-tab-item">
				<?php 
            		$rebate_odds = json_decode($rebates['rebate_odds'], true);
            	?>
					<table class="mod-tableA">
						<colgroup>
							<col width="200">
							<col width="260">
							<col width="520">
						</colgroup>
						<thead>
							<tr>
								<th>彩种</th>
								<th>返点比例</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($rebate_odds as $lid => $val):?>
							<tr>
				              <td><?php echo $betType[$lid];?></td>
				              <td><span <?php if($val > 0):?>class="main-color"<?php endif;?>><?php echo $val;?>%</span></td>
				              <td></td>
				            </tr>
				            <?php endforeach;?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php if(empty($rebates['puid'])):?>
		<div class="tab-item subordinate-form" id="subordinate" has_load='false'></div>
		<?php endif;?>
		<div class="tab-item">
			<div class="article">
				<h2>特别提示：</h2>
       			<p>本平台的推广返利是给予长期在本平台上预约购彩的忠实用户的优惠奖励。欢迎有意愿成为代理的彩民朋友联系我们，详情请联系在线客服。</p>
       			<dl>
       				<dt>一、返利如何计算？</dt>
       				<dd>
       					<p>本平台返利为全方案（实名认证红包、充值红包参与返利）返利奖励，您发起的方案满员并已出票，无平台网站保底，不论您自购多少金额，系统会按方案总金额返利给您。例如：</p>
       					<ol>
       						<li>1、您自购了100元的方案且已出票，没有使用红包，您的返利=100*返点比例；</li>
       						<li>2、您已使用充100送10元的红包，现自购了200元的方案且已出票，现在您的返利仍为=200*返点比例；</li>
       					</ol>
       				</dd>
       				<dt>二、什么时候返利？</dt>
       				<dd>
       					<ol>
       						<li>1、高频彩种：每期出票成功以后返利；</li>
       						<li>2、慢频数字彩：每期出票成功以后返利；</li>
       						<li>3、胜负彩、任选九：每期出票成功以后返利；</li>
       						<li>4、竞彩：方案出票成功以后返利；</li>
       					</ol>
       				</dd>
       				<dt>三、返利标准</dt>
       				<dd>
       					<ol>
       						<li>1、推广系统支持发展下线，您可以将您的点位分配给您的下线，您的下线或者他们所发展的用户发起的方案，如果符合返利标准，您将获得返利，返利比例为您的点位扣除分给该下线或相关下线的点位；</li>
       						<li>2、为保障推广人权益，不得将网站用户或其他推广人下线发展为自己下线。 如违反上述注意事项，网站有权采取降低推广点位、暂停合作、单方终止合作等措施 。</li>
       					</ol>
       				</dd>
       			</dl>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="/caipiaoimg/src/date/WdatePicker.js"></script>
<script>
	var target = '/rebates';
	$(function(){
		new cx.vform('.detail-form', {
			checklogin: true,
	        submit: function(data) {
			 	if(checkDate()){
				 	return ;
			 	};
			 	data.userName = (data.userName === '输入用户名查询') ? '' : data.userName;
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  target,
	                data: data,
	                success: function(response) {//alert(response);
	            		$('#container-detail-form').html(response);
	                }
	            });
	        }
		 });

		new cx.vform('.subordinate-form', {
			 checklogin:true,
		        submit: function(data) {
				 	if(checkDate()){
					 	return ;
				 	};
		            var self = this;
//	 	            data.uname = (data.uname === '输入用户名查询') ? '' : data.uname;
		            $.ajax({
		                type: 'post',
		                url:  target,
		                data: data,
		                success: function(response) {//alert(response);
		            		$('#subordinate').html(response);
		                }
		            });
		        }
		 });
		// 返点页面
	    $(".p-rebates .mod-tab ul").tabPlug({
	        cntSelect: '.mod-tab-con',
	        menuChildSel: 'li ',
	        onStyle: 'current',
	        cntChildSel: '.mod-tab-item',
	        eventName: 'click'
	    });

	    $('.subordinate-form').on('click', '.addRebate', function(){
	    	 $.ajax({
		            type: 'post',
		            url: '/pop/addRebate',
		            data: {version: version},
		            success: function (response) {
		                $('body').append(response);
		                cx.PopCom.show('.addRebatePop');
		                cx.PopCom.close('.addRebatePop');
		                cx.PopCom.cancel('.addRebatePop');
		            }
		        });
		})

		$('.subordinate-form').on('click', '.setRebate', function(){
	    	var id = $(this).attr("data-val");
			 $.ajax({
	            type: 'post',
	            url: '/pop/setRebateOdd',
	            data: {version: version, id:id},
	            success: function (response) {
	                $('body').append(response);
	                cx.PopCom.show('.set-rebates');
	                cx.PopCom.close('.set-rebates');
	                cx.PopCom.cancel('.set-rebates');
	            }
		     });
		})

		$('.subordinate-form').on('click', '.fn-sx-sort', function(){
			 if($(this).hasClass('sx-sort-b-current')){
	     		$(this).removeClass('sx-sort-b-current').addClass('sx-sort-t-current');
	     	}else{
	     		$(this).removeClass('sx-sort-t-current').addClass('sx-sort-b-current');
	     	}
		    if($(this).is('.sx-sort-t-current')){
			    $('input[name="orderBy"]').val('asc');
			}else if ($(this).is('.sx-sort-b-current')){
				$('input[name="orderBy"]').val('desc');
			}
			$('.subordinate-form').find('.submit').trigger("click");
		 });
	});
    
    function jsCopy(){
        var e=document.getElementById("pro_link");//对象是contents 
        e.select(); //选择对象 
        document.execCommand("Copy"); //执行浏览器复制命令
        document.getElementById("copy-callback").style.display = "";
    }
    
    function stab(ele, url)
    {
    	target = '/rebates/subordinate';
        if($("#"+ele).attr("has_load") == 'false')
        {
            $("#"+ele).load("/"+url,function(){
                $("#"+ele).attr("has_load",'true')
            });
        }
    }

 
</script>
<?php endif;?>