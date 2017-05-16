<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<h1 class="text-center text-primary"><?php echo $pro_title;?></h1>
		<h4 class="text-center text-danger">时间限制: <?php echo $time_limit;?>ms 内存限制: <?php echo $memory_limit;?>KB</h4>
		<h4 class="text-center text-danger">通过次数: <span class="badge"><?php echo $aSubmit;?></span>
			总提交次数: <span class="badge"><?php echo $tSubmit;?></span></h4>
		<div class="panel panel-default">
			<div class="panel-body">
<?php
	if($pro_descrip){
?>
				<div class="panel panel-default">
					<div class="panel-heading">问题描述</div>
					<div class="panel-body"><?php echo $pro_descrip;?></div>
				</div>
<?php } 
	if($pro_in){
?>
				<div class="panel panel-default">
					<div class="panel-heading">输入描述</div>
					<div class="panel-body"><?php echo $pro_in;?></div>
				</div>
<?php } 
	if($pro_out){
?>
				<div class="panel panel-default">
					<div class="panel-heading">输出描述</div>
					<div class="panel-body"><?php echo $pro_out;?></div>
				</div>
<?php } 
	if($pro_dataIn){
?>
				<div class="panel panel-default panel-danger">
					<div class="panel-heading">样例输入</div>
					<div class="panel-body"><pre><?php echo $pro_dataIn;?></pre></div>
				</div>
<?php } 
	if($pro_dataOut){
?>
				<div class="panel panel-default  panel-danger">
					<div class="panel-heading">样例输出</div>
					<div class="panel-body"><pre><?php echo $pro_dataOut;?></pre></div>
				</div>
<?php } 
	if($author){
?>
				<div class="panel panel-default">
					<div class="panel-heading">来源</div>
					<div class="panel-body"><?php echo $author;?></div>
				</div>
<?php } 
	if($hint){
?>
				<div class="panel panel-default">
					<div class="panel-heading">提示</div>
					<div class="panel-body"><?php echo $hint?></div>
				</div>
<?php } ?>
				<div class="panel panel-default">
					<div class="panel-body text-center">
						<button type="button" class="btn btn-danger btn-lg"
							data-toggle="modal"
							data-target="#<?php if(isset($_SESSION['username'])) echo 'codeModal'; else echo 'signModal';?>">提交</button>
						<button data-toggle="modal" id="getStatistics" data-target="#statisticsModal" type="button" class="btn btn-success btn-lg">统计</button>
						<button type="button" class="btn btn-info btn-lg">讨论</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog"
	aria-labelledby="statisticsModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="codeModalLabel">该题提交记录统计</h4>
			</div>
			<div class="modal-body">
			<table class="table table-hover text-center" id="statisticsInfo">
				<tr>
					<th>AC</th>
					<th>PE</th>
					<th>WA</th>
					<th>RE</th>
					<th>TLE</th>
					<th>MLE</th>
					<th>OLE</th>
					<th>CE</th>
				</tr>
			</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$("#getStatistics").click(function(){
			$.post("/problem/get_statistics", {pro_id:<?php echo $pro_id;?><?php if($contest) echo ',cid:'.$contest?>}, function(data){
				var arr = eval("(" + data + ")");
				if(arr['status']) {
					$("#statisticsInfo").html("<tr><th>AC</th><th>PE</th><th>WA</th><th>RE</th><th>TLE</th><th>MLE</th><th>OLE</th><th>CE</th></tr>");
					$("#statisticsInfo").append('<tr>');
					for(var i = 0; i < arr['info'][0].length; ++i)
						$("#statisticsInfo").append('<td>'+ arr['info'][0][i]+'</td>');
					$("#statisticsInfo").append("</tr>");
				}
			})
		})
	})
