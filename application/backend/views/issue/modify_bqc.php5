<form action="/backend/Issue/modifyBonusDetail" method="get"  id="submit_form">
        <h2 class="kj-dtail-fix-title">
            <em><?php echo $name;?></em>第<?php echo $issue;?>期开奖详情
        </h2>
        <div class="data-table-filter" style=" width: 100%;">
            <table>
                <tbody>
                    <tr>
                        <td colspan="9">
                        开奖号码
                        <input type="text" class="ipt w150" name="awardNum" value="<?php echo $awardNum;?>">
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
                       <th>奖金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>一等奖</td>
                        <td><input type="text" class="ipt w150" name="1dj_zs" value="<?php echo $bonusDetail['1dj']['zs'];?>"></td>
                        <td><input type="text" class="ipt w150" name="1dj_dzjj" value="<?php echo $bonusDetail['1dj']['dzjj'];?>"></td>
                    </tr>    
                </tbody>
            </table>
            <div class="btn-group mt10">
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
    $("#submit").click(function(){
        var sale = $('input[name="sale"]').val();
        var pool = $('input[name="pool"]').val();
        var zs1 = $('input[name="1dj_zs"]').val();
        var dzjj1 = $('input[name="1dj_dzjj"]').val();
        var reg = /^[0-9]\d*$/;
        if(!reg.test(sale) || !reg.test(pool) || !reg.test(zs1) || !reg.test(dzjj1))
        {
            layer.alert('修改内容必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        //开奖号码验证
        var awardNum = $('input[name="awardNum"]').val();
        ball = awardNum.split(',');
        if(ball.length != 12)
        {
            layer.alert('请输入12个开奖号码~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return false;
        }
        var ballArray = ["0","1","3"];
        for(var i in ball)
        {
            if(!reg.test(ball[i]))
            {
                layer.alert('开奖号码必须为非负整数~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
            if($.inArray(ball[i],ballArray) < 0)
            {
                layer.alert('开奖号码值只能为0,1,3~', {icon: 2,btn:'',title:'温馨提示',time:0});
                return false;
            }
        }
        $('#submit_form').submit();
    });
</script>