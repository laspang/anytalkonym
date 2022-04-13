<?php
include('class/User.php');
$user = new User();
$errorMessage =  $user->login();
include('include/header.php');
?>
<title>3AN Network | Anytalkonym</title>
<script src="https://www.google.com/recaptcha/api.js"></script>
<?php include('include/container.php');?>
<div class="container contact">
	<h2><?php echo $authsys;?> - <?php echo $login;?>
	</h2>
	<div class="col-md-6">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title"><?php echo $login;?>
				</div>
			</div>
			<div style="padding-top:30px" class="panel-body">
				<?php if ($errorMessage != '') { ?>
				<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $errorMessage; ?>
				</div>
				<?php } ?>
				<?php if (isset($_COOKIE["loginPass"]) && (isset($_COOKIE["loginId"]))) {?>
				<p><?php echo $remeberwarn;?>
				</p><?php }?>
				<form id="loginform" class="form-horizontal" role="form" method="POST" action="">
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i> <?php echo $username;?></span>
						<input type="text" class="form-control" id="loginId" name="loginId" value="<?php if (isset($_COOKIE["loginId"])) {
    echo $_COOKIE["loginId"];
} ?>" placeholder="email">
					</div>
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i> <?php echo $password;?></span>
						<input type="password" class="form-control" id="loginPass" name="loginPass" value="<?php if (isset($_COOKIE["loginPass"])) {
    echo $_COOKIE["loginPass"];
} ?>" placeholder="password">
					</div>
					<div class="input-group">
						<div class="checkbox">
							<label>
								<input type="checkbox" id="remember" name="remember" <?php if (isset($_COOKIE["loginId"])) { ?>
								checked <?php } ?>> <?php echo $remeberme;?>
							</label>
							<label><a href="forget_password.php"><?php echo $passwordrecover;?></a></label>
						</div>
					</div>
					<div style="margin-top:10px" class="form-group">
						<div class="col-sm-12 controls">
							<!--<input type="submit" name="login" value="Login" class="btn btn-info">	-->
							<script>
								function onSubmit(token) {
									document.getElementById("loginform").submit();
								}
							</script>

							<button class="g-recaptcha btn btn-info"
								data-sitekey="6Lfn1jUeAAAAAP3XzYsFsirZ_vQghxITdwFQQ35P" data-callback='onSubmit'
								data-action='submit'>login</button>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12 control">
							<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
								<?php echo $noaccount;?>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include('include/footer.php');
