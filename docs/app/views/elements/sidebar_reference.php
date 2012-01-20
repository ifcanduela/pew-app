<?php
$index = array(
    'index' => 'Introduction',
    'overview' => 'Overview',
    'example' => 'First example',
    'second' => 'Complicating things',
    'database' => 'Adding data',
);
?>

<div id="sidebar">
    <ul>
    <?php foreach ($index as $k => $v): ?>
        <li>
            <a <?php if ($k === $this->parameters['action']): ?> class="active" <?php endif; ?>
                href="<?php url("reference/$k"); ?>"><?php echo $v; ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>