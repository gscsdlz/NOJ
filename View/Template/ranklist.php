<div class="row">
	<div class="col-md-8 col-md-offset-2 text-center">
		<table class="table table-hover text-left">
			<tr>
				<th>排名</th>
				<th>用户名</th>
				<th>签名</th>
				<th>通过题目数</th>
				<th>提交题目数</th>
				<th>AC率</th>
			</tr>
			<?php
				if(isset($args)) {
					
					foreach($args[1] as $row) {
						if(isset($_SESSION['username']) && $_SESSION['username'] == $row[0]){
							echo '<tr class="bg-danger">';
						} else {
							echo '<tr>';
						}
						echo '<td>'.$row[4].'</td>';
						echo '<td><a href="/user/show/'.$row[0].'">'.$row[0].'</a></td>';
						echo '<td>'.htmlspecialchars($row[1]).'</td>';
						echo '<td>'.$row[2].'</td>';
						echo '<td>'.$row[3].'</td>';
						if($row[3] == 0)
							$row[3] = 1;
						echo '<td>'.number_format($row[2] / $row[3] * 100, 2, '.', '').'%</td>';
						echo "</tr>\n";
					}
				}
			?>
		</table>
		<nav>
			<ul class="pagination pagination-lg text-center">
				<li><a href="/rank/page/0">首页</a></li>
				<li><a href="#">...</a></li>
				<li><a href="/rank/page/<?php if(isset($_GET['id']) && $_GET['id'] > 0) echo $_GET['id'] - 1;?>">上一页</a></li>
				<li><a href="#">...</a></li>
				<li><a href="/rank/page/
<?php 
				if(isset($_GET['id'])) {
					if($_GET['id'] < ($args[0][1]  + 1)/ $args[0][0] - 1) 
						echo $_GET['id'] + 1;
				} else {
					echo '1';
				}
?>">下一页</a></li>
				<li><a href="#">...</a></li>
				<li><a href="/rank/page/<?php echo (int)(($args[0][1] + 1) / $args[0][0])?>">尾页</a></li>
			</ul>
		</nav>
	</div>
</div>
<?php
