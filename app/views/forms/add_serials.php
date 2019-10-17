<?php
//echo "<pre>",print_r($items),"</pre>";
?>
<?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>

<form id="add_serials" method="post" action="/form/procAddSerials">
    <?php $entered_serials = array();
    foreach($items as $item):
        $serials = $this->controller->orderitemserials->getRecordedSerials($item['order_id'], $item['item_id']);
        //echo "<pre>",print_r($serials),"</pre>";
        $c = 1;
        while($c <= $item['qty'])
        {
            foreach($serials as $s):
                //echo "<pre>",print_r($s),"</pre>";
                $entered_serials[] = $s['serial_number'];?>
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']." (".$item['sku'].")";?></label>
                    <div class="col-md-3"><input type="text" name="serial[<?php echo $c;?>][<?php echo $item['item_id'];?>][number]" class="form-control required unique" placeholder="Serial Number" value="<?php echo $s['serial_number'];?>" /></div>
                    <input type="hidden" name="serial[<?php echo $c;?>][<?php echo $item['item_id'];?>][line_id]" value="<?php echo $s['id'];?>" />
                </div>
                <?php ++$c;?>
            <?php endforeach;?>
            <?php if($c <= $item['qty']):?>
                <div class="form-group row">
                    <label class="col-md-5 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> <?php echo $item['name']." (".$item['sku'].")";?></label>
                    <div class="col-md-3"><input type="text" name="serial[<?php echo $c;?>][<?php echo $item['item_id'];?>][number]" class="form-control required unique" placeholder="Serial Number" /></div>
                    <input type="hidden" name="serial[<?php echo $c;?>][<?php echo $item['item_id'];?>][line_id]" value="0" />
                </div>
                <?php ++$c;
            endif;
        }?>
    <?php endforeach;
    //echo "<pre>",print_r($entered_serials),"</pre>";
    $sers = implode(",", $entered_serials);?>
    <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
    <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id;?>" />
    <input type="hidden" name="entered_serials" id="entered_serials" value="<?php echo $sers?>" />
    <div class="form-group row">
        <label class="col-md-5 col-form-label">&nbsp;</label>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary" id="add_serials_submitter">Update Order</button>
        </div>
    </div>
</form>