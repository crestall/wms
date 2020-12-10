<?php
    //$link_text = (!$active)? "<a href='/finishers/view-finishers' class='btn btn-outline-fsg'>View Active Finishers</a>" : "<a href='/finishers/view-finishers/active=0' class='btn btn-outline-fsg'>View Inactive Finishers</a>";
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <?php if(count($finishers)):
            //echo "<pre>",print_r($finishers),"</pre>";?>
            <?php if($role == "production admin" || $role == "production sales admin"):?>
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3"><button class="btn btn-sm btn-block btn-outline-danger" id="deactivate"><i class="fal fa-times-circle"></i> Delete Selected Finishers</button></div>
                </div>
            <?php endif;?>
            <div class="row">
                <div class="col-12">
                    <?php if(isset($_SESSION['feedback'])) :?>
                       <div class='feedbackbox'><?php echo Session::getAndDestroy('feedback');?></div>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['errorfeedback'])) :?>
                       <div class='errorbox'><?php echo Session::getAndDestroy('errorfeedback');?></div>
                    <?php endif; ?>
                </div>
                <div class="col" id="table_holder" style="display:none">
                    <table id="finisher_list_table" class="table-striped table-hover" style="width:90%">
                        <thead>
                        	<tr>
                                <th></th>
                                <th>Finisher Name</th>
                                <th>Contact Details</th>
                                <th>Address Details</th>
                                <?php if($role == "production admin" || $role == "production sales admin"):?>
                                    <th nowrap>
                                        Select
                                        <div class="checkbox checkbox-default">
                                            <input id="select_all" class="styled" type="checkbox">
                                            <label for="select_all"><em><small>(all)</small></em></label>
                                        </div>
                                    </th>
                                    <th></th>
                                <?php endif;?>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($finishers as $s):
                            $address_string = "";
                            if(!empty($s['address'])) $address_string .= $s['address'];
                            if(!empty($s['address_2'])) $address_string .= "<br/>".$s['address_2'];
                            if(!empty($s['suburb'])) $address_string .= "<br/>".$s['suburb'];
                            if(!empty($s['state'])) $address_string .= "<br/>".$s['state'];
                            if(!empty($s['country'])) $address_string .= "<br/>".$s['country'];
                            if(!empty($s['postcode'])) $address_string .= "<br/>".$s['postcode'];
                            $contact_string = "";
                            if(!empty($s['contacts']))
                            {
                                $contacts = explode("|", $s['contacts']);
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
                            if(!empty($s['website'])) $contact_string .= "<br/><a href='http://".$s['website']."' target='_blank'>".$s['website']."</a>";
                            ?>
                        	<tr>
                                <td><?php echo $i;?></td>
                                <td data-label="Finisher Name"><?php echo $s['name'];?></td>
                                <td data-label="Contact Details"><?php echo $contact_string;?></td>
                                <td data-label="Address Details" class="text-right"><?php echo $address_string;?></td>
                                <?php if($role == "production admin" || $role == "production sales admin"):?>
                                    <td data-label="Select" class="chkbox">
                                        <div class="checkbox checkbox-default">
                                            <input type="checkbox" class="select styled" data-finisherid='<?php echo $s['id'];?>' name="select_<?php echo $s['id'];?>" id="select_<?php echo $s['id'];?>" />
                                            <label for="select_<?php echo $s['id'];?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <p><a class="btn btn-outline-secondary" href="/finishers/edit-finisher/finisher=<?php echo $s['id'];?>" >Edit Details</a></p>
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
                        <h2>No Finishers Listed</h2>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
</div>