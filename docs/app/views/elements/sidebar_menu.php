<?php if (isset($title)): ?>
<h2><?php echo $title; ?></h2>
<?php endif; ?>
<ul>
    <li><a href="<?php url('albums/view/1'); ?>">Featured Album 1</a></li>
    <li><a href="<?php url('albums/view/2'); ?>">Featured Album 2</a></li>
</ul>

<p></p>

<ul>
    <li><a href="<?php url('albums/index'); ?>">Other albums</a></li>
    <li><a href="<?php url('page/equipment'); ?>">My Equipment</a></li>
    <li><a href="<?php url('page/friends'); ?>">My Friends</a></li>
    <li><a href="<?php url('page/about_me'); ?>">About me</a></li>
</ul>
    