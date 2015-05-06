<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
  $attributes = array('class' => 'form-horizontal');
  echo form_open('admin/importreferences',$attributes);
?>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
        <textarea name="edreferences" cols="80" rows="20"></textarea>
    </div>
        
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" name="submitReferences" value="1" class="btn btn-default">Import references</button>
    </div>
</div>


</form>

