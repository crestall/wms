<?php

foreach($delivery_ids as $id):
    $dd = $this->controller->delivery->getDeliveryDetails($id);
    echo "<pre>",print_r($dd),"</pre>";//die();
    ?>
    
    <pagebreak />
<?php endforeach; ?>