<div class="row">
    <div class="col-md-6 col-md-offset-3 text-center">
        <?php if($status == false) {?>
            <h3>密码重置未完成，请重试</h3>
        <?php } else {?>
            <h3>密码重置已完成<br/>
                您的新密码为:<span class="text-danger"><?php echo $pass;?></span><br/>
                点击<a href="/">这里</a>登录，登录后请及时修改密码</h3>
        <?php } ?>
    </div>
</div>
<?php
?>