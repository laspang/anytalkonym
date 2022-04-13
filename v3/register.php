<?php
include('class/User.php');
$user = new User();
$message =  $user->register();
include('include/header.php');
?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<title>Register 3AN Network | Anytalkonym</title>
<?php include('include/container.php');?>
<div class="container contact">
	<h2>User auth system - register</h2>
	<div id="signupbox" class="col-md-7">
		<div class="panel panel-info">
			<div class="panel-heading">
				<div class="panel-title">Sign Up</div>
			</div>
			<div class="panel-body">
				<form id="signupform" class="form-horizontal" role="form" method="POST" action="">
					<?php if ($message != '') { ?>
					<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $message; ?>
					</div>
					<?php } ?>
					<p>From version 32. ALL user have to register with your school email that end with @tmjh.tp.edu.tw .
						AND must use a xxx.xxxx@tmjh.tp.edu.tw format. Username can be a nick name and will be public.
						Nickname have to start with char and can only conatins letters, numbers, ".-_" .</p>
					<div class="form-group">
						<label for="firstname" class="col-md-3 control-label">First Name*</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?php if (!empty($_POST["firstname"])) {
    echo $_POST["firstname"];
} ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="lastname" class="col-md-3 control-label">Last Name</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php if (!empty($_POST["lastname"])) {
    echo $_POST["lastname"];
} ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="username" class="col-md-3 control-label">Username</label>
						<div class="col-md-9">
							<input required type="text" class="form-control" name="username" pattern="[A-z][a-zA-Z0-9_.-]*$"
								placeholder="username" value="<?php if (!empty($_POST["username"])) {
    echo $_POST["username"];
} ?>" >
						</div>
					</div>
					<div class="form-group">
						<label for="email" class="col-md-3 control-label">Email*</label>
						<div class="col-md-9">
							<input type="email" class="form-control" name="email"
								pattern="[0-9]*\.[0-9]+@tmjh\.tp\.edu\.tw" placeholder="Email Address" value="<?php if (!empty($_POST["email"])) {
    echo $_POST["email"];
} ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="col-md-3 control-label">Password*</label>
						<div class="col-md-9">
							<input type="password" class="form-control" name="passwd" placeholder="Password" required>
						</div>
					</div>
					<script>
						function onSubmit(token) {
							document.getElementById("signupform").submit();
						}
					</script>
					<div class="form-group">
						<p>By clicking the submit button means you agree with out <a href="privacypolicy.html">privacy
								policy</a>.</p>
					</div>
					<div class="form-group">
						<div class="col-md-offset-3 col-md-9">
							<button id="btn-signup" name="register" value="register" class="g-recaptcha btn btn-info"
								data-sitekey="6Lfn1jUeAAAAAP3XzYsFsirZ_vQghxITdwFQQ35P" data-callback='onSubmit'
								data-action='submit'><i class="icon-hand-right"></i> &nbsp Register</button>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12 control">
							<div style="border-top: 1px solid#888; padding-top:15px; font-size:85%">
								If You've already an account!
								<a href="login.php">
									Log In
								</a>Here
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php include('include/footer.php');
