<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="panel panel-success">
			<div class="panel-heading">
<?php
global $contest;
global $statusArr;
global $langArr;
if(isset($_SESSION['user_id']) && (($_SESSION['user_id'] == $user_id && ($options == 0 || $options == -1)) || $_SESSION['privilege'][0] == 1 || isset($_SESSION['privilege'][1][$contest]))) { ?>
                <h3 class="text-success">用户<a href="/user/show/<?php echo $username;?>"><?php echo $username.'('.htmlspecialchars($nickname).')';?></a>的提交记录  记录号：<?php echo $submit_id; ?></h3>
                <?php if($contest) {?>
                    <h4 class="text-muted">题目编号：<a href="/contest/problem/<?php echo $contest.'/' . $pro_id?>"><?php echo $pro_id ;?></a></h4>
                <?php }  else {?>
                    <h4 class="text-muted">题目编号：<a href="/problem/show/<?php echo  $pro_id ;?>"><?php echo $pro_id ?></a></h4>
                <?php } ?>
                <h4 class="text-muted">提交时间：  <?php echo date ( "Y-m-d h:s:i", $submit_time ) ;?></h4>
                <h4 class="text-muted">运行时间： <?php echo $run_time;?>MS 运行内存： <?php echo  $run_memory ;?>KB</h4>
                <h4 class="text-danger">语言： <?php echo $langArr [$lang];?> </h4>
                <h4 class="text-danger">状态： <?php echo $statusArr [$status];?></h4>
            </div>
            <div class="panel-body">
<pre class="line-numbers command-line data-line"><code class="language-<?php if ($lang == 1) echo 'c';  else if ($lang == 2)  echo 'cpp';  else if ($lang == 3) echo 'java';?>" style="font-size: 18px;"><?php if(isset($code)) echo htmlspecialchars($code); else echo htmlspecialchars($info); ?></code>
</pre>
            </div>
        </div>
<?php } else { ?>
	<h3 class="text-danger text-center">权限不足，或者未登录 3秒后自动跳转<a href="/status<?php if($contest) echo '?cid='.$contest;?>">立即跳转</a></h3>
<script>
	$(document).ready(function(){
		var t=setTimeout("history.go(-1)", 3000)
	})
</script>
<?php }?>
	</div>
</div>
