<?php
/**
 * @var \pew\View $this
 */
$this->title("Sign up");
$this->layout("users.layout");
?>
<div class="user-wrapper">
    <form action="<?= url(here()) ?>" method="POST" id="form-users-signup">
        <h1>Sign Up</h1>

        <?php foreach (flash() as $class => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <p class="alert <?= $class ?>"><?= $message ?></p>    
            <?php endforeach ?>
        <?php endforeach ?>

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="form-users-signup-username">
        </div>

        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="form-users-signup-password">
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirm Password:</label>
            <input type="password" name="password_confirm" id="form-users-signup-password-confirm">
        </div>

        <div class="form-group">
            <label for="email">E-mail address:</label>
            <input type="email" name="email" id="form-users-signup-email">
        </div>
        
        <div class="form-actions">
            <a href="<?= url("login") ?>">Log in</a>
            <button type="submit" id="form-users-signup-submit">Sign Up</button>
        </div>
    </form>
</div>
