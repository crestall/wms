<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-l">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <?php //echo "FINISHERS<pre>",print_r($production_finishers)."</pre>";?>
        <?php //echo "CUSTOMERS<pre>",print_r($production_customers)."</pre>";?>
        <div class="row">
            <div class="col">
                <h1>Finishers and Customers</h1>
                <?php /* foreach($production_customers as $pc)
                {
                    if(empty(trim($pc['contacts'])))
                    {
                        if(!empty($pc['contact']))
                        {
                            echo "<p>Will Add ".$pc['contact']." for ".$pc['name']." (".$pc['id'].")</p>";
                            $data = array(
                                'name'          => $pc['contact'],
                                'customer_id'   => $pc['id']
                            );
                            echo "<pre>",print_r($data),"</pre>";
                            //$this->controller->productioncontact->addContact($data);
                            echo "<p>Done</p>";
                        }
                        else
                        {
                            echo "<p>No contact listed for ".$pc['name']." (".$pc['id'].")</p>";
                        }
                    }
                    else
                    {
                        echo "<p>Contacts already listed for ".$pc['name']." (".$pc['id'].") - ".$pc['contacts']."</p>";
                    }
                    echo "<p>-------------------------------------------------------------------------------------</p>";
                }
                */?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h1>Add Finishers To Jobs</h1>
                <?php echo "JOBS<pre>",print_r($production_jobs)."</pre>";?>
            </div>
        </div>
    </div>
</div>
