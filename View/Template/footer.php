﻿<nav class="navbar navbar-inverse" style="background:rgb(248,248,248); border:0px solid white" role="navigation">
	<div class="container">

		<div class="row">
			<div class="col-md-6 col-md-offset-3 text-center">

				<h3>Welcome to NUC Online Judge</h3>
				<table class="table">
					<tr>
						<td colspan="3">中北大学ACM-ICPC程序设计创新实验室 版权所有</td>
					</tr>
					<tr>
						<td colspan="3">NUC Online Judge Version 2017 || Developed & Design By <a href="mailto:lz842063523@foxmail.com">gscsdlz</a></td>
						
					</tr>
				</table>
							<h4>执行时间:<?php global $stime; global $serverId; echo  number_format(microtime(true) - $stime, 5, '.', '');?> 服务器时间:<?php echo date('Y-m-d H:i:s', time()); echo ' @'.$serverId;?></h4>
			</div>
		</div>

	</div>
</nav>
﻿
</body>
</html>
