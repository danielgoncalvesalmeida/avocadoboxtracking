<!-- employees_editpassword -->

<?php if(isset($flash_success)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success" id="flash_success">
                <?php echo $flash_success ?>
            </div> 
        </div>
    </div>
<?php endif; ?>

<?php if(isset($flash_error)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger" id="flash_error">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <?php echo $flash_error ?>
            </div> 
        </div>
    </div>
<?php endif; ?>



<?php 
    $attributes = array('class' => 'form-horizontal', 'id' => 'frm_editpassword', 'role' => 'form');
    echo form_open_multipart('admin/employees/editpassword', $attributes);
?>

    
    <div class="form-group <?php echo (strlen(form_error('edpassword1')) > 0 ? 'has-error' : '' ) ?>" id="cgPassword1">
        <label class="col-sm-2 control-label" for="iPassword1">Nouveau code d'accès (min 3 chiffres)</label>
        <div class="col-sm-4">
            <input class="form-control" type="password" name="edpassword1" id="iPassword1" placeholder="indiquez votre nouveau code d'accès" value="">
            <span class="help-block msg-required" style="display:none" >Champ obligatoire</span>
            <?php if(strlen(form_error('edpassword1')) > 0): ?>
                <span class="help-block"><?php echo form_error('edpassword1') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edpassword2')) > 0 ? 'has-error' : '' ) ?>" id="cgPassword2">
        <label class="col-sm-2 control-label" for="iPassword2">Confirmez le nouveau code</label>
        <div class="col-sm-4">
            <input class="form-control" type="password" name="edpassword2" id="iPassword2" placeholder="confirmez votre nouveau code d'accès" value="">
            <span class="help-block msg-required" style="display:none">Champ obligatoire</span>
            <?php if(strlen(form_error('edpassword2')) > 0): ?>
                <span class="help-block"><?php echo form_error('edpassword2') ?></span>
            <?php endif; ?>
        </div>
    </div>
    
        
    <div class="row">
        <div class="col-md-12">
        <div class="form-group">
            <div class="col-xs-12 col-sm-offset-2 col-sm-4">
                <input type="submit" class="btn btn-primary" name="submitSave" id="submitSave" value="Enregistrer">
                &nbsp;&nbsp;
                <a class="btn btn-danger" href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>">Annuler</a>
            </div>
        </div>

        </div>
    </div>

</form>
        
