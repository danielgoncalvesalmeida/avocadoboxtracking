<!-- box_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Boxes</h2>
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
            <b>Info</b> : Currently there are no boxes inserted!
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
            <label for="edbarcode">Barcode</label>
            <input type="text" class="form-control" id="edbarcode" name="filter[edbarcode]" placeholder="Enter the barcode" value="<?php echo (isset($filter['edbarcode']) ? $filter['edbarcode'] : '') ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="">Status</label>
            <select class="form-control" name="filter[edstatus]">
                <option value="0" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==0 ? 'selected' : '') ?> >All</option>
                <option value="1" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==1 ? 'selected' : '') ?>>IN</option>
                <option value="2" <?php echo (isset($filter['edstatus']) && $filter['edstatus']==2 ? 'selected' : '') ?>>OUT</option>
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
<!-- /Filter -->



<div class="row">
    <div class="col-md-12">
    
    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <th>Barcode</th>
            <th>Status</th>
            <th>Info</th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            <?php  
                foreach ($items as $item):
                    if($item->status == 1)
                    {
                        $date_outbound = new DateTime( substr($item->date_outbound,0,10) );
                        $date_today = new DateTime( date('Y-m-d') );
                        $age = date_diff($date_today, $date_outbound);
                    }
            ?>
                <tr>
                    <td><?php echo $item->barcode ?></td>
                    <td><?php if($item->status == 0): ?><span class="label label-success">IN</span><?php else: ?><span class="label label-danger">OUT</span><?php endif; ?></td>
                    <td>
                        <?php if($item->status == 1): ?>
                        <strong>
                            <?php if($age->d == 0)
                                echo 'Today';
                            elseif($age->d == 1)
                                echo '1 day ago ';
                            elseif($age->d > 1)
                                echo $age->d.' days ago ';
                                ?>
                        </strong> on shipping <strong><?php echo $item->reference ?></strong>
                        <?php endif; ?>
                    </td>
                    <td></td>
                    <td><a class="btn btn-primary btn-sm pull-right" href="<?php echo sbase_url() ?>admin/box/viewhistory/<?php echo $item->id_pack ?>?<?php echo (isset($link_back) ? 'link_back='.$link_back : '') ?>">View history</a></td>
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