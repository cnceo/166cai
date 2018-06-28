$(function(){
    var upload_no = '';
    createUpload (0);

    function createUpload (i) {
        var wUpL = WebUploader.create({
            swf: '/source/js/Uploader.swf',
            server: '/ajax/uploadFile',
            pick: '#picker' + i,
            accept: {
                title: 'txt文本格式',
                extensions: 'txt',
                mimeTypes: 'text/plain'
            },
            formData: {
             lid:cx.Lottery.lid,
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
                return ;
            }
            if(response.code) {
                new cx.Alert({content: "<i class='icon-font green-color'>&#xe646;</i> 恭喜你上传成功，共 <span class='num-red'>"+response.data.betTnum+"</span> 注"});
                wUpL.removeFile(file);
                //处理 
                cx._basket_.playType  = 'dssc';
                cx._basket_.betNum = response.data.betTnum;
                cx._basket_.betMoney = $('#zjtz').length && $('#zjtz').is(":checked") ? response.data.betTnum * 3: response.data.betTnum * 2;
                cx._basket_.upload_no = response.data.upload_no;
                cx._basket_.renderAllBet()
                cx._basket_.chase.setChaseMoney();
                //合买更新
                cx._basket_.hemai.renderHeMai();
                return ;
            }else{
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> "+response.msg});
                wUpL.removeFile(file);
                //清空
                cx._basket_.removeAll();
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
                cx._basket_.removeAll();
                return ;
              }
            if (type=="Q_TYPE_DENIED"){
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> 请先选择文件（以.txt 结尾的文本文件）"});
                //清空
                cx._basket_.removeAll();
             }
         });
        $('#ctlBtn' + i).on('click', function () {
            var $tips = $(this).closest('.up-prog').find('.uploader-list-tips');
            wUpL.upload()
            if (!wUpL.getStats().queueNum) {
                new cx.Alert({content: "<i class='icon-font'>&#xe611;</i> 请先选择文件（以.txt 结尾的文本文件）"});
                //清空
                cx._basket_.removeAll();
                return ;
            }
        })
        return wUpL;
    }


});
