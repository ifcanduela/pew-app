<?php
$current = $prev = $next = null;

foreach ($this->index as $i => $a) {
    if ($a[0] === $this->parameters['action']) {
        $current = $this->index[$i];
        $prev = deref($this->index, $i - 1);
        $next = deref($this->index, $i + 1);
    }
}

?>

<div class="chapters">
    <?php if (isset($prev)): ?>
    <a class="prev" href="<?php url($this->parameters['controller'] . '/' . $prev[0]); ?>">&laquo; Previous: <?php echo $prev[1]; ?></a>
    <?php endif; ?>
    
    <?php if (isset($next)): ?>
    <a class="next" href="<?php url($this->parameters['controller'] . '/' . $next[0]); ?>">Next: <?php echo $next[1]; ?> &raquo;</a>
    <?php endif; ?>
</div>
