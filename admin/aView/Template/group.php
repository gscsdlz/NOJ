<div class="row">

	<div class="col-md-3 col-md-offset-1">
		<form class="form-horizontal">
			<div class="form-group">
				<label for="group_name" class="col-sm-4">新小组名称</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="group_name"
						placeholder="请输入小组名称" />
				</div>
			</div>
			<div class="form-group text-center">
				<button type="button" class="btn btn-primary" id="addGroup">提交</button>
			</div>
			<div class="form-group text-center" id="error"></div>
		</form>
	</div>
	<div class="col-md-2 col-md-offset-1">
		<h4>单击显示组内成员</h4>
		<ul class="list-group">
		<?php
		foreach ( $args as $row ) {
			echo '<li id="' . $row [0] . 'group" name="' . $row [1] . '" class="list-group-item"><span class="badge">' . $row [3] . '</span>' . $row [1] . '<input type="hidden" value="'.$row[2].'"/></li>';
		}
		?>
		</ul>
	</div>
	<div class="col-md-3 text-center col-md-offset-1">
		<table class="table table-hover">
			<tr class="text-center">
                <td colspan="4" id="groupName">小组名</td>
            </tr>
            <tr class="text-center">
                <td><button type="button" class="btn btn-success" data-toggle="modal" data-target="#updateModal">修改小组信息</button></td>
                <td><button type="button" class="btn btn-danger" data-toggle="modal"
                            data-target="#delModal">删除本小组</button></td>
            </tr>
		</table>
        <table class="table table-hover" id="grouplist">

        </table>
		<table class="table table-hover">
			<tr>
				<th><button type="button" class="btn btn-primary" id="selectAll">全选</button></th>
				<th><button type="button" class="btn btn-primary" id="unselect">清除全选</button></th>
				<th colspan="2"><button type="button" class="btn btn-success" data-toggle="modal"
						data-target="#changeModal">调整</button></th>
			</tr>
		</table>
	</div>
</div>
<div class="modal fade" id="delModal" tabindex="-1" role="dialog"
	aria-labelledby="delModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h1 class="modal-title" >警告删除不可逆</h1>
			</div>
			<div class="model-body text-center">
				<h4 class="text-danger">删除会导致该组所有成员重新恢复到None组，无法撤回，请谨慎操作！</h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="closeModel">关闭</button>
				<button type="button" class="btn btn-danger" id="delGroup">确认删除</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog"
     aria-labelledby="updateModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h1 class="modal-title" >修改小组信息</h1>
            </div>
            <form class="form-horizontal" role="form" style="margin-top:20px;">
                <div class="form-group">
                    <label class="control-label col-sm-2">小组名</label>
                    <div class="col-sm-8">
                        <input id="groupNewName" type="text" value="" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">小组权限</label>
                    <div class="col-sm-8">
                        <select class="form-control" id="privates">
                            <option value="0">开放式小组任何人可以加入</option>
                            <option value="1">私有小组，仅管理员可以控制</option>
                        </select>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"
                        id="closeModel">关闭</button>
                <button type="button" class="btn btn-success" id="updateGroup">确认</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="changeModal" tabindex="-1" role="dialog"
	aria-labelledby="changeModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h3 class="modal-title" id="codeModalLabel">为这些用户选择新的小组</h3>
			</div>
			<div class="model-body text-left">
				<div>
					<form class="form-horizontal">
						<div class="form-group" style="padding: 10px 30px;">
							<label for="new_name" class="col-sm-4">选择新的小组</label>
							<div class="col-sm-8">
								<select id="new_group" class="form-control">
								<?php
								foreach ( $args as $row ) {
									echo '<option value="' . $row [0] . '">' . $row [1] . '</option>';
								}
								?>
							</select>
							</div>
						</div>
					</form>
				</div>
				<div class="row">
					<div class="col-md-8 col-md-offset-2" id="errorInfo">
						
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="closeModel">关闭</button>
				<button type="button" class="btn btn-danger" id="changeGroup">调整</button>
			</div>
		</div>
	</div>
