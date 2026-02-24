<!-- Login block -->
<div class="navbar">
    <div class="navbar-inner">
        <h6>Please enter your email address and we will send your password</h6>
    </div>
</div>
<div class="well">
    <form action="<?=(APP_URL)?>auth/process_forgotten_password" method="post" class="row-fluid">
        <div class="control-group">
            <label class="control-label">Email</label>
            <div class="controls">
                <input class="span12" type="email" name="email" placeholder="Email"/>
            </div>
        </div>

        <div class="login-btn">
            <input type="submit" value="Send me my password" class="btn btn-danger btn-block"/>
			<a class="btn btn-primary btn-block" href="<?=(APP_URL)?>auth">Back to Login screen</a>
        </div>
    </form>
</div>
<!-- /login block -->

