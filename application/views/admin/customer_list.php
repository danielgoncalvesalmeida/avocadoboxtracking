<!-- customer_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Customers</h2>
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
            <b>Info</b> : Currently there are no customers inserted!
        </div>
    </div>
</div>

<?php else:
    $f_url_params = (isset($filter_url_params) ? $filter_url_params : '');
    if(isset($_GET['p']))
        $f_url_params .= '&p='.$_GET['p'];
    if(isset($_GET['n']))
        $f_url_params .= '&n='.$_GET['n'];

?>

<!-- Filter -->
<form method="get">
<div class="row">
    <div class="filter-wrapper">
    <div class="col-md-2">
        <div class="form-group">
            <label for="edbarcode">Customer</label>
            <input type="text" class="form-control" id="edbarcode" name="filter[edusername]" placeholder="username" value="<?php echo (isset($filter['edusername']) ? $filter['edusername'] : '') ?>">
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
            <th>Customer</th>
            <th>Boxes out</th>
            <th></th>
            <th></th>
            <th></th>
        </thead>
        <tbody>
            <?php  
                foreach ($items as $item):
            ?>
                <tr>
                    <td><?php echo $item->username ?></td>
                    <td>
                        <?php                            
                            if(count($item->boxes) > 0):
                                foreach ($item->boxes as $b):
                                    $date_outbound = new DateTime(substr($b->date_outbound,0,10));
                                    $date_today = new DateTime( date('Y-m-d') );
                                    $age = date_diff($date_today, $date_outbound);
                        ?>
                            <strong><?php echo $b->barcode ?></strong>
                            
                            <?php if($age->d == 0)
                                echo 'Today';
                            elseif($age->d == 1)
                                echo '1 day ago ';
                            elseif($age->d > 1)
                                echo $age->d.' days ago ';
                                ?>
                            on shipping <strong><?php echo $b->reference ?></strong><br />
                        <?php
                                endforeach;
                            endif;   
                        ?>
                        
                    </td>
                    <td></td>
                    <td></td>
                    <td><a class="btn btn-primary btn-sm pull-right" href="<?php echo sbase_url() ?>admin/customer/viewhistory/<?php echo $item->username ?>?<?php echo (isset($link_back) ? 'link_back='.$link_back : '').$f_url_params ?>">View history</a></td>
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