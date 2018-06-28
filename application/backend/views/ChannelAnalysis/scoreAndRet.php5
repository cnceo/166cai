<?php $this->load->view("templates/head") ?>
<style type="text/css">
	.mod-tab-bd ul li{display: block;}
	.pop-dialog{display: block;}
	.tab-radio-del{margin;0px;padding: 0px;float:none;}
</style>
<?php date_default_timezone_set('Asia/Shanghai');?>
<div id="app">
    <div class="path">您的位置：<a href="javascript:;">渠道分析</a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/scoreAndRet">渠道评分及扣减</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
              <li><a href="/backend/ChannelAnalysis/manage">渠道管理</a></li>
              <li><a href="/backend/ChannelAnalysis/countData">渠道数据</a></li>
              <li class="current"><a href="/backend/ChannelAnalysis/scoreAndRet">渠道评分及扣减</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd">
            <ul>
                <li>
                    <Radio-group v-model="qdpf.radio" class="mt10">
                      <Radio label="1">渠道评分设置</Radio>
                      <Radio label="2">扣减比例设置</Radio>
                    </Radio-group>
                    <div class="data-table-list mt10 mb20" v-if="qdpf.radio == 1">
                        <table style="width: 500px;">
                            <colgroup>
                            <col width="169"><col width="169"><col width="160">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>产品</th>
                                    <th>渠道评分标准</th>
                                    <th>更新时间</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>166彩票</td>
                                    <td><a href="javascript:" class="cBlue" id="jzCast">修改</a></td>
                                    <td><?php echo $rule[0]['modified'] ;?></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>备注：举例：输入x＞70%，输入格式为：70%＜x≤ *，请以*替代（shift+8）</p>
                    </div>
                    <div class="data-table-list mt10" v-if="qdpf.radio == 1">
                        <table width="100%">
                            <tbody>
                                <tr>
                                <!--表头-->
                                系数占比
                                <?php foreach ($rule as $k => $v): ?>
                                    <th><?php echo $v['name'] ;?> 系数占比</th>
                                    <td><?php echo $v['percent'] ;?></td>
								<?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                        <table width="100%">
                            <thead>
                                <tr>
                                <?php foreach ($rule as $k => $v): ?>
                                    <th><?php echo $v['name'] ;?> <?php echo $v['des'] ;?></th>
                                    <th>对应得分</td>
								<?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php for ($i = 0; $i < $maxLen; $i++): ?> 
                                <tr>
                                <?php foreach (array_keys($rule) as  $v): ?>
                                	<td><?php
                                    if( isset($rule[$v]['rule'][$i]) )
                                    {
                                        if($rule[$v]['id'] == 2)
                                        {
                                            echo $rule[$v]['rule'][$i]['min_percent'].'＜x≤'.$rule[$v]['rule'][$i]['max_percent'];
                                        }else{
                                            echo $rule[$v]['rule'][$i]['min_percent'].'%＜x≤'.$rule[$v]['rule'][$i]['max_percent'].($rule[$v]['rule'][$i]['max_percent']=='*'?'':'%');
                                        }
                                    }else{
                                        echo '';
                                    }

                                     ?></td>
                                    <td><?php echo isset($rule[$v]['rule'][$i]) ? $rule[$v]['rule'][$i]['score'] : ''; ?></td>
								<?php endforeach; ?>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="data-table-filter mt10 mb20" v-if="qdpf.radio == 2">
                        <form action="/backend/ChannelAnalysis/scoreAndRet" method="post" id="search_form" name="search_form">
                            <table>
                                <colgroup>
                                    <col width="160">
                                    <col width="170">
                                    <col width="160">
                                </colgroup>
                                <tbody>
                                    <tr>
                                        <td>
                                            <label for="">平台：
                                                <select class="selectList w98" id="platform" name="platform">
                                                    <option value="">全部</option>
                                                <?php foreach ($platform as $key => $val):?>
                                                    <option value="<?php echo $key;?>"><?php echo $val;?></option>
                                                <?php endforeach;?>
                                                </select>
                                            </label>
                                        </td>
                                        <td>渠道名称：
                                            <input class="ipt w98" name="channel" value="">
                                        </td>
                                        <td>渠道号：
                                            <input class="ipt w98" name="channelId" value="">
                                        </td>
                                        <td>
                                            <a id="search" href="javascript:;" class="btn-blue ml10">查询</a>
                                            <span class="ml10">注：默认扣减比例为1.00</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="data-table-list mt10 koujian" v-if="qdpf.radio == 2">
                        <table style="width: 600px;">
                            <colgroup>
                              <col width="139">
                              <col width="160">
                              <col width="160">
                              <col width="139">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>渠道名称</th>
                                    <th>扣减比例</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($channels as $k => $v): ?>
                                <tr>
                                    <td><?php echo $v['name']; ?></td>
                                    <td>
                                      <div class="table-modify">
                                          <p class="table-modify-txt" data-val="<?php echo $v['ret_ratio']; ?>"><?php echo $v['ret_ratio']; ?><i></i></p>
                                          <p class="table-modify-ipt"><input type="text" class="ipt" value="<?php echo $v['ret_ratio']; ?>"><i></i></p>
                                      </div>
                                    </td>
                                    <td><?php echo $v['modify_retRatio_time']=='0000-00-00 00:00:00' ? $v['created'] : $v['modify_retRatio_time'] ; ?></td>
                                    <td><a href="javascript:;" class="btn-blue _btn-blue" data-id='<?php echo $v['id']; ?>'>保存</a></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p>备注：① 默认扣减比例为1.00；</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 弹出层配置 start -->
