<!-- box_list -->
<div class="row">
    <div class="col-md-12">
        <h2 class="pull-left">Shippings</h2>
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
            <b>Info</b> : Currently there are no shippings inserted!
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
            <label for="edreference">Reference</label>
            <input type="text" class="form-control" id="edreference" name="filter[edreference]" placeholder="Enter the reference" value="<?php echo (isset($filter['edreference']) ? $filter['edreference'] : '') ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="eddatedeliverystart">Date delivery</label>
            <input type="text" class="form-control" id="eddatedeliverystart" name="filter[eddatedeliverystart]" placeholder="Enter a date" value="<?php echo (isset($filter['eddatedeliverystart']) ? $filter['eddatedeliverystart'] : '') ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="eddatedeliveryend">Date delivery</label>
            <input type="text" class="form-control" id="eddatedeliveryend" name="filter[eddatedeliveryend]" placeholder="Enter a date" value="<?php echo (isset($filter['eddatedeliveryend']) ? $filter['eddatedeliveryend'] : '') ?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label for="edusername">Customer</label>
            <input type="text" class="form-control" id="edusername" name="filter[edusername]" placeholder="Enter a username" value="<?php echo (isset($filter['edusername']) ? $filter['edusername'] : '') ?>">
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
            <th>Reference</th>
            <th>Date delivery</th>
            <th>Customer</th>
            <th>Boxes</th>
            <th></th>
        </thead>
        <tbody>
            <?php  
                foreach ($items as $item):
                    if($item->date_delivery !== null)
                    {
                        $date_delivery = new DateTime( substr($item->date_delivery,0,10) );
                    }
            ?>
                <tr>
                    <td><?php echo $item->reference ?></td>
                    <td><?php if(isset($date_delivery)): ?><?php echo date_format($date_delivery, $this->config->item('format_date_human')) ?><?php endif; ?></td>
                    <td><?php echo $item->username ?></td>
                    <td>
                        <span class="label label-default" title="Boxes used"><span class="glyphicon glyphicon-th"></span>&nbsp;<?php echo $item->count_packs ?></span>
                        <?php if($item->count_packs_outbound > 0): ?><span class="label label-danger" title="Boxes still in outbound"><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo $item->count_packs_outbound ?></span><?php endif; ?>
                        <?php if($item->count_packs_inbound > 0): ?><span class="label label-success" title="Boxes returned"><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo $item->count_packs_inbound ?></span><?php endif; ?>
                        
                        <div class="shipping-details-wrapper">
                            <?php if(isset($item->packs) && count($item->packs) > 0):
                                foreach ($item->packs as $pack):
                                    $date_out = new DateTime($pack->date_outbound);
                                    if($pack->date_inbound !== null)
                                    {
                                        $date_in = new DateTime($pack->date_inbound);
                                        $age = date_diff($date_in, $date_out);
                                    }
                                ?>
                            <strong><?php echo $pack->barcode ?></strong>
                            &nbsp;<span class="label label-danger" ><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo date_format($date_out, $this->config->item('format_date_human')) ?></span>
                            <?php if(isset($date_in)): ?>
                                &nbsp;<span class="label label-default"><span class="glyphicon glyphicon-resize-horizontal"></span>&nbsp;<?php echo ($age->days < 2 ? $age->days.' day' : $age->days.' days') ?></span>
                                &nbsp;<span class="label label-success" ><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo date_format($date_in, $this->config->item('format_date_human')) ?></span>
                            <?php endif; ?>
                            <br />
                            <?php
                                endforeach;
                                endif; ?>
                        </div>
                    </td>
                    <td><a class="btn btn-primary btn-sm pull-right" href="<?php echo sbase_url() ?>admin/box/viewhistory/<?php echo $item->id_shipping ?>?<?php echo (isset($link_back) ? 'link_back='.$link_back : '') ?>">View history</a></td>
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