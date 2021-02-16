<?php
$contact_array = array();
if(!empty($contacts))
{
    $ca = explode("|", $contacts);
    foreach($ca as $c)
    {
        list($a['contact_id'], $a['name'],$a['email'],$a['phone'],$a['role']) = explode(',', $c);
        $contact_array[] = $a;
    }
}
$cc = 1;
$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php if(empty($customer['id'])):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_customer_found.php");?>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
            <?php //echo "<pre>",print_r($customer),"</pre>";?>
            <div id="print_this" class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                        <div class="card border-secondary h-100 order-card">
                            <div class="card-header bg-secondary text-white">
                                Customer Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <label class="col-5">Name</label>
                                    <div class="col-7"><?php echo $customer['name'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Address</label>
                                    <div class="col-7"><?php echo $customer['address'];?></div>
                                </div>
                                <?php if(!empty($customer['address_2'])):?>
                                    <div class="row">
                                        <label class="col-5">&nbsp;</label>
                                        <div class="col-7"><?php echo $customer['address_2'];?></div>
                                    </div>
                                <?php endif;?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $customer['suburb'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $customer['state'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $customer['postcode'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $customer['country'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Phone</label>
                                    <div class="col-7"><?php echo $customer['phone'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Email</label>
                                    <div class="col-7"><?php echo $customer['email'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Website</label>
                                    <div class="col-7"><?php echo $customer['website'];?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php foreach($contact_array as $contact):?>
                        <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                            <div class="card border-secondary h-100 order-card">
                                <div class="card-header bg-secondary text-white">
                                    Contact <?php echo ucwords($f->format($cc));?>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <label class="col-5">Name</label>
                                        <div class="col-7"><?php echo $customer['name'];?></div>
                                    </div>
                                    <?php if(!empty($customer['role'])):?>
                                        <div class="row">
                                            <label class="col-5">Role</label>
                                            <div class="col-7"><?php echo $customer['role'];?></div>
                                        </div>
                                    <?php endif;?>
                                    <?php if(!empty($customer['phone'])):?>
                                        <div class="row">
                                            <label class="col-5">Phone</label>
                                            <div class="col-7"><?php echo $customer['phone'];?></div>
                                        </div>
                                    <?php endif;?>
                                    <?php if(!empty($customer['email'])):?>
                                        <div class="row">
                                            <label class="col-5">Email</label>
                                            <div class="col-7"><?php echo $customer['email'];?></div>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
        <?php endif;?>
    </div>
</div>