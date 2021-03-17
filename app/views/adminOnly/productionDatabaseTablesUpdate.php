<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-l">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php"); ?>
        <?php //echo "FINISHERS<pre>",print_r($production_finishers)."</pre>";?>
        <?php //echo "CUSTOMERS<pre>",print_r($production_customers)."</pre>";?>
        <div class="row">
            <div class="col">
                <?php foreach($production_finishers as $pf)
                {
                    if(!empty(trim($pf['contacts'])))
                    {
                        if(!empty($pf['contact']))
                        {
                            echo "<p>Will Add ".$pf['contact']." for ".$pf['name']." (".$pf['id'].")</p>";
                        }
                        else
                        {
                            echo "<p>No contact listed for ".$pf['name']." (".$pf['id'].")</p>";
                        }
                    }
                    else
                    {
                        echo "<p>Contacts already listed for ".$pf['name']." (".$pf['id'].") - ".$pf['contacts']."</p>";
                    }
                    echo "<p>-------------------------------------------------------------------------------------</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
