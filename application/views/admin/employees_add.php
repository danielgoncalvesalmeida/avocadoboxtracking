<!-- employees_add -->
        
<?php if(isset($flash_success)): ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-success">
                <?php echo $flash_success ?>
            </div>
        
            <div class="form-group">
                <div class="controls">
                    <a class="btn btn-default" href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>">OK</a>
                    
                    <?php if(isset($id_user)): ?><a class="btn btn-default" href="<?php echo sbase_url().'admin/employees/edit/'.$id_user ?>">Edit</a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
        
<?php else: ?>
        
    <div class="row">
        <div class="col-md-12">
            <a href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>" class="btn btn-default pull-right">Back</a>
            <h3>Add an employée</h3>
            <br />
        </div>
    </div>

        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'frm_add', 'role' => 'form');
            echo form_open('admin/employees/add',$attributes);
        ?>

    <div class="form-group <?php echo (strlen(form_error('edfirstname')) > 0 ? 'has-error' : '' ) ?>" id="cgFirstname">
        <label class="col-sm-2 control-label" for="iFirstname">Firstname</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edfirstname" id="iFirstname" placeholder="firstname" value="<?php echo set_value('edfirstname'); ?>">
            <span class="help-block msg-required" style="display:none">Mandatory field</span>
            <?php if(strlen(form_error('edfirstname')) > 0): ?>
                <span class="help-block"><?php echo form_error('edfirstname') ?></span>
            <?php endif; ?>
        </div>
    </div>


     
    <div class="form-group <?php echo (strlen(form_error('edlastname')) > 0 ? 'has-error' : '' ) ?>" id="cgLastname">
        <label class="col-sm-2 control-label" for="iLastname">Lastname</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edlastname" id="iLastname" placeholder="lastname" value="<?php echo set_value('edlastname'); ?>">
            <span class="help-block msg-required" style="display:none" >Mandatory field</span>
            <?php if(strlen(form_error('edlastname')) > 0): ?>
                <span class="help-block"><?php echo form_error('edlastname') ?></span>
            <?php endif; ?>
        </div>
    </div>

        <div class="form-group <?php echo (strlen(form_error('edusername')) > 0 ? 'has-error' : '' ) ?>" id="cgUsername">
        <label class="col-sm-2 control-label" for="iUsername">Username</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edusername" id="iUsername" placeholder="username" value="<?php echo set_value('edusername'); ?>">
            <span class="help-block msg-exists" style="display:none">Error : The given username is already in use</span>
            <?php if(strlen(form_error('edusername')) > 0): ?>
                <span class="help-block"><?php echo form_error('edusername') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edpassword')) > 0 ? 'has-error' : '' ) ?>" id="cgUsername">
        <label class="col-sm-2 control-label" for="iPassword">Password</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edpassword" id="iPassword" placeholder="password" value="<?php echo set_value('edpassword'); ?>">
            <span class="help-block msg-required" style="display:none">Mandatory field</span>
            <?php if(strlen(form_error('edpassword')) > 0): ?>
                <span class="help-block"><?php echo form_error('edpassword') ?></span>
            <?php endif; ?>
        </div>
    </div>
        
    <div class="form-group">
        <div class="col-xs-12 col-sm-offset-2 col-sm-4">
            <label class="radio-inline" >
                <input type="radio" name="edenabled" id="iEnabled" value="1" <?php echo set_radio('edenabled', '0', true); ?> > Enabled
            </label>
            <label class="radio-inline" >
                <input type="radio" name="edenabled" id="iDisabled" value="0" <?php echo set_radio('edenabled', '1', true); ?> > Disabled
            </label>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edprofile')) > 0 ? 'has-error' : '' ) ?>" >
        <label class="col-sm-2 control-label" for="iProfile">User profile</label>
        <div class="col-sm-4">
            <?php
                $options = array();
                foreach ($profiles as $profile) {
                    $options[$profile->id_right_profile] = $profile->name;
                }
                echo form_dropdown('edprofile', $options, null, 'class="form-control"');
            ?>
            <span class="help-block msg-validation-error" ><?php echo form_error('edprofile') ?></span>
        </div>
    </div>
            
    <div class="form-group">
        <div class="col-xs-12 col-sm-offset-2 col-sm-4">
            <button type="submit" class="btn btn-primary" name="submitAdd" id="submitAdd">Save</button>
            &nbsp;&nbsp;
            <a class="btn btn-danger" href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>">Cancel</a>
        </div>
    </div>
        
    </form>
    
        
<?php endif ?>

