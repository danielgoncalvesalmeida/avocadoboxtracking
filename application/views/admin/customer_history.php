<!-- customer_history -->
<div class="row">
    <div class="col-md-12">
        <a href="<?php echo (empty($link_back) ? sbase_url().'admin/customer/' : sbase_url().$link_back ) ?>" class="btn btn-default pull-right">Back</a>
        <h2 class="pull-left">Pack history | <?php echo $username ?></h2>
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
            <b>Info</b> : Currently there is no history for the customer <strong><?php echo $username ?></strong>!
        </div>
    </div>
</div>

<?php else: ?>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <th>Box</th>
                    <th>Outbound</th>
                    <th>Inbound</th>
                    <th>Days out</th>
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
                            <td><?php echo $item->barcode ?></td>
                            <td><?php if($item->outbound == 1): ?><span class="label label-danger"><span class="glyphicon glyphicon-share"></span>&nbsp<?php echo date_format($date_out, $this->config->item('format_date_human')) ?><?php endif; ?></td>
                            <td><?php if($item->inbound == 1): ?><span class="label label-success"><span class="glyphicon glyphicon-share"></span>&nbsp<?php echo date_format($date_in, $this->config->item('format_date_human')) ?><?php endif; ?></td>
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