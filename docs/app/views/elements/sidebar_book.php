<?php
$index = array(
    'index' => 'A programming environment',
    'configuration' => 'Reason for treason',
    'kitchen_sink' => 'Kitchen Sink',
);
?>

<div id="sidebar">
    <ul>
    <?php foreach ($index as $k => $v): ?>
        <li>
            <a <?php if ($k === $this->parameters['action']): ?> class="active" <?php endif; ?>
            href="<?php url("book/$k"); ?>"><?php echo $v; ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
