<?php
if (isset ( $args [0] [0] ))
	$base = $args [0] [0];
if (isset ( $args [1] ))
	$prolist = $args [1];
if (isset ( $args [2] ))
	$groups = $args [2];
if (isset ( $args [3] ))
	$users = $args [3];
unset($args);
?>
<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div>
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#base"
					aria-controls="home" role="tab" data-toggle="tab">比赛基本信息</a></li>
				<li role="presentation"><a href="#user" aria-controls="profile"
					role="tab" data-toggle="tab">用户管理</a></li>
				<li role="presentation"><a href="#balloon" aria-controls="settings"
					role="tab" data-toggle="tab">气球管理</a></li>
				<li role="presentation"><a href="#rejudge" aria-controls="settings"
					role="tab" data-toggle="tab">重判</a></li>
				<li role="presentation"><a href="#sim" aria-controls="settings"
					role="tab" data-toggle="tab">代码查重</a></li>
			</ul>

			<!-- Tab panes -->
			<div class="tab-content well" style="padding-top: 20px;">
				<div role="tabpanel" class="tab-pane active" id="base">
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<form class="form-horizontal">
								<div class="form-group">
									<label for="contest_name" class="col-sm-4">比赛名称</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="contest_name"
											placeholder="请输入比赛名称"
											value="<?php if(isset($base['contest_name'])) echo $base['contest_name'];?>">
									</div>
								</div>
								<div class="form-group">
									<label for="username" class="col-sm-4">比赛管理员</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="username"
											placeholder="请输入比赛管理员"
											value="<?php if(isset($base['username'])) echo $base['username'];?>">
									</div>
								</div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4">奖牌设置</label>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 col-sm-offset-1">金牌（一等奖）数量</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="auNum"
                                               placeholder="金牌（一等奖）数量"
                                               value="<?php if(isset($base['au'])) echo $base['au'];?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 col-sm-offset-1">银牌（二等奖）数量</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="agNum"
                                               placeholder="银牌（二等奖）数量"
                                               value="<?php if(isset($base['ag'])) echo $base['ag'];?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 col-sm-offset-1">铜牌（三等奖）数量</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="cuNum"
                                               placeholder="铜牌（三等奖）数量"
                                               value="<?php if(isset($base['cu'])) echo $base['cu'];?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-6 col-sm-offset-1">铁牌（优秀奖）数量</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" id="feNum"
                                               placeholder="铁牌（优秀奖）"
                                               value="<?php if(isset($base['fe'])) echo $base['fe'];?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-4">比赛模式选择</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" id="options">
                                            <option value="0" <?php if(isset($base['options']) && $base['options'] == 0) echo 'selected="true"';?>>普通模式</option>
                                            <option value="-1" <?php if(isset($base['options']) && $base['options'] == -1) echo 'selected="true"';?>>关闭排名</option>
                                            <option value="1"<?php if(isset($base['options']) && $base['options'] == 1) echo 'selected="true"';?>>禁止查看自己的代码</option>
                                            <option value="-2" <?php if(isset($base['options']) && $base['options'] == -2) echo 'selected="true"';?>>关闭排名且禁止查看自己代码</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
									<label for="contest_pass" class="col-sm-4">比赛权限</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="contest_pass"
											placeholder="请输入权限"
											value="<?php if(isset($base['contest_pass'])) echo $base['contest_pass'];?>">
									</div>
								</div>
								<label class="text-danger"> 1表示公开 2表示私有比赛(指定用户可参加)
									6位以上字符表示参加比赛需要使用该密码</label>
								<p>&nbsp;</p>
								<div class="form-group">
									<label for="c_stime" class="col-sm-4">比赛开始时间<br />格式：1970-01-30
										00:00:00
									</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="c_stime"
											placeholder="请输入开始时间"
											value="<?php if(isset($base['c_stime'])) echo date('Y-m-d H:i:s', $base['c_stime'])?>">
									</div>
								</div>
								<div class="form-group">
									<label for="c_etime" class="col-sm-4">比赛结束时间<br />格式：1970-01-30
										00:00:00
									</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" id="c_etime"
											placeholder="请输入结束时间"
											value="<?php if(isset($base['c_etime']))echo date('Y-m-d H:i:s', $base['c_etime'])?>">
									</div>
								</div>
								<label for="contest_title">比赛题目列表 <span class="text-danger">双击题目实际ID
										即可删除当前题目 已经在比赛中的题目，如果删除会一并删除相关的提交数据，请注意！</span></label>
								<div id="prolist">
						<?php
						if (isset ( $prolist ))
							for($i = 1; $i < count ( $prolist ); $i ++) {
								echo '<div class="form-group">';
								echo '<div class="col-sm-4"><input value="' . $prolist [$i] [0] . '" type="text" class="form-control"/></div>';
								echo '<div class="col-sm-4"><input readonly="true" ondblclick="delete_pro($(this).parent().parent())" value="' . $prolist [$i] [4] . '" type="text" class="form-control" /></div>';
								echo '<div class="col-sm-4"><label><a href="/problem/show/' . $prolist [$i] [4] . '" target="_blank">' . $prolist [$i] [1] . '</a></label></div>';
								echo '</div>';
							}
						?>
							</div>
								<div class="form-group">
									<div class="col-sm-4">
										<input id="pro_id" value="" type="text" class="form-control"
											placeholder="请填写题目实际编号" />
									</div>
									<div class="col-sm-4">
										<label><a id="pro_title" href="javascript:void(0)">请填写题目实际编号</a></label>
									</div>
								</div>
								<div class="form-group text-center">
									<button type="button" style="width: 100px"
										class="btn btn-primary" id="add">确认添加</button>
								</div>
								<hr />
								<p class="text-danger">没有点击保存之前，所做的操作不会同步到数据库中，请不要离开该页面或者刷新页面</p>
								<div class="form-group text-center">
									<button type="button" style="width: 100px"
										class="btn btn-success" id="save">保存</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="user">

		<?php
		if (! isset ( $base ))
			echo '<h3 class="text-center text-danger">请先添加比赛</h3>';
		else if ($base ['contest_pass'] != 2)
			echo '<h3 class="text-center text-danger">用户管理仅针对私有比赛</h3>';
		else {
			?>
					<div class="row">
						<div class="col-md-6 col-md-offset-3 text-center"
							style="margin-bottom: 20px;">
							<span class="text-danger">点击保存之前所做的更改不会生效，请注意</span><br />
							<button type="button" class="btn btn-primary" id="saveUser">保存</button>
							<button type="button" class="btn btn-danger" id="clearList">清空现有列表</button>
							<br /> <span class="text-danger" id="usererror"></span><br />
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 col-md-offset-1 well">
							<h4 class="text-center">现有小组</h4>
							<ul class="list-group" id="groupList">
		<?php
			foreach ( $groups as $row ) {
				echo '<li id="groups' . $row [0] . '" class="list-group-item"><span class="badge">' . $row [2] . '</span>' . $row [1] . '</li>';
			}
			?>
							</ul>
						</div>
						<div class="col-md-4 col-md-offset-2 well">
							<h4 class="text-center" id="totalUser">现有用户 共计<?php echo count($users)?>人</h4>
							<ul class="list-group" id="userList">
		<?php
			foreach ( $users as $row ) {
				echo '<li ondblclick="delete_pro($(this))" id="users' . $row [0] . '"  class="list-group-item">' . $row [1] . '('.$row[2].')</li>';
			}
			?>
							</ul>
						</div>
					</div>
		<?php }?>
					
				</div>
				<div role="tabpanel" class="tab-pane" id="balloon">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<table class="table table-hover table-bordered" id="balloonList">
								<tr>
									<th>提交号</th>
									<th>用户名</th>
									<th>通过题目</th>
									<th>通过时间</th>
									<th>座位号</th>
									<th>发出气球</th>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="rejudge">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<form class="form-horizontal">
								<div class="form-group">
									<label for="contest_id" class="col-sm-10">重判当前比赛（全部重判会带来巨大的判题压力，请注意）</label>
									<button id="recontest" type="button"
										class="col-sm-2 btn btn-danger">确定</button>
								</div>
								<div class="form-group">
									<label for="rejudgeusername" class="col-sm-4">指定用户重判</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="rejudgeusername"
											placeholder="请输入用户名">
									</div>
									<button id="reusername" type="button"
										class="col-sm-4 btn btn-success">确定</button>
								</div>
								<div class="form-group">
									<label for="rejudgproid" class="col-sm-4">指定题目重判</label>
									<div class="col-sm-4">
										<input  type="text" class="form-control"
											id="rejudgeproid" placeholder="请输入题目ID">
									</div>
									<button id="reproid" type="button" class="col-sm-4 btn btn-success">确定</button>
								</div>
								<div class="form-group">
									<label for="rejudgsubmitid" class="col-sm-4">基于提交号重判 也可以在状态页面 直接双击提交号</label>
									<div class="col-sm-4">
										<input type="text" class="form-control" id="rejudgesubmitid"
											placeholder="请输入提交ID">
									</div>
									<button id="resubmitid" type="button"
										class="col-sm-4 btn btn-success">确定</button>
								</div>
							</form>
							<p class="text-danger text-center" id="progress"></p>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="sim">
					<div class="row">
						<div class="col-md-12">
						<p class="text-danger">本着宁愿找不出作弊代码，也不要误判的原则<br/>代码查重只是给出相似度50%以上的代码，请管理员仔细查看以后点击确定</p>
						</div>
						<table class="table table-hover" id="simlist">
							<tr>
								<th>题目编号</th>
								<th>提交记录号1</th>
								<th>用户名1</th>
								<th>座位号1</th>
								<th>提交时间1</th>
								<th>提交记录号2</th>
								<th>用户名2</th>
								<th>座位号2</th>
								<th>提交时间2</th>
								<th>相似度</th>
								<th colspan="2">操作</th>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="simcodeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="simcodeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <div class="modal-body">
	  <div class="row well">
      	<div class="col-md-6 text-center">
      		<label  id="userlabel1">用户XXX对题目1003的提交</label><br/>
      		<button type="button" id="cheat1" onclick="do_sim1($(this).attr('name'), $('#cheat2').attr('name'))" name="" class="btn btn-danger">确认作弊</button>
      	</div>
      	<div class="col-md-6 text-center">
      		<label  id="userlabel2">用户XXX对题目1003的提交</label><br/>
      		<button type="button" id="cheat2" onclick="do_sim2($(this).attr('name'), $('#cheat1').attr('name'))" name="" class="btn btn-danger">确认作弊</button>
      	</div>
       </div>
      <div class="row well">
		<div class="col-md-6">
