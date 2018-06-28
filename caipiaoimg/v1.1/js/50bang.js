//统计
function clickCount( vUrl,areaNum )
{
	//url组合
	var url = 'http://union2.50bang.org/web/ajax112?' 
			+ 'uId2=SPTNPQRLSX' 
			+ '&r=' + encodeURIComponent(document.location.href) 
			+ '&fBL=' + screen.width  + '*'+screen.height 
			+ '&lO='+encodeURIComponent(vUrl) 
			+ ( areaNum ? '' : '?tj=' + areaNum )
			+ "?nytjsplit="+encodeURIComponent(location.href);
	var _dh = document.createElement("script");
	_dh.setAttribute("type","text/javascript");
	_dh.setAttribute("src",url);
	document.getElementsByTagName("head")[0].appendChild(_dh);
	return true;
}

$('a').on('click', function(){
	var href = '';
		href = $(this).attr('href');
	if( href && href.indexOf('javascript') != 0 ){
		clickCount( href , '');
	};
});
