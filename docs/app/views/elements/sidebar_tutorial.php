<div id="sidebar">
    <ul>
    <?php foreach ($this->index as $page): list($k, $v) = $page ?>
        <li>
            <a <?php if ($k === $this->parameters['action']): ?> class="active" <?php endif; ?>
                href="<?php url("tutorial/$k"); ?>"><?php echo $v; ?></a>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
