<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row">
    
    <div class="col-md-9">
    <div class="current-status">
        <h2>Currently</h2>
        <div class="row">
            <div class="col-sm-6 col-md-6">
               <div class="dashboard panel panel-danger">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <span class="glyphicon glyphicon-share huge"></span>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?php echo (!empty($outbound_count) ? count($outbound_count) : '0') ?></div>
                                <div class="dashboard-label">Boxes out</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left"><a href="<?php echo sbase_url() ?>admin/box?filter[edbarcode]=&filter[edstatus]=2">View all</span>
                            <span class="pull-right"><span class="glyphicon glyphicon-chevron-right"></span></span></a>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
    
    
    
            <div class="col-sm-6 col-md-6">
                <div class="dashboard dashboard-success panel panel-success">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-3">
                                <span class="glyphicon glyphicon-check huge"></span>
                            </div>
                            <div class="col-xs-9 text-right">
                                <div class="huge"><?php echo (!empty($inbound_count) ? count($inbound_count) : '0') ?></div>
                                <div class="dashboard-label">Boxes in</div>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left"><a href="<?php echo sbase_url() ?>admin/box?filter[edbarcode]=&filter[edstatus]=1">View all</span>
                            <span class="pull-right"><span class="glyphicon glyphicon-chevron-right"></span></span></a>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
    </div>
    </div>
    
    
    
        
</div>
<br />
<div class="row">
    <div class="col-md-9">
        
        <div class="import-panel">
            <h2>Import actions</h2>
            <div class="row">
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            
                            <p>Import shipping references</p>
                            <div>
                                <a href="<?php echo sbase_url() ?>admin/importreferences/addshipping" class="btn btn-primary">Import shipping</a>
                            </div>
                        </div>

                    </div>
                </div>
                
                
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            
                            <p>Import outbound references</p>
                            <div>
                                <a href="<?php echo sbase_url() ?>admin/importreferences/addoutbound" class="btn btn-primary">Submit outbound</a>
                            </div>
                        </div>

                    </div>
                </div>
                

                
                <div class="col-xs-12 col-sm-4 col-md-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            
                            <p>Import inbound</p>
                            <div>
                                <a href="<?php echo sbase_url() ?>admin/importreferences/addinbound" class="btn btn-primary">Submit inbound</a>
                            </div>
                        </div>

                    </div>
                </div>
               
                
            </div>
        </div>
        
    </div>
</div>
<br />

<div class="row">
    <div class="col-lg-6">
       
        <div class="quick-search-wrapper">
            <div class="title">Quick search</div>
            <?php
                $attributes = array('class' => 'form-inline');
                echo form_open('',$attributes);
              ?>
                <div class="form-group">
                    <input type="text" name="search" class="form-control" id="exampleInputEmail1" placeholder="Enter any reference">
                </div>
                <button type="submit" name="submitQuicksearch" value="1" class="btn btn-primary">Go</button>
            </form>
                 
        </div>        
        
    </div>
</div>

<!-- show no results found -->
<?php 
    if(isset($quick_serach_notfound) ): ?>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="panel panel-danger">
                <div class="panel-heading">
                    No results found for your search
                </div>
                
                <div class="panel panel-body">             
                    <div class="row">
                        <div class="col-xs-12">The provided string doesn't correspond to a username, neither a shipping or a pack!</div>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- /show no results found -->

<!-- results for username search -->
<?php 
    if(isset($quick_search_userfound) ): ?>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    Customer identified : <strong><?php echo $quick_search_userfound['username'] ?></strong>
                </div>
                
                
                <div class="panel panel-body">
                    
                    <?php if($quick_search_userfound['packs_outbound'] !== false && count($quick_search_userfound['packs_outbound']) >0 ): ?>
                    <div class="quick-search-body-subtitle">Corrently unreturned packs</div>
                    
                    <?php foreach ($quick_search_userfound['packs_outbound'] as $op): 
                            $date_out = new DateTime( substr($op->date_outbound,0,10) );
                            $date_in = new DateTime( date('Y-m-d') );
                            $age = date_diff($date_in, $date_out);
                        ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-3"><strong><?php echo $op->barcode ?></strong></div>
                            <div class="col-xs-12 col-sm-7">
                                <span class="label label-danger"><span class="glyphicon glyphicon-share"></span>&nbsp<?php echo date_format($date_out, $this->config->item('format_date_human')) ?></span>
                                &nbsp;
                                    <?php if($age && $age->d > 0): ?>
                                    <span class="label label-danger">
                                    <?php if($age->d == 1)
                                        echo '1 day out';
                                    elseif($age->d > 1)
                                        echo $age->d.' days out';
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <br />
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <br />
                    <?php if($quick_search_userfound['shippings'] !== false ): ?>
                    <div class="quick-search-body-subtitle">Recent shippings</div>
                    
                    <?php foreach ($quick_search_userfound['shippings'] as $s): 
                            $date_delivery = new DateTime( substr($s->date_delivery,0,10) );
                        ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-3"><strong><?php echo $s->reference ?></strong></div>
                            <div class="col-xs-12 col-sm-7">
                                Delivered on <?php echo date_format($date_delivery, $this->config->item('format_date_human')) ?>
                            </div>
                        </div>
                        <br />
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <br />
                    <?php if($quick_search_userfound['packs'] !== false ): ?>
                    <div class="quick-search-body-subtitle">Recent packs used</div>
                    
                    <?php foreach ($quick_search_userfound['packs'] as $p): 
                            $date_delivery = new DateTime( substr($p->date_delivery,0,10) );
                            $date_out = new DateTime( substr($p->date_outbound,0,10) );
                            if($p->date_inbound !== null)
                            {
                                $date_in = new DateTime( substr($p->date_inbound,0,10) );
                                $age = date_diff($date_in, $date_out);
                            }
                        ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-3"><strong><?php echo $p->barcode ?></strong></div>
                            <div class="col-xs-12 col-sm-7">
                                <span class="label label-danger" ><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo date_format($date_out, $this->config->item('format_date_human')) ?></span>
                                <?php if($p->date_inbound !== null): ?>
                                &nbsp;<span class="label label-default"><span class="glyphicon glyphicon-resize-horizontal"></span>&nbsp;<?php echo ($age->days < 2 ? $age->days.' day' : $age->days.' days') ?></span>
                                &nbsp;<span class="label label-success" ><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo date_format($date_in, $this->config->item('format_date_human')) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <br />
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <br />
                    <div class="row">
                        <div class="col-xs-12"><a class="btn btn-primary btn-sm" href="<?php echo sbase_url() ?>admin/box/viewhistory/<?php echo $quick_search_userfound['username'] ?>">View history</a></div>
                    </div>
                </div>
                
                
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- /results for username search -->

