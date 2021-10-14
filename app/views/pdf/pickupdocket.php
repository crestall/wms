<?php
echo "<pre>",print_r($pickup),"</pre>";
$items = explode("~",$pickup['items']);
?>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
<body>
    <div id="pud_body">
        <table id="page_head">
            <tr>
                <td  style="width: 125mm"><img style="height:18mm;width:auto;" src="https://wms.fsg.com.au/images/delivery_docket_logos/fsg_logo.png"></td>
                <td class="right-align sender-address">
                    <strong>FSG Priniting & 3PL Services</strong><br>
                    865 Mountain Hwy, Bayswater VIC 3153<br>
                    <strong>T.</strong>03 9873 5144 - <strong>E.</strong>info@fsg.com.au<br>
                    <strong>www.fsg.com.au</strong>
                </td>
            </tr>
        </table>
        <h2>PICKUP #<?php echo $pickup['pickup_number'];?></h2>
    </div>
</body>