</div>
<script>
	var id;
	$(document).ready(function(){
		$("#addGroup").click(function(){
			var group_name = $("#group_name").val();
			if(group_name.length != 0) {
				$.post("/admin/userM/groupAdd", {groupName:group_name}, function(data){
					var arr = eval("(" + data + ")");
					if(arr['status'] == true) {
						window.location.reload();
					} else {
						$("#error").html('<h4 class="text-danger">' + arr['info'] + '</span>'); 
					}
				})
			}
		})

		$(".list-group li").click(function(){
			$("li").removeClass("active");
			$(this).addClass("active");
			id = parseInt($(this).attr("id"));
			var info = $(this).attr('name');
			var private = $(this).children().eq(1).val();
            str = $(this).html();
            str = str.substring(str.indexOf("/span>") + 6, str.indexOf("<input"));

			$("#groupNewName").val(str);
			$("#privates").val(private);
            $("#grouplist").html("");
			$.post("/admin/userM/groupList", {gid:id}, function (data){
				var arr = eval("(" + data + ")");
				$("#groupName").html("组名：" + info);
				for(var i = 0; i < arr.length; i++){
					$("#grouplist").append('<tr><th colspan="4"><label><input type="checkbox" value="'+ arr[i][1] +'" /> '+ arr[i][0]+'('+arr[i][2]+')</label></th></tr>');
				}
			})
		})
		$("#delGroup").click(function(){
			$.post("/admin/userM/deleteGroup", {gid:id}, function (data){
				var arr = eval("(" + data + ")");
				if(arr['status'])
					window.location.reload();
			})
		})
		$("#changeGroup").click(function(){
			var obj = $(":checkbox");
			var userlist = new Array();
			var k = 0;
			for(var i = 0; i < obj.length; ++i) {
				if($(obj[i]).prop("checked")) {
					userlist[k++] = $(obj[i]).val();
				}
			}
			if(userlist.length != 0) {
				var groupId = $("#new_group").val();
				if(groupId != id) {
					$.post("/admin/userM/groupChange", {gid:groupId, users:userlist}, function(data){
						var arr = eval("(" + data + ")");
						if(arr['status'])
							window.location.reload();
						else
							$("#errorInfo").html('<h3 class="text-center text-danger">'+ arr['info']+'</h3>');
					})
				} else {
					$("#errorInfo").html('<h3 class="text-center text-danger">用户已经在该组，无法调整</h3>');
				}
			} else {
				$("#errorInfo").html('<h3 class="text-center text-danger">还未选择任何用户</h3>');
			}
		})

        $("#updateGroup").click(function(){
            var groupName = $("#groupNewName").val();
            var private = $("#privates").val();
            $.post("/admin/userM/updateGroup", {gid:id, groupName:groupName, private:private}, function(data){
                var arr = eval("(" + data + ")");
                if(arr['status'] == true)
                    window.location.reload();
            })
        })

		$("#selectAll").click(function(){
			$(":checkbox").prop("checked", "true");  //不用attr
		})
/*
Attribute/Property	.attr()	.prop()
accesskey			√ 	 
align			 	√ 	 
async 				√ 		√
autofocus		 	√ 		√
checked			 	√ 		√
class 				√ 	 
contenteditable 	√ 	 
draggable 			√ 	 
href 				√ 	 
id 					√ 	 
label 				√ 	 
location ( i.e. window.location ) 	√ 	√
multiple 			√ 		√
readOnly 			√ 		√
rel 				√ 	 
selected 			√ 		√
src 				√ 	 
tabindex 			√ 	 
title 				√ 	 
type 				√ 	 
width ( if needed over .width() ) 	√ 	
*/
		$("#unselect").click(function(){
			$(":checkbox").removeAttr("checked");
		})
	})
</script>

