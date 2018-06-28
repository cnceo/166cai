<?php $this->load->view("templates/head") ?>
<?php
$platform = array(1=>'网站',2=>'Android',3=>'IOS',4=>'M版',5=>'安卓马甲',6=>'IOS马甲');
$payType =  array(5=>'中信微信',
9=>'威富通支付宝',10=>'威富通微信sdk',11=>'威富通微信PC',12=>'现在支付宝h5',15=>'汇聚无限支付宝h5',16=>'兴业支付宝H5',17=>'微众银行支付宝',
18=>'微信H5-兴业银行',19=>'鸿粤浦发银行',20=>'众邦银行支付宝',21=>'厦门国际银行支付宝',22=>'微信H5-鸿粤兴业银行',24=>'平安银行支付宝',23=>'微信H5-浦发白名单渠道',28 => '盈中平安银行','29' => '番茄支付支付宝h5', '31' => '上海银行微信h5','33' => '盈中平安银行微信h5','34' => '支付宝H5-上海银行','37' => '番茄支付微信h5');
$ctyName = array(2=>'微信支付',3=>'微信扫码',4=>'支付宝');
?>
<style type="text/css">
  #box{display: none;}
  .add_box{width: 600px;height: auto;}
  .add_box .line_box{height: 30px;padding-left:10px; padding-right: 10px;}
  .line_box strong{display: inline-block;height: 30px;line-height: 30px;text-align: right;font-weight: normal;}
  .line_box select{display: inline-block;width: 140px;text-align: center;border:1px solid #ccc;height: 24px;line-height: 24px;}
  .line_box_1{padding-left:10px; padding-right: 10px;height: auto;}
  .configDetailLeft{display: block;float: left;}
  .configDetail{display: inline-block;width: 510px;}
  .configDetail strong{display: block;float:left;text-align: left;font-weight: normal;width: 120px;line-height: 20px;}
  .configDetail textarea{display:inline-block;width:380px;border: 1px solid #ccc;max-width: 390px;min-width: 390px;min-height: 25px;}
</style>
<!-- <div id='box'>
    <div class='add_box'>
        <form>
            <div class='line_box'>
                <strong>充值平台：</strong>
                <span>
                    <select name='platform'>
                        <option value="">全部</option>
                        <?php foreach ($platform as $k => $v): ?>
                        <option value="<?php echo $k;?>"><?php echo $v; ?></option>
                        <?php endforeach ?>
                    </select>
                </span>
            </div> 
            <div class='line_box'>
                <strong>充值渠道：</strong>
                <span>
                    <select name='ctype'>
                        <option value="">全部</option>
                        <?php foreach ($cty as $k => $v): ?>
                        <option value="<?php echo $k;?>" data-type='<?php echo json_encode($v['pay_type']);?>'><?php echo $v['name']; ?></option>
                        <?php endforeach ?>
                    </select>
                </span>
            </div>
            <div class='line_box'>
                <strong>充值方式：</strong>
                <span>
                    <select name='pay_type'>
                        <option value=''>全部</option>
                        <?php foreach ($payType as $k => $v): ?>
                        <option value='<?php echo $k;?>'><?php echo $v;?></option>
                        <?php endforeach ?>
                    </select>
                </span>
            </div>
            <div class='line_box_1'>
                <div class='configDetailLeft'>配置参数：</div>
                <div class='configDetail'>
                    <strong>3434443344商户号：</strong>
                    <span>
                        <textarea name='appId'></textarea>
                    </span>
                </div>
            </div> 
        </form>
    </div>
</div> -->
<div class="path">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">商户号管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Management/payconfig/1">网页端</a></li>
            <li><a href="/backend/Management/payconfig/2">安卓</a></li>
            <li><a href="/backend/Management/payconfig/3">IOS</a></li>
            <li ><a href="/backend/Management/payconfig/4">M版</a></li>
            <li ><a href="/backend/Management/payconfig/5">安卓马甲</a></li>
            <li ><a href="/backend/Management/payconfig/6">IOS马甲</a></li>
            <li class="current"><a href="/backend/Management/payAddList">商户号管理</a></li>
        </ul>
    </div>
</div>   
<div class="data-table-filter mt10">
    <form action="/backend/Management/payAddList" method="get" id="search_form">
        <table>
            <colgroup>
                <col width="62"/>
                <col width="140"/>
                <col width="62"/>
                <col width="232"/>
                <col width="232"/>
                <col width="232"/>
            </colgroup>
            <tbody>
            <tr>
                <th>充值平台：</th>
                <td>
                    <select class="selectList w130" name="platform1">
                        <option value="">全部</option>
                        <?php foreach ($platform as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php if ($search['platform'] === "{$k}"): echo "selected"; endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <th>充值方式：</th>
                <td>
                    <select class="selectList w146" name="pay_type1">
                        <option value="">全部</option>
                        <?php foreach ($payType as $k => $v): ?>
                            <option value="<?php echo $k; ?>" <?php if ($search['pay_type'] === "{$k}"): echo "selected"; endif; ?>><?php echo $v; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <a  href="javascript:void(0);" class="btn-blue ml35 addBtn" >新增商户号</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <a id="searchTransction" href="javascript:void(0);" class="btn-blue ml35 btn-search" >查询</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>
<div class="data-table-list mt20">
    <table>
        <colgroup>
            <col width="150"/>
            <col width="100"/>
            <col width="150"/>
            <col width="100"/>
            <col width="150"/>
            <col width="100"/>
        </colgroup>
        <thead>
        </thead>
        <tbody>
        <tr>
            <th>商户号</th>
            <th>充值平台</th>
            <th>充值方式</th>
            <th>状态 </th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($data as $k => $v): ?>
            <tr>
                <td><?php echo $v['mer_id']; ?></td>
                <td><?php echo $platform[$v['platform']]; ?></td>
                <td><?php echo $payType[$v['pay_type']]; ?></td>
                <td>
                <?php if ($v['status']==0 && ( (in_array($v['ctype'], array(2,3,4))&&$v['rate']>0) || (in_array($v['ctype'], array(1,5)) && $v['rate']==-1)) || ($v['ctype']==6 && $v['rate']==100) ): ?>
                使用中<?php else: ?>停用<?php endif ?>
                </td>
                <td><?php echo substr($v['created'], 0, -3)?></td>
                <td>
                <?php if ($v['status']==1 || ( $v['status']==0 && (in_array($v['ctype'], array(2,3,4))&&$v['rate']==0) || ($v['status']==0 && in_array($v['ctype'], array(1,5)) && $v['rate']!=-1)) || ($v['status']==0 && $v['ctype']==6 && $v['rate']!=100) ): ?>
                <a href="javascript:;" class='modifyConfig' data-platfrom = "<?php echo $v['platform']; ?>" data-ctype = "<?php echo $v['ctype']; ?>"  data-ptype = "<?php echo $v['pay_type']; ?>" data-extra='<?php echo $v['extra']; ?>'; data-id='<?php echo $v['id']; ?>' >修改</a>
                <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5">
              <div class="stat">
                <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
              </div>
            </td>
          </tr>
        </tfoot>
    </table>
</div>
<div class="page mt10 transactions_info">
    <?php echo $pages[0] ?>
</div>
</div>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
var dataType ;
var dataExtra =0;
var load = 0;
$(function(){
    $('.btn-search').click(function(){
        $('#search_form').submit();
    });
    var html = "<div class='add_box'>";
        html+="<form class='addForm'><div class='line_box'><strong>充值平台：</strong><span><select name='platform'><option value=''>全部</option><?php foreach ($platform as $k => $v): ?><option value='<?php echo $k;?>'><?php echo $v; ?></option><?php endforeach ?></select></span></div>";
        html+="<div class='line_box'><strong>充值渠道：</strong><span><select name='ctype'><option value=''>全部</option><?php foreach ($cty as $k => $v): ?><option value='<?php echo $k;?>' data-type='"+'<?php echo json_encode($v['pay_type']);?>'+"'><?php echo $v['name']; ?></option><?php endforeach ?></select></span></div>";
        html+="<div class='line_box'><strong>充值方式：</strong><span><select name='pay_type'><option value=''>全部</option><?php foreach ($payType as $k => $v): ?><option value='<?php echo $k;?>'><?php echo $v;?></option><?php endforeach ?></select></span></div>";
        html+="<div class='line_box_1'><div class='configDetailLeft'>配置参数：</div><div class='configDetail'></div></div></form></div>";
    $('.addBtn').click(function(){
        layer.open({
          'title':'商户号信息',
          'type': 1,
          'area': '600px;',
          'closeBtn': 1, //不显示关闭按钮
          'btn': ['提交'],
          'shadeClose': true, //开启遮罩关闭
          'content': html, 
          'btnAlign': 'c',
          'yes': function()
            {
                load = layer.load(0, {shade: [0.5, '#393D49']});
                if(!$('select[name=platform]').val())
                {
                    layer.alert('充值平台不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){layer.close(load);}});
                    return;
                }
               if(!$('select[name=ctype]').val())
                {
                    layer.alert('充值渠道不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){layer.close(load);}});
                    return;
                }
               if(!$('select[name=pay_type]').val())
                {
                    layer.alert('充值方式不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){layer.close(load);}});
                    return;
                }
                //验证
                var flag = true;
                $('.configDetail textarea').each(function(){
                    if(!$(this).val())
                    {
                        layer.alert('必要参数不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){layer.close(load);}});
                        flag = false;
                        return;
                    }
                });
                if(flag===false) return;
                $.ajax({
                    type: "post",
                    url: "/backend/Management/storePayConfig",
                    data: $('.addForm').serialize(),
                    success: function(data)
                    {
                        var json = $.parseJSON(data);
                        layer.closeAll();
                        if(json.status == 'SUCCESS')
                        {
                            layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
                        }else{
                            layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                        }
                    }
                });
            }
        }); 
    });

/**
 * [平台变化]
 * @author LiKangJian 2018-01-09
 * @param  {[type]} ){                $('select[name [description]
 * @return {[type]}     [description]
 */
$('body').on('change','select[name=platform]',function(){
   $('select[name=ctype]').val('');
   $('select[name=pay_type]').val('');
   $('.configDetail').html('');
 });
/**
 * [充值渠道改变]
 * @author LiKangJian 2018-01-09
 * @param  {[type]} ){                $('select[name [description]
 * @return {[type]}     [description]
 */
$('body').on('change','select[name=ctype]',function(){
    var platform = $('select[name=platform]').val();
    $('.configDetail').html('');
   if(platform=='')
   {
        layer.alert('请选择充值平台~', {icon: 2,btn:'',title:'温馨提示',time:0});
        $(this).val(''); 
        return;
   }
   $('select[name=pay_type]').val('');
   dataType = $(this).find('option:selected').data('type');
   if(dataType[platform].length==0)
   {
    $('select[name=pay_type] option').each(function(index){if(index>0){$(this).hide();}});
   }else{
     $('select[name=pay_type] option').each(function(index){
        if(index>0)
        {
            if($.inArray($(this).attr('value'),dataType[platform])!='-1')
            {
                $(this).show();
            }else{
               $(this).hide(); 
            }
        }
     });
     
   }

 });
/**
 * [充值方式]
 * @author LiKangJian 2018-01-10
 * @param  {[type]} ){                if($('select[name [description]
 * @return {[type]}     [description]
 */
$('body').on('change','select[name=pay_type]',function(){
    $('.configDetail').html('');
   if($('select[name=platform]').val()=='')
   {
        layer.alert('请选择充值平台~', {icon: 2,btn:'',title:'温馨提示',time:0});
        $(this).val(''); 
        return;
   }
    if($('select[name=ctype]').val()=='')
   {
        layer.alert('请选择充值渠道~', {icon: 2,btn:'',title:'温馨提示',time:0});
        $(this).val(''); 
        return;
   }
   setPayConfig();
 });
 //修改
 $('.modifyConfig').click(function(){
        layer.open({
          'title':'商户号信息',
          'type': 1,
          'area': '600px;',
          'closeBtn': 1, //不显示关闭按钮
          'btn': ['提交'],
          'shadeClose': true, //开启遮罩关闭
          'content': html, 
          'btnAlign': 'c',
          'yes': function()
            {
                load = layer.load(0, {shade: [0.5, '#393D49']});
                //验证
                var flag = true;
                $('.configDetail textarea').each(function(){
                    if(!$(this).val())
                    {
                        layer.alert('必要参数不能为空~', {icon: 2,btn:'',title:'温馨提示',time:0,end:function(){layer.close(load);}});
                        flag = false;
                        return;
                    }
                });
                if(flag===false) return;
                $('select[name=platform]').removeAttr("disabled");
                $('select[name=ctype]').removeAttr("disabled");
                $('select[name=pay_type]').removeAttr("disabled");
                $.ajax({
                    type: "post",
                    url: "/backend/Management/storePayConfig",
                    data: $('.addForm').serialize(),
                    success: function(data)
                    {
                        var json = $.parseJSON(data);
                        layer.close(load);
                        if(json.status == 'SUCCESS')
                        {
                            layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
                        }else{
                            layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                        }
                    }
                });
            }
        });
        dataExtra = $(this).data('extra');
        $('.addForm').append('<input type="hidden" name="id" value="'+$(this).data('id')+'"/>');
        $('select[name=platform]').val($(this).data('platfrom')).attr("disabled","disabled"); 
        $('select[name=ctype]').val($(this).data('ctype')).attr("disabled","disabled");
        $('select[name=pay_type]').val($(this).data('ptype')).attr("disabled","disabled");
        getPayCofig($(this).data('platfrom'),$(this).data('ctype'),$(this).data('ptype'),2); 
 })
 //删除方法
 $('.btn-del').click(function(){
   var trObj = $(this).parent().parent();
   var id = $(this).data('id');
   var layerConfirm =  layer.confirm('您确定要删除商户号？', {
      btn: ['确认','取消'], //按钮
      title:'温馨提示',
      btnAlign: 'c',
    }, function(){
        load = layer.load(0, {shade: [0.5, '#393D49']});
        $.ajax({
            type: "post",
            url: "/backend/Management/delPayConfig",
            data: {'id':id},
            success: function(data)
            {
                var json = $.parseJSON(data);
                layer.close(load);
                if(json.status == 'SUCCESS')
                {
                    trObj.remove();
                    layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0});
                }else{
                    layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                }
            }
        });  
    }, function(){
        layer.close(layerConfirm);
    });

 });
 /**
  * [setPayConfig 吊起配置参数]
  * @author LiKangJian 2018-01-10
  */
 function setPayConfig()
 {
    if($('select[name=pay_type]').val())
    {
        getPayCofig($('select[name=platform]').val(),$('select[name=ctype]').val(),$('select[name=pay_type]').val(),1);
    }

 }
 function getPayCofig(platform,ctype,pay_type,way)
 {
    load = layer.load(0, {shade: [0.5, '#393D49']});
    $.ajax({
        type: "post",
        url: "/backend/Management/payTypeConfig",
        data: {'platform':platform,'ctype':ctype,'pay_type':pay_type},
        success: function(data)
        {
            var json = $.parseJSON(data);
            console.log(json);
            var str = "";
            for(var i in json) 
            {
                str +="<strong>"+json[i]+"("+i+")：</strong>";
                if(way==1)
                {
                    str +='<span><textarea name="'+i+'"></textarea></span>';
                }else{
                    str +='<span><textarea name="'+i+'">'+(dataExtra==0 ? '' : dataExtra[i])+'</textarea></span>';
                }
                
            } 
            layer.close(load);
            $('.configDetail').html(str);
        }
    });
 }

});
</script>