<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>


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
            
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Add references</label>
        <div class="col-sm-6">
          <textarea class="form-control" name="edreferences" placeholder="Enter references series"></textarea>
        </div>
    </div>  
            
    <div class="form-group">
        
        <div class="col-sm-offset-2 col-sm-10">
          <span class="label label-info">Or upload a file</span>
        </div>
    </div>  
            
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">Upload a file</label>
        <div class="col-sm-6">
            <input type="file" name="edfile" >
            <p class="help-block">Select the file with the scanned sequenced references</p>
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
    if(isset($conflict) && count($conflict) > 0):
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-warning">
            <div class="panel-heading">
                Errors
            </div>
            <div class="panel-body">
            <?php foreach ($conflict as $item ):
                $date = new DateTime($item['date_outbound']);
                if(isset($item['cycled']))
                    $date_in = new DateTime($item['date_inbound']);
            ?>
                <div class="row">
                    <div class="col-md-12">
                        <?php if(!isset($item['cycled'])): ?>
                            <strong><?php echo $item['pack'] ?></strong> -> out on <?php echo date_format($date, 'd/m/Y') ?> in shipping <strong><?php echo $item['shipping'] ?></strong>
                        <?php elseif(isset($item['cycled'])): ?>
                            <strong><?php echo $item['pack'] ?></strong> -> out on <?php echo date_format($date, 'd/m/Y') ?> in shipping <strong><?php echo $item['shipping'] ?></strong> and returned on <?php echo date_format($date_in, 'd/m/Y') ?>
                        <?php endif; ?> 
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
                foreach ($success as $shipping => $packs):
                    foreach ($packs as $pack ): ?>
                <div class="row">
                    <div class="col-md-12">Pack <strong><?php echo $pack ?></strong> outbound for shipping <strong><?php echo $shipping ?></strong></div>
                </div>
            <?php 
                    endforeach;
                endforeach; ?>
            </div>
        
        </div>
    </div>
</div>
<?php 
    endif;
?>
