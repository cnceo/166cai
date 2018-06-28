<?php $lottery=array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负彩','19' => '任选九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三','35' => '排列五') ;?>
<div class="pub-pop pop-pay pop-w-max pop-id">
    <div class="pop-in">
        <div class="pop-head">
            <h2>定制跟单</h2>
            <span class="pop-close" title="关闭">×</span>
        </div>
        <div class="pop-body">
            <div class="buy-dzgd">
                <table class="buy-dzgd-info">
                    <colgroup>
                        <col width="80"><col width="80"><col width="120"><col width="80"><col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <th rowspan="3">
                                <div class="lotteryTit">
                                    <div class="lottery-info lottery-<?php echo $gendanUser['liden']; ?>">
                                        <div class="lottery-img">
                                            <svg width="320" height="320">
                                            <image alt="<?php echo $lottery[$gendanUser['lid']]; ?>" xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="//888.166cai.cn/caipiaoimg/v1.1/img/lottery-logo.svg?v=208" src="//888.166cai.cn/caipiaoimg/v1.1/img/lottery-logo.png?v=208" width="320" height="320"></image>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th>发起人：</th><td><?php echo uname_cut($gendanUser['uname']);?></td><th>合买战绩：</th><td><?php  if($gendanUser['award']){ ?><span class="level"><span class="level"><?php echo $gendanUser['award'];?></span></span><?php  }else{ echo '无';} ?></td>
                        </tr>
                        <tr>
                            <th>定制彩种：</th><td><?php echo $lottery[$gendanUser['lid']]; ?></td><th>定制人数：</th><td><?php echo $gendanUser['isFollowNum'];?>人(剩<?php echo 2000-$gendanUser['isFollowNum']; ?>人）</td>
                        </tr>
                        <tr>
                            <th>中奖次数：</th><td><?php echo $gendanUser['winningTimes'];?>次</td><th>累计奖金：</th><td><em class="main-color-s"><?php echo number_format($gendanUser['bonus']/100,2); ?></em>元</td>
                        </tr>
                    </tbody>
                </table>
                <form class="form">
                    <div class="cp-box-hd clearfix">
                        <div class="payway-txt" tab="PAY_WAY">预先支付一定金额，定制发起人的方案</div>
                        <div class="payway-txt" tab="PAY_WAY" style="display: none;">发起人发单时，从账户余额中扣款进行跟单</div>
                        <ul class="bet-type-link JS_TAB" data-rule='{
                            "currentClass": "selected",
                            "linkItem": "PAY_WAY"
                        }'>
                            <li class="selected fukuantype" data-type="1"><a href="javascript:;">预付扣款</a></li>
                            <li class="fukuantype" data-type="2"><a href="javascript:;">实时扣款</a></li>
                        </ul>
                    </div>
                    <div class="form-item tab-radio">
                        <label for="" class="form-item-label">定制方式：</label>
                        <div class="form-item-con">
                            <ul class="tab-radio-hd JS_TAB" data-rule='{
                                "currentClass": "acitve",
                                "linkItem": "DZ_WAY"
                            }'>
                                <li class="active"><label><input name="bmsz" type="radio" checked class="form-item-radio numInput" value="0">按固定金额</label></li>
                                <li><label><input name="bmsz" type="radio" class="form-item-radio numInput" value="1">按百分比</label></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-radio-bd">
                            <div class="form-item" tab="DZ_WAY" style="display: block;">
                                <label for="" class="form-item-label">每次认购金额：</label>
                                <div class="form-item-con">
                                    <div class="commission">
                                        <input type="text" class="form-item-ipt numInput" name="gendanmoney" id="gendanmoney" value="5" data-max="10000">元
                                        <s>至少认购1元</s>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item" tab="DZ_WAY" style="display: none;">
                                <label for="" class="form-item-label">每次认购比例：</label>
                                <div class="form-item-con">
                                    <div class="commission">
                                        <div><input type="text" class="form-item-ipt numInput" value="5" data-max="100" id="gendanpercent">%，但每次不超过 <input type="text" class="form-item-ipt numInput" data-max="10000" value="10" id="gendanmax">元</div>
                                        <s>每次认购金额 = 方案金额*认购比例</s>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="form-item">
                        <label for="" class="form-item-label">定制次数：</label>
                        <div class="form-item-con">
                            <div class="commission">
                                <input type="text" class="form-item-ipt numInput" value="10" name="gendannum" id="gendannum" data-max="100">次
                                <s>最多定制100次，成功跟单1个算1次</s>
                            </div>
                        </div>
                    </div>
                    <div class="form-pop-tip buy-together-foot" id="yufukuan">
                        <p>您需要预付：<em class="main-color-s" id="gendanAllMoney">50</em> 元</p>
                    </div>
                    <div class="form-pop-tip buy-together-foot hidden" id="shishifukuan">
                        <p>无须预付，系统跟单时请保证账户余额充足</p>
                    </div>                    
                </form>
                <div class="btn-group">
                    <a class="btn btn-main btn-gendan-buy <?php echo $showBind ? ' not-bind': '';?>" data-lid="<?php echo $gendanUser['lid']; ?>" data-uid="<?php echo $gendanUser['uid']; ?>" href="javascript:;">立即定制</a>
                </div>
                <div class="warn-tip">
                    <h3>跟单提醒：</h3>
                    <ol>
                        <li>1、发起人发起方案时，系统会按定制跟单者的定制时间顺序去认购；</li>
                        <li>2、当可认购金额小于您的设定金额，则系统默认认购方案的剩余部分；</li>
                        <li>3、当按百分比跟单时，认购金额最大为上限金额；</li>
                        <li>4、实时扣款无须提前预付，系统认购时，帐户余额小于每次认购金额则按账户余额认购。</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(".numInput").on('click blur', function(){
    var $this = $(this);
    if ((/^(.*)\D+(.*)$/.test($(this).val()))) $this.val(1);
    if (!$this.val()) $this.val(1);
    if ($this.data('max') && $this.val() >= parseInt($this.data('max'))) $this.val($this.data('max'));
    calAllMoney();
});
$("input:radio[name='bmsz']").change(function() {
    calAllMoney(); 
});
payType = 1;
$(".fukuantype").click(function(){
    payType = $(this).data("type");
    if(payType==1)
    {
        $("#yufukuan").removeClass("hidden");
        $("#shishifukuan").addClass("hidden");
    }
    if(payType==2)
    {
        $("#shishifukuan").removeClass("hidden");
        $("#yufukuan").addClass("hidden");
    }    
});
var calAllMoney = function(){
    var  type= $("input:radio[name='bmsz']:checked").val();
    if(type == 0)
    {
        var  money = $("#gendanmoney").val();
        var  num = $("#gendannum").val();
        $("#gendanAllMoney").html(money*num);
    }
    if(type == 1)
    {
        var  money = $("#gendanmax").val();
        var  num = $("#gendannum").val();
        $("#gendanAllMoney").html(money*num);
    }    
}
$('.JS_TAB').tabPlug();
$('.btn-gendan-buy').on('click', function () {
        var  uid = $(this).data('uid');
        var  lid = $(this).data('lid');
        var  type= $("input:radio[name='bmsz']:checked").val();
        var  money = $("#gendanmoney").val();
        var  num = $("#gendannum").val();
        var percent= $("#gendanpercent").val();
        var max = $("#gendanmax").val();
        if (!$.cookie('name_ie')) {//登录过期
            $(".pop-close").click();
            $(this).addClass('needTigger');
            cx.PopAjax.login(1);
            return;
        }
        if ($(this).hasClass('not-bind')){
            $(".pop-close").click();
            return;
        }
        $.ajax({
            type: "post",
            url: "/orders/createGendan",
            data: {
                'uid': uid,
                'lid': lid,
                'type':type,
                'money':money,
                'num':num,
                'percent':percent,
                'max':max,
                'payType':payType
            },
            dataType: "json",
            success: function (res) {
                $(".pop-close").click();
                if (res.code==401) {
                    cx.Alert({content: '<i class="icon-font">&#xe600;</i>您已定制发起人的方案，换个彩种试试吧',
                        confirmCb: function () {
                            $('.gendan').find('.submit').trigger('click');
                    }});
                    return false;
                }
                if (res.code==402) {
                    cx.Alert({content: '<i class="icon-font">&#xe600;</i>定制人数已达上限，换个彩种试试吧',
                        confirmCb: function () {
                            $('.gendan').find('.submit').trigger('click');
                    }});
                    return false;
                }
                if (res.code==100) {
                    cx.Alert({content: res.msg,
                        confirmCb: function () {
                            $('.gendan').find('.submit').trigger('click');
                    }});
                    return false;
                }
                if(res.code==200 && payType==1)
                {
                    cx.castCb(res.data, {ctype:'pay', orderType:5, buyMoney:res.data.totalMoney,msgconfirmCb:function(){$('.gendan').find('.submit').trigger('click');}});
                }
                if(res.code==200 && payType==2){
                    new cx.Confirm({
                        content: '<div class="mod-result result-success"><div class="mod-result-bd"><i class="icon-result"></i><div class="result-txt"><h2 class="result-txt-title">恭喜您，定制跟单成功</h2></div></div></div>',
                        btns: [{type: 'cancel', txt: "关闭"}, {type: 'confirm', target: '_blank', txt: '查看详情', href: baseUrl + 'hemai/gdetail/gd' + res.data.followId}],
                        cancelCb: function() {var str = location.href.split("?"); location.href = str[0];},
                        confirmCb: function() {var str = location.href.split("?"); location.href = str[0];},
                        append:'<div class="pop-side"><div class="qrcode"><table><tbody><tr><td><img src="/caipiaoimg/v1.1/img/qrcode-pay.png" width="94" height="94" alt=""></td><td><p><b>扫码下载手机客户端</b>中奖结果早知道</p></td></tr></tbody></table></div></div>'
                    });
                }
            }
        });            
    });        
</script>