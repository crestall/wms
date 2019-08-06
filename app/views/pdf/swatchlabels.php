<table width="100%" style="font-size:16px; margin: 0 auto; border: thin solid black">
    <tr style="white-space:nowrap; height: 28mm">
    <?php
    $this_order = 1;
    foreach($orders_ids as $id):
        $od = $this->controller->swatch->getSwatchDetail($id);
        //echo "<pre>",print_r($od),"</pre>";//die();
        $address_string = ucwords($od['name'])."<br/>";
        $address_string .= ucwords($od['address'])."<br/>";
        if(!empty($od['address_2']))
            $address_string .= ucwords($od['address_2'])."<br/>";
        $address_string .= strtoupper($od['suburb'])."<br/>";
        $address_string .= strtoupper($od['state'])."<br/>";
        $address_string .= $od['postcode'];
        if($this_order % 16 == 0)
            echo "</tr></table><pagebreak/><table width='100%' style='font-size:16px'><tr style='white-space:nowrap; height: 28mm'>";
        elseif($this_order % 2 == 0)
            echo '</tr><tr style="white-space:nowrap; height: 28mm">';
        ?>
        <td style="padding:3mm; text-align:right; width:96mm; border:thin dotted black">
            <?php echo $address_string;?>
        </td>
        <?php ++$this_order;
    endforeach; ?>
    </tr>
</table>