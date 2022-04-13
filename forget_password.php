<?php
include('class/User.php');
$user = new User();
$errorMessage = '';
if (!empty($_POST['forgetpassword']) && $_POST['forgetpassword']) {
    $errorMessage =  $user->resetPassword();
}
include('include/header.php');
?>
<title>3AN network | Anytalkonym</title>
<?php include('include/container.php');?>
<div class="container contact">
	<h2 style="padding: 0.7em 0px;">User auth system - Password recovery</h2>
	<div class="col-md-6">
		<div class="panel panel-info">
			<div class="card text-center">
				<div class="card-header"><?php echo $forgotpass;?>
				</div>
				<div style="padding-top:30px" class="card-body">
					<?php if ($errorMessage != '') { ?>
					<h5 class="card-title">
						<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $errorMessage; ?>
						</div>
					</h5>
					<?php } ?>
					<form id="loginform" class="form-horizontal" role="form" method="POST" action="">
						<div style="margin-bottom: 25px" class="input-group">
							<span class="input-group-text">
								<i class="fa-solid fa-envelope"></i>
							</span>
							<input type="email" class="form-control" id="email" name="email" placeholder="email"
								required>
						</div>
						<div style="margin-top:10px" class="input-group">
							<div class="col-sm-12 controls">
								<input type="submit" name="forgetpassword" value="Submit" class="btn btn-primary">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="card-footer text-muted">
				If You've already an account!
				<a href="login.php">
					Log In
				</a>Here
			</div>
		</div>
	</div>
	<?php include('include/footer.php');