<div class="pop-dialog" id="dialog-jzCast" style="display: none;">
<form name="getRule">
    <div class="pop-in">
        <div class="pop-head">
            <h2>设置渠道评分标准</h2>
            <span class="pop-close _cancel" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <div class="tab-radio-bd" id="tab-config">
                    <ul>
                        <li style="display: block;">
                            <table id="hover-table" class="data-table-list" style="width:420px">
                                <?php foreach ($rule as $k => $v): ?>
                                <thead>
                                     <tr>
                                        <th width="50%"><?php echo $v['name'] ; ?> 系数占比</th>
                                        <td width="50%" colspan="2"><span class="_hide_span"></span><input type="text" class="ipt w60 _input _percent"  name="percent[]" value="<?php echo $v['percent'] ; ?>">&nbsp;&nbsp;&nbsp;<span class='_desc_span'>范围0.00~1.00</span></td>
                                    </tr>
                                    <tr>
                                        <th width="50%"><?php echo $v['name'] ; ?> <?php echo $v['des'] ; ?></th>
                                        <th width="25%">对应得分</th>
                                        <th width="25%">操作</th>
                                    </tr>
                                </thead>
                                <?php if($k==0) :?>
                                <tbody>
                                <?php endif; ?>
                                	<?php if(count($v['rule'])) :?>
                                	<?php foreach ($v['rule'] as $k1 => $v1): ?>
                                    <tr class="_rule">
                                        <td>
                                            <span class="_hide_span"></span><input type="text" class="ipt w60 _input _min" value="<?php echo $v1['min_percent'] ;?>"  data-type="<?php echo $v['id']; ?>" name="min_percent_<?php echo $v['id']; ?>[]">&nbsp;<?php echo $v['id'] == 2? '':'%' ;?>&nbsp;
                                            &lt; x ≤&nbsp;
                                            <span class="_hide_span"></span><input type="text" class="ipt w60 _input _max" value="<?php echo $v1['max_percent'] ; ?>" data-type="<?php echo $v['id']; ?>" name="max_percent_<?php echo $v['id']; ?>[]">&nbsp;<?php echo $v['id'] == 2 ? '':'%' ;?>
                                        </td>
                                        <td>
                                            <span class="_hide_span"></span><input type="text" class="ipt w60 _input _score" value="<?php echo $v1['score'] ; ?>" name="score_<?php echo $v['id']; ?>[]">
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="tab-radio-del" style="padding: 0px;float:none;">×</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                	<?php endif; ?>
                                	<tr class='_rule_op'>
                                    	<td colspan="3">
                                    		<a href="javascript:;" class="btn-white" data-id="<?php echo $v['id']; ?>" data-type='<?php echo $v['id'] == 2 ? '2':'1' ;?>'>添加一行</a>
                                    	</td>
                                    </tr>
                                <?php if($k==0) :?>
                                </tbody>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </table>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmCast">确认</a>
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="submitCast" style="display: none;">确认</a>
            <a href="javascript:;" class="btn-b-white _cancel" id="_cancel">取消</a>
        </div>
    </div>
    </form>
</div>
<!-- 弹出层配置 end -->
<script>
    new Vue({
        el: '#app',
        data: function () {
            return {
                qdpf: {
                    radio: 1
                }
            }
        }
    })
