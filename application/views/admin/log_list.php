<!-- log_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Log</h2>
    </div>
</div>

<?php if(!$items && !isset($items_filtered)): ?>

<div class="row">
    <div class="col-md-12">&nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <b>Info</b> : Currently there are no records to display!
        </div>
    </div>
</div>

<?php else: ?>

<?php 

    $type = array(
        '1' => 'Info',
        '2' => 'Warning',
        '3' => 'Error',
    );
            
?>
<!-- Filter -->
<form method="get">
<div class="row">
    <div class="filter-wrapper">
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">User</label>
            <select class="form-control" name="filter[eduser]">
                <option value="0" <?php echo (isset($filter['eduser']) && $filter['eduser']==0 ? 'selected' : '') ?> >All</option>
                <?php foreach ($users as $k => $v): ?>
                    <option value="<?php echo $v->id_user ?>" <?php echo (isset($filter['eduser']) && $filter['eduser']==$v->id_user ? 'selected' : '') ?>><?php echo $v->firstname.' '.$v->lastname ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Type</label>
            <select class="form-control" name="filter[edtype]">
                <option value="0" <?php echo (isset($filter['edtype']) && $filter['edtype']==0 ? 'selected' : '') ?> >All</option>
                <?php foreach ($type as $k => $v): ?>
                    <option value="<?php echo $k ?>" <?php echo (isset($filter['edtype']) && $filter['edtype']==$k ? 'selected' : '') ?>><?php echo $v ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Operations</label>
            <select class="form-control" name="filter[edoperation]">
                <option value="0" <?php echo (isset($filter['edoperation']) && $filter['edoperation']==0 ? 'selected' : '') ?> >All</option>
                <?php foreach ($operations as $k => $v): ?>
                    <option value="<?php echo $v->id_log_operation ?>" <?php echo (isset($filter['edoperation']) && $filter['edoperation']==$v->id_log_operation ? 'selected' : '') ?>><?php echo $v->label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Date begin</label>
            <div class="input-group" id="datetimepicker1">
                <input type="text" class="form-control" id="eddatebegin" name="filter[eddatebegin]" placeholder="From date" value="<?php echo (isset($filter['eddatebegin']) ? $filter['eddatebegin'] : '') ?>">
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
            </div>
        </div>
    </div>
        
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Date end</label>
            <div class="input-group" id="datetimepicker2">
                <input type="text" class="form-control" id="eddateend" name="filter[eddateend]" placeholder="To date" value="<?php echo (isset($filter['eddateend']) ? $filter['eddateend'] : '') ?>">
                <div class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></div>
            </div>
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
            <th>Date</th>
            <th>User</th>
            <th>Type</th>
            <th>Operation</th>
            <th>Message</th>
        </thead>
        <tbody>
            <?php
                foreach ($items as $item): ?>
                <tr>
                    <td><?php echo date_format(new DateTime($item->date_add), $this->config->item('format_datetimeseconds_human')) ?></td>
                    <td><?php echo (!empty($item->firstname) ? $item->firstname.' '.$item->lastname : '') ?></td>
                    <td><?php echo (isset($type[$item->type])? $type[$item->type] : '' ) ?></td>
                    <td><?php echo (!empty($item->operation)? $item->label : '' ) ?></td>
                    <td>
                        <?php if(!empty($item->message_short)): ?>
                        <div class="log-caption">
                            <?php echo $item->message_short ?>
                        </div>
                        <?php endif; ?>
                        <?php echo $item->message ?>
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
        <div class="col-md-6">
            
        <span class="label label-info">
            <?php if(isset($items_filtered)): ?>
                Total filtered :
            <?php else: ?>
                Total : 
            <?php endif; ?>
                <?php echo $items_count ?></span>
 <?php 
    if(!empty($p) && !empty($items_count) && $items_count > $this->config->item('results_per_page_default')):
        $pages = ceil($items_count / $this->config->item('results_per_page_default'));
        $uri = uri_string();
        
        $f_url_params = (isset($filter_url_params) ? $filter_url_params : '');
?>           
        </div>
        <div class="col-md-6">
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