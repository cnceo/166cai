<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/image">首页管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li><a href="/backend/Info/center">资讯管理</a></li>
            <li class="current"><a href="/backend/Info/image">轮播图管理</a></li>
            <li><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li><a href="/backend/Info/infoList">首页资讯管理</a></li>
        </ul>
    </div>
    <div class="mod-tab-bd">
        <ul>
            <li style="display: block">
                <div class="data-table-list mt10">
                    <table>
                        <colgroup>
                            <col width="5%"/>
                            <col width="20%"/>
                            <col width="26%"/>
                            <col width="6%"/>
                            <col width="24%"/>
                            <col width="14%"/>
                            <col width="5%"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>序号</th>
                            <th>标题（长度建议在<span class="cRed">10-15</span>个字之间）</th>
                            <th>图片</th>
                            <th>背景色值</th>
                            <th>链接</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="pic-table">
                        <tr>
                            <td>
                                <input type="text" class="ipt w40 tac" value="1">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w184" value="">
                            </td>
                            <td>
                                <input type="file" id="up_img0" class="up_img"/>
                                <div id="imgdiv0" class="imgDiv"><img id="imgShow0" width="50" height="50"/></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w60" value="">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w222" value="">
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="javascript:;" class="cBlue removeTr">删除</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="ipt w40 tac" value="2">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w184" value="">
                            </td>
                            <td>
                                <input type="file" id="up_img1" class="up_img"/>
                                <div id="imgdiv1" class="imgDiv"><img id="imgShow1" width="50" height="50"/></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w60" value="">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w222" value="">
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="javascript:;" class="cBlue removeTr">删除</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="ipt w40 tac" value="3">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w184" value="">
                            </td>
                            <td>
                                <input type="file" id="up_img2" class="up_img"/>
                                <div id="imgdiv2" class="imgDiv"><img id="imgShow2" width="50" height="50"/></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w60" value="">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w222" value="">
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="javascript:;" class="cBlue removeTr">删除</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="ipt w40 tac" value="4">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w184" value="">
                            </td>
                            <td>
                                <input type="file" id="up_img3" class="up_img"/>
                                <div id="imgdiv3" class="imgDiv"><img id="imgShow3" width="50" height="50"/></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w60" value="">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w222" value="">
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="javascript:;" class="cBlue removeTr">删除</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" class="ipt w40 tac" value="5">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w184" value="">
                            </td>
                            <td>
                                <input type="file" id="up_img4" class="up_img"/>
                                <div id="imgdiv4" class="imgDiv"><img id="imgShow4" width="50" height="50"/></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w60" value="">
                            </td>
                            <td>
                                <input type="text" class="ipt tac w222" value="">
                            </td>
                            <td>

                            </td>
                            <td>
                                <a href="javascript:;" class="cBlue removeTr">删除</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!-- <a href="javascript:;" class="btn-white mt20" id="add-row">添加一行</a> -->
                    <p class="mt20">备注：<span
                            class="cRed">标题长度建议在10-15个字之间，前台不展示。<br>&nbsp;&nbsp;&nbsp;图片尺寸1000*320px</span></p>
                    <div class="tac">
                        <a href="javascript:;" class="btn-blue mt20">保存并上线</a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<script>
    $(function () {
        //首页资讯管理内部tab切换
        $('.mod0-tab-hd li').click(function () {
            $(this).addClass('current').siblings().removeClass('current');
            var _this = $(this).index();
            $('.mod0-tab-bd li').eq(_this).addClass('current').siblings().removeClass('current');
        })
    });
</script>
<script src="/caipiaoimg/v1.1/js/uploadPreview.min.js" type="text/javascript"></script>
<script>
    // 上传图片
    $('#pic-table,#pic-table-eidt').on('click', '.up_img', function () {
        var thisP = $(this).parents('td');
        new uploadPreview({
            UpBtn: $(this).attr('id'),
            DivShow: thisP.find('.imgDiv').attr('id'),
            ImgShow: thisP.find('img').attr('id')
        });
    })

</script>