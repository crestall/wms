<?php
    $link_text = (!$active)? "<a href='/suppliers/view-suppliers' class='btn btn-outline-fsg'>View Active Suppliers</a>" : "<a href='/suppliers/view-suppliers/active=0' class='btn btn-outline-fsg'>View Inactive Suppliers</a>";
    $i = 1;
?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="row">
            <div class="col">
                <p class="text-right"><?php echo $link_text;?></p>
            </div>
        </div>
        <?php if(count($suppliers)):?>
            <div class="col" id="table_holder" style="display:none">
                <table id="supplier_list_table" class="table-striped table-hover">
                    <thead>
                    	<tr>
                            <th></th>
                            <th>Supplier Name</th>
                            <th>Contact Details</th>
                            <th>Address Details</th>
                             <?php if($role == "production admin"):?>
                                <th></th>
                            <?php endif;?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($suppliers as $s):?>
                    	<tr>
                            <td><?php echo $i;?></td>
                            <td data-label="Supplier Name"><?php echo $s['name'];?></td>
                            <td data-label="Contact Details"></td>
                            <td data-label="Address Details"></td>
                            <?php if($role == "production admin"):?>
                                <td>
                                    <p><a class="btn btn-outline-secondary" href="/suppliers/edit-supplier/supplier=<?php echo $s['id'];?>" >Edit Details</a></p>
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