<pre class="line-numbers command-line data-line"><code id="user1" class="language-cpp"></code></pre>
		</div>
		<div class="col-md-6">
<pre class="line-numbers command-line data-line"><code id="user2" class="language-cpp"></code></pre>
		</div>
	  </div>
      </div>
    </div>
    </div>
</div>
<script>
	var pro_id;
	var pro_title;
	var valid_id = false;
	var prolist = new Array();
	var contest_id = <?php if(isset($base['contest_id'])) echo $base['contest_id']; else echo '-1';?>;
	$(document).ready(function(){
		$(window).bind('beforeunload',function(){
			return '您输入的内容尚未保存，确定离开此页面吗？';
		});
		$("#balloon").mousemove(function(){
			$(window).unbind('beforeunload');
		})
		$("#rejudge").mousemove(function(){
			$(window).unbind('beforeunload');
		})
		$("#sim").mousemove(function(){
			$(window).unbind('beforeunload');
		})
		get_balloon();
		get_sim()
		self.setInterval("get_balloon()", 60000);
	//	self.setInterval("get_sim()", 60000);
		
		$("#pro_id").keyup(function(){
			valid_id = false;
			pro_id = parseInt($(this).val());
			if(pro_id >= 1000) {
				$.post("/admin/contestM/pro_check", {pro_id:pro_id}, function(data){
					arr = eval("(" + data + ")");
					if(arr['status']) {
						valid_id = true;
						pro_title = arr['pro_title'];
						$("#pro_title").html(arr['pro_title']);
						$("#pro_title").attr("href", "/problem/show/" + pro_id);
						$("#pro_title").attr("target", "_blank");
					} else {
						$("#pro_title").html("题目ID不合法");
						$("#pro_title").attr("href", "javascript:void(0)");
						$("#pro_title").removeAttr("target");
					}
				})
			}
		})
		$("#add").click(function(){
			if(valid_id) {
				var lastId = parseInt($("#prolist").children().last().children().eq(0).children().eq(0).val());
				if(isNaN(lastId))
					lastId = 1000;
				else
					lastId += 1;
				$("#prolist").append('<div class="form-group">'+'<div class="col-sm-4"><input placeholder="请输入题目的比赛编号" value="'+lastId+'" type="text" class="form-control"/></div>'+'<div class="col-sm-4"><input readonly="true" ondblclick="delete_pro($(this).parent().parent())" value="' + pro_id + '" type="text" class="form-control" /></div>' + '<div class="col-sm-4"><label><a href="/problem/show/' + pro_id + '" target="_blank">' + pro_title+ '</a></label></div>' + '</div>');
				$("#pro_id").val("");
				$("#pro_title").html("请填写题目实际编号");
				$("#pro_title").attr("href", "javascript:void(0)");
				$("#pro_title").removeAttr("target");
			}
		})
		$("#save").click(function(){
			$("h3").remove();
			$("p").remove();
			var ok = true;
			var contest_name = $("#contest_name").val();
			var username = $("#username").val();
			var contest_pass = $("#contest_pass").val();
			var c_stime = parseInt(Date.parse(new Date($("#c_stime").val())) / 1000);
			var c_etime = parseInt(Date.parse(new Date($("#c_etime").val())) / 1000);
            var auNum = parseInt($("#auNum").val());
            var agNum = parseInt($("#agNum").val());
            var cuNum = parseInt($("#cuNum").val());
            var feNum = parseInt($("#feNum").val());
            var options = $("#options").val();
			$("#prolist div.form-group").each(function(index){
				var inner_id = $(this).children().eq(0).children().eq(0).val();
				var pro_id = $(this).children().eq(1).children().eq(0).val();
				if(inner_id < 1000) {
					$(this).attr("class", "form-group has-error");
					$(this).append('<p class="text-danger">比赛中的题目ID不合法</p>');
					ok = false;
				}
				prolist[index] = new Array(inner_id, pro_id);
			})
			if(ok) {
				$.post("/admin/contestM/save", {<?php if(isset($base['contest_id'])) echo 'contest_id:'.$base['contest_id'].',';?>contest_name:contest_name,username:username, contest_pass:contest_pass,c_stime:c_stime, c_etime:c_etime, prolist:prolist, auNum:auNum, agNum:agNum, cuNum:cuNum, feNum:feNum, options:options}, function(data){
					var arr = eval("(" + data + ")");
					if(arr['status']) {
						$(window).unbind('beforeunload');
						window.location.href = "/admin/contestM/edit/" + arr['contest_id'];
					} else {
						$("#base").append('<h3 class=" text-center text-danger">'+arr['info']+'</h3>');
					}
				});
			}
		})

		$("#groupList li").dblclick(function(){
			var id = parseInt(($(this).attr("id").substr(6)));
			$.post("/admin/userM/groupList", {gid:id}, function (data){
				var arr = eval("(" + data + ")");
				if(typeof arr == null)
					return;
				var idArr = new Array();
				$("#userList li").each(function(index){
					idArr[index] = parseInt($(this).attr("id").substr(5)); 
				})
				var size = 0;
				for(var i = 0; i < arr.length; i++){
					if($.inArray(parseInt(arr[i][1]), idArr)) {
						size++;
						$("#userList").append('<li ondblclick="delete_pro($(this))" id="users' + arr[i] [1] + '"  class="list-group-item">'+ arr[i] [0] + '(' + arr[i][2]+')</li>');
					}
				}
				$("#totalUser").html("现有用户，共计" + size + "人");
			})
			$(this).remove();
		})
		
		$("#clearList").click(function(){
			$("#userList").html("");
			$("#totalUser").html("现有用户，共计0人");
		})
		
		$("#saveUser").click(function(){
			var id = <?php if(isset($base['contest_id'])) echo $base['contest_id']; else echo '-1';?>;
			var idArr = new Array();
			$("#userList li").each(function(index){
				idArr[index] = parseInt($(this).attr("id").substr(5)); 
			})
			$.post("/admin/contestM/save_user", {cid:id, users:idArr}, function(data){
				var arr = eval("(" + data + ")");
				if(arr['status']) {
					$(window).unbind('beforeunload');
					window.location.reload();
				} else {
					$("#usererror").html(arr['info']);
				}
			})
		})

		$("#resubmitid").click(function(){
			var id = $("#rejudgesubmitid").val();
			if(id > 0 ) {
				$.post("/admin/contestM/rejudge", {cid:contest_id, submit_id:id}, function(data){
					var arr = eval("(" + data + ")");
					if(arr['status']) {
						$("#progress").html("已经收到"+arr['status']+"条重判数据");
					}
				})
			}
		})

		$("#reproid").click(function(){
			var id = $("#rejudgeproid").val();
			if(id > 0 ) {
				$.post("/admin/contestM/rejudge", {cid:contest_id, pro_id:id}, function(data){
					var arr = eval("(" + data + ")");
					if(arr['status']) {
						$("#progress").html("已经收到"+arr['status']+"条重判数据");
					}
				})
			}
		})
		
		$("#reusername").click(function(){
			var id = $("#rejudgeusername").val();
			if(id.length > 0 ) {
				$.post("/admin/contestM/rejudge", {cid:contest_id, username:id}, function(data){
					var arr = eval("(" + data + ")");
					if(arr['status']) {
						$("#progress").html("已经收到"+arr['status']+"条重判数据");
					}
				})
			}
		})
		
		$("#recontest").click(function(){
			$.post("/admin/contestM/rejudge", {cid:contest_id, rejudgeall:true}, function(data){
				var arr = eval("(" + data + ")");
				if(arr['status']) {
					$("#progress").html("已经收到"+arr['status']+"条重判数据");
				}
			})
		})
		
	})
	
	function get_sim() {
		$("#simlist").html('<tr><th>题目编号</th><th>提交记录号1</th><th>用户名1</th><th>座位号1</th><th>提交时间1</th><th>提交记录号2</th><th>用户名2</th><th>座位号2</th><th>提交时间2</th><th>相似度</th><th colspan="3">操作</th></tr>');
		$.post("/admin/contestM/sim", {cid:contest_id}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true) {
				var infos = arr['info'];
				for(var i = 0; i < infos.length; ++i) {
					
					var info = infos[i];
					if(info.length == 0)
						break;
					$("#simlist").append("<tr><td><a target=\"_blank\" href=\"/contest/problem/"+contest_id+"/"+info[0]+"\">"+info[0]+"</a></td><td>"+info[4]+"</td><td><a target=\"_blank\" href=\"/user/show/"+info[2]+"\"> "+info[2]+"(" + info[3]+ ")" + "</a></td><td>"+info[5]+"</td><td>"+info[6]+"</td><td>"+info[9]+"</td><td><a target=\"_blank\" href=\"/user/show/"+info[7]+"\">"+info[7]+"(" + info[8]+ ")" + "</a></td><td>"+info[10]+"</td><td>"+info[11]+"</td><td>"+info[1]+"</td>"+'<td>'+
'<button onclick="get_code('+info[4]+','+info[9]+')" data-toggle="modal" data-target="#simcodeModal" type="button" class="btn btn-primary">查看代码</button></td>'+
'<td><button onclick="mis_sim('+info[4]+','+info[9]+')" type="button" class="btn btn-success">误判</button></td>'+"</tr>");
				}
			} 
		})
	}

	function get_code(sid1, sid2) {
		$.post("/admin/contestM/get_code",{cid:contest_id, sid1:sid1, sid2:sid2},function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true) {

				var info = arr['info'];
				$("#cheat1").attr("name", info['submit_id1']);
				$("#cheat2").attr("name", info['submit_id2']);
				if(info['status1'] == 13)
					$("#cheat1").html("已处理");
				else
					$("#cheat1").html("确认作弊");
				if(info['status2'] == 13)
					$("#cheat2").html("已处理");
				else
					$("#cheat2").html("确认作弊");
				$("#userlabel1").html("用户<span class=\"text-danger\">" + info['username1'] + "</span>对题目<span class=\"text-danger\">" + info['pro_id1'] + "</span>的提交<br/>运行时间：<span class=\"text-danger\">" + info['run_time1'] + "ms </span>运行内存: <span class=\"text-danger\">"+ info['run_memory1'] + "</span>KB<br/>提交时间："+info['submit_time1']);
				$("#user1").html(info['code1']);
				$("#user1").attr("class", "language-"+info['style1']);
				$("#userlabel2").html("用户<span class=\"text-danger\">" + info['username2'] + "</span>对题目<span class=\"text-danger\">" + info['pro_id2'] + "</span>的提交<br/>运行时间：<span class=\"text-danger\">" + info['run_time2'] + "ms </span>运行内存: <span class=\"text-danger\">"+ info['run_memory2'] + "</span>KB<br/>提交时间："+info['submit_time2']);
				$("#user2").html(info['code2']);
				$("#user2").attr("class", "language-"+info['style2']);
				$("#script").remove();
				$("body").append("<script id=\"script\" src=\"/View/Template/js/prism.js\">");
			}
		})
	}

	function mis_sim(sid1, sid2) {
		$.post("/admin/contestM/mis_sim", {cid:contest_id, sid1:sid1, sid2:sid2}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true) {
				get_sim();
			}
		})
	}

	function do_sim1(sid, sid2) {
		$.post("/admin/contestM/do_sim", {cid:contest_id, sid:sid}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true) {
				get_code(sid, sid2);
			}
		})
	}
	function do_sim2(sid, sid2) {
		$.post("/admin/contestM/do_sim", {cid:contest_id, sid:sid}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true) {
				get_code(sid2, sid);
			}
		})
	}
	
	function get_balloon() {
		$("#balloonList").html("<tr><th>提交号</th><th>用户名</th><th>通过题目</th><th>通过时间</th><th>座位号</th><th>发出气球</th></tr>");
		
		$.post("/admin/contestM/get_balloon", {cid:contest_id}, function(data){
			var arr = eval("(" + data + ")");

			if(arr['status'] == true) {
				
				var info = arr['info'];

				for(var i = 0; i < info.length; ++i) {
					if(info[i][6] == 0) {	
						if(info[i][7] == 1)
							$("#balloonList").append('<tr><td>'+ info[i][5] +'</td><td>'+ info[i][0]  +'</td><td>' + info[i][2] + '</td><td>'+ info[i][4] +'</td><td>'+info[i][1]+'</td><td><button type="button" class="btn btn-danger" onclick="sendBalloon('+ info[i][5] +')">First Blood</button></td></tr>');
						else
							$("#balloonList").append('<tr><td>'+ info[i][5] +'</td><td>'+ info[i][0]  +'</td><td>' + info[i][2] + '</td><td>'+ info[i][4] +'</td><td>'+info[i][1]+'</td><td><button type="button" class="btn btn-success" onclick="sendBalloon('+ info[i][5] +')">送出</button></td></tr>');
					}
				}
			}
		})
	}

	
	function delete_pro(pro_dom) {
		$(pro_dom).remove();
	}

	function sendBalloon(id) {
		$.post("/admin/contestM/send_balloon", {cid:<?php if(isset($base['contest_id'])) echo $base['contest_id']; else echo '-1';?>, sid:id}, function(data){
			var arr = eval("(" + data + ")");
			if(arr['status'] == true)
				get_balloon();
		})
	}

		
</script>
