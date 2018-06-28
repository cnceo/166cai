<!--容器-->
<div class="wrap mod-box bet-jc jc-sg">
    <?php echo $this->load->view('v1.1/elements/lottery/detail_info_panel'); ?>

    <!--彩票-->
    <div class="cp-box-bd">
        <div class="filter-date">
            <strong>日期</strong>
                <dl class="simu-select select-small">
                    <dt><?php echo date('Y.m.d', strtotime($date)); ?><i class="arrow"></i></dt>
                    <dd class="select-opt">
                        <div class="select-opt-in">
                            <?php foreach ($dates as $key => $value): ?>
                                <a href="<?php echo $baseUrl; ?>kaijiang/jczq/<?php echo $value; ?>"><?php echo $key; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </dd>
                </dl>
                <script>
                // select
                $("dl[class^='simu-select'] dt").bind('click', function () {
                    _this = $(this);
                    var dt = _this.parent();
                    dt.addClass('selected');
                    _this.siblings('.select-opt').find('a').on('click', function () {
                        _this.html($(this).html() + '<i class="arrow"></i>');
                        dt.removeClass('selected');
                    });
                });
                $(document).bind("click", function (e) {
                    var menu = $(e.target).hasClass('simu-select') || ($(e.target).parents(".simu-select").length > 0);
                    if (!menu) {
                        $(".simu-select").removeClass("selected");
                    }
                });
            </script>
        </div>
        <div class="table">
            <table>
                <thead>
                        <tr>
                            <th width="8%">编号</th>
                            <th width="8%">赛事</th>
                            <th width="13%">比赛时间</th>
                            <th width="10%">主队（让球）</th>
                            <th width="10%">客队</th>
                            <th width="7%">半场比分</th>
                            <th width="7%">全场比分</th>
                            <th width="6%">胜平负</th>
                            <th width="9%">让球胜平负</th>
                            <th width="7%">总进球</th>
                            <th width="7%">半全场</th>
                            <th width="9%">开奖详情</th>
                        </tr>
                    </thead>
                <tbody>
                    <?php if ( ! empty($matches)): ?>
                        <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><?php echo $match['matchId']; ?></td>
                            <td><?php echo $match['name']; ?></td>
                            <td><?php echo date("Y-m-d H:i", strtotime($match['end_sale_time'])); ?></td>
                            <td><?php echo $match['home']; ?>(<span class="<?php echo ($match['let']>0)?'num-red':'num-blue'?>"><?php echo $match['let']; ?></span>)</td>
                            <td><?php echo $match['awary']; ?></td>
                            <td><?php echo $match['m_status']==1?'取消':$match['scoreHalf']; ?></td>
                            <td class="main-color-s score"><?php echo $match['m_status']==1?'取消':$match['score']; ?></td>
                            <td class="main-color-s"><?php echo $match['m_status']==1?'取消':$match['spf']; ?></td>
                            <td class="main-color-s"><?php echo $match['m_status']==1?'取消':$match['rqspf']; ?></td>
                            <td class="main-color-s"><?php echo $match['m_status']==1?'取消':$match['jqs']; ?></td>
                            <td class="main-color-s"><?php echo $match['m_status']==1?'取消':$match['bqc']; ?></td>
                            <?php if($match['showDetail']): ?>
                            <td><a href="<?php echo $baseUrl; ?>kaijiang/jczqDetail/<?php echo $match['mid']; ?>" class="sub-color" target="_blank">查看详情</a></td>
                            <?php else: ?>
                            <td><span class="fcw">暂无</span></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                           <td colspan="12" class="no-data">
                                <p><b>亲，当日无赛事</b></p>去看看其他的场次吧～
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>    
    </div>
    <!--彩票end-->


</div>
<!--容器end-->