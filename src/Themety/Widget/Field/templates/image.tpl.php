<div class="widgets-upload-image" data-multi="<?php echo $data['field']['multi']; ?>">
  <div class="controls">
    <a href="" class="add"><?php echo __('Add Image'); ?></a>
  </div>

  <div class="preview-item template hidden">
    <div class="preview-img" style="background-image: url()"></div>
    <div class="controls">
      <a href="" class="remove"><?php echo __('Remove'); ?></a>
    </div>
    <input type="hidden" <?php echo $attributes; ?> />
  </div>

  <div class="preview">
    <?php $value = is_array($value) ? $value : array($value); ?>
    <?php foreach ($value as $itemId): ?>
      <?php $att = wp_get_attachment_image_src($itemId, 'admin-preview'); ?>
      <?php if($att): ?>
      <div class="preview-item">
        <div class="preview-img" style="background-image: url(<?php echo $att[0]; ?>)"></div>
        <div class="controls">
          <a href="" class="remove"><?php echo __('Remove'); ?></a>
        </div>
        <input type="hidden" <?php echo $attributes; ?> value="<?php echo $itemId; ?>" />
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
