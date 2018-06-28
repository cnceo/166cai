<div class="pub-pop edit-self-introduction J-wordsCd pop-id">
    <div class="pop-in" id="edit_self">
        <div class="pop-head">
            <h2>编辑个人介绍</h2>
            <span class="pop-close" title="关闭">&times;</span>
        </div>
        <div class="pop-body">
            <div class="textarea-box">
                <textarea class="J-wordsCd-in" rows="7" data-rule='{"num": [10, 80]}' placeholder="请输入10~80个字符"><?php if($user['introduction']){ echo $user['introduction']; }else{ echo "想中大奖的，抓紧跟单啦！"; } ?></textarea>
            </div>
            <p class="pTips">还可输入<em class="J-wordsCd-num">80</em>个字符</p>
            <p class="pTips hidden" id="tishi"></p>
        </div>
        <div class="pop-foot">
            <div class="btn-group">
                <a class="btn-pop-confirm J-wordsCd-btn" href="javascript:;">提交</a>
                <a class="btn-pop-cancel cancel" href="javascript:;">取消</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        new cx.vform('#edit_self', {
            renderTip: 'renderTips',
            submit: function (data) {
                var self = this;
                var data = data || {};

            }
        });
    });

    // 还剩多少字(粗略计算，字母、中文、符号都算1个字)
    !function () {
        var iptWords;
        var oldValue = $('.J-wordsCd-in').val();
        $('.J-wordsCd-in').val('').focus().val(oldValue);
        calcWords($('.J-wordsCd-in'))
        $(document).on('input propertychange', '.J-wordsCd .J-wordsCd-in', function () {
            calcWords($(this))
        })
        $(document).on('click', '.J-wordsCd .J-wordsCd-btn', function () {
            if (!$(this).hasClass('btn-disabled')) {
                $(this).addClass('btn-disabled');
                $.ajax({
                    type: "post",
                    url: "/user/updateIntroduction",
                    data: {
                        "txt": $('.J-wordsCd-in').val().trim()
                    },
                    dataType: "json",
                    success: function (res) {
                        if (res.status) {
                            $('.edit-self-introduction').remove();
                            cx.PopCom.hide();
                            cx.Alert({content: '<i class="icon-font">&#xe600;</i>您好，编辑个人介绍成功。',
                                confirmCb: function () {
                                    location.href = location.href;
                                }});
                        }else{
                            $("#tishi").removeClass("hidden");
                            $("#tishi").html("超出长度");
                        }
                    }
                });
            }
        })
        function calcWords (context) {
            var num = context.data('rule').num;
            iptWords = context.val().trim();
            var iptNum = iptWords.length;
            if (iptNum < 10) {
                $('.J-wordsCd-btn').addClass('btn-disabled')
            } else {
                $('.J-wordsCd-btn').removeClass('btn-disabled')
            }
            if (iptNum > 80) {
                context.val(iptWords.slice(0, 80))
            }
            iptNum = context.val().trim().length
            context.closest('.J-wordsCd').find('.J-wordsCd-num').text(num[1] - iptNum);
        }
    }()
</script>