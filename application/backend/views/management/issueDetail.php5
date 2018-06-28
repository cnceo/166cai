<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageIssue">期次管理</a></div>
    <div class="kj-dtail-fix mt10">
        <h2 class="kj-dtail-fix-title">
            <em><?php echo $name; ?></em>第<?php echo $issue; ?>期开奖详情
        </h2>

        <div class="data-table-filter" style=" width: 100%;">
            <table>
                <tbody>
                <tr>
                    <td>
                        开奖号码:<span><?php echo $awardNum; ?></span>
                    </td>
                    <td style="text-align:right;">
                    </td>
                </tr>
                <tr>
                    <?php if (in_array($lid, array('sfc', 'rj'))): ?>
                        <td>
                            任九销售:<span><?php $rj_sale = $rj_sale?$rj_sale:0;  echo number_format($rj_sale); ?></span>元
                        </td>
                        <td>
                            胜负彩销售:<span><?php $sfc_sale = $sfc_sale?$sfc_sale:0; echo number_format($sfc_sale); ?></span>元
                        </td>
                    <?php else: ?>
                        <td>
                            全国销售:<span><?php $sale = $sale?$sale:0; echo number_format($sale); ?></span>元
                        </td>
                    <?php endif; ?>
                    <td>
                        奖池滚存:<span><?php $pool = $pool?$pool:0; echo number_format($pool); ?></span>元
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?php $page = '/issue/display_' . $lid;
        $this->load->view($page); ?>
    </div>
</div>
</body>
</html>
