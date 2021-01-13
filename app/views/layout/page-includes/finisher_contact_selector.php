<?php

?>
<select id="finisher_contact_id" class="form-control selectpicker" data-style="btn-outline-secondary"><option value="0">Choose One</option><?php echo $this->controller->productionfinisher->getSelectFinisherContacts($finisher_id);?></select>