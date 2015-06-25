<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php if(isset($submit_error)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
                Error 
            </div>
            <div class="panel-body">
                <?php echo $submit_error ?>
            </div>
        
        </div>
    </div>
</div>
<?php
    endif;
?>
<div class="row">
    <div class="col-md-12">
        
        <div class="form-import-wrapper">
            
    <?php
      $attributes = array('class' => 'form-horizontal');
      echo form_open_multipart('admin/importreferences/addoutbound',$attributes);
    ?>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <h2>Import outbound</h2>
        </div>
    </div>
            
    <div class="row">
        <div class="col-sm-offset-2 col-sm-10">
            
            <ul class="nav nav-tabs">
                <li role="presentation" data-divtarget="div-tab-upload" class="active" ><a href="#">Upload a file with references</a></li>
                <li role="presentation" data-divtarget="div-tab-manual"><a href="#">Add references manually</a></li>
            </ul>
        </div>
    </div>
    <br />
    
    <div id="div-tab-manual" class="tab-container" style="display: none;">
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Add references</label>
        <div class="col-sm-6">
          <textarea class="form-control" name="edreferences" placeholder="Enter references series"></textarea>
        </div>
    </div>  
    </div>      
    
    <!--
    <div class="form-group">
        
        <div class="col-sm-offset-2 col-sm-10">
          <span class="label label-info">Or upload a file</span>
        </div>
    </div>  
    -->
          
    <div id="div-tab-upload" class="tab-container">
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Upload a file</label>
        <div class="col-sm-6">
            <input type="file" name="edfile" >
            <p class="help-block">Select the file with the scanned sequenced references</p>
        </div>
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

<?php if(isset($parse_result['boxes']) || isset($parse_result['shippings'])): ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-info">
            <div class="panel-heading">
                Summary 
            </div>
            <div class="panel-body">
                Processed : 
                    <?php if(isset($parse_result['boxes'])): echo count($parse_result['boxes']); else: echo '0'; endif; echo ' Boxes '; ?>
                    |
                    <?php if(isset($parse_result['shippings'])): echo count($parse_result['shippings']); else: echo '0';  endif; echo ' Shippings '; ?>
            </div>
        
        </div>
    </div>
</div>
<?php
    endif;
?>

<?php
    if(isset($failed) && count($failed) > 0):
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-danger">
            <div class="panel-heading">
                Failure
            </div>
            <div class="panel-body">
            <?php foreach ($failed as $item ): ?>
                <?php if(isset($item['tag'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <strong><?php echo $item['tag'] ?></strong> 
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $item['message'] ?> 
                        <br /><br />
                    </div>
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
    if(isset($warning) && count($warning) > 0):
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                Warning
            </div>
            <div class="panel-body">
            <?php foreach ($warning as $item ): ?>
                <?php if(isset($item['tag'])): ?>
                <div class="row">
                    <div class="col-md-12">
                        <strong><?php echo $item['tag'] ?></strong> 
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $item['message'] ?> 
                        <br /><br />
                    </div>
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
                Successful outbound
            </div>
            <div class="panel-body">

            <?php 
                foreach ($success as $item): ?>
                <div class="row">
                    <div class="col-md-12">
                        <strong><?php echo $item['tag'] ?></strong>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php echo $item['message'] ?> 
                        <br /><br />
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        
        </div>
    </div>
</div>
<?php 
    endif;
?>
