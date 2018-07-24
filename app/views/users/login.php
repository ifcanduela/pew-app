<?php
/**
 * @var \pew\View $this
 */
$this->title("Log in");
$this->layout("users.layout");
?>
<div class="user-wrapper">
    <form action="<?= url(here()) ?>" method="POST" id="form-users-login">
        <h1>Log In</h1>

        <?php foreach (flash() as $class => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p class="alert <?= $class ?>"><?= $message ?></p>    
            <?php endforeach ?>
        <?php endforeach ?>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="form-users-login-username">
        </div>
        
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="form-users-login-password">
        </div>

        <div class="form-actions">
            <a href="<?= url("signup") ?>">Sign up</a>
            <button type="submit" id="form-users-login-submit">Log In</button>
        </div>
    </form>
</div>
