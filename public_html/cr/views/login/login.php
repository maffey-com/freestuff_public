<!-- Login block -->
<div class="navbar">
    <div class="navbar-inner">
        <h6><i class="icon-user"></i>Login</h6>
        <div class="nav pull-right">
            <a href="#" class="dropdown-toggle navbar-icon" data-toggle="dropdown"><i class="icon-cog"></i></a>
            <ul class="dropdown-menu pull-right">
                <li><a href="<?=(SITE_URL)?>login/forgotten_password"><i class="icon-refresh"></i>Recover password</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="well">
    <form action="<?=(APP_URL)?>auth/process_login" method="post" class="row-fluid">
        <div class="control-group">
            <label class="control-label">Username</label>
            <div class="controls">
                <input class="span12" type="text" name="username" placeholder="username"/>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label">Password:</label>
            <div class="controls">
                <input class="span12" type="password" name="password" placeholder="password"/>
            </div>
        </div>

        <div class="control-group">
            <div class="controls">
                <label class="checkbox inline">
                    <input type="checkbox" name="remember" class="styled" value="1" checked="checked">Remember me
                </label>
            </div>
        </div>
        <div class="login-btn">
            <input type="submit" value="Log me in" class="btn btn-danger btn-block"/>
        </div>
    </form>
</div>
<!-- /login block -->