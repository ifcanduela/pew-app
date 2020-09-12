<?php
/**
 * @var \pew\View $this
 */
$this->title("Log in");
$this->layout("users.layout");
?>
<div class="user-wrapper">
    <form action="<?= here() ?>" method="POST" id="form-users-login">
        <h1>Log In</h1>

        <?php foreach (flash() as $class => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p class="alert <?= $class ?>"><?= $message ?></p>
            <?php endforeach ?>
        <?php endforeach ?>

        <div class="form-group">
            <label for="user-username">Username:</label>
            <input type="text" name="username" id="user-username">
        </div>

        <div class="form-group">
            <label for="user-password">Password:</label>
            <input type="password" name="password" id="user-password">
        </div>

        <div class="form-group">
            <label for="user-remember-me">
                <input type="hidden" name="remember_me" value="">
                <input type="checkbox" name="remember_me" id="user-remember-me" value="1">
                Remember me for 30 days
            </label>
        </div>

        <div class="form-actions">
            <a href="<?= url("signup") ?>">Sign up</a>
            <button type="submit" id="form-users-login-submit">Log In</button>
        </div>
    </form>
</div>
