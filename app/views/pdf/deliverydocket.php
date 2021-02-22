<?php
//echo "<pre>",print_r($sender_details),"</pre>";
?>
<div id="dd_body">
    <div id="top_half">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/<?php echo $sender_details['logo'];?>"></td>
                <td class="right-align sender-address"><?php echo $sender_details['address'];?></td>
            </tr>
        </table>
    </div>
    <div id="divider">
        <p>-------------------------------------------------------------------------------------------------------------------------------<br>
        <span class="inst">[Detach Here]</span</p>
    </div>
    <div id="bottom_half">

    </div>
</div>