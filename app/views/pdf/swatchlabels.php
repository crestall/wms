<table width="100%" style="font-size:16px; line-height:1.5">
    <tr>
    <?php
    $this_order = 1;
    foreach($orders_ids as $id):
        $od = $this->controller->swatch->getSwatchDetail($id);
        //echo "<pre>",print_r($od),"</pre>";//die();
        $address_string = $od['name']."br/>";
        $address_string .= $od['address']."<br/>";
        if(!empty($od['address_2']))
            $address_string .= " ".$od['address_2']."<br/>";
        $address_string .= " ".$od['suburb']."<br/>";
        $address_string .= " ".$od['state']."<br/>";
        $address_string .= " ".$od['postcode'];
        if($this_order % 16 == 0)
            echo "</tr></table><pagebreak/><table width='100%' style='font-size:16px; line-height:1.5>";
        elseif($this_order % 2 == 0)
            echo "</tr><tr>";
        ?>
        <td style="margin:8mm; text-align: right">
            <?php echo $address_string;?>
        </td>
        <?php ++$this_order;
    endforeach; ?>
    </tr>
</table>