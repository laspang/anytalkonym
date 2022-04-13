<?php
include('class/User.php');
require "settings.php";
$user = new User();
$message =  $user->register();
include('include/header.php');
?>
<script src="https://www.google.com/recaptcha/api.js"></script>
<title>Register 3AN Network | Anytalkonym</title>
<?php include('include/container.php');?>
<div class="container contact">
	<h2 style="padding: 0.7em 0px;"><?php echo $authsys;?> - <?php echo $signup;?>
	</h2>
	<div id="signupbox" class="col-md-7">
		<div class="card text-center">
			<div class="card-header"><?php echo $signup;?>
			</div>
			<div class="card-body">
				<form id="signupform" class="form-horizontal was-validated" role="form" method="POST" action="">
					<?php if ($message != '') { ?>
					<h4 class="card-title">
						<div id="login-alert" class="alert alert-danger col-sm-12"><?php echo $message; ?>
						</div>
					</h4>
					<?php } ?>
					<p>From version 32. ALL user have to register with your school email that end with @tmjh.tp.edu.tw .
						AND must use a xxx.xxxx@tmjh.tp.edu.tw format. Username can be a nick name and will be public.
						Nickname have to start with char and can only conatins letters, numbers, ".-_" .</p>
					<div class="input-group">
						<label for="firstname" class="col-md-3 input-group-text control-label">First Name*</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?php if (!empty($_POST["firstname"])) {
    echo $_POST["firstname"];
} ?>" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Please fill out this field.</div>
						</div>
					</div>
					<div class="input-group">
						<label for="lastname" class="col-md-3 input-group-text control-label">Last Name</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?php if (!empty($_POST["lastname"])) {
    echo $_POST["lastname"];
} ?>">
							<div class="valid-feedback">Good.</div>
							<div class="invalid-feedback">Input error emm...</div>
						</div>
					</div>
					<div class="input-group">
						<label for="username" class="col-md-3 input-group-text control-label">Username</label>
						<div class="col-md-9">
							<input type="text" class="form-control" name="username" pattern="[A-z][a-zA-Z0-9_.-]*$"
								placeholder="username" value="<?php if (!empty($_POST["username"])) {
    echo $_POST["username"];
} ?>" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Sorry Invaild user name.</div>
						</div>
					</div>
					<div class="input-group">
						<label for="email" class="col-md-3 input-group-text control-label">Email*</label>
						<div class="col-md-9">
							<input type="email" class="form-control" name="email"
								pattern="[0-9]*\.[0-9]+@tmjh\.tp\.edu\.tw" placeholder="Email Address" value="<?php if (!empty($_POST["email"])) {
    echo $_POST["email"];
} ?>" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Is that email? or did you filled it?</div>
						</div>
					</div>
					<div class="input-group">
						<label for="password" class="col-md-3 input-group-text control-label">Password*</label>
						<div class="col-md-9">
							<input type="password" class="form-control" name="passwd" placeholder="Password" required>
							<div class="valid-feedback">Valid.</div>
							<div class="invalid-feedback">Please fill out this field.</div>
						</div>
					</div>
					<script>
						function onSubmit(token) {
							document.getElementById("signupform").submit();
						}
					</script>
					<div class="input-group">
						<p>By clicking the submit button means you agree with out <a href="privacypolicy.html">privacy
								policy</a>.</p>
					</div>
					<div class="form-group">
						<div class="col-md-offset-3 col-md-9">
							<button id="btn-signup" name="register" value="register" class="g-recaptcha btn btn-primary"
								data-sitekey="<?php echo $set_recaptchapublickey;?>"
								data-callback='onSubmit' data-action='submit'><i class="icon-hand-right"></i> &nbsp
								Register</button>
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
