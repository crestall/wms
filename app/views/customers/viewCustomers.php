<?php
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($customers)):?>
            <div id="waiting" class="row">
                <div class="col-lg-12 text-center">
                    <h2>Drawing Table..</h2>
                    <p>May take a few moments</p>
                    <img class='loading' src='/images/preloader.gif' alt='loading...' />
                </div>
            </div>
            <div class="row" id="table_holder" style="display:none">
                <?php if($role == "production admin" || $role == "production sales admin"):?>
                    <div class="col-12">
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-danger" id="deactivate"><i class="fal fa-times-circle"></i> Delete Selected Customers</button></div>
                    </div>
                <?php endif;?>
                <div class="col-12">
                    <table id="customer_list_table" class="table-striped table-hover" style="width:100%">
                        <thead>
                        	<tr>
                                <th></th>
                                <th>Customer Name</th>
                                <th>Contact Details</th>
                                <th>Address Details</th>
                                <th></th>
                                <?php if($role == "production admin" || $role == "production sales admin"):?>
                                    <th nowrap>
                                        Select
                                        <div class="checkbox checkbox-default">
                                            <input id="select_all" class="styled" type="checkbox">
                                            <label for="select_all"><em><small>(all)</small></em></label>
                                        </div>
                                    </th>
                                <?php endif;?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($customers as $c):
                            $address_string = "";
                            if(!empty($c['address'])) $address_string .= $c['address'];
                            if(!empty($c['address_2'])) $address_string .= "<br/>".$c['address_2'];
                            if(!empty($c['suburb'])) $address_string .= "<br/>".$c['suburb'];
                            if(!empty($c['state'])) $address_string .= "<br/>".$c['state'];
                            if(!empty($c['country'])) $address_string .= "<br/>".$c['country'];
                            if(!empty($c['postcode'])) $address_string .= "<br/>".$c['postcode'];
                            $contact_string = "";
                            if(!(empty($c['email']) && empty($c['phone']) && empty($c['website'])))
                            {
                                $contact_string .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3'>";
                                $contact_string .= "<span class='font-weight-bold'>Company Contact</span>";
                                if(!empty($c['email'])) $contact_string .= "<br><a href='mailto:".$c['email']."'>".$c['email']."</a>";
                                if(!empty($c['phone'])) $contact_string .= "<br>".$c['phone'];
                                if(!empty($c['website'])) $contact_string .= "<br/><a href='http://".$c['website']."' target='_blank'>".$c['website']."</a>";
                                $contact_string .= "</div>";
                            }
                            if(!empty($c['contacts']))
                            {
                                $contacts = explode("|", $c['contacts']);
                                foreach($contacts as $co)
                                {
                                    list($contact_id, $c_name,$c_email,$c_phone,$c_role) = explode(',', $co);
                                    $contact_string .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3'>";
                                    $contact_string .= "<span class='font-weight-bold'>".ucwords($c_name)."</span>";
                                    if(!empty($c_role)) $contact_string .= "<br>$c_role";
                                    if(!empty($c_phone)) $contact_string .= "<br>$c_phone";
                                    if(!empty($c_email)) $contact_string .= "<br><a href='mailto:".$c_email."'>$c_email</a>";

                                    $contact_string .= "</div>";
                                }
                            }
                            ?>
                        	<tr>
                                <td><?php echo $i;?></td>
                                <td data-label="Supplier Name"><?php echo $c['name'];?></td>
                                <td data-label="Contact Details"><?php echo $contact_string;?></td>
                                <td data-label="Address Details" class="text-right"><?php echo $address_string;?></td>
                                <td>
                                    <?php if($role == "production admin"):?>
                                        <p><a class="btn btn-outline-secondary" href="/customers/edit-customer/customer=<?php echo $c['id'];?>" >Edit Details</a></p>
                                    <?php endif;?>
                                    <p><a class="btn btn-outline-fsg" href="/customers/view-customer/customer=<?php echo $c['id'];?>" >View/Print Details</a></p>
                                </td>
                                <?php if($role == "production admin" || $role == "production sales admin"):?>
                                    <td data-label="Select" class="chkbox">
                                        <div class="checkbox checkbox-default">
                                            <input type="checkbox" class="select styled" data-finisherid='<?php echo $c['id'];?>' name="select_<?php echo $c['id'];?>" id="select_<?php echo $c['id'];?>" />
                                            <label for="select_<?php echo $c['id'];?>"></label>
                                        </div>
                                    </td>
                                <?php endif;?>
                            </tr>
                        <?php ++$i; endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else:?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="errorbox">
                        <h2>No Suppliers Listed</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>