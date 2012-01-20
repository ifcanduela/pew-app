<h1>Login</h1>
<?php
    echo Pew::Get('Session')->get_flash();
?>
<form action="" method="post">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username">
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
    </div>
    <div>
        <input type="submit" value="Go">
    </div>
</form>
