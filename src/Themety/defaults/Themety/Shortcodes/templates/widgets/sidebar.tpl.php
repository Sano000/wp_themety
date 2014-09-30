<?php if($id): ?>
<div id="<?php echo $id; ?>" class="<?php echo $class; ?>">
  <?php dynamic_sidebar($id); ?>
</div>
<?php endif; ?>