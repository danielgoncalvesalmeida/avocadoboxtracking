<!-- box_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Packs</h2>
    </div>
</div>

<?php if(!$items): ?>

<div class="row">
    <div class="col-md-12">&nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <b>Info</b> : Currently there are no packs inserted!
        </div>
    </div>
</div>

<?php else: ?>

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
        <div class="col-md-12">
            
        <span class="label label-info">Total : <?php echo count($items) ?></span>
 <?php 
    if(!empty($p) && !empty($children_count) && $children_count > $this->config->item('results_per_page_default')):
        $pages = ceil($children_count / $this->config->item('results_per_page_default'));
?>           
        <!-- pagination -->    
        <ul class="pagination pagination-sm pull-right">
            <?php
                if($p > 1):
            ?>
                <li><a href="<?php echo sbase_url() ?>admin/children/?p=<?php echo $p - 1  ?>&n=<?php echo $this->config->item('results_per_page_default') ?>">&laquo;</a></li>
            <?php
                endif;
            ?>
            <?php
              for ($i = 1; $i <= $pages ; $i++):
            ?>
              <li class="<?php echo (!empty($p) && $p == $i ? 'active' : '') ?>"><a  href="<?php echo sbase_url() ?>admin/children/?p=<?php echo $i ?>&n=<?php echo $this->config->item('results_per_page_default') ?>"><?php echo $i ?></a></li>
            <?php
              endfor;
            ?>
            <?php
                if($p < $pages):
            ?>
                <li><a href="<?php echo sbase_url() ?>admin/children/?p=<?php echo $p + 1  ?>&n=<?php echo $this->config->item('results_per_page_default') ?>">&raquo;</a></li>
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