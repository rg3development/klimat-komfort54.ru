$(function(){
	var i = 1;
	// top banners widjet
	$(".small_img").removeClass("active");
	$("#img2-tag").click(function () {
			$("#img1-tag").fadeOut(400, function() {
				tmp_attr	= $("#img1-tag").attr("ref");
				small_attr	= $("#img2-tag").attr("ref");
				$("#img1-tag").attr("src",$("#"+small_attr).attr("src"));
				$("#img2-tag").attr("src",$("#small_"+tmp_attr).attr("src"));
				$("#img2-tag").attr("ref", tmp_attr);
				$("#img1-tag").attr("ref", small_attr);
				$("#img1-tag").fadeIn();
			});
	});
	$("#img3-tag").click(function () {
			$("#img1-tag").fadeOut(400, function() {
				tmp_attr	= $("#img1-tag").attr("ref");
				small_attr	= $("#img3-tag").attr("ref");
				$("#img1-tag").attr("src",$("#"+small_attr).attr("src"));
				$("#img3-tag").attr("src",$("#small_"+tmp_attr).attr("src"));
				$("#img3-tag").attr("ref", tmp_attr);
				$("#img1-tag").attr("ref", small_attr);
				$("#img1-tag").fadeIn();
			});
	});
})