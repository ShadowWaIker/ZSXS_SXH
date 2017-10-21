$(document).ready(function(){
	first();
	/*
	$('#btn_addtr').click(function (){
		$("#zy").append($("#xqzy").clone());
	});
	$('#send').click(function (){
		if(document.getElementById("xq").value=='null' || document.getElementById("zpzw").value==''  || document.getElementById("zpsl").value==''){
			alert('请填写完整！');
		}else{
			upload();
			window.location="position.html";
		}
	});*/
});
function first() {
	$.ajax({
		url:'position.php',
        type:'post',
		dataType:'json',
		data:"flag=f5",
		success:update_page
		});
}
/*
function upload() {
	var val='';
	$("select[id='xq']").each(function(){
		val+=$(this).val()+',';
	});
	var params = $('select').serialize()+'&'+$('input').serialize()+'&xq='+val;
	$.ajax({
		url:'position.php',
    	type:'post',
		dataType:'json',
		data:params, 
	});
}*/
function update_page(json) {
	var obj = JSON.parse(json);
	$("#readydiv").html(obj.val);
}