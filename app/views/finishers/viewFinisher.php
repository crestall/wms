<?php
$contact_array = array();
if(!empty($finisher['contacts']))
{
    $ca = explode("|", $finisher['contacts']);
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
    <div id="page_container" class="container-xxl">
        <?php if(empty($finisher['id'])):?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/no_finisher_found.php");?>
        <?php else:?>
            <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
            <?php //echo "<pre>",print_r($finisher),"</pre>";?>
            <div id="print_this" class="container-fluid">
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-xl-4 mb-3">
                        <div class="card border-secondary h-100 order-card">
                            <div class="card-header bg-secondary text-white">
                                Finisher Details
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <label class="col-5">Name</label>
                                    <div class="col-7"><?php echo $finisher['name'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Address</label>
                                    <div class="col-7"><?php echo $finisher['address'];?></div>
                                </div>
                                <?php if(!empty($finisher['address_2'])):?>
                                    <div class="row">
                                        <label class="col-5">&nbsp;</label>
                                        <div class="col-7"><?php echo $finisher['address_2'];?></div>
                                    </div>
                                <?php endif;?>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $finisher['suburb'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $finisher['state'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $finisher['postcode'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">&nbsp;</label>
                                    <div class="col-7"><?php echo $finisher['country'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Phone</label>
                                    <div class="col-7"><?php echo $finisher['phone'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Email</label>
                                    <div class="col-7"><?php echo $finisher['email'];?></div>
                                </div>
                                <div class="row">
                                    <label class="col-5">Website</label>
                                    <div class="col-7"><?php echo $finisher['website'];?></div>
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
                                        <div class="col-7"><?php echo $contact['name'];?></div>
                                    </div>
                                    <?php if(!empty($contact['role'])):?>
                                        <div class="row">
                                            <label class="col-5">Role</label>
                                            <div class="col-7"><?php echo $contact['role'];?></div>
                                        </div>
                                    <?php endif;?>
                                    <?php if(!empty($contact['phone'])):?>
                                        <div class="row">
                                            <label class="col-5">Phone</label>
                                            <div class="col-7"><?php echo $contact['phone'];?></div>
                                        </div>
                                    <?php endif;?>
                                    <?php if(!empty($contact['email'])):?>
                                        <div class="row">
                                            <label class="col-5">Email</label>
                                            <div class="col-7"><?php echo $contact['email'];?></div>
                                        </div>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    <?php ++$cc; endforeach;?>
                </div>
            </div>
            <div class="row">
                <?php if($role == "production admin"):?>
                    <div class="col-6">
                        <a class="btn btn-outline-secondary" href="/finishers/edit-finisher/finisher=<?php echo $finisher['id'];?>" >Edit These Details</a>
                    </div>
                <?php endif;?>
                <div class="col text-right">
                    <button class="btn btn-outline-fsg" id="print">Print These Details</button>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>