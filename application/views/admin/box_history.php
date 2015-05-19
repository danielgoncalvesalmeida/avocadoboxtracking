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
                    <th>Customer</th>
                    <th>Outbound</th>
                    <th>Inbound</th>
                    <th></th>
                    <th></th>
                </thead>
                <tbody>
                    <?php  
                        foreach ($history as $item):
                            
                            $date_out = new DateTime( substr($item->date_outbound,0,10) );
                            if($item->inbound == 1)
                            {
                                $date_in = new DateTime( substr($item->date_inbound,0,10) );
                                $age = date_diff($date_in, $date_out);
                            }
                            else
                            {
                                $date_in = new DateTime( date('Y-m-d') );
                                $age = date_diff($date_in, $date_out);
                            }
                    ?>
                        <tr>
                            <td><?php echo $item->reference ?></td>
                            <td><?php echo $item->customer ?></td>
                            <td><?php if($item->outbound == 1): ?><span class="label label-danger"><span class="glyphicon glyphicon-share"></span>&nbsp<?php echo date_format($date_out, $this->config->item('format_date_human')) ?>
                                <?php echo (!empty($item->out_firstname) ? ' - '.$item->out_firstname : '') ?></span><?php endif; ?></td>
                            <td><?php if($item->inbound == 1): ?><span class="label label-success"><span class="glyphicon glyphicon-share"></span>&nbsp<?php echo date_format($date_in, $this->config->item('format_date_human')) ?>
                                <?php echo (!empty($item->in_firstname) ? ' - '.$item->in_firstname : '') ?></span><?php endif; ?></td>
                            <td>
                                <?php if($age && $age->d > 0): ?>
                                    <span class="label <?php echo ($item->inbound == 0 ? 'label-danger' : 'label-default') ?>">
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