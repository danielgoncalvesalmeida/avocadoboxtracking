<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row">
    
    <div class="col-sm-6 col-md-3">
       <div class="panel panel-danger">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <span class="glyphicon glyphicon-share huge"></span>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo (!empty($outbound_count) ? count($outbound_count) : '0') ?></div>
                        <div>Boxes out</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left"><a href="<?php echo sbase_url() ?>admin/box/showallout">View all</span>
                    <span class="pull-right"><span class="glyphicon glyphicon-chevron-right"></span></span></a>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-sm-6 col-md-3">
        <div class="panel panel-success">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <span class="glyphicon glyphicon-check huge"></span>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?php echo (!empty($inbound_count) ? count($inbound_count) : '0') ?></div>
                        <div>Boxes in</div>
                    </div>
                </div>
            </div>
            <a href="#">
                <div class="panel-footer">
                    <span class="pull-left"><a href="<?php echo sbase_url() ?>admin/box/showallin">View all</span>
                    <span class="pull-right"><span class="glyphicon glyphicon-chevron-right"></span></span></a>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-sm-12 col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <span class="glyphicon glyphicon-check"></span>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="">Import outbound</div>
                        <div>
                            <a href="<?php echo sbase_url() ?>admin/importreferences/addoutbound" class="btn btn-primary">Submit outbound</a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <div class="col-sm-12 col-md-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <span class="glyphicon glyphicon-check"></span>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="">Import inbound</div>
                        <div>
                            <a href="<?php echo sbase_url() ?>admin/importreferences/addinbound" class="btn btn-primary">Submit inbound</a>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
        
</div>

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

<?php 
    if(isset($quick_search_packfound[0])): ?>
    <div class="row">
        <div class="col-lg-8">
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    Result
                </div>
                <div class="panel panel-body">
                    <div class="row">
                        <div class="col-xs-3">Pack <strong><?php echo $quick_search_packfound[0]->barcode ?></strong></div>
                        <div class="col-xs-9">
                            <?php if($quick_search_packfound[0]->status == 0): ?>
                                <span class="label label-success">IN</span> on <?php echo date_format(new DateTime($quick_search_packfound[0]->date_inbound), 'd/m/Y') ?> from shipping <strong><?php echo $quick_search_packfound[0]->reference ?></strong>
                            <?php else: ?>
                                <span class="label label-danger">OUT</span> on <?php echo date_format(new DateTime($quick_search_packfound[0]->date_outbound), 'd/m/Y') ?> in shipping <strong><?php echo $quick_search_packfound[0]->reference ?></strong>
                            <?php endif; ?>
                        </div>
                    </div>
                    <br >
                    <div class="row">
                        <div class="col-xs-12"><a class="btn btn-primary btn-sm" href="<?php echo sbase_url() ?>admin/box/viewhistory/<?php echo $quick_search_packfound[0]->id_pack ?>">View history</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

