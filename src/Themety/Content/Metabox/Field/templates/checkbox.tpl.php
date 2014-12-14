<input type="hidden" name="<?php echo $data['id']; ?>" value="0" />
<input
  type="checkbox"
  value="1"
  <?php echo $value->getValue() ? 'checked="checked"' : ""; ?>
  <?php echo $attributes; ?>
/>