<?php
$bc = (!empty($dl_details['box_count']))? $dl_details['box_count'] : 1;
$tb = 1;
while ($tb <= $bc):?>
<table class="dl_body">
    <tr>
        <td>
            Box <?php echo $tb;?> of <?php echo $bc;?>
        </td>
    </tr>
</table>
<pagebreak />
<?php ++$tb;
endwhile;?>