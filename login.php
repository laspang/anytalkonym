<?php
include('class/User.php');
require "settings.php";
$user = new User();
$errorMessage =  $user->login();
include('include/header.php');
?>
<title>3AN Network | Anytalkonym</title>
<script src="https://www.google.com/recaptcha/api.js"></script>
<?php include('include/container.php');?>
<div class="container contact">
	<h2 style="padding: 0.7em 0px;"><?php echo $authsys;?> - <?php echo $login;?>
	</h2>
	<div class="col-md-6">
		<div class="card text-center">
			<div class="card-header"><?php echo $login;?>
			</div>
			<div class="card-body">
				<!--start here-->
				<h4 class="card-title"><?php if ($errorMessage != '') { ?>
					<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $errorMessage; ?>
					</div>
					<?php } ?>
				</h4>
				<!--<div style="padding-top:30px" class="panel-body">-->
				<?php if (isset($_COOKIE["loginPass"]) && (isset($_COOKIE["loginId"]))) {?>
				<p><?php echo $remeberwarn;?>
				</p><?php }?>
				<p class="card-text">
				<form id="loginform" class="form-horizontal" role="form" method="POST" action="">
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-text"><i class="fa-solid fa-user-tie"></i> <?php echo $username;?></span>
						<input type="text" class="form-control" id="loginId" name="loginId" value="<?php if (isset($_COOKIE["loginId"])) {
    echo $_COOKIE["loginId"];
} ?>" placeholder="email">
					</div>
					<div style="margin-bottom: 25px" class="input-group">
						<span class="input-group-text"><i class="fa-solid fa-lock"></i> <?php echo $password;?></span>
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

							<button class="g-recaptcha btn btn-primary"
								data-sitekey="<?php echo $set_recaptchapublickey;?>"
								data-callback='onSubmit' data-action='submit'>login</button>
						</div>
					</div>

				</form>
			</div>
		</div>
		<div class="card-footer text-muted">
			<?php echo $noaccount;?>

		</div>
	</div>
</div>
<?php include('include/footer.php');
