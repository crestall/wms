<?php
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($customers)):?>
            <div class="col" id="table_holder" style="display:none">
                <table id="customer_list_table" class="table-striped table-hover" style="width:80%">
                    <thead>
                    	<tr>
                            <th></th>
                            <th>Customer Name</th>
                            <th>Contact Details</th>
                            <th>Address Details</th>
                             <?php if($role == "production admin"):?>
                                <th></th>
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
                        if(!empty($c['contacts']))
                        {
                            $contacts = explode("|", $c['contacts']);
                            foreach($contacts as $c)
                            {
                                list($contact_id, $c_name,$c_email,$c_phone,$c_role) = explode(',', $c);
                                $contact_string .= "<div class='border-bottom border-secondary border-bottom-dashed mb-3 pb-3'>";
                                $contact_string .= "<span class='font-weight-bold'>".ucwords($c_name)."</span>";
                                if(!empty($c_role)) $contact_string .= "<br>$c_role";
                                if(!empty($c_phone)) $contact_string .= "<br>$c_phone";
                                if(!empty($c_email)) $contact_string .= "<br><a href='mailto:".$c_email."'>$c_email</a>";

                                $contact_string .= "</div>";
                            }

                        }
                        if(!empty($c['website'])) $contact_string .= "<br/><a href='http://".$c['website']."' target='_blank'>".$c['website']."</a>";
                        ?>
                    	<tr>
                            <td><?php echo $i;?></td>
                            <td data-label="Supplier Name"><?php echo $c['name'];?></td>
                            <td data-label="Contact Details"><?php echo $contact_string;?></td>
                            <td data-label="Address Details" class="text-right"><?php echo $address_string;?></td>
                            <?php if($role == "production admin"):?>
                                <td>
                                    <p><a class="btn btn-outline-secondary" href="/customers/edit-customer/customer=<?php echo $c['id'];?>" >Edit Details</a></p>
                                </td>
                            <?php endif;?>
                        </tr>
                    <?php ++$i; endforeach;?>
                    </tbody>
                </table>
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