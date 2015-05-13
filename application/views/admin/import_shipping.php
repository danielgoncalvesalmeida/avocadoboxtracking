<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


<div class="row">
    <div class="col-md-12">
        
        <div class="form-import-wrapper">
            
    <?php
      $attributes = array('class' => 'form-horizontal');
      echo form_open_multipart('admin/importreferences/addshipping',$attributes);
    ?>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <h2>Import shipping</h2>
        </div>
    </div>
            
            
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Upload a file</label>
        <div class="col-sm-6">
            <input type="file" name="edfile" >
            <p class="help-block">Select the file with the shipping information</p>
        </div>
    </div> 
            
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
          <button type="submit" name="submitImport" class="btn btn-primary">Import</button>
        </div>
    </div>

    </form>
        </div>
    </div>
</div>

<?php
    if(isset($failed) && count($failed) > 0):
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                Failures
            </div>
            <div class="panel-body">
            <?php foreach ($failed as $item ): ?>
                <div class="row">
                    <div class="col-md-12">Line <strong><?php echo $item['line'] ?></strong> | <?php echo $item['message'] ?><br /></div>
                </div>
            <?php endforeach; ?>
            </div>
        
        </div>
    </div>
</div>
<?php 
    endif;
?>
    
<?php
    if(isset($success) && count($success) > 0):
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-success">
            <div class="panel-heading">
                Successfully imported/updated
            </div>
            <div class="panel-body">

            <?php foreach ($success as $item): ?>
                <div class="row">
                    <div class="col-md-12">Line <strong><?php echo $item['line'] ?></strong> | <?php echo $item['message'] ?></div>
                </div>
            <?php endforeach; ?>
            </div>
        
        </div>
    </div>
</div>
<?php 
    endif;
?>


