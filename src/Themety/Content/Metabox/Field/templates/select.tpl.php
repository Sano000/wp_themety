<?php if($options && is_array($options)): ?>
<select <?php echo $attributes; ?>>
  <?php foreach($options as $key => $title): ?>
  <option value="<?php echo $key; ?>" <?php echo $key == $value ? 'selected' : ''; ?>><?php echo $title; ?></option>
  <?php endforeach; ?>
</select>
<?php endif; ?>



