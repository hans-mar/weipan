//动态调整rem值,除以50
function setsize() {
  var winW = document.documentElement.clientWidth,
  	  winH = document.documentElement.clientHeight,
	  baseFontSize = 50,
	  baseWidth = 640,
	  winWidthSize = Math.min(winW, winH);
  if (winWidthSize > 560) {
	winWidthSize = 560;
  }
  if (winWidthSize < 270) {
	winWidthSize = 270;
  }
  var _html = document.getElementsByTagName('html')[0];
  _html.style.fontSize =winWidthSize / baseWidth * baseFontSize + 'px';
}
setsize();
window.addEventListener('resize',setsize,false);
function preventDefault(e){
    e.preventDefault();
}
$(function(){
	$discbox=$('#discbox');
	var num=45,n=0;
	
	function goNext(){
		$('.cell em,.cell b').css({'-webkit-transition':'all ease-out 0.5s','transition':'all ease-out 0.5s'});
		$('.div1').html($('.div1').html()+'left');
		n--;
		var deg=num*n;
		var m=-n%8;
		
		$('.cell').removeClass('cell_on').eq(m).addClass('cell_on');
		$('#disc_center').html($('.cell b').eq(m).html());
		$('#cellbox').css({'-webkit-transform':'rotate('+deg+'deg)','transform':'rotate('+deg+'deg)'});
	}
	function goPrev(){
		$('.cell em,.cell b').css({'-webkit-transition':'all ease-out 0.5s','transition':'all ease-out 0.5s'});
		$('.div1').html($('.div1').html()+'right');
		n++;
		var deg=num*n;
		var m=-(n-8)%8;
		
		$('.cell').removeClass('cell_on').eq(m).addClass('cell_on');
		$('#disc_center').html($('.cell b').eq(m).html());
		$('#cellbox').css({'-webkit-transform':'rotate('+deg+'deg)','transform':'rotate('+deg+'deg)'});
	}
	var slider=$('#discbox');
	$('.discbox').on('touchstart','.cellbox div', function(e){
		var touch = e.originalEvent,
			startX = touch.changedTouches[0].pageX;
		slider.on('touchmove',function(e){
			e.preventDefault();
			touch = e.originalEvent.touches[0] ||
					e.originalEvent.changedTouches[0];
			if(touch.pageX - startX > 10){
				slider.off('touchmove');
				goPrev();
			}
			else if (touch.pageX - startX < -10){
				slider.off('touchmove');
				goNext();
			}
		});
		return false;
	}).on('touchend',function(){
		slider.off('touchmove');
	});
});
