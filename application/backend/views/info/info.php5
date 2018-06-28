<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">首页管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li><a href="/backend/Info/center">资讯管理</a></li>
            <li><a href="/backend/Info/image">轮播图管理</a></li>
            <li><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li class="current"><a href="/backend/Info/infoList">首页资讯管理</a></li>
        </ul>
    </div>
    
    <div class="mod-tab-bd">
        <ul>
            <!-- 首页资讯管理 开始 -->
            <li style="display: block">
                <div class="data-table-list mt10"><!-- 分类表格开始 -->
                    <table>
                        <colgroup>
                            <col width="5%" />
                            <col width="15%" />
                            <col width="30%" />
                            <col width="5%" />
                            <col width="15%" />
                            <col width="30%" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>数字彩</th>
                            <th>分类名（仅可输入<span class="cRed">2</span>个字）</th>
                            <th>URL链接</th>
                            <th>竞技彩</th>
                            <th>分类名</th>
                            <th>URL链接</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>1</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                            <td>1</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                            <td>2</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                            <td>3</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                            <td>4</td>
                            <td><input type="text" class="ipt tac w84" value=""></td>
                            <td><input type="text" class="ipt tac w264" value=""></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="tac">
                        <a href="javascript:;" class="btn-blue mt20 mb20">保存并上线</a>
                    </div>
                </div><!-- 分类表格结束 -->
                <div class="mt20"><!-- 标题表格开始 -->
                    <div class="mod0-tab-hd">
                        <ul class="clearfix">
                            <li class="current"><a href="javascript:void(0);">数字彩</a></li>
                            <li><a href="javascript:void(0);" class="nobdr">竞技彩</a></li>
                        </ul>
                    </div>
                    <div class="mod0-tab-bd">
                        <ul>
                            <li class="current"><!-- 数字彩开始 -->
                                <div class="data-table-list mt10">
                                    <table>
                                        <colgroup>
                                            <col width="10%" />
                                            <col width="30%" />
                                            <col width="50%" />
                                            <col width="10%" />
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>位置</th>
                                            <th>标题（长度建议在<span class="cRed">9-10</span>个字之间）</th>
                                            <th>URL链接</th>
                                            <th>标红</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>热门1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>热门2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>分类一1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类一2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类一3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类二2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类二3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="tac mt20 mb20">
                                        <a href="javascript:;" class="btn-blue ">保存并预览</a>
                                        <a href="javascript:;" class="btn-blue ml40">上线</a>
                                    </div>
                                </div><!-- 数字彩结束 -->
                            </li>
                            <li><!-- 竞技彩开始 -->
                                <div class="data-table-list mt10">
                                    <table>
                                        <colgroup>
                                            <col width="10%" />
                                            <col width="30%" />
                                            <col width="50%" />
                                            <col width="10%" />
                                        </colgroup>
                                        <thead>
                                        <tr>
                                            <th>位置</th>
                                            <th>标题（长度建议在<span class="cRed">9-10</span>个字之间）</th>
                                            <th>URL链接</th>
                                            <th>标红</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>热门1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>热门2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>分类一1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类一2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类一3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类二2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类二3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类三3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四1</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四2</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        <tr>
                                            <td>分类四3</td>
                                            <td><input type="text" class="ipt tac w222" value=""></td>
                                            <td><input type="text" class="ipt tac w264" value=""></td>
                                            <td><input type="checkbox" class="vam"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="tac mt20 mb20">
                                        <a href="javascript:;" class="btn-blue ">保存并预览</a>
                                        <a href="javascript:;" class="btn-blue ml40">上线</a>
                                    </div>
                                </div><!-- 竞技彩结束 -->
                            </li>
                        </ul>
                    </div>
                </div><!-- 标题表格结束 -->
                <div class="data-table-list mt40 mb40"><!-- 图片编辑开始 -->
                    <table>
                        <colgroup>
                            <col width="5%" />
                            <col width="30%" />
                            <col width="30%" />
                            <col width="30%" />
                        </colgroup>
                        <thead>
                        <tr>
                            <th>位置</th>
                            <th>标题（建议在<span class="cRed">10-15</span>个字之间）</th>
                            <th>图片（尺寸<span class="cRed">230*158px</span>）</th>
                            <th>URL链接</th>
                        </tr>
                        </thead>
                        <tbody id="pic-table-eidt">
                        <tr>
                            <td>1</td>
                            <td><input type="text" class="ipt tac w264"></td>
                            <td>
                                <input type="file" id="up_img_edit1" class="up_img" /><div id="imgdivEdit1" class="imgDiv"><img id="imgShowEdit1" width="50" height="50" /></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w264" value="">
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><input type="text" class="ipt tac w264"></td>
                            <td>
                                <input type="file" id="up_img_edit2" class="up_img" /><div id="imgdivEdit2" class="imgDiv"><img id="imgShowEdit2" width="50" height="50" /></div>
                            </td>
                            <td>
                                <input type="text" class="ipt tac w264" value="">
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="tac">
                        <a href="javascript:;" class="btn-blue mt20">保存并上线</a>
                    </div>
                </div><!-- 图片编辑结束 -->
            </li>
            <!-- 首页资讯管理结束 -->
        </ul>
    </div>
</div>

<script>
    $(function() {
        //首页资讯管理内部tab切换
        $('.mod0-tab-hd li').click(function(){
            $(this).addClass('current').siblings().removeClass('current');
            var _this=$(this).index();
            $('.mod0-tab-bd li').eq(_this).addClass('current').siblings().removeClass('current');
        })
    });
</script>
<script src="/caipiaoimg/v1.1/js/uploadPreview.min.js" type="text/javascript"></script>
<script>
    // 上传图片
    $('#pic-table,#pic-table-eidt').on('click', '.up_img', function(){
        var thisP = $(this).parents('td');
        new uploadPreview({
            UpBtn: $(this).attr('id'),
            DivShow: thisP.find('.imgDiv').attr('id'),
            ImgShow: thisP.find('img').attr('id')
        });
    })

</script>