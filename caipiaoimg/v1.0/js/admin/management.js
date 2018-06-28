$(function(){
  curr_issue = $('td.firstTd input[type=checkbox]:checked').attr('data-issue') || '';
  awardNum = $('td.firstTd input[type=checkbox]:checked').attr('data-awardnum') || '';
  //点击表格
  $(document).on('click', 'td.firstTd', function(e){
    if($(this).find('input[name=selectIssue]').is(':checked'))
    {
      $('input[name=selectIssue]').removeAttr('checked');
      $("tr").removeClass("select");
      curr_issue = '';
      awardNum = '';
      aduitflag = 0;
    }else{
      $('input[name=selectIssue]').removeAttr('checked');
      $(this).find('input[name=selectIssue]').attr('checked','checked');
      $("tr").removeClass("select");
      $(this).parent().addClass("select");
      curr_issue = $(this).find('input[name=selectIssue]').attr("data-issue");
      awardNum = $(this).find('input[name=selectIssue]').attr("data-awardNum");
      aduitflag = $(this).find('input[name=selectIssue]').attr("data-aduit");
    }
    e.stopPropagation();
  });
  //勾选操作
  $(document).on('click', 'input[name=selectIssue]', function(e){
    if($(this).is(':checked'))
    {
      $('input[name=selectIssue]').removeAttr('checked');
      $(this).attr('checked','checked');
      $("tr").removeClass("select");
      $(this).parent().parent().addClass("select");
      curr_issue = $(this).attr("data-issue");
      awardNum = $(this).attr("data-awardNum");
      aduitflag = $(this).attr("data-aduit");
    }else{
      $('input[name=selectIssue]').removeAttr('checked');
      $("tr").removeClass("select");
      curr_issue = '';
      awardNum = '';
      aduitflag = 0;
    }
    e.stopPropagation();
  });
  //审核弹窗
  $(document).on('click', "#sh_"+type, function(){
    //权限验证
    if(!checkCapacity($(this).attr('data-capacity'))) return ;
    if(!curr_issue)
    {
      layer.alert('请先选择你要修改的期次~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //验证是否有开奖号码
    if(!awardNum)
    {
      layer.alert('所选期次暂无开奖号码，请选择已抓取号码期次进行审核', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //重复审核
    if(aduitflag!=0)
    {
      layer.alert('开奖号码已审核，无须再审~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    $("#pop_sh_"+type).find('h2').html(lname+curr_issue+'期开奖号码');
    popdialog("pop_sh_"+type);
    
  });
  //开奖号码弹窗
  $(document).on('click', "#hm_"+type, function(){
    //权限验证
    if(!checkCapacity($(this).attr('data-capacity'))) return ;
    if(!curr_issue)
    {
      layer.alert('请先选择你要修改的期次~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //验证是否审核
    if(aduitflag!=0)
    {
      layer.alert('号码已审核，无法修改~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    $("#pop_hm_"+type).find('h2').html(lname+curr_issue+'期开奖号码');
    popdialog("pop_hm_"+type);
  });
  //详情录入
  $(document).on('click', '#modifyDetail', function(){
    //权限验证
    if(!checkCapacity($(this).attr('data-capacity'))) return ;
    if(!curr_issue)
    {
      layer.alert('请先选择你要修改的期次~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //验证是否有开奖号码
    if(!awardNum)
    {
      layer.alert('所选期次暂无开奖号码，请选择已抓取号码期次进行详情录入~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    var status = $(".select").find('.status').html();
    if(status == "开启")
    {
        layer.alert('不能对开启状态的期次进行开奖信息修改~', {icon: 2,btn:'',title:'温馨提示',time:0});
        return false;
    }
    var matchStatus;
    status == "截止" ? matchStatus = 0 : matchStatus = 1;
    var type = $("input[name='type']").val();
    window.location.href = '/backend/Issue/modifyIssueDetail?lid=' + type + '&issue=' + curr_issue + '&matchStatus=' + matchStatus;
  });
  //遗漏弹窗唤起
  $("#recount").click(function(){
    //权限验证
    if(!checkCapacity($(this).attr('data-capacity'))) return ;
    popdialog("recountPop");
  });
  
  //核对异常查询操作
  $(document).on('click', '.compare-detail', function(){
     layer.load(0, {shade: [0.5, '#393D49']});
      var issue = $(this).attr("data-issue");
      var type = $("input[name='type']").val();
      $.ajax({
            type: "post",
            url: '/backend/Issue/compareDetail',
            data: {type:type,issue:issue},
            success: function (data) {
              layer.closeAll();
                if(data == '2')
                {
                  layer.alert('获取不到任何开奖抓取信息', {icon: 2,btn:'',title:'温馨提示',time:0});
                }else{
                  $('#compareForm').html(data);
                  popdialog("idetail");
                }
            }
        });
  });
  //监控回车时间
  $(document).keyup(function(event){
    if(event.keyCode ==13){

      if($(".pop-dialog:visible").length)
      {
        $(".pop-dialog:visible").find('.btn-blue-h32').trigger("click");
      }
      
    }
  });

});
/**
* [getCode 得到号码数组]
* @author LiKangJian 2017-09-22
* @param  {[type]} len  [description]
* @param  {[type]} flag [description]
* @return {[type]}      [description]
*/
function getCode(len,flag)
{
	var code_arr = new Array();
	var i = 0;
	do
	{
	 var code = (i+1).toString();//转化成字符串
	 if(flag==1) code =  code.length == 2 ? code :'0'+code;
	 code_arr.push(code);
	 i++;
	}while (i<len);

	return code_arr;
}
/**
 * [checkCapacity 验证是否有权限操作]
 * @author LiKangJian 2017-09-28
 * @return {[type]} [description]
 */
function checkCapacity(capacity)
{
   if($.inArray(capacity,allCapacity)===-1)
   {
      layer.alert('呐，这么做最重要的是要有权限啦！', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
   }
   return true;
}