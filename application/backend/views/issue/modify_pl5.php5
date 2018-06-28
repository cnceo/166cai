<style type="text/css">
    .none_input{border: none;box-shadow:none;-webkit-box-shadow:none;}
</style>
<form action="/backend/Issue/modifyBonusDetail" method="get"  id="submit_form">
        <h2 class="kj-dtail-fix-title">
            <em><?php echo $name;?></em>第<input type="text" class="ipt w84" name="issue_fill">期开奖详情
        </h2>
        <div class="data-table-filter" style=" width: 100%;">
            <table>
                <tbody>
                    <tr>
                        <td colspan="9">
                        开奖号码
                        <input type="text" class="ipt w150 none_input" name="awardNum" value="<?php echo $awardNum;?>" readonly='true'>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        全国销售
                        <input type="text" class="ipt w150" name="sale" value="<?php echo $sale;?>">元
                        </td>
                        <td>
                          奖池滚存
                          <input type="text" class="ipt w150" name="pool" value="<?php echo $pool;?>">元
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="20%">
                    <col width="40%">
                    <col width="40%">
                  </colgroup>
                  <thead>
                    <tr>
                      <th>奖级</th>
                      <th>注数</th>
                      <th>奖金（元）</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                     <td>直选</td>
                     <td><input type="text" class="ipt w150" name="zx_zs" value="<?php echo $bonusDetail['zx']['zs'];?>"></td>
                     <td><?php echo !empty($bonusDetail['zx']['dzjj'])?$bonusDetail['zx']['dzjj']:'100000';?></td>
                     <input type="hidden" class="ipt w150" name="zx_dzjj" value="<?php echo !empty($bonusDetail['zx']['dzjj'])?$bonusDetail['zx']['dzjj']:'100000';?>">
                    </tr>
                </tbody>
            </table>
            <div class="btn-group mt10">
                <input type='hidden' class='' name='backUrl' value=''/>
                <input type='hidden' class='' name='type' value='<?php echo $lid;?>'/>
                <input type='hidden' class='' name='issue' value='<?php echo $issue;?>'/>
                <input type='hidden' class='' name='status' value='<?php echo $matchStatus;?>'/>
                <a href="javascript:void(0);" class="btn-blue-h32" id="submit">确认</a>
            </div>
        </div>
</form>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
    $('input[name=backUrl]').val(window.location.href);
    $("#submit").click(function(){
    	var issue = $('input[name="issue_fill"]').val();
        var sale = $('input[name="sale"]').val();
        var pool = $('input[name="pool"]').val();
        var zx_zs = $('input[name="zx_zs"]').val();
        var reg = /^[0-9]\d*$/;
        if (issue !== '<?php echo $issue;?>') {
        	layer.alert('期次信息有误，请确认后填写', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        if(!reg.test(sale) || !reg.test(pool) || !reg.test(zx_zs))
        {
            layer.alert('修改内容必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        //开奖号码验证
        var awardNum = $('input[name="awardNum"]').val();
        ball = awardNum.split(',');
        if(ball.length != 5)
        {
            layer.alert('请输入5个开奖号码~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        for(var i in ball)
        {
            if(!reg.test(ball[i]))
            {
                layer.alert('开奖号码必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
            if(ball[i] < 0 || ball[i] > 9)
            {
                layer.alert('开奖号码范围为0-9~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
        }
        popdialog("confirm-submit");
    });
</script>