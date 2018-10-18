<?php
echo "<pre>",print_r($scan_data),"</pre>";//die();
?>
<div class="row">
    <div class="col-md-12">
        <h2>Items In Orders By Location</h2>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form id="order_picking" method="post" action="/form/procPickOrder">
                <?php foreach($scan_data as $key => $sd):?>
                    <div class="form-group row unscanned" id="<?php echo strtoupper(str_replace(".", "", $sd['location_name']));?>">
                        <label class="col-md-3 col-form-label"><?php echo $sd['location_name'];?></label>
                        <?php $c = 1; foreach($sd['items'] as $i):?>
                            <div class="col-md-3"><?php echo $i['name'];?></div>
                            <div class="col-md-1"><input type="text" class="form-control number pick_check" disabled value="<?php echo $i['qty'];?>" /></div>
                            <div class="col-md-2"><input name="pick_<?php echo $key.$i['id'];?>" id="pick_<?php echo $key.$i['id'];?>" type="text" class="form-control  pick_count" disabled data-pickcheck="<?php echo $i['qty'];?>" data-itemid="<?php echo $i['id'];?>" data-location="<?php echo $sd['location_name'];?>" /></div>
                            <?php if($c < count($sd['items'])):?>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">&nbsp;</label>
                            <?php endif;?>
                        <?php ++$c; endforeach;?>
                    </div>
                <?php endforeach;?>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary" id="submit_button" disabled>Pick Order</button>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-danger" id="reset">Reset Page</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>