</script>
<?php
if (isset ( $_SESSION ['username'] )) {
	?>
<div class="modal fade" id="codeModal" tabindex="-1" role="dialog"
	aria-labelledby="codeModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="codeModalLabel">请选择适当的语言并粘贴代码</h4>
			</div>
			<div class="modal-body">
				<form class="form-inline">
					<div class="form-group">
						<h4 id="submitCodeError" class="text-danger">提交错误，请重试</h4>
						<input type="text" readonly="readonly" class="form-control" id="pid"
							value="<?php echo $pro_id;?>"> <select class="form-control"
							id="lang">
							<?php
	global $langArr;
	$i = 0;
	foreach ( $langArr as $row ) {
		if ($i == 0) {
			$i ++;
			continue;
		}
		if(isset($_SESSION['lang']) && $_SESSION['lang'] == $i)
			echo '<option selected="true" value="' . $i ++ . '">' . $row . '</option>';
		else
			echo '<option value="' . $i ++ . '">' . $row . '</option>';
	}
	?>
						</select>

					</div>
				</form>
				<label id="missPidError" class="text-danger">题目编号为空或非法</label>
				<p></p>
				<div class="form-group">
					<textarea class="form-control" rows="10" id="code"></textarea>
					<label id="emptyCodeError" class="text-danger">代码为空</label>
                    <label id="codeCE" class="text-danger"></label>
					<p></p>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
				<button type="button" class="btn btn-primary" id="submitCode">提交</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){

		$("#missPidError").hide();
		$("#emptyCodeError").hide();
		$("#submitCodeError").hide();

		$("#code").keypress(function(){
		    $("#submitCode").attr("class", "btn btn-primary");
            $("#codeCE").html("");
        })

		$("#submitCode").click(function(){

			$("#missPidError").hide();
			$("#emptyCodeError").hide();
			$("#submitCodeError").hide();
			var pid = $("#pid").val();
			var lang = $("#lang").val();
			var codes = $("#code").val();
            if($(this).attr("class") == 'btn btn-danger')
                submit(pid, lang, codes);
            else {
                var tmp = codes;
                tmp = ClearBr(tmp);
                tmp = trim(tmp);
                tmp = CTim(tmp);


                if (pid.length != 4)
                    $("#missPidError").show();
                else if (codes.length == 0)
                    $("#emptyCodeError").show();
                else if ( tmp.indexOf("publicclassMain") == -1 && lang == 3) {//Java need public class Main
                    $("#codeCE").html("在你的Java代码中未发现public class Main，请检查，若确定无误，请继续点击提交！");
                    $(this).attr("class", "btn btn-danger");
                } else if ( tmp.indexOf("package") != -1 && lang == 3) {//Java can't use package
                    $("#codeCE").html("在你的Java代码中发现package关键字，请检查，若确定无误，请继续点击提交！");
                    $(this).attr("class", "btn btn-danger");
                } else if(tmp.indexOf("publicclass") != -1 && lang != 3) {  //C/C++ do not include public class
                    $("#codeCE").html("在你的C/C++代码中发现Java的代码，请选择正确的语言，若确定无误，请继续点击提交！");
                    $(this).attr("class", "btn btn-danger");
                } else if((tmp.indexOf("iostream") || tmp.indexOf("usingnamespacestd;")) && lang == 1){ //C can't use C++ header file
                    $("#codeCE").html("在你的C代码中发现C++的代码，请选择正确的语言，若确定无误，请继续点击提交！");
                    $(this).attr("class", "btn btn-danger");
                } else if(tmp.indexOf('system("pause");') != -1){
                    $("#codeCE").html("在你的代码中发现system(\"pause\")，如果是注释，请继续点击提交！");
                    $(this).attr("class", "btn btn-danger");
                }
                else
                    submit(pid, lang, codes);
            }
		})

        function submit(pid, lang, codes) {
            $.post("/submit", {pro_id:pid, lang:lang, codes:codes<?php global $contest; if($contest) echo ',contestId:'.$contest;?>}, function(data){
                var obj = eval("(" + data + ")");
                if(obj['status'] == true) {
                    <?php
                    if($contest) {
                        echo 'location.href="/status?cid='.$contest.'";';
                    } else {
                        echo 'location.href="/status";';
                    }
                    ?>
                } else {
                    $("#submitCodeError").show();
                }
            })
        }
		 $("textarea").on('keydown', function(e) {
                if (e.keyCode == 9) {
                    e.preventDefault();
                    var indent = '    ';
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    var selected = window.getSelection().toString();
                    selected = indent + selected.replace(/\n/g, '\n' + indent);
                    this.value = this.value.substring(0, start) + selected
                            + this.value.substring(end);
                    this.setSelectionRange(start + indent.length, start
                            + selected.length);
                }
            })
	})
    //去除换行
    function ClearBr(key) {
        key = key.replace(/<\/?.+?>/g,"");
        key = key.replace(/[\r\n]/g, "");
        return key;
    }

    //去掉字符串两端的空格
    function trim(str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    }

    //去除字符串中间空格
    function CTim(str) {
        return str.replace(/\s/g,'');
    }
</script>
﻿<?php }?>
