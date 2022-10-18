<?php

/**
 * @var \pew\View $this
 * @var string $name
 */
$this->title("Welcome to Pew-Pew-Pew");
$this->layout("default.layout");
?>

<div class="page-title">
    <h1>Welcome, <?= $name ?>!</h1>
</div>

<p>This is <code><?= __FILE__ ?></code>.</p>
