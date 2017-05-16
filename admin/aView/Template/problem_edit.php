<h1 class="text-center">
<?php
if (isset ( $args [0] ) && count ( $args [0] )) {
	extract ( $args [0] );
	echo '修改·编号' . $pro_id;
} else
	echo '新增题目';
?>
</h1>
<p>&nbsp;</p>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<form class="form-horizontal" method="post">
			<div class="form-group">
				<label for="title" class="col-sm-2 control-label">题目名</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="title"
						value="<?php if(isset($pro_title)) echo $pro_title;?>">
				</div>
				<label for="author" class="col-sm-2 control-label">来源</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" id="author"
						value="<?php if (isset($author)) echo $author;?>">
				</div>
			</div>
			<div class="form-group">
				<label for="time_limit"
					class="col-sm-2 col-sm-offset-2 control-label">时间限制(ms)</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="time_limit"
						value="<?php if (isset($time_limit)) echo $time_limit;?>">
				</div>
				<label for="memory_limit" class="col-sm-2 control-label">内存限制(KB)</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" id="memory_limit"
						value="<?php if (isset($memory_limit)) echo $memory_limit;?>">
				</div>
			</div>
			<!-- 编辑器区域-->
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="text-center">题目描述</h1>
						</div>
						<div class="panel-body">
							<script id="editor" type="text/plain"
								style="width: 100%; height: 500px;"></script>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="text-center">输入描述</h1>
						</div>
						<div class="panel-body">
							<script id="editorIn" type="text/plain"
								style="width: 100%; height: 200px;"></script>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="text-center">输出描述</h1>
						</div>
						<div class="panel-body">
							<script id="editorOut" type="text/plain"
								style="width: 100%; height: 200px;"></script>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h1 class="text-center">输入样例描述</h1>
								</div>
								<div class="panel-body">
									<textarea id="pro_dataIn" rows="" cols=""
										style="width: 100%; height: 100px"><?php if(isset($pro_dataIn)) echo $pro_dataIn;?></textarea>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="panel panel-danger">
								<div class="panel-heading">
									<h1 class="text-center">输出样例描述</h1>
								</div>
								<div class="panel-body">
									<textarea id="pro_dataOut" rows="" cols=""
										style="width: 100%; height: 100px"><?php if(isset($pro_dataOut)) echo $pro_dataOut;?></textarea>
								</div>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h1 class="text-center">提示</h1>
						</div>
						<div class="panel-body">
							<script id="editorHint" type="text/plain"
								style="width: 100%; height: 200px;"></script>
						</div>
					</div>
<?php if(isset($pro_id)) {?>
					<div class="panel panel-danger">
						<div class="panel-heading">
							<h1 class="text-center">数据文件</h1>
						</div>
						<div class="panel-body">
							<table class="table table-bordered">
								<tr>
									<td>文件名</td>
									<td colspan="3">操作</td>
								</tr>
<?php
	if(isset($args[1]) && count($args[1])) 
	foreach ( $args [1] as $file ) {
		if (strlen ( $file ) <= 2)
			continue;
		echo '<tr>';
		echo '<td>' . $file . '</td>';
		echo '<td><button onclick="download_file($(this).attr(\'id\'))" type="button" id="'.str_replace('.','yyyyyyyy',$file).'" class="btn btn-primary">下载</button></td>';
		echo '<td><button onclick="delete_file($(this).attr(\'id\'))" type="button" id="'.str_replace('.','xxxxxxxx',$file).'" class="btn btn-danger">删除</button></td>';
		echo '</tr>';
	}
?>

							</table>
								<div class="form-group">
									<label class="col-sm-6">
									<span class="text-danger">只允许上传.in 和 .out文件</span><br/>
									<span class="text-danger">请注意同一份输入输出文件名应该相同</span><br/>
									例如1.n 1.out; 2.in 2.out <br/>
									文件名可以使用任意合法字符，但是最好和题目关联<br/>
									例如题目2017的第一组样例可以使用2017A.in 2017B.out
									</label>
									<div class="col-sm-6">
										<input class="form-control" type="file" name="file" id="uploadFile" />
									</div>
								</div>
						</div>
					</div>
				</div>
			</div>
<?php }?>
			<div class="form-group">
				<div class="text-center">
					<button type="button" class="btn btn-success" style="width: 100px"
						id="save">保存</button>
					<br /> <label class="text-danger" id="errorInfo"></label>
				</div>
			</div>
		</form>
	</div>
