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
                    
                    <?php if(isset($id_user)): ?><a class="btn btn-default" href="<?php echo sbase_url().'admin/employees/edit/'.$id_user ?>">Editer</a><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
        
<?php else: ?>
        
    <div class="row">
        <div class="col-md-12">
            <a href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>" class="btn btn-default pull-right">Retour</a>
            <h3>Ajouter un employée</h3>
            <br />
        </div>
    </div>

        <?php 
            $attributes = array('class' => 'form-horizontal', 'id' => 'frm_add', 'role' => 'form');
            echo form_open('admin/employees/add',$attributes);
        ?>

    
     
    <div class="form-group <?php echo (strlen(form_error('edlastname')) > 0 ? 'has-error' : '' ) ?>" id="cgLastname">
        <label class="col-sm-2 control-label" for="iLastname">Nom</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edlastname" id="iLastname" placeholder="nom de l'employée" value="<?php echo set_value('edlastname'); ?>">
            <span class="help-block msg-required" style="display:none" >Champ obligatoire</span>
            <?php if(strlen(form_error('edlastname')) > 0): ?>
                <span class="help-block"><?php echo form_error('edlastname') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edfirstname')) > 0 ? 'has-error' : '' ) ?>" id="cgFirstname">
        <label class="col-sm-2 control-label" for="iFirstname">Prénom</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edfirstname" id="iFirstname" placeholder="prénom de l'employée" value="<?php echo set_value('edfirstname'); ?>">
            <span class="help-block msg-required" style="display:none">Champ obligatoire</span>
            <?php if(strlen(form_error('edfirstname')) > 0): ?>
                <span class="help-block"><?php echo form_error('edfirstname') ?></span>
            <?php endif; ?>
        </div>
    </div>
        
    <div class="form-group <?php echo (strlen(form_error('edsocialid')) > 0 ? 'has-error' : '' ) ?>" id="cgSocialid">
        <label class="col-sm-2 control-label" for="iSocialid">Numéro social</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edsocialid" id="iSocialid" placeholder="numéro social" value="<?php echo set_value('edsocialid'); ?>">
            <span class="help-block msg-required" style="display:none" >Champ obligatoire</span>
            <span class="help-block msg-exists" style="display:none">Erreur : Ce numéro est déjà attribué</span>
            <?php if(strlen(form_error('edsocialid')) > 0): ?>
                <span class="help-block"><?php echo form_error('edsocialid') ?></span>
            <?php endif; ?>
        </div>
    </div>
            
    <div class="form-group <?php echo (strlen(form_error('edbirthdate')) > 0 ? 'has-error' : '' ) ?>" id="cgBirthdate">
        <label class="col-sm-2 control-label" for="iBirthdate">Date de naissance</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edbirthdate" id="iBirthdate" placeholder="AAAA-MM-JJ" value="<?php echo set_value('edbirthdate'); ?>">
            <span class="help-block msg-required" style="display:none">Champ obligatoire</span>
            <span class="help-block msg-invalid" style="display:none">Date non valide</span>
            <?php if(strlen(form_error('edbirthdate')) > 0): ?>
                <span class="help-block"><?php echo form_error('edbirthdate') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edemployeenumber')) > 0 ? 'has-error' : '' ) ?>" id="cgEmployeenumber">
        <label class="col-sm-2 control-label" for="iEmployeenumber">Numéro d'employée</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edemployeenumber" id="iEmployeenumber" placeholder="numéro d'employée" value="<?php echo set_value('edemployeenumber'); ?>">
            <span class="help-block msg-exists" style="display:none">Erreur : Ce numéro est déjà attribué</span>
            <?php if(strlen(form_error('edemployeenumber')) > 0): ?>
                <span class="help-block"><?php echo form_error('edemployeenumber') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edemail')) > 0 ? 'has-error' : '' ) ?>" id="cgEmail">
        <label class="col-sm-2 control-label" for="iEmail">Email</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edemail" id="iEmail" placeholder="email de l'employée" value="<?php echo set_value('edemail'); ?>">
            <span class="help-block msg-exists" style="display:none">Erreur : Cet email est déjà attribué</span>
            <?php if(strlen(form_error('edemail')) > 0): ?>
                <span class="help-block"><?php echo form_error('edemail') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edusername')) > 0 ? 'has-error' : '' ) ?>" id="cgUsername">
        <label class="col-sm-2 control-label" for="iUsername">Nom d'utilisateur</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edusername" id="iUsername" placeholder="nom ou identifiant" value="<?php echo set_value('edusername'); ?>">
            <span class="help-block msg-exists" style="display:none">Erreur : Ce nom d'utilisateur est déjà attribué</span>
            <?php if(strlen(form_error('edusername')) > 0): ?>
                <span class="help-block"><?php echo form_error('edusername') ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edpassword')) > 0 ? 'has-error' : '' ) ?>" id="cgUsername">
        <label class="col-sm-2 control-label" for="iPassword">Mot de pass</label>
        <div class="col-sm-4">
            <input class="form-control" type="text" name="edpassword" id="iPassword" placeholder="mot de pass" value="<?php echo set_value('edpassword'); ?>">
            <span class="help-block msg-required" style="display:none">Champ obligatoire</span>
            <?php if(strlen(form_error('edpassword')) > 0): ?>
                <span class="help-block"><?php echo form_error('edpassword') ?></span>
            <?php endif; ?>
        </div>
    </div>
        
    <div class="form-group">
        <div class="col-xs-12 col-sm-offset-2 col-sm-4">
            <label class="radio-inline" >
                <input type="radio" name="edenabled" id="iEnabled" value="1" <?php echo set_radio('edenabled', '1', true); ?> > Actif
            </label>
            <label class="radio-inline" >
                <input type="radio" name="edenabled" id="iDisabled" value="0" <?php echo set_radio('edenabled', '0', true); ?> > Désactivé
            </label>
        </div>
    </div>

    <div class="form-group <?php echo (strlen(form_error('edprofile')) > 0 ? 'has-error' : '' ) ?>" >
        <label class="col-sm-2 control-label" for="iProfile">Profile de l'utilisateur</label>
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
        
    <div class="form-group <?php echo (strlen(form_error('edsite')) > 0 ? 'has-error' : '' ) ?>" >
        <label class="col-sm-2 control-label" for="iSite">Site par défaut</label>
        <div class="col-sm-4">
            <?php 
                $options = array();
                foreach ($sites as $site) {
                    $options[$site->id_site] = $site->name;
                }
                echo form_dropdown('edsite', $options, null, 'class="form-control"');
            ?>
            <span class="help-block msg-validation-error" ><?php echo form_error('edsite') ?></span>
        </div>
    </div>
        
       
            
    <div class="form-group">
        <div class="col-xs-12 col-sm-offset-2 col-sm-4">
            <button type="submit" class="btn btn-primary" name="submitAdd" id="submitAdd">Enregistrer</button>
            &nbsp;&nbsp;
            <a class="btn btn-danger" href="<?php echo (empty($link_back) ? sbase_url().'admin/employees/' : sbase_url().$link_back ) ?>">Annuler</a>
        </div>
    </div>
        
    </form>
    
        
<?php endif ?>