</script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
$(function(){
	//点击事件
	$("body").on("click",".table-modify",function(){
		$(this).find('.table-modify-txt').hide();
		$(this).find('.table-modify-ipt').show();
	});
	$("body").on("click","._btn-blue",function(){
		var obj = $(this).parent().parent();
		var ipt = obj.find('.table-modify-ipt');
		var txt = obj.find('.table-modify-txt');
		var val = ipt.find("input").val();
		if( ipt.is(':hidden') || (val == txt.attr('data-val')))
		{
			layer.alert('请修改后点击保存', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){ ipt.hide();txt.show();} });
		}else{
			if(! /^\d+(\.\d+)?$/.test(val) || val > 1 || val.length > 4)
			{
				layer.alert('请输入0.00-1.00范围内的数字', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){ ipt.hide();txt.show();ipt.find("input").val(txt.attr('data-val'));} });
			}else{
				var jsonArray = []
                var modify_retRatio_time = '<?php echo date('Y-m-d H:i:s');?>';
				var arr = {'id':$(this).attr('data-id'),'ret_ratio':ipt.find("input").val(),'modify_retRatio_time':modify_retRatio_time};
				jsonArray.push(arr);
				ajaxUpdateRetRatio({'data':JSON.stringify(jsonArray)},obj,modify_retRatio_time);				
			}

		}
	});
	 // 投注栏配置
    $('body').on('click', '#jzCast', function(){
        popdialog("dialog-jzCast");
        return false;
    });
    // 新增一行
    $('body').on('click', '.btn-white', function(){
    	$(getHtml($(this).attr('data-id'),$(this).attr('data-type'))).insertBefore($(this).parent().parent());
    })

    // 删除一行
    $('body').on('click', '.tab-radio-del', function(){
		$(this).parents('tr').remove();
    })
    //confirmCast
    $('body').on('click', '#confirmCast', function(){
    	$tag = checkFrom();
        if($tag === -1)
        {
            layer.alert('系数占比之和应等于1', {icon: 2,btn:'',title:'温馨提示',time:0});
        }else if($tag === -3){
           layer.alert('系数比例区间不连续', {icon: 2,btn:'',title:'温馨提示',time:0}); 
        }
        else if($tag === false || $tag === -2 ){
            layer.alert('请正确填写完整的得分或比例', {icon: 2,btn:'',title:'温馨提示',time:0});
        }else{
            doSure();//提交确认
        }
    })
 	//输入数字
 	$('body').on('keyup', '._score', function(){
 		var val = $(this).val();
 		var tag = /^\d+(\.\d+)?$/.test(val);
 		if(!tag)
 		{
 			$(this).val('');
 		}else{

 			if(parseInt(val) < 0){ $(this).val('0'); }
 			if(parseInt(val) >10){ $(this).val('10'); }
 		}
 	});
    /**
     * [更新规则]
     * @author Likangjian  2017-04-29
     * @param  {[type]} ){} [description]
     * @return {[type]}       [description]
     */
    $('body').on('click', '#submitCast', function(){
        $.ajax({
            type: "post",
            url: "/backend/ChannelAnalysis/upDateRule",
            data: $('form[name=getRule]').serialize(),
            success: function(data)
            {
                var json = jQuery.parseJSON(data);
                layer.closeAll();
                if(json.status == 'SUCCESSS')
                {
                    layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
                }else{
                    layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                }
            }
        })
    });
    
    /**
     * [getHtml 构建dom]
     * @author Likangjian  2017-04-29
     * @param  {[type]} id [description]
     * @return {[type]}    [description]
     */
    function getHtml(id,type)
    {
      var type = type == 2 ? '' : '%';
      var html ='';
      	  html+='<tr class="_rule">';
      	  html+='<td><span class="_hide_span"></span><input type="text" class="ipt w60 _input _min" value="" name="min_percent_'+id+'[]" data-type="'+id+'">&nbsp;'+type+'&nbsp;< x ≤&nbsp;<span class="_hide_span"></span><input type="text" class="ipt w60 _input _max" value="" data-type="'+id+'" name="max_percent_'+id+'[]">&nbsp;'+type+'</td>';
      	  html+='<td><span class="_hide_span"></span><input type="text" class="ipt w60 _input _score" value="" name="score_'+id+'[]"></td>';
      	  html+='<td><a href="javascript:;" class="tab-radio-del" style="padding: 0px;float:none;">×</a></td></tr>';
      	  return  html;                              
    }
    // 取消
    $("._cancel").click(function(){
        $("#dialog-jzCast").hide();
        $(".pop-mask").hide();
        clearData();
        return false;
    });
 	/**
     * [ajaxUpdateRetRatio 异步更新字段]
     * @author LiKangJian 2017-05-04
     * @param  {[type]} data                 [description]
     * @param  {[type]} obj                  [description]
     * @param  {[type]} modify_retRatio_time [description]
     * @return {[type]}                      [description]
     */
 	function ajaxUpdateRetRatio(data,obj,modify_retRatio_time)
 	{
		$.ajax({
		    type: "post",
		    url: "/backend/ChannelAnalysis/updateRetRatio",
		    data: data,
		    success: function(data)
		    {
		    	var json = jQuery.parseJSON(data);
		    	layer.closeAll();
		    	if(json.status == 'SUCCESSS')
		    	{
		    		layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){obj.find('.table-modify-ipt').hide();obj.find('.table-modify-txt').html(obj.find('.table-modify-ipt input').val()).show();obj.find('td').eq(2).html(modify_retRatio_time);}});
		    	}else{
		    		layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
		    	}
		    }
		})
 	}
    /**
     * [checkFrom 提交验证]
     * @author Likangjian  2017-04-29
     * @return {[type]} [description]
     */
 	function checkFrom()
 	{
        var tag = true;//_percent
        var sum = 0;
        var min_tag = 0; //确保min<max
        $('._input').each(function(index)
        {
            //小的值大于大的值
            if($(this).hasClass('_min') )
            {
                var m_index = $('._min').index($(this));
                if( $('._input').eq(index+1).val() !='*' && ( parseFloat($(this).val()) >= parseFloat($('._input').eq(index+1).val()) ))
                {
                    min_tag = 1; 
                    return ;
                }
                //大于100
                if( parseFloat( $(this).val() ) > 100 && $(this).attr('data-type') != 2 )
                {
                    tag = false;
                    alert(1);
                    return ;
                }
                if( m_index>=1  
                    &&  
                   ( $(this).attr('data-type') == $('._max').eq(m_index-1).attr('data-type') )
                   && ($('._max').eq(m_index-1).val() != $(this).val())
                  )
                {
                    tag = -3;
                    return;
                }
                
            }else if($(this).hasClass('_score'))
            {
                if(! /^[1-9]*[1-9][0-9]*$/.test($(this).val()) && $(this).val()!=0){tag = false;return;}
            }else if($(this).hasClass('_percent'))
            {
                    sum = sum + parseFloat( $(this).val() );
            }else if($(this).hasClass('_max'))
            {
                if(/^\d+(\.\d+)?$/.test( $(this).val() ) && (parseFloat( $(this).val() ) > 100) && $(this).attr('data-type') != 2 )
                {
                    tag = false;
                    return ;
                }
            }
            
        });

        //验证min<max
        if(min_tag===1){return tag = -2;}
 		//验证 和 不等于1
 		if(sum!=1){ return tag = -1;}
 		return tag ;
 	}
    
    /**
     * [doSure 提交确认]
     * @author Likangjian  2017-04-29
     * @return {[type]} [description]
     */
 	function doSure()
 	{
 		$('.tab-radio-del').hide();
 		$('._input').each(function(index){
 			$('._hide_span').eq(index).html($(this).val());
 			$(this).hide();
 		});
 		$('#confirmCast').hide();
        $('._desc_span').hide();
        $('.btn-white').hide();
 		$('#submitCast').show();
 	}
    /**
     * [clearData 清理数据]
     * @author Likangjian  2017-04-29
     * @return {[type]} [description]
     */
 	function clearData()
 	{
 		$('.tab-radio-del').show();
 		$('._hide_span').html('');
 		$('._input').show();
 		$('#confirmCast').show();
        $('._desc_span').show();
        $('.btn-white').show();
 		$('#submitCast').hide();
 	}
    
    //扣减比例查询
    $('body').on('click', '#search', function(){
        $.ajax({
            type: "post",
            url: "/backend/ChannelAnalysis/koujianData",
            data: $("#search_form").serialize(),
            success: function(html)
            {
                $(".koujian").find("tbody").html(html);
            }
        })
    });
});
</script>
</body>
</html>