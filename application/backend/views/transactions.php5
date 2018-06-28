<?php if ($fromType != 'ajax'): $this->load->view("templates/head") ?>
    <div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">交易明细</a></div>
<?php endif; ?>
    <div class="data-table-filter mt10">
        <form action="/backend/Transactions/" method="get" id="search_form">
            <table>
                <colgroup>
                    <col width="62"/>
                    <col width="140"/>
                    <col width="62"/>
                    <col width="232"/>
                    <col width="62"/>
                    <col width="400"/>
                </colgroup>
                <tbody>
                <tr>
                    <th>关键字：</th>
                    <td>
                        <input type="text" class="ipt w130" name="name" value="<?php echo $search['name'] ?>"
                               placeholder="用户名..."/>
                    </td>
                    <th>交易编号：</th>
                    <td>
                        <input type="text" class="ipt w222" name="trade_no" value='<?php echo $search['trade_no'] ?>'/>
                    </td>
                    <th>交易时间：</th>
                    <td>
                        <span class="ipt ipt-date w184"><input type="text" name='start_time'
                                                               value="<?php echo $search['start_time'] ?>"
                                                               class="Wdate1"/><i></i></span>
                        <span class="ml8 mr8">至</span>
                        <span class="ipt ipt-date w184"><input type="text" name='end_time'
                                                               value="<?php echo $search['end_time'] ?>"
                                                               class="Wdate1"/><i></i></span>
                    </td>
                </tr>
                <tr>
                    <th>交易类型：</th>
                    <td>
                        <select class="selectList w130" name="jylx">
                            <option value="">全部</option>
                            <?php foreach ($this->jylx_cfg as $key => $jylx): ?>
                                <option
                                    value="<?php echo $key; ?>" <?php if ($search['jylx'] === "{$key}"): echo "selected"; endif; ?>><?php echo $jylx; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <th>交易金额：</th>
                    <td>
                        <input type="text" class="ipt w98" name="start_money"
                               value='<?php echo $search['start_money'] ?>'/>
                        <span class="ml8 mr8">至</span>
                        <input type="text" class="ipt w98" name="end_money" value='<?php echo $search['end_money'] ?>'/>
                    </td>
                    <td colspan="2">
                        <label for="shouru"><input id="shouru" name="is_shouru" value='1' type="checkbox"
                                                   class="ckbox" <?php if ($search['is_shouru'] == 1): echo "checked"; endif; ?> />收入交易</label>
                        <label for="zhichu" class="ml20"><input id="zhichu" name="is_zhichu" type="checkbox"
                                                                class="ckbox"
                                                                value='1' <?php if ($search['is_zhichu'] == 1): echo "checked"; endif; ?> />支出交易</label>
                        <a id="searchTransction" href="javascript:void(0);" class="btn-blue ml35" >查询</a>
                    </td>
                </tr>

                </tbody>
            </table>
            <input type="hidden" name="fromType" value="<?php echo $fromType ?>" id="fromType"/>
            <?php if ($fromType == 'ajax'): ?><input type="hidden" name="uid"
                                                     value="<?php echo $search['uid'] ?>"/><?php endif; ?>
        </form>
    </div>

    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="129"/>
                <col width="100"/>
                <col width="78"/>
                <col width="105"/>
                <col width="78"/>
                <col width="85"/>
                <col width="112"/>
                <col width="120"/>
                <col width="90"/>
            </colgroup>
            <thead>
            <tr>
                <td colspan="9">
                    <div class="tal">
                        <strong>收入交易笔数</strong>
                        <span><?php echo intval($tj[1]['ct']); ?> </span>
                        <strong class="ml20">收入交易总额</strong>
                        <span><?php echo m_format($tj[1]['mon']); ?> 元</span>
                    </div>
                    <div class="tal">
                        <strong>支出交易笔数</strong>
                        <span><?php echo intval($tj[0]['ct']); ?> </span>
                        <strong class="ml20">支出交易总额</strong>
                        <span><?php echo m_format($tj[0]['mon']); ?> 元</span>
                    </div>
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>交易编号</th>
                <th>用户名</th>
                <th>真实姓名</th>
                <th>交易类型</th>
                <th>收入（元）</th>
                <th>支出（元）</th>
                <th>账户余额（元）</th>
                <th>交易时间</th>
                <th>交易说明</th>
            </tr>
            <?php foreach ($trans as $key => $tran): ?>
                <tr>
                    <td><?php echo $tran['trade_no'] ?></td>
                    <td><?php echo $tran['uname'] ?></td>
                    <td><?php echo $tran['real_name'] ?></td>
                    <td><?php echo $this->jylx_cfg[$tran['ctype']]; ?></td>
                    <td><?php if ($tran['mark'] == 1): echo m_format($tran['money']);
                        else: echo "--"; endif; ?></td>
                    <td><?php if ($tran['mark'] == 0): echo m_format($tran['money']);
                        else: echo "--"; endif; ?></td>
                    <td><?php echo m_format($tran['umoney']) ?></td>
                    <td><?php echo $tran['created'] ?></td>
                    <td><?php echo !empty($tran['content'])?$tran['content']:''; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>

            <tr>
                <td colspan="9">
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
    <script src="/source/date/WdatePicker.js"></script>
    <script>
        var caipiao_cfg = jQuery.parseJSON('<?php echo json_encode($this->caipiao_cfg) ?>');
        $(function () {
        	$("#searchTransction").click(function(){
                if ($("#fromType").val() == "ajax") {
                	$("#transactions_info").load("/backend/Transactions/index?" + $("#search_form").serialize() + "&fromType=ajax");
                    return false;
                }
                $('#search_form').submit();
          	});
        	
            $("#caipiao_play").bind('change', function () {
                play = caipiao_cfg[$(this).val()]['play'];
                if ($("#play_type").length > 0) {
                    $("#play_type").remove();
                }
                if (play != undefined) {
                    html = ' <select class="selectList w130"  name="playType" id="play_type">';
                    html += '<option value="0">全部</option>';
                    for (var key in play) {
                        html += '<option value="' + key + '">' + play[key]['name'] + '</option>';
                    }
                    html += '</select>';
                    $("#caipiao_play").after(html);
                }
            });

            $(".Wdate1").focus(function () {
                dataPicker();
            });

            $('#search_form').submit(function () {
                if ($("#fromType").val() == "ajax") {
                    $("#transactions_info").load("/backend/Transactions/index?" + $("#search_form").serialize() + "&fromType=ajax");
                    return false;
                }
                return true;
            });
            $('.transactions_info a').click(function () {
                if ($("#fromType").val() == "ajax") {
                    var _this = $(this);
                    $("#transactions_info").load(_this.attr("href"));
                    return false;
                }
                return true;
            });
        });

    </script>
<?php if ($fromType != 'ajax'): ?>
    </body>
    </html>
<?php endif; ?>