</div>
<script src="/admin/ueditor/ueditor.config.js"></script>
<script src="/admin/ueditor/ueditor.all.min.js"> </script>
<script src="/admin/ueditor/editor/lang/zh-cn/zh-cn.js"></script>
<script>
	var ue = UE.getEditor('editor'); 
	var ueIn = UE.getEditor('editorIn'); 
	var ueOut = UE.getEditor('editorOut'); 
	var ueHint = UE.getEditor('editorHint'); 
	var arr;
<?php
	if(isset($pro_id)) {
?>

	function delete_file(id) {
		$.post("/admin/problemM/del_file", {pro_id:<?php echo $pro_id;?>,filename:id.replace(/xxxxxxxx/, ".")}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status']) {
				$("#" + id).parent().parent().html('<td colspan="3">当前文件已经移动至'+arr['info']+' 一段时间后自动清除</td>');
			} 
		})
	}

	function download_file(id) {
		$("body").append('<iframe width="0px" height="0px" src="/admin/problemM/download?pro_id='+<?php echo $pro_id;?>+'&filename='+id.replace(/yyyyyyyy/, ".")+'"></iframe>');
	}
	
	ue.ready(function() {
		ue.setContent(arr['pro_descrip']);
	});
	ueIn.ready(function(){
		ueIn.setContent(arr['pro_in']);
	});
	ueOut.ready(function(){
		ueOut.setContent(arr['pro_out']);
	});
	ueHint.ready(function(){
		ueHint.setContent(arr['hint']);
	});
<?php }?>
	$(document).ready(function(){
<?php
	if(isset($pro_id)) {
?>
		$("#uploadFile").AjaxFileUpload({
				action: "/admin/problemM/upload/<?php echo $pro_id?>",
				onComplete: function(filename, response) {
					 var arr = eval(response);
					 $("table").append('<tr><td>'+arr['filename']+'</td><td><button  onclick="download_file($(this).attr(\'id\'))" type="button" id="'+arr['filename'].replace(/\./,'yyyyyyyy')+'" class="btn btn-primary">下载</button></td><td><button  onclick="delete_file($(this).attr(\'id\'))" type="button" id="'+arr['filename'].replace(/\./,'xxxxxxxx')+ '" class="btn btn-danger">删除</button></td></tr>');
				}
			});
		
		$.post("/admin/problemM/editPost", {<?php if(isset($pro_id)) echo 'id:'.$pro_id?>} , function(data){
			arr = eval("(" + data + ")");
		})
<?php }?>
		$("#save").click(function(){
			var time_limit = $("#time_limit").val();
			var memory_limit = $("#memory_limit").val();
			var pro_title = $("#title").val();
			var author = $("#author").val();
			var pro_dataIn = $("#pro_dataIn").val();
			var pro_dataOut = $("#pro_dataOut").val();
			var pro_descrip = ue.getContent();
			var pro_in = ueIn.getContent();
			var pro_out = ueOut.getContent();
			var hint = ueHint.getContent();
			$.post("/admin/problemM/savePro", {<?php if(isset($pro_id)) echo 'pro_id:'.$pro_id.',';?>pro_title:pro_title, time_limit:time_limit, memory_limit:memory_limit, pro_descrip:pro_descrip, pro_in:pro_in,pro_out:pro_out, pro_dataIn:pro_dataIn, pro_dataOut:pro_dataOut, hint:hint, author:author}, function(data) {
				var arr = eval("(" + data + ")");
				if(arr['status'] == true)
					window.location.href = "/admin/problemM/edit/" + arr['pro_id'];
				else
				$("#errorInfo").html(arr['status']);
			})
		})
	})
</script>
