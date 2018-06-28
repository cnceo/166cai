$(function(){
    var upload_no = '';
    var _isAdd = $('#zjtz').is(':checked') ? 1 : 0;//是否追加投注
    var _rgjeVal = 0; //认购金额
    var normalPlay = new Array(); //记录非单式投注信息
    var singlePlay = new Array(); //记录单式的相关投注记录
    var emptyPlay = InitData(); //记录空
    createUpload (0);
    if($('._gc_buy').hasClass('btn-disabled'))
    {
        $('._submit').addClass('btn-disabled').html($('._gc_buy').html());
    }else{
       $('._submit').html('确认预约').removeClass('btn-disabled'); 
    }
    $("._guarantee").on("mouseenter", ".bubble-tip", function() {
        $.bubble({
            target: this,
            position: "b",
            align: "l",
            content: $(this).attr("tiptext"),
            width: "auto"
        })
    }).on("mouseleave", ".bubble-tip", function() {
        $(".bubble").hide()
    });
    function createUpload (i) {
        var wUpL = WebUploader.create({
            swf: _STATIC_FILE+'/js/Uploader.swf',
            server: '/ajaxSingle/uploadFile',
            pick: '#picker' + i,
            accept: {
                title: 'txt文本格式',
                extensions: 'txt',
                mimeTypes: 'text/plain'
            },
            formData: {
             lid:LOTTERY_ID,
             endTime:ENDTIME,
             playType:playType,
            },
            fileSizeLimit: 256*1024
        });
        wUpL.on('fileQueued', function( file ) {
            var _fileName = file.name;
            _fileName = _fileName.length > 14 ?_fileName.substring(0,10)+'....txt':_fileName;
            $('#thelist' + i).append( '<div id="' + file.id + '" class="item"><h4 class="info">' + _fileName + '</h4><a href="javascript:;" class="remove-file">删除</a></div>').find('.uploader-list-tips').hide();
            $('#ctlBtn' + i).addClass('btn-specail');
            var $current = $( '#'+file.id );
            $current.prev().find('.remove-file').trigger('click');
            $( '#'+file.id ).on('click', '.remove-file', function() {
                wUpL.removeFile(file, true);
            })
        })
        wUpL.on('fileDequeued', function( file ) {
            var $this = $('#' + file.id);
            var $parent = $this.closest('.uploader-list');
            var $tips = $parent.find('.uploader-list-tips');
            $this.remove();
            if (!$parent.find('.item').length > 0) $('#ctlBtn' + i).removeClass('btn-specail')
            $tips.siblings().length || $tips.show(); 
                                
        });
        wUpL.on('uploadSuccess', function( file, response ) {
            var $this = $('#' + file.id);
            var $parent = $this.closest('.uploader-list');
            var $tips = $parent.find('');
            if(response.code=='undefined')
            {
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> "+'上传文件超过最大256KB'});
                wUpL.removeFile(file);
                //清空
                clearEmpty();
                return ;
            }
            if(response.code)
            {
                new cx.Alert({content: "<i class='icon-font green-color'>&#xe646;</i> 恭喜你上传成功，共 <span class='num-red'>"+response.data.betTnum+"</span> 注"});
                wUpL.removeFile(file);
                //处理 
                uploadSucc(response.data);
                return ;
            }else{
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> "+response.msg});
                wUpL.removeFile(file);
                //清空
                clearEmpty();
                return ;  
            }
        });
        wUpL.on('uploadError', function( file, reason ) {
            $('.uploader-list-tips').html(reason)
        });
        wUpL.on('uploadComplete', function( file, reason ) {
            $('.up-prog .item').html('<span class="uploader-list-tips">未选择任何文件</span>');;
        });
        
        wUpL.on("error",function (type){ 
              if(type == "Q_EXCEED_SIZE_LIMIT")
              {
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> "+'上传文件超过最大256KB'});
                //清空
                clearEmpty();
                return ;
              }
            if (type=="Q_TYPE_DENIED"){
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> 请先选择文件（以.txt 结尾的文本文件）"});
                //清空
                clearEmpty();
             }
         });
        $('#ctlBtn' + i).on('click', function () {
            var $tips = $(this).closest('.up-prog').find('.uploader-list-tips');
            wUpL.upload()
            if (!wUpL.getStats().queueNum) {
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> 请先选择文件（以.txt 结尾的文本文件）"});
                //清空
                clearEmpty();
                return ;
            }
        })
        return wUpL;
    }
    /**
     * [clearEmpty description]
     * @author LiKangJian 2017-08-14
     * @return {[type]} [description]
     */
    function clearEmpty()
    {
       singlePlay = emptyPlay;
       initPlay(1);
    }
    /**
     * [uploadSucc 上传成功需要的处理]
     * @author LiKangJian 2017-08-12
     * @param  {[type]} data [description]
     * @return {[type]}      [description]
     */
    function uploadSucc(data)
    {
        var _Smulti = parseInt($('._multi').val()) ;
        upload_no = data.upload_no;
        $('.betNum').html(data.betTnum);
        var _SMM = $('#zjtz').is(':checked') ? data.betTnum * _Smulti *3 : data.betTnum * _Smulti *2;
        $('.num-money').html(_SMM );
        $('.follow-money').html(_SMM);
        $('.betMoney').html(_SMM);
        $('.Multi').val(_Smulti);
        //更新追号信息
        // var _UObj = $('.chase-number-table-bd table tbody tr');
        // var _UIssue = 0;
        // var _UMoney = 0;
        // var _UbetMoney = parseInt($('._betMoney').html());
        // _UObj.each(function(){
        //     if(!$(this).find('input[type=checkbox]').is(':checked'))
        //     {
        //         $(this).find('.follow-multi').val('');
        //         $(this).find('.follow-money').html('0');
        //     }else{
        //         var _Umulti = parseInt($('._multi').val());
        //         _UIssue++;
        //         _UMoney = _UMoney + _UbetMoney;
        //         $(this).find('.follow-multi').val(_Umulti);
        //         $(this).find('.follow-money').html(_UbetMoney); 
        //     }
        // });
        // $('._totalQc').html(_UIssue);
        // $('._totalMoney').html(_UMoney);
        //合买信息
        heMaiProcess(1);
    }
   
    //对福彩3D 排列3结构做处理
    $('._radio_selected ul li').on('click',function(){
        window.location.href = baseUrl + $(this).attr('data-url');
    });


    //计算追号总金额
    function _calculateTotalMoney()
    {
        //追号
        $('.fbig em').eq(1).html(parseInt($('.fbig em').eq(0).html() )* parseInt( $('.follow-money').eq(0).html() ));
        //合买
        $('.betNum').html($('._betNum').html());
        $('.Multi').html($('._multi').val());
        $('.betMoney').html($('._betMoney').html());
        var _tm = parseInt($('._betMoney').html());
        var _momeyS = parseInt( _tm/10 );
        _momeyS = _momeyS > 0 ? _momeyS : 1;
        $('._hmrg').val(_momeyS);
        var _blje = _tm-_momeyS<0?0:_tm-_momeyS;
        $('._blje').html(_tm-_momeyS<0?0:_tm-_momeyS);
    }

    //提交处理
    $('._submit').on('click',function(){

        if ($(this).hasClass('btn-disabled')) return ;
        if ($(this).hasClass('not-bind')) return ;
        //1.登录验证
        if ($('.not-login').length>0 || !$.cookie('name_ie')) 
        {
            cx.PopAjax.login();
            return ;
        }
        //验证
        var _gc_type_id = $('._select_buy_way').find('input[name=chaseNumberTab]:checked').attr('id');
        //构建参数
        if(_gc_type_id=='ordertype0')
        {
            var _ajax_data = getZgParams();
        }else if(_gc_type_id=='ordertype1'){
            var _ajax_data = getChaseParams();
        }else if(_gc_type_id=='ordertype2'){
            var _ajax_data =  getHeMaiParams();
        }
        //验证注数
        if (_ajax_data.betTnum == 0) {
            new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>至少选择<span class='num-red'>１</span>注号码才能投注，请先选择方案"});
            return ;
        }
        if (_ajax_data.orderType == 1 && _ajax_data.totalIssue <= 1) {
            cx.Alert({content: "<i class='icon-font'>&#xe611;</i>您好，追号玩法须至少选择<span class='num-red'> 2 </span>期"});
            return ;
        }
        if(_ajax_data.lid == '19' || _ajax_data.lid == '11')
        {
            // 最大金额前端限制
            if (_ajax_data.orderType == 1) {
                $.each(_ajax_data.chases.split(';'), function(i, e){
                    if (parseInt(e.split('|')[2], 10) > 200000) {
                        new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>20万</span>元，请修改订单后重新投注"});
                        return ;
                    }
                })
            }else {
                if ( _ajax_data.money >200000 ) {
                    new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>20万</span>元，请修改订单后重新投注"});
                    return ;
                }
            } 
        }else{
            // 最大金额前端限制
            if (_ajax_data.orderType == 1) {
                $.each(_ajax_data.chases.split(';'), function(i, e){
                    if (parseInt(e.split('|')[2], 10) > 20000) {
                        new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                        return ;
                    }
                })
            }else {
                if ( _ajax_data.money >20000 ) {
                    new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                    return ;
                }
            }  
        }
        if ($(".ipt_checkbox#agreenment").get(0) && !$(".ipt_checkbox#agreenment").attr("checked")) {
            if ($(".risk_pro").length > 0) {
                return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》<br>《限号投注风险须知》后才能继续"});
            } else {
                return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》后才能继续"});
            }
        }
        if($('#agreenment1').length > 0 && !$('#agreenment1').is(':checked') )
        {
            return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》<br>《限号投注风险须知》后才能继续"});
        }
        //
        cx.single(_ajax_data, {ctype:'create', lotteryId:_ajax_data.lid, orderType:_ajax_data.orderType, betMoney:_ajax_data.money, chaseLength:_ajax_data.totalIssue!==undefined ?_ajax_data.totalIssue:0 , buyMoney:_ajax_data.buyMoney!==undefined ?_ajax_data.buyMoney:0, guarantee:_ajax_data.guaranteeAmount!==undefined ? _ajax_data.guaranteeAmount : 0, issue:CUR_ISSUE});
    });
    /**
     * [getZgParams 自购参数]
     * @author LiKangJian 2017-08-01
     * @return {[type]} [description]
     */
    function getZgParams()
    {
      var params = {
            money:$('._betMoney').html(),
            multi:$('._multi').val(),
            issue:CUR_ISSUE,
            endTime:ENDTIME,
            ctype:'create',
            buyPlatform:0,
            'upload_no':upload_no,
            lid:LOTTERY_ID,
            playType:0,
            betTnum:$('._betNum').html(),
            isChase:$('#zjtz').is(':checked') ? 1 : 0,
            orderType:0,
            isToken:1,
            url:'order/create',
            isJson:0,
      };
      return params;
    }
    /**
     * [getChaseParams 追号参数]
     * @author LiKangJian 2017-08-01
     * @return {[type]} [description]
     */
    function getChaseParams()
    {
      var _chase = chase();
      var params = {
        money:_chase['money'],
        multi:_chase['multi'],
        setStatus:$('.chase-number-table-ft input[type=checkbox]').is(':checked')?1:0,
        followMoney:$('.follow-money').eq(0).html(),
        setMoney:$('.setMoney').parent().find('input[type=checkbox]').is(':checked') ? $('.setMoney').val() : 0,
        totalIssue:_chase['totalIssue'],
        chases:_chase['str'],
        endTime:_chase['endTime'],
        ctype:'create',
        buyPlatform:0,
        upload_no:upload_no,
        lid:LOTTERY_ID,
        playType:0,
        betTnum:$('._betNum').html(),
        isChase:$('#zjtz').is(':checked') ? 1 : 0,
        orderType:1,
        isToken:1,
        url:'order/create',
        isJson:0,       
      };
      return params;
    }
    /**
     * [chase 追号参数计算]
     * @author LiKangJian 2017-08-01
     * @return {[type]} [description]
     */
    function chase()
    {
    var chase_arr = new Array();
        chase_arr['str'] = new Array();
        chase_arr['totalIssue'] = 0;
        chase_arr['multi'] = 0;
        chase_arr['money'] = 0; 
        chase_arr['endTime'] = '';
        $('.chase-number-table-bd tbody tr').each(function(){
            if($(this).find('input').attr('checked') == 'checked'){
                //到JSON 包里面解析
                var _Stime = chases[$(this).attr('data-issue')].award_time;
                var _Etime = chases[$(this).attr('data-issue')].show_end_time;
                if(chase_arr['endTime']=='')
                {
                    chase_arr['endTime'] = _Etime ;
                }
                chase_arr['totalIssue'] ++;
                chase_arr['multi'] = chase_arr['multi'] + parseInt($(this).find('.follow-multi').val());
                chase_arr['money'] = chase_arr['money'] + parseInt($(this).find('.follow-money').html());
                chase_arr['str'].push($(this).attr('data-issue')+'|'+$(this).find('.follow-multi').val()+'|'+$(this).find('.follow-money').html()+"|"+_Stime+'|'+_Etime+';' );
            }
        });
        chase_arr['str'] = chase_arr['str'].join('');
        return chase_arr;
    }
    /**
     * [getHeMaiParams 合买参数]
     * @author LiKangJian 2017-08-01
     * @return {[type]} [description]
     */
    function getHeMaiParams()
    {
      var params = {
        money:$('._betMoney').html(),
        multi:$('._multi').val(),
        issue:CUR_ISSUE,
        endTime:formatDateTime(hmendTime*1000),
        buyMoney:$('._buyMoney input').val(),
        commissionRate:$('.commission .cur').attr('data-val'),
        guaranteeAmount:$('._guarantee input').val(),
        openStatus:$('input[name=bmsz]:checked').val(),
        openEndtime:realendTime,
        ctype:'create',
        buyPlatform:0,
        upload_no:upload_no,
        lid:LOTTERY_ID,
        playType:0,
        betTnum:$('._betNum').html(),
        isChase:$('#zjtz').is(':checked') ? 1 : 0,
        orderType:4,
        isToken:1,
        url:'order/create',
        isJson:0        
      };
      return params;
    }
    /**
     * [formatDateTime 时间格式化]
     * @author LiKangJian 2017-08-01
     * @param  {[type]} inputTime [description]
     * @return {[type]}           [description]
     */
    function formatDateTime(inputTime) 
    {    
        var date = new Date(inputTime);  
        var y = date.getFullYear();    
        var m = date.getMonth() + 1;    
        m = m < 10 ? ('0' + m) : m;    
        var d = date.getDate();    
        d = d < 10 ? ('0' + d) : d;    
        var h = date.getHours();  
        h = h < 10 ? ('0' + h) : h;  
        var minute = date.getMinutes();  
        var second = date.getSeconds();  
        minute = minute < 10 ? ('0' + minute) : minute;    
        second = second < 10 ? ('0' + second) : second;   
        return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;    
    }; 

    //对投注倍数鼠标抬起事件  
    $(document).on('keyup', '._multi', function(){
        //只是针对单式上传功能
        //multiProcess($(this));
    });
    //对投注倍数市区焦点 ===== 此处问题暂时没有解决
    $(document).on('blur', '._multi', function(){
        //只是针对单式上传功能
        multiProcess($(this));
    });
    //改变期次 
    $(document).on('keyup', '._follow-issue', function(){
        followIssueProcess();
    });
    $(document).on('blur', '._follow-issue', function(){
        followIssueProcess();
    });
    //追号总倍数
    $(document).on('keyup', '._follow-multi', function(){
            var _J_multi = $(this).val()=='' ? 1 : parseInt($(this).val()); //倍数
            $('.follow-multi').val(_J_multi);
            //遍历排查错值
            $('.chase-number-table-bd tbody tr').each(function(){
                if(!$(this).find('input[type=checkbox]').is(':checked'))
                {
                    $(this).find('.follow-multi').val('');
                    //有一坑 没有成功
                    $(this).find('.follow-money').html('0');
                    
                }
            });
            $(this).val(_J_multi);
            $('.follow-money').html(_JCalculationMoney(_J_multi ));
            //总期次 总金额
            _JCalculationChase(); 
    });
    //追号小倍数
    $(document).on('keyup', '.follow-multi', function(){

            var _J_multi = $(this).val()=='' ? '' : parseInt($(this).val()); //倍数
            $(this).val(_J_multi);
            $(this).parent().parent().find('.follow-money').html(_J_multi=='' ? 0: _JCalculationMoney(_J_multi))
            //总期次 总金额
            _JCalculationChase();
       
    });
    //追号check
    $(document).on('click', '.chase-number-table-bd tbody tr td input[type=checkbox]', function(){
        var _JFVal = parseInt ($('._follow-issue').val());
        var _JJOBJ = $(this);
        setTimeout(function(){
            
            if(_JJOBJ.is(':checked'))
            {
                var _vv = $('._follow-multi').val()=='' ? $('._multi').val() : $('._follow-multi').val()
                _JJOBJ.parent().parent().find('.follow-multi').val(_vv);
                console.log(_vv,_JCalculationMoney(_vv));
                _JJOBJ.parent().parent().find('.follow-money').html(_JCalculationMoney(_vv));
            }else{
                _JJOBJ.parent().parent().find('.follow-multi').val('');
               _JJOBJ.parent().parent().find('.follow-money').html(0);
            }
            //计算
            _JCalculationChase();         
        },10);

    });
    //加法操作
    $(document).on('click', '._plus', function(){
        var _plusVal = parseInt($('._multi').val());
        var _maxVal = parseInt($(this).attr('data-max'));
        var _pMul = _plusVal +1 >=_maxVal ? _maxVal : _plusVal +1;
        $('._multi').val(_pMul);
        $('.follow-multi').val(_pMul);
        $('.Multi').html(_pMul);
        $('.betMoney').html(_JCalculationMoney($('._multi').val()));
        $('._betMoney').html(_JCalculationMoney($('._multi').val()));
        //更新追号
       // updateCharse();
        //合买加
        heMaiProcess(1);
    });
    //减操作
    $(document).on('click', '._minus', function(){
        var _minusVal = parseInt($('._multi').val());
        var _minusMul = _minusVal>=2 ? _minusVal-1 : 1;
        $('._multi').val(_minusMul);
        $('.follow-multi').val(_minusMul);
        $('.Multi').html(_minusMul);
        $('.betMoney').html(_JCalculationMoney($('._multi').val()));
        $('._betMoney').html(_JCalculationMoney($('._multi').val()));
        //更新追号
        //updateCharse();
        //合买加
        heMaiProcess(0);
    });
    //盈利佣金
    $(document).on('click', '._JHm li', function(){
            var _JBetMoneyV = parseInt($('._betMoney').html());
            var _Curr_Data_Val = parseInt($(this).attr('data-val')) <= 5 ? 5 :parseInt($(this).attr('data-val'));
            $('._zsrg').html(_Curr_Data_Val) ;
            if(_JBetMoneyV > 0)
            {
                var _JrgjeV = Math.ceil(_JBetMoneyV *_Curr_Data_Val /100);
                _JrgjeV = _JrgjeV < parseInt($('._rgje').val()) ? parseInt($('._rgje').val()) : _JrgjeV;
                $('._rgje').val(_JrgjeV);
                var _Jyjrg = Math.round(_JrgjeV* 100/_JBetMoneyV);
                $('._yjrg').html(_Jyjrg);
                $('._buyMoney ._yjrg').html(_Jyjrg);
                $('._zdblje').html(_JBetMoneyV  -_JrgjeV );
                if($('._guaranteeAll').is(':checked'))
                {
                    $('._blbfb').html(100-_Jyjrg);
                    $('._bdje').val(_JBetMoneyV  -_JrgjeV );
                }else{
                    var _jjBDJE = parseInt($('._bdje').val());
                        _jjBDJE = _jjBDJE > (_JBetMoneyV  -_JrgjeV) ? (_JBetMoneyV  -_JrgjeV ):_jjBDJE;
                    var _JJ = _JBetMoneyV == 0 ? 0 : Math.round(parseInt(_jjBDJE) *100/_JBetMoneyV) ;
                    $('._bdje').val(_jjBDJE);
                    $('._blbfb').html(_JJ);
                }
                $('._buyMoney .small u').show();
            }else{
                $('._buyMoney u').hide();
            }

            $('.buy_txt em').eq(0).html(parseInt($('._rgje').val()) + parseInt($('._bdje').val()));
            $('.buy_txt span').eq(0).html('（认购'+$('._rgje').val()+'元+保底'+$('._bdje').val()+'元）'); 
    });
    //全额保底
    $(document).on('click', '._guaranteeAll', function(){
        if($(this).is(':checked')){
            $('._bdje').val($('._zdblje').html());
            $('._blbfb').html($('._betMoney').html()==0? 0:100-parseInt($('._yjrg').html()));
            $('.buy_txt em').eq(0).html(parseInt($('._rgje').val()) + parseInt($('._bdje').val()));
            $('.buy_txt span').eq(0).html('（认购'+$('._rgje').val()+'元+保底'+$('._bdje').val()+'元）'); 
        }
    });
    //认购金额 事件绑定 _rgje
    $(document).on('keyup', '._rgje', function(){
        //hmRGProcess($(this));
    });
    $(document).on('blur', '._rgje', function(){
        hmRGProcess($(this));
    });
    //_bdje 保底
    $(document).on('keyup', '._bdje', function(){
        //hmBDProcess($(this));
    });
    $(document).on('blur', '._bdje', function(){
        hmBDProcess($(this));
    });
    //追加投注
    $(document).on('click', '#zjtz', function(){
        addProcess();
    });
    //_select_buy_way 合买
    $(document).on('click', '._select_buy_way li', function(){
        if($('._gc_buy').hasClass('btn-disabled'))
        {
            $('._submit').addClass('btn-disabled').html($('._gc_buy').html());
        }else{
           $('._submit').html('确认预约').removeClass('btn-disabled'); 
        }
    });
    //追加全选问题
    $(document).on('click', '.chase-number-table-hd tbody input[type=checkbox]', function(){
        if($(this).is(':checked')){
            $('.follow-multi').val($('._multi').val());
            setTimeout(function(){
             $('.follow-money').html($('._betMoney').html());
             $('._totalQc').html($('._follow-issue').val());
             $('._totalMoney').html(parseInt($('._follow-issue').val()) * parseInt($('._betMoney').html()));               
            },10);

        }
    });
    
    /**
     * [followIssueProcess description]
     * @author LiKangJian 2017-08-10
     * @return {[type]} [description]
     */
    function followIssueProcess()
    {
        var _Jfollow_issue = $('._follow-issue').val()=='' || $('._follow-issue').val()=='1' ? 2 :$('._follow-issue').val();
        // 同一个数字保持不变 否则更新
        // var flag = 0;
        // $('.chase-number-table-bd tbody tr').each(function(){

        // });
        setTimeout(function(){
        $('.follow-multi').val($('._follow-multi').val());
        var _JJjE = parseInt($('._follow-multi').val()) * parseInt($('._betNum').html()) * (_isAdd?3:2);
        $('.follow-money').html(_JJjE);
        $('._totalQc').html(_Jfollow_issue);
        $('._totalMoney').html(parseInt(_Jfollow_issue)* _JJjE);         
        },10);

    }
    /**
     * [_JCalculationChase 追号数据相关计算的统一方法]
     * @author LiKangJian 2017-08-04
     * @return {[type]} [description]
     */
    function _JCalculationChase()
    {
        var _JObj = $('.chase-number-table-bd tbody tr');
        var _JToatalIssue = 0;//累计期次
        var _JToatalMulti = 0;//累计倍数
        _JObj.each(function(){
            if($(this).find('input[type=checkbox]').is(':checked'))
            {
                _JToatalIssue ++;
                _JToatalMulti += parseInt( $(this).find('.follow-multi').val() );
            }
        });
        //更新值
        $('._totalQc').html(_JToatalIssue);
        $('._totalMoney').html(_JCalculationMoney(_JToatalMulti));
    }
    /**
     * [_JCalculationMoney 计算金额]
     * @author LiKangJian 2017-08-04
     * @param  {[type]} multi [description]
     * @return {[type]}       [description]
     */
    function _JCalculationMoney(multi)
    {
        return  parseInt(multi) * parseInt( $('._betNum').html() )  * (_isAdd ==0 ? 2 :3);
    }
    /**
     * [updateCharse 更新追号]
     * @author LiKangJian 2017-08-04
     * @return {[type]} [description]
     */
    function updateCharse()
    {
        $('.betMoney').html(_JCalculationMoney($('._multi').val()));
        $('._betMoney').html(_JCalculationMoney($('._multi').val()));
        var _JObj = $('.chase-number-table-bd tbody tr');
        var _JToatalIssue = 0;//累计期次
        var _JToatalMulti = 0;//累计倍数
        _JObj.each(function(){
            if($(this).find('input[type=checkbox]').is(':checked'))
            {
                $(this).find('.follow-multi').val($('._multi').val());
                $(this).find('.follow-money').html(_JCalculationMoney($('._multi').val()));
                _JToatalIssue ++;
                _JToatalMulti += parseInt( $(this).find('.follow-multi').val() );
            }else{
                $(this).find('.follow-multi').val('');
                $(this).find('.follow-money').html('0');
            }
        });
        //更新值
        $('._follow-issue').val(_JToatalIssue);
        $('._totalQc').html(_JToatalIssue);
        $('._totalMoney').html(_JCalculationMoney(_JToatalMulti));
    }
    /**
     * [multiProcess 对倍数的操作]
     * @author LiKangJian 2017-08-04
     * @return {[type]} [description]
     */
    function multiProcess(obj)
    {

        var reg = new RegExp("^[0-9]*[1-9][0-9]*$");
        var _J_multi = (!reg.test(obj.val() )|| obj.val()==0) ? 1 : parseInt(obj.val()); //倍数
           _J_multi = _J_multi > $('._plus').attr('data-max') ? parseInt($('._plus').attr('data-max')) : _J_multi;
        var _J_betNum = parseInt($('._betNum').html()); //注数
        var _J_Money = _isAdd ==0 ? 2 : 3;//追加每一注 3元
        var _J_TMoney = _J_multi * _J_betNum * _J_Money;
        var _J_Total_Issue = parseInt($('._follow-issue').val());
        obj.val(_J_multi);
        //延迟处理
        setTimeout(function () {
            $('.betMoney').html(_J_TMoney );
            $('.follow-money').html(_J_TMoney );
            $('.Multi').html(_J_multi);
            $('.follow-multi').val(_J_multi);
            $('._totalMoney').html(_J_Total_Issue * _J_TMoney);
            heMaiProcess(1);
        }, 5)

    }

    /**
     * [heMaiProcess 计算合买]
     * @author LiKangJian 2017-08-04
     * @param  {[type]} flag [1加法0减法]
     * @return {[type]}      [description]
     */
    function heMaiProcess(flag)
    {
        var _betMoney = parseInt($('._betMoney').html());
        var _pVal = parseInt( $('._JHm').find('.cur').attr('data-val') );//盈利佣金百分比默认5%
            _pVal = _pVal <= 5 ? 5 :_pVal;
        var _checked = $('._guaranteeAll').is(':checked') ? 1 : 0;
        var _cur_rgjr = parseInt($('input._rgje').val());
        var _rgje = 0;
        var _srgje = Math.ceil(_betMoney*_pVal/ 100);
        //减法操作
        // if(flag == 0 && _betMoney >  _cur_rgjr)
        // {
        //     var _rgje = _cur_rgjr;
        // }else if(flag == 0 && _cur_rgjr>=_betMoney ) {
        //     var _rgje = _betMoney;
        // }else{
        //     var _rgje = Math.round( _betMoney * _pVal / 100 );
        // }
        if(_cur_rgjr>=_betMoney && flag == 0 )
        {
            _rgje = _betMoney;
        }else{
            _rgje = _cur_rgjr > _srgje ? _cur_rgjr :_srgje;
        }
        //进一步判断
        _rgje = _rgje > _betMoney ? _betMoney : _rgje;
        //认购金额进一步处理
        //var LL = Math.ceil(_betMoney*_pVal/ 100);
        //_rgje = LL > _rgje ? LL : _rgje;

        $('._rgje').val(_rgje);
        var _yjrg = _betMoney == 0 ? 0 : Math.round(_rgje *100/_betMoney);
        $('._rgje').val(_rgje);//认购金额
        $('._yjrg').html(_yjrg);//认购百分比
        var _blje = _betMoney - _rgje; //最多保底
            _blje = _blje < 0 ? 0 : _blje;
        $('._zdblje').html(_blje);
        if($('._bdje').val()==''){$('._bdje').val(0);}
        if(_checked || parseInt($('._bdje').val()) >_blje){ $('._bdje').val(_blje)}
        if(_checked){
           var _blbfb = 100 - _yjrg; //保底百分比 
        }else{
          var _blbfb = _betMoney == 0 ? 0 :Math.round(parseInt($('._bdje').val()) *100/ _betMoney) ;
        }
        $('._blbfb').html(_blbfb);
        if(_rgje!=0)
        {
            $('._buyMoney .small u').show();
        }else{
            $('._buyMoney .small u').hide();
        }
        $('.buy_txt em').eq(0).html(_rgje + parseInt($('._bdje').val()));
        $('.buy_txt span').eq(0).html('（认购'+_rgje+'元+保底'+$('._bdje').val()+'元）');     
    }
    /**
     * [hmRGProcess 合买认购]
     * @author LiKangJian 2017-08-04
     * @return {[type]} [description]
     */
    function hmRGProcess(obj)
    {
        
        var reg = new RegExp("^[0-9]*[1-9][0-9]*$");
        var _JJV = obj.val();
        if(!reg.test(_JJV )){obj.val(0);}
        $('._buyMoney .small u').show();
        if(parseInt(_JJV ) > parseInt($('._betMoney').html()))
        { 
            obj.val($('._betMoney').html());
            $('._yjrg').html(100);
            $('._bdje').val(0);
            
        }
        if(_JJV == 0)
        {
            $('._yjrg').html(0);
            $('._buyMoney .small u').hide();
        }
        var _betMoney = parseInt($('._betMoney').html());
        var _checked = $('._guaranteeAll').is(':checked') ? 1 : 0;
        var _rgje = parseInt($('._rgje').val());//认购金额
        //认购金额进一步处理
        var _pVal = $('._JHm li.cur').attr('data-val');
            _pVal = _pVal < 5 ? 5 :parseInt(_pVal);
        var LL = Math.ceil(_betMoney*_pVal/ 100);
        _rgje = LL > _rgje ? LL : _rgje;
        $('._rgje').val(_rgje);
        var _yjrg = _betMoney == 0 ? 0 : Math.round(_rgje *100 /_betMoney); 
        $('._yjrg').html(_yjrg);//认购百分比
        var _blje = _betMoney - _rgje; //最多保底
        $('._zdblje').html(_blje);
        var _blbfb = 100 - _yjrg; //保底百分比
        //$('._blbfb').html(_blbfb);
        if(_checked || parseInt($('._bdje').val()) > _blje ){ 
            $('._bdje').val(_blje);
            $('._guaranteeAll').attr('checked','checked');
        }
        if(_checked){
           var _blbfb = 100 - _yjrg; //保底百分比 
        }else{
          //var _blbfb =  Math.ceil(_betMoney==0?0:parseInt($('._bdje').val()) *100 / _betMoney);
          var _blbfb = _betMoney == 0 ? 0 :Math.round(parseInt($('._bdje').val()) *100/ _betMoney ) ;
        }
        _blbfb = _betMoney == 0 ? 0 :_blbfb;
        $('._blbfb').html(_blbfb);
        $('.buy_txt em').eq(0).html(_rgje + parseInt($('._bdje').val()));
        $('.buy_txt span').eq(0).html('（认购'+_rgje+'元+保底'+$('._bdje').val()+'元）'); 
        if(_betMoney == 0){
            $('._buyMoney u').hide();
        }
    }

    /**
     * [hmRGProcess 合买保底金额]
     * @author LiKangJian 2017-08-04
     * @return {[type]} [description]
     */
    function hmBDProcess(obj)
    {
        
        var reg = new RegExp("^[0-9]*[1-9][0-9]*$");
        var _JJV = obj.val();
        var _betMoney = parseInt($('._betMoney').html());
        var _checked = $('._guaranteeAll').is(':checked') ? 1 : 0;
        var _rgje = parseInt($('._rgje').val());//认购金额
        if(!reg.test(_JJV )){obj.val(0);}
        if(parseInt(_JJV ) >= _betMoney - _rgje )
        { 
            obj.val(_betMoney - _rgje);
            $('._guaranteeAll').attr('checked','checked');
        }else{
            $('._guaranteeAll').removeAttr("checked");
        }
        _JJV = obj.val();
        //更新问题
        $('._blbfb').html(_betMoney==0 ?0 :Math.round(_JJV*100/_betMoney));
        $('.buy_txt em').eq(0).html(_rgje + parseInt($('._bdje').val()));
        $('.buy_txt span').eq(0).html('（认购'+_rgje+'元+保底'+$('._bdje').val()+'元）'); 
    }
    /**
     * [addProcess description]
     * @author LiKangJian 2017-08-09
     */
    function addProcess()
    {
        var _AbetNum = parseInt($('._betNum').html());
        var _Amulti = parseInt($('._multi').val());
        if($('#zjtz').is(':checked'))
        {
            _isAdd = 1;
            var _dzjg = 3;
        }else{
            _isAdd = 0;
            var _dzjg = 2;
        }
        $('.betMoney').html(_AbetNum * _Amulti * _dzjg);
        //更新追号
        $('.betMoney').html(_JCalculationMoney($('._multi').val()));
        $('._betMoney').html(_JCalculationMoney($('._multi').val()));
        heMaiProcess(1);
        // var _JObj = $('.chase-number-table-bd tbody tr');
        // var _JToatalIssue = 0;//累计期次
        // var _JToatalMulti = 0;//累计倍数
        // _JObj.each(function(){
        //     if($(this).find('input[type=checkbox]').is(':checked'))
        //     {
        //         //$(this).find('.follow-multi').val($('._multi').val());
        //         $(this).find('.follow-money').html(_JCalculationMoney($(this).find('.follow-multi').val()));
        //         _JToatalIssue ++;
        //         _JToatalMulti += parseInt( $(this).find('.follow-multi').val() );
        //     }else{
        //         $(this).find('.follow-multi').val('');
        //         $(this).find('.follow-money').html('0');
        //     }
        // });
        // //更新值
        // $('._follow-issue').val(_JToatalIssue);
        // $('._totalQc').html(_JToatalIssue);
        // $('._totalMoney').html(_JCalculationMoney(_JToatalMulti));
    }
    /**
     * [initPlay 初始化数据]
     * @author LiKangJian 2017-08-09
     * @param  {[type]} type [1 单式 0 单式]
     * @return {[type]}      [description]
     */
    function initPlay(type)
    {
        var initArray = type === 0 ? normalPlay : singlePlay;
        if(!initArray.betNum) 
        {
            initArray = emptyPlay;
         }
        initDo(initArray);

    }
    /**
     * [initFc3d 福彩3D]
     * @author LiKangJian 2017-08-11
     * @param  {[type]} playType [description]
     * @return {[type]}          [description]
     */
    function initFc3d()
    {
        var initArray = singlePlay[playType]  ? singlePlay[playType] : emptyPlay;
        initDo(initArray);
    }
    function initDo(initArray)
    {
        $('.betNum').html(initArray['betNum']);//注数
         $('._betNum').html(initArray['betNum']);//注数
         $('._multi').val(initArray['multi']);//倍数
         $('.Multi').html(initArray['multi']);
         $('.betMoney').html(initArray['betMoney']);//金额
         //$('._follow-issue').val(initArray['issueLen']);//总期次
         //$('._follow-multi').val(initArray['total-multi']);//追号倍数
         //$('._totalQc').html(initArray['_totalQc'] );
         //$('._totalMoney').html(initArray['_totalMoney'] );
         //$('.follow-issue').trigger("keyup");
         //$('._follow-issue').val(initArray['total-issue']);//总期次
         $('._select_buy_way li').eq(initArray['buyWay']).find('input[type=radio]').trigger("click");
        
         //追号初始化
         // setTimeout(function(){
         //      $('.chase-number-table-bd table tbody tr').each(function(){
         //        if(initArray['chase'][$(this).attr('data-issue')] && initArray['chase'][$(this).attr('data-issue')]['check'] =='checked')
         //        {
         //            $(this).find('input[type=checked]').attr('checked',initArray['chase'][$(this).attr('data-issue')]['check']);
         //            $(this).find('.follow-multi').val(initArray['chase'][$(this).attr('data-issue')]['follow-multi']);
         //            $(this).find('.follow-money').html(initArray['chase'][$(this).attr('data-issue')]['follow-money']);
         //        }else{
         //            $(this).find('input[type=checked]').removeAttr('checked');
         //            $(this).find('.follow-multi').val(0);
         //            $(this).find('.follow-money').html(0); 
         //        }
               
         //     })           
         // },10)

         //初始化合买
         $('._JHm li').removeClass('cur');
         $('._JHm li[data-val='+initArray['hm']['commission']+']').addClass('cur');
         $('._buyMoney input').val(initArray['hm']['rgje']) 
         $('.buyMoney input').val(initArray['hm']['rgje']);
         $('._buyMoney em').eq(0).html(initArray['hm']['zsrg'])
         $('.buyMoney em').eq(0).html(initArray['hm']['zsrg']);
         $('._buyMoney u em').eq(0).html(initArray['hm']['yjrg']);
         $('.buyMoney u em').eq(0).html(initArray['hm']['yjrg']);
         $('._guarantee input').eq(0).val(initArray['hm']['blje']) ;
         $('.guarantee input').eq(0).val(initArray['hm']['blje']);
         $('._guarantee').find('input[type=checkbox]').attr('checked',initArray['hm']['guaranteeAll']);
         $('.guarantee').find('input[type=checkbox]').attr('checked',initArray['hm']['guaranteeAll']) ;
         $('._guarantee em').eq(0).html(initArray['hm']['zdblje']);
         $('.guarantee em').eq(0).html(initArray['hm']['zdblje']);
         $('._guarantee u em').eq(0).html(initArray['hm']['blbfb']);
         $('.guarantee u em').eq(0).html(initArray['hm']['blbfb']);
         $('.buy_txt em').html(initArray['hm']['buy_txt']);
         $('.buy_txt span').html(initArray['hm']['buy_txt_span']);
         $('input[name=bmsz]').removeAttr('checked');
         $('input[name=bmsz]').eq(initArray['hm']['bmsz']).attr('checked','checked');    
    }
    /**
     * [InitData 记录数据]
     * @author LiKangJian 2017-08-07
     */
    function InitData()
    {
        var arr = new Array();
        arr['betNum'] = $('._betNum').html();//注数
        arr['multi'] = $('._multi').val();//倍数
        arr['betMoney'] = $('._betMoney').html();//金额
        arr['total-issue'] = $('._follow-issue').val();//总期次
        arr['total-multi'] = $('._follow-multi').val();//追号倍数
        arr['_totalQc'] = $('._totalQc').html();
        arr['_totalMoney'] = $('._totalMoney').html();
        var Jstr = $('._select_buy_way li.cur').find('input[type=radio]').attr('id');
            Jstr = Jstr ? Jstr : 'ordertype0';
        arr['buyWay'] = Jstr.substring(9);
        arr['issueLen'] = 0;
        //var _chaseArr = new Array();
        var _hm = new Array();
        // var _obj = $('.chase-number-table-bd tbody tr');
        // _obj.each(function(){
        //     var _issue = $(this).attr('data-issue');
        //     _chaseArr[_issue] = new Array();
        //     arr['issueLen'] = arr['issueLen'] +1;
        //     _chaseArr[_issue]['follow-multi'] = $(this).find('.follow-multi').val();
        //     _chaseArr[_issue]['follow-money'] = $(this).find('.follow-money').html();
        //     _chaseArr[_issue]['check'] = $(this).find('input[type=checkbox]').is(':checked') ? 'checked' : '';
            
        // });
        _hm['commission'] = $('._JHm li.cur').attr('data-val');
        _hm['rgje'] =  $('._buyMoney input').val() ;
        _hm['zsrg'] = $('.buyMoney').is(':hidden') ? $('._buyMoney em').eq(0).html() : $('.buyMoney em').eq(0).html();
        _hm['yjrg'] = $('.buyMoney').is(':hidden') ? $('._buyMoney u em').eq(0).html() : $('.buyMoney u em').eq(0).html();
        _hm['blje'] =  $('._guarantee input').eq(0).val() ;
        _hm['guaranteeAll'] = $('.guarantee').is(':hidden') ? $('._guarantee').find('input[type=checkbox]').is(':checked') : $('.guarantee').find('input[type=checkbox]').is(':checked') ;
        _hm['zdblje'] = $('.guarantee').is(':hidden') ? $('._guarantee em').eq(0).html() :$('.guarantee em').eq(0).html();
        _hm['blbfb'] = $('.guarantee').is(':hidden') ? $('._guarantee u em').eq(0).html() :$('.guarantee u em').eq(0).html();
        _hm['buy_txt'] = $('.buy_txt em').html();
        _hm['buy_txt_span'] = $('.buy_txt span').html();
        _hm['bmsz'] = $('input[name=bmsz]').val();
        //arr['chase'] = _chaseArr;
        arr['hm'] = _hm;
        return arr;
    }

    cx.single = function(data, obj, aptype) {
        switch (obj.ctype) {
            case 'create':
                cx.singleAjax.post({
                    url: "order/create",
                    data: data,
                    success: function(response) {
                        if ($.inArray(obj.lotteryId, [42, 43]) > -1 && obj.orderType == 4) cx.PopCom.hide($('.pop-pay'));
                        obj.ctype = 'pay';
                        obj.fromcreate = 1;
                        data = response.data;
                        data.code = response.code;
                        data.msg = response.msg;
                        cx.single(data, obj, aptype);
                    }
                });
                break;
            case 'paysearch':
                var datas = { orderId: data.orderId };
                switch (obj.orderType) {
                    case 4:
                        var url = '/hemai/getOrderInfo';
                        datas.buyMoney = data.buyMoney;
                        break;
                    case 1:
                        var url = '/chases/info';
                        break;
                    default:
                        var url = '/orders/info';
                        break;
                }
                $.ajax({
                    type: 'post',
                    url: url,
                    data: datas,
                    success: function(response) {
                        obj.ctype = 'pay';
                        obj.lotteryId = parseInt(response.lid, 10);
                        obj.issue = response.issue;
                        obj.typeCnName = response.typeCnName;
                        response.type = 1;
                        switch (obj.orderType) {
                            case 4:
                                response.money = obj.buyMoney;
                                break;
                            case 1:
                                obj.chaseLength = response.totalIssue;
                                obj.betMoney = response.betMoney;
                                break;
                            default:
                                break;
                        }
                        response.orderId = data.orderId;
                        cx.single(response, obj, aptype);
                    }
                })
                break;
            case 'pay':
                var tip = '付款后，您的订单将会自动分配到空闲的投注站出票', txt = '付款到彩店';
                if ($.inArray(data.code, [0, 12]) > -1) {
                    switch (obj.orderType) {
                        case 1:
                            var datas = {ctype: 'pay', orderType:obj.orderType, chaseId: data.orderId, money: data.money};
                            var binfo = betInfo.chase(getCnName(obj.lotteryId), obj.betMoney, obj.chaseLength, data.money, data.remain_money);
                            break;
                        case 4:
                            var datas = {ctype: 'pay', orderType:obj.orderType, type:(data.type ? data.type : 0), orderId: data.orderId, money: data.money};
                            if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
                            }else {
                                var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
                            }
                            tip = '付款后，合买订单满员将会自动分配到空闲的投注站出票';
                            txt = obj.fromcreate !== undefined ? '发起合买' : '参与合买';
                            break;
                        case 0:
                        default:
                            if(data.redpack !== undefined){
                                if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                    var binfo = betInfo.redpackJc(obj.typeCnName, data.money, data.remain_money, data.redpack);
                                }else {
                                    var binfo = betInfo.redpackNumber(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money, data.redpack);
                                }
                                var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money, redpackId:data.redpackId};
                            }
                            else
                            {
                                var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money};
                                if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                    var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money);
                                }else {
                                    var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money);
                                }
                            }
                            var cmoney = parseInt(data.money.replace(/,|\./g, ''));
                            if(data.redpackId !== undefined){
                                for (i = 0; i < data.redpack.length; i++) {
                                    if(data.redpack[i].id == data.redpackId){
                                        cmoney = cmoney - parseInt(data.redpack[i].money.replace(/,|\./g, ''));
                                        break;
                                    }
                                }
                            }
                            if(parseInt(data.remain_money.replace(/,|\./g, '')) < cmoney){
                                tip = '';
                                txt = '去充值';
                            }
                            else {
                                data.code = 0;
                            }
                            break;
                    }
                    obj.binfo = binfo;
                }
                delete obj.ctype;
                if (data.code == 0) {
                    new cx.Confirm({
                        title: '确认投注信息',
                        content: binfo,
                        input: 0,
                        tip: tip,
                        btns: [{type: 'confirm', txt: txt, href: 'javascript:;'}],
                        confirmCb: function() {
                            if(data.redpackId !== undefined){
                                var redpackId = $("input[name='redpackId']").val();
                                datas.redpackId = redpackId;
                                if($('#mustRecharge').closest('.form-pop-tip').is(":visible")){
                                    self.location=baseUrl+'wallet/directPay?orderId='+data.orderId+'&orderType='+obj.orderType + '&betRedpack=' + redpackId;
                                }else{
                                    cx.singleAjax.post({
                                        url: 'order/pay', 
                                        data: datas, 
                                        success: function(response) {
                                            for (i in response) {
                                                data[i] = response[i];
                                            }
                                            data.code = response.code;
                                            data.msg = response.msg;
                                            cx.single(data, obj);
                                        }
                                    });
                                }
                            }else{
                                cx.singleAjax.post({
                                    url: 'order/pay', 
                                    data: datas, 
                                    success: function(response) {
                                        for (i in response) {
                                            data[i] = response[i];
                                        }
                                        data.code = response.code;
                                        data.msg = response.msg;
                                        cx.single(data, obj);
                                    }
                                });
                            }
                        }
                    });
                }else {
                    cx.single(data, obj);
                }
                break;
            default:
                switch (data.code) {
                    case 0:
                    case 200:
                        if ('random' in obj) obj.random();
                        if(aptype){
                            $('.pop-confirm').remove();
                            cx.Mask.hide();
                        }
                        switch (obj.orderType) {
                            case 1:
                                var href = baseUrl + 'chases/detail/' + data.orderId;
                                break;
                            case 4:
                                var href = baseUrl + 'hemai/detail/hm' + data.orderId;
                                break;
                            default:
                                var href = baseUrl + 'orders/detail/' + data.orderId;
                                break;
                        }
                        new cx.Confirm({
                            content: data.msg,
                            btns: obj.btns || [{type: 'cancel', txt: '再来一单'}, {type: 'confirm', target: '_blank', txt: '查看详情', href: href}],
                            cancelCb: function() {var str = location.href.split("?"); location.href = str[0];},
                            confirmCb: function() {var str = location.href.split("?"); location.href = str[0];},
                            append:'<div class="pop-side"><div class="qrcode"><table><tbody><tr><td><img src="/caipiaoimg/v1.1/img/qrcode-pay.png" width="94" height="94" alt=""></td><td><p><b>扫码下载手机客户端</b>中奖结果早知道</p></td></tr></tbody></table></div></div>'
                        });
                        break;
                    case 402:
                        new cx.Alert({content:"<i class='icon-font'>&#xe611;</i>"+data.msg+"<br><a class='sub-color fz16' href='/help/index/b2-s2-f3' target='_blank'>什么是限号?</a>"});
                        break;
                    case 12:
                        var href = baseUrl + 'wallet/recharge';
                        var target = "_blank";
                        new cx.Confirm({title: '确认投注信息', content: obj.binfo, btns: [{type: 'confirm', txt: '去充值', href: href, target : target}]});
                        break;
                    case 3000:
                        new cx.Confirm({
                            content: '<div class="mod-result result-success"><div class="mod-result-bd"><div class="result-txt"><h2 class="result-txt-title">您的登录已超时，请重新登录！</h2></div></div></div>',
                            btns: [{type: 'confirm', txt: '重新登录', href: baseUrl + 'main/login'}]
                        });
                        break;
                    case 998:
                        new cx.Alert({content: data.msg});
                        //清空
                        clearEmpty();
                        break;
                    default:
                        //清空
                        clearEmpty();
                        if (obj.msgconfirmCb) {
                            new cx.Confirm({single: data.msg, btns: [{type: 'confirm', txt: '确定', href: href}], confirmCb:obj.msgconfirmCb});
                        }else if(aptype){
                            $(aptype).html(data.msg);
                        }else{
                            new cx.Alert({content: data.msg});
                        }
                        break;
                }
                break;
        }
    };

    var singleAjax  = cx.singleAjax = (function() {
        var me = {};
        var success;
        var locks = {};
        me.get = function(options) {
            var url = baseUrl + 'ajax/get';
            options.data || (options.data = {});
            var data = options.data;
            data['url'] = options.url;
            return $.ajax({
                url: url,
                data: data,
                success: function(response) {
                    if ('success' in options) {
                        options.success(response);
                    }
                }
            });
        }

        me.post = function(options) {
            var url = baseUrl + 'ajaxSingle/post';
            var data = options.data;
            data['url'] = options.url;
            var isJson = data.isJson || 0;
            data.isJson = isJson;
            if (!locks[options.url]) {
                locks[options.url] = true;
                return $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    success: function(response) {
                        options.success(response);
                    },
                    complete: function() {
                        locks[options.url] = false;
                    }
                });
            }
            return false;
        };

        return me;
    })();


});
