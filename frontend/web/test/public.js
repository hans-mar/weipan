//页面切换
	$(function(){
	var a = $('#bottom li');
	a.each(function () {
		var href = $(this).children().attr('href');
		if ((location.href + '/').indexOf(href) > -1&&href!='') {
		    a.children().addClass('col-3'); 
		    $(this).children().find("i").addClass('col-4');
		    $(this).children().addClass('col-4');
		    return false;
		}
	});
});
//时间选择器默认当天时间
	$(function(){
		var date = new Date();
	    var day = date.getDate()>=10 ? date.getDate : "0"+date.getDate()
		var month =date.getMonth()>=10 ? (date.getMonth()+1) : ("0"+(date.getMonth()+1))
		var mydate = date.getFullYear()+"-"+month+"-"+day
		$(".mydate").val(mydate.toString());
	})