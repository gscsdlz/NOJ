<?php
global $contest;
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1 text-center">
        <?php
        if($args[1]['options'] < 0 && !(isset($_SESSION['user_id']) && ($_SESSION['privilege'][0] == 1 || isset($_SESSION['privilege'][$contest])))) {
            unset($args[1]);
            echo '<div class="well"><h1 class="text-danger">本场比赛已经隐藏排名</h1></div>';
        } else {
            unset($args[1]['options']);
        ?>
<?php

    echo '<h1>'.$args[0][count($args[0]) - 1][0]['contest_name'].'</h1><p>&nbsp;</p>';
    unset($args[0][count($args[0]) - 1]);
?>
	<form class="form-inline" role="form">
		<div class="form-group">
			<label>显示小组排名</label>
			<select class="form-control" id="groupfilter">
			<option value="">ALL</option>
<?php
    if(isset($args[1]['teams']) && count($args[1]['teams']) != 0) {
        foreach($args[1]['teams'] as $value) {
            echo '<option value="'.$value.'">'.$value.'</option>';
        }
        unset ($args[1]['teams']);
    }
?>
			</select>
		</div>
		<div class="form-group">
			<button id="filter" type="button" class="btn btn-primary">筛选</button>
		</div>
		<div class="form-group">
			<button id="export" type="button" class="btn btn-success " onclick="window.location.href='<?php echo '/Src/File/contest_rankList'.$contest.'.csv';?>'">导出CSV数据</button>
		</div>
<?php
	if(isset($args[1]['ttl'])) {
		echo '<br /><label class="text-danger">数据来自缓存，每5分钟更新一次, 请勿频繁刷新 下次更新还有'.$args[1]['ttl'].'s</label>';
		unset($args[1]['ttl']);
	}
	$pageN = (int)$args[1]['pageN'];
	$pageT = (int)$args[1]['pageT'];
	$auNum = (int)$args[1]['medal'][0]; //金牌数
    $agNum = (int)$args[1]['medal'][1];
    $cuNum = (int)$args[1]['medal'][2];
    $feNum = (int)$args[1]['medal'][3];
	$pageP = RANKPAGEMAXSIZE;
	$team = $args[1]['team'];
	$oi_mode = (int)$args[1]['oi_mode'];
/**
 * 必须释放，保证下面对于args[1]的操作的正确性
 */
	unset($args[1]['pageN']);
	unset($args[1]['pageT']);
	unset($args[1]['medal']);
    unset($args[1]['team']);
    unset($args[1]['oi_mode']);
?>
	
	</form>
<nav aria-label="Page navigation">
  <ul class="pagination pagination-lg">
    <li>
<?php 
	echo '<a href="/contest/ranklist/'.$contest.'/0/'.$team.'" aria-label="Previous">';
?>
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
<?php
	for($i = 0; $i < $pageT; ++$i)
		echo '<li><a href="/contest/ranklist/'.$contest.'/'.$i.'/'.$team.'">'.$i.'</a></li>';
?>
	
    <li>
<?php 
	echo '<a href="/contest/ranklist/'.$contest.'/'.($pageT - 1).'/'.$team.'" aria-label="Next">';
?>
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

		<table class="table table-hover table-bordered"
			style="vertical-align: middle; margin-top:20px;">
			<tr>
				<th>排名</th>
				<th>用户名</th>
				<th>所在小组</th>
                <?php if($oi_mode != 1) {?>
				    <th>通过题目总数</th>
				    <th>总时长</th>
                <?php } else {?>
                    <th>分数</th>
                    <th>提交次数</th>
                <?php }?>
			<?php
			
			$ids = array();
			if (isset ( $args [0] ) && count ( $args [0] )) {
				foreach ( $args [0] as $row ) {
					echo '<th><a href="/contest/problem/' . $contest . '/' . $row . '">' . $row . '</a></th>';
					$ids[] = $row;
				}
			}
			?>
		</tr>
		<?php

        if($oi_mode == 1) {
            if (isset ($args [1]) && count($args [1])) {
                foreach ($args [1] as $row) {
                    $k = $row[5];
                    echo '<tr><td>' . $k;
                    if ($row[1] > 0 && $k <= $feNum + $cuNum + $agNum + $auNum) {
                        echo '<img width="30px" height="30px" src="/Src/Image/';
                        if ($k <= $auNum)
                            echo 'au.svg';
                        else if ($k <= $agNum + $auNum)
                            echo 'ag.svg';
                        else if ($k <= $cuNum + $agNum + $auNum)
                            echo 'cu.svg';
                        else
                            echo 'fe.svg';
                        echo '" />';
                    }
                    echo '</td>';
                    echo '<td><a href="/user/show/' . $row [2] . '">' . $row [2] . '(' . $row[4] . ')</a></td>';
                    echo '<td>' . $row[3] . '</td>';
                    echo '<td>' . $row [0] . '</td>';
                    echo '<td>' . $row [1] . '</td>';
                    foreach ($ids as $i) {
                        if (isset ($row [$i])) {
                            echo '<td ';
                            /*
                             * $row[$i] -> [
                             *  0=>socre
                             *  1=>submit
                             *  2=>ac?
                             * ]
                             */

                            if ($row [$i] [2] == true) {// 完整通过一道题目
                                echo 'class="bg-success">';
                                echo $row[$i][0];
                                echo '<br/>(' . $row [$i] [1] . ')';
                            } else {// 有提交记录
                                echo 'class="bg-danger">';
                                echo $row[$i][0];
                                echo '<br/>(' . $row [$i] [1] . ')';
                            }
                            echo '</td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';
                }
            }
        } else {
            if (isset ($args [1]) && count($args [1])) {
                foreach ($args [1] as $row) {
                    $k = $row[5];
                    echo '<tr><td>' . $k;
                    if ($row[1] > 0 && $k <= $feNum + $cuNum + $agNum + $auNum) {
                        echo '<img width="30px" height="30px" src="/Src/Image/';
                        if ($k <= $auNum)
                            echo 'au.svg';
                        else if ($k <= $agNum + $auNum)
                            echo 'ag.svg';
                        else if ($k <= $cuNum + $agNum + $auNum)
                            echo 'cu.svg';
                        else
                            echo 'fe.svg';
                        echo '" />';
                    }
                    echo '</td>';
                    echo '<td><a href="/user/show/' . $row [2] . '">' . $row [2] . '(' . $row[4] . ')</a></td>';
                    echo '<td>' . $row[3] . '</td>';
                    echo '<td>' . $row [1] . '</td>';
                    echo '<td>' . format_time($row [0]) . '</td>';
                    foreach ($ids as $i) {
                        if (isset ($row [$i])) {
                            echo '<td ';
                            if (isset ($row [$i] [2]) && $row [$i] [2] == 1) {// 第一个通过该题
                                echo 'class="bg-primary">' . format_time($row [$i] [0]);
                                if ($row[$i][1])
                                    echo '<br/>(-' . $row [$i] [1] . ')';
                            } else if ($row [$i] [0] && !$row [$i] [1]) // 通过且没有罚时
                                echo 'class="bg-success">' . format_time($row [$i] [0]);
                            else if ($row [$i] [0] && $row [$i] [1]) // 通过且有罚时
                                echo 'class="bg-success">' . format_time($row [$i] [0]) . '<br/>(-' . $row [$i] [1] . ')';
                            else
                                echo 'class="bg-danger text-center">-' . $row [$i] [1];
                            echo '</td>';
                        } else {
                            echo '<td></td>';
                        }
                    }
                    echo '</tr>';
                }
            }
        }
		?>
	</table>
    <nav aria-label="Page navigation">
        <ul class="pagination pagination-lg">
            <li>
                <?php
                echo '<a href="/contest/ranklist/'.$contest.'/0/'.$team.'" aria-label="Previous">';
                ?>
                <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
            for($i = 0; $i < $pageT; ++$i)
                echo '<li><a href="/contest/ranklist/'.$contest.'/'.$i.'/'.$team.'">'.$i.'</a></li>';
            ?>

            <li>
                <?php
                echo '<a href="/contest/ranklist/'.$contest.'/'.($pageT - 1).'/'.$team.'" aria-label="Next">';
                ?>
                <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
	</div>
</div>
<script>
	$(document).ready(function(){
		
//		$(".table-bordered tr").each(function(index, item){
//			if(index != 0)
//			groupSet.add($(this).children().eq(2).html());
//		})
//		groupSet.forEach(function(item){
//			$("#groupfilter").append('<option value='+ item.toString() +'>'+item.toString()+'</option>');
//		})
		$("#filter").click(function(){
			var group = $("#groupfilter").val();
//			$(".table-bordered tr").each(function(index, item){
//				if(index != 0 && $(this).children().eq(2).html() != group && group != "")
//					$(this).hide();
//				else
//					$(this).show();
//			})
            window.location.href = "/contest/ranklist/<?php echo $contest;?>/<?php echo $pageN;?>/"+ group;
		})
	})
</script>
<?php }?>
