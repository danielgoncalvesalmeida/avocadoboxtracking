<!-- box_list -->
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo (empty($link_back) ? sbase_url().'admin/box/' : sbase_url().$link_back ) ?>" class="btn btn-default pull-right">Back</a>
        <h2 class="pull-left">Pack history <?php echo (isset($pack->id_pack) ? ' | '.$pack->barcode : '') ?></h2>
    </div>
</div>

<?php if(!$history): ?>

<div class="row">
    <div class="col-md-12">&nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-info">
            <b>Info</b> : Currently there is no history for the given pack!
        </div>
    </div>
</div>

<?php else: ?>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <th>Shipping</th>
                    <th>Outbound</th>
                    <th>Inbound</th>
                    <th></th>
                    <th></th>
                </thead>
                <tbody>
                    <?php  
                        foreach ($history as $item):
                            
                            if($item->outbound == 1 && $item->inbound == 1)
                            {
                                $date_outbound = new DateTime( substr($item->date_outbound,0,10) );
                                $date_inbound = new DateTime( substr($item->date_inbound,0,10) );
                                $age = date_diff($date_inbound, $date_outbound);
                            }
                            else
                                $age = false;
                    ?>
                        <tr>
                            <td><?php echo $item->reference ?></td>
                            <td><?php if($item->outbound == 1): ?><span class="label label-danger"><?php echo $item->date_outbound ?></span><?php endif; ?></td>
                            <td><?php if($item->inbound == 1): ?><span class="label label-success"><?php echo $item->date_inbound ?></span><?php endif; ?></td>
                            <td>
                                <?php if($age && $age->d > 0): ?>
                                    <span class="label label-danger">
                                    <?php if($age->d == 1)
                                        echo '1 day out';
                                    elseif($age->d > 1)
                                        echo $age->d.' days out';
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td></td>
                        </tr>
                    <?php
                        endforeach;
                    ?>
                </tbody>
            </table>
            </div>
    </div>    
</div>

<?php endif; ?>