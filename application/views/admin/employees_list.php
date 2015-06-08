<!-- employees_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Employees</h2>
        <span class="pull-right"><a class="btn btn-default" href="<?php echo sbase_url() ?>admin/employees/add">Add an employee</a></span>
    </div>
</div>

<?php if(!$employees && !isset($items_filtered)): ?>

<div class="row">
    <div class="col-md-12">&nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <b>Info</b> : Currently there are no employees inserted!
        </div>
    </div>
</div>

<?php else: ?>

<!-- Filter -->
<form method="get">
<div class="row">
    <div class="filter-wrapper">
    <div class="col-md-2">
        <div class="form-group">
            <label for="edfirstname">Firstname</label>
            <input type="text" class="form-control" id="edfirstname" name="filter[edfirstname]" placeholder="Enter the firstname" value="<?php echo (isset($filter['edfirstname']) ? $filter['edfirstname'] : '') ?>">
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="edlastname">Lastname</label>
            <input type="text" class="form-control" id="edlastname" name="filter[edlastname]" placeholder="Enter the lastname" value="<?php echo (isset($filter['edlastname']) ? $filter['edlastname'] : '') ?>">
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="edusername">Username</label>
            <input type="text" class="form-control" id="edusername" name="filter[edusername]" placeholder="Enter the username" value="<?php echo (isset($filter['edusername']) ? $filter['edusername'] : '') ?>">
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">User profile</label>
            <select class="form-control" name="filter[eduserprofile]">
                <option value="0" <?php echo (isset($filter['eduserprofile']) && $filter['eduserprofile']==0 ? 'selected' : '') ?> >All</option>
                <?php foreach ($userprofiles as $k => $v): ?>
                    <option value="<?php echo $v->id_right_profile ?>" <?php echo (isset($filter['eduserprofile']) && $filter['eduserprofile']==$v->id_right_profile ? 'selected' : '') ?>><?php echo $v->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Status</label>
            <select class="form-control" name="filter[edstatus]">
                <option value="0" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==0 ? 'selected' : '') ?> >All</option>
                <option value="1" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==1 ? 'selected' : '') ?>>Enabled</option>
                <option value="2" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==2 ? 'selected' : '') ?>>Disabled</option>
            </select>
        </div>
    </div>
    
    
    <div class="col-md-2">
        <div class="form-group">
            <br />
            <button type="submit" class="btn btn-primary">Filter</button> 
        
        <?php if(isset($items_filtered)): ?>
       
            <a class="btn btn-warning" href="<?php echo sbase_url().uri_string() ?>">Reset</a> 
        
        <?php endif; ?>
        </div>
    </div>
        <div class="clearfix"></div>
    </div>
</div>
</form>
<!-- /filter -->

<div class="row">
    <div class="col-md-12">
    
    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>username</th>
            <th>User profile</th>
            <th>Status</th>
            <th></th>
        </thead>
        <tbody>
            <?php  
                foreach ($employees as $employee):
            ?>
                <tr>
                    <td><?php echo $employee->firstname ?></td>
                    <td><?php echo $employee->lastname ?></td>
                    <td><?php echo $employee->username ?></td>
                    <td><?php echo $employee->right_profile_name ?></td>
                    <td>
                        <?php if($employee->active): ?><span class="glyphicon glyphicon-ok"></span><?php else: ?><span class="glyphicon glyphicon-remove"></span><?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group pull-right" role="group">
                            <a href="<?php echo sbase_url() ?>admin/employees/edit/<?php echo $employee->id_user ?>" class="btn btn-primary">Edit</a>
                            <?php if($employee->id_user != getUserId()): ?>
                            <a href="<?php echo sbase_url() ?>admin/employees/delete/<?php echo $employee->id_user ?>" class="btn btn-danger btdelete_employee">Delete</a>
                            <?php endif; ?>
                        </div>
                    </td>
                    
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
    </table>
    </div>
        
    </div>
    
</div>

<!-- Listing footer -->
    <div class="row">
        <div class="col-md-12">
            
        <span class="label label-info">
            <?php if(isset($items_filtered)): ?>
                Total filtered :
            <?php else: ?>
                Total : 
            <?php endif; ?>
                <?php echo $employees_count ?></span>
<?php 
    if(!empty($p) && !empty($employees_count) && $employees_count > $this->config->item('results_per_page_default')):
        $pages = ceil($employees_count / $this->config->item('results_per_page_default'));
        $uri = uri_string();
        
        $f_url_params = (isset($filter_url_params) ? $filter_url_params : '');
?>            
        <!-- pagination -->    
        <ul class="pagination pagination-sm pull-right">
            <?php
                if($p > 1):
            ?>
                <li><a href="<?php echo sbase_url().$uri ?>/?p=<?php echo $p - 1  ?>&n=<?php echo $this->config->item('results_per_page_default').$f_url_params ?>">&laquo;</a></li>
            <?php
                endif;
            ?>
            <?php
              for ($i = 1; $i <= $pages ; $i++):
            ?>
              <li class="<?php echo (!empty($p) && $p == $i ? 'active' : '') ?>"><a  href="<?php echo sbase_url().$uri ?>/?p=<?php echo $i ?>&n=<?php echo $this->config->item('results_per_page_default').$f_url_params ?>"><?php echo $i ?></a></li>
            <?php
              endfor;
            ?>
            <?php
                if($p < $pages):
            ?>
                <li><a href="<?php echo sbase_url().$uri ?>/?p=<?php echo $p + 1  ?>&n=<?php echo $this->config->item('results_per_page_default').$f_url_params ?>">&raquo;</a></li>
            <?php
                endif;
            ?>
        </ul>
<?php
    endif;
?>
        </div>
    </div>
<!-- /Listing footer -->


<?php endif; ?>