<!-- results for pack search -->
<?php 
    if(isset($quick_search_packfound) ): ?>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    Pack identified : <strong><?php echo $quick_search_packfound['barcode'] ?></strong>
                    <?php if($quick_search_packfound['status'] == false ): ?>
                        is currently <span class="label label-success">IN</span>
                    <?php else: ?>
                        is currently <span class="label label-danger">OUT</span>
                    <?php endif; ?>
                </div>
                
                <?php if($quick_search_packfound['history'] !== false ): ?>
                <div class="panel panel-body">
                    <div class="quick-search-body-subtitle">Recent history</div>
                    
                    <?php foreach ($quick_search_packfound['history'] as $h): 
                            $date_delivery = new DateTime( substr($h->date_delivery,0,10) );
                            $date_out = new DateTime( substr($h->date_outbound,0,10) );
                            if($h->date_inbound !== null)
                            {
                                $date_in = new DateTime( substr($h->date_inbound,0,10) );
                                $age = date_diff($date_in, $date_out);
                            }
                        ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-4"><strong><?php echo $h->reference ?></strong> for user <?php echo $h->username ?></div>
                            <div class="col-xs-12 col-sm-7">
                                <span class="label label-danger" ><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo date_format($date_out, $this->config->item('format_date_human')) ?></span>
                                <?php if($h->date_inbound !== null): ?>
                                &nbsp;<span class="label label-default"><span class="glyphicon glyphicon-resize-horizontal"></span>&nbsp;<?php echo ($age->days < 2 ? $age->days.' day' : $age->days.' days') ?></span>
                                &nbsp;<span class="label label-success" ><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo date_format($date_in, $this->config->item('format_date_human')) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <br >
                    <div class="row">
                        <div class="col-xs-12"><a class="btn btn-primary btn-sm" href="<?php echo sbase_url() ?>admin/box/viewhistory/<?php echo $quick_search_packfound['id_pack'] ?>">View history</a></div>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- /results for pack search -->

<!-- results for shipping search -->
<?php 
    if(isset($quick_search_shippingfound) ):
        if($quick_search_shippingfound['date_delivery'] !== null)
        {
            $date_delivery = new DateTime( substr($quick_search_shippingfound['date_delivery'],0,10) );
        }
    ?>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    Shipping identified : <strong><?php echo $quick_search_shippingfound['reference'] ?></strong> for customer <strong><?php echo $quick_search_shippingfound['username'] ?></strong> delivered on <?php echo date_format($date_delivery, $this->config->item('format_date_human')) ?>
                </div>
                
                <?php if($quick_search_shippingfound['packs'] !== false ): ?>
                <div class="panel panel-body">
                    <div class="quick-search-body-subtitle">Packs</div>
                    
                    <?php foreach ($quick_search_shippingfound['packs'] as $item):
                            $date_out = new DateTime( substr($item->date_outbound,0,10) );
                            if($item->date_inbound !== null)
                            {
                                $date_in = new DateTime( substr($item->date_inbound,0,10) );
                                $age = date_diff($date_in, $date_out);
                            }
                        ?>
                        <div class="row">

                            <div class="col-xs-12 col-sm-4"><strong><?php echo $item->barcode ?></strong></div>
                            <div class="col-xs-12 col-sm-7">
                                <span class="label label-danger" ><span class="glyphicon glyphicon-share"></span>&nbsp;<?php echo date_format($date_out, $this->config->item('format_date_human')) ?></span>
                                <?php if($item->date_inbound !== null): ?>
                                &nbsp;<span class="label label-default"><span class="glyphicon glyphicon-resize-horizontal"></span>&nbsp;<?php echo ($age->days < 2 ? $age->days.' day' : $age->days.' days') ?></span>
                                &nbsp;<span class="label label-success" ><span class="glyphicon glyphicon-check"></span>&nbsp;<?php echo date_format($date_in, $this->config->item('format_date_human')) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
<?php endif; ?>
<!-- /results for pack search -->

