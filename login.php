<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
  	<title>REST application</title>
  	<meta name="description" content="Login to see your tasks. />
  	<meta name="keywords" content="REST, tasks, login, login form, register" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />	

	<!-- stylesheets -->
  	<link rel="stylesheet" href="/css/style.css" type="text/css" media="screen" />
  	<link rel="stylesheet" href="/css/slide.css" type="text/css" media="screen" />
	
   
    <!-- jQuery - the core -->
	<script src="/js/jquery-1.3.2.min.js" type="text/javascript"></script>
	<!-- Sliding effect -->
	<script src="/js/slide.js" type="text/javascript"></script>
	
</head>

<body>
<!-- Panel -->
<div id="toppanel">
    <div id="panel">
        <div class="content clearfix">
            <form class="clearfix" action="/loginsubmit.php" method="post">
                <h1>User Login</h1>
                <label class="grey" for="username">Username:</label>
                <input class="field" type="text" name="username" id="username" 
                       value="" size="23" />
                <label class="grey" for="uri">Link:</label>
                <input class="field" type="text" name="uri" id="uri" value=""
                       size="23" />
                <label class="grey" for="password">Password:</label>
                <input class="field" type="password" name="password" 
                       id="password" size="23" />
                <label>
                    <input name="rememberme" id="rememberme" type="checkbox" 
                           checked="checked" value="forever" /> 
                    &nbsp;Remember me
                </label>
                <div class="clear"></div>
                <input type="submit" name="submit" value="Login" 
                       class="bt_login" />
		<!--<a class="lost-pwd" href="#">Lost your password?</a>-->
            </form>
        </div>
        <!--<div class="left right">			
            <form action="#" method="post">
                <h1>Not a User yet? Register!</h1>				
                <label class="grey" for="signup">Username:</label>
                <input class="field" type="text" name="signup" id="signup" 
                       value="" size="23" />
                <label class="grey" for="email">Email:</label>
                <input class="field" type="text" name="email" id="email" 
                       size="23" />
                <label>A password will be e-mailed to you.</label>
                <input type="submit" name="submit" value="Register" 
                       class="bt_register" />
	    </form>
	</div>-->
    </div>
</div> <!-- /login -->	

<!-- The tab on top -->	
<div class="tab">
    <ul class="login">
        <li class="left">&nbsp;</li>
        <li>Hello Guest!</li>
        <li class="sep">|</li>
        <li id="toggle">
            <a id="open" class="open" href="#">Log In | Register</a>
            <a id="close" style="display: none;" class="close" 
               href="#">Close Panel</a>			
        </li>
        <li class="right">&nbsp;</li>
    </ul> 
</div> <!-- / top -->
	
</div> <!--panel -->
<? if (!isset($_SESSION)) session_start(); ?>
<? if (isset($_SESSION["flash"])): ?>
<div id="container">
    <div id="content" style= "padding-center:100PX;">
        <h1>Login to see your tasks </h1>
        <font color = "red">
            <?= $_SESSION['flash']; ?>
        </font>
    </div>
</div>
<? unset($_SESSION['flash']); ?>
<? endif; ?>

</body>
</html>
