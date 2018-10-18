<?php
$html = "
    <h2>Inwards/Outwards Goods Barcodes</h2>
    <table border='0' cellspacing='2' align='center'>
        <tr>
            <td><barcode code='2105633' type='EAN8' class='barcode' size='2' height='0.5' /><br />Pallet</td>
            <td><barcode code='0404022' type='EAN8' class='barcode' size='2' height='0.5' /><br />Carton</td>
            <td><barcode code='2210044' type='EAN8' class='barcode' size='2' height='0.5' /><br />Satchel</td>
        </tr>
    </table>
    <table border='0' cellspacing='2' align='center'>
        <tr>
            <td><barcode code='0000000' type='EAN8' class='barcode' size='2' height='0.5' /><br />Zero</td>
            <td><barcode code='0000001' type='EAN8' class='barcode' size='2' height='0.5' /><br />1</td>
        </tr>
        <tr>
            <td><barcode code='0000002' type='EAN8' class='barcode' size='2' height='0.5' /><br />2</td>
            <td><barcode code='0000003' type='EAN8' class='barcode' size='2' height='0.5' /><br />3</td>
        </tr>
        <tr>
            <td><barcode code='0000004' type='EAN8' class='barcode' size='2' height='0.5' /><br />4</td>
            <td><barcode code='0000005' type='EAN8' class='barcode' size='2' height='0.5' /><br />5</td>
        </tr>
        <tr>
            <td><barcode code='0000006' type='EAN8' class='barcode' size='2' height='0.5' /><br />6</td>
            <td><barcode code='0000007' type='EAN8' class='barcode' size='2' height='0.5' /><br />7</td>
        </tr>
        <tr>
            <td><barcode code='0000008' type='EAN8' class='barcode' size='2' height='0.5' /><br />8</td>
            <td><barcode code='0000009' type='EAN8' class='barcode' size='2' height='0.5' /><br />9</td>
        </tr>
        <tr>
            <td><barcode code='0000100' type='EAN8' class='barcode' size='2' height='0.5' /><br />00</td>
            <td><barcode code='0001000' type='EAN8' class='barcode' size='2' height='0.5' /><br />000</td>
        </tr>
    </table>
    <pagebreak />
    <p><barcode code='COMPLETE' type='C39+' class='barcode' size='3' height='1' /></p>
    <p>Finish Scan</p>
    <p></p>
    <p><barcode code='COMPLETE' type='C39+' class='barcode' size='3' height='1' /></p>
    <p>Finish Scan</p>
    <p></p>
    <p><barcode code='COMPLETE' type='C39+' class='barcode' size='3' height='1' /></p>
    <p>Finish Scan</p>
    <p></p>
    <p><barcode code='COMPLETE' type='C39+' class='barcode' size='3' height='1' /></p>
    <p>Finish Scan</p>
    <p></p>
    <p><barcode code='COMPLETE' type='C39+' class='barcode' size='3' height='1' /></p>
    <p>Finish Scan</p>
    <pagebreak/>
    <p><barcode code='VV733873001001275' type='C39+' class='barcode' /></p>
    <pagebreak />
    <table>
    <tr>";
    $count = 1;
    foreach($items as $i)
    {
        $html .= "<td><barcode code='{$i['barcode']}' size='2' height='.5' type='EAN13' class='barcode' /><br />{$i['name']}</td>";
        if($count % 2 == 0)
        {
            $html .= "</tr><tr>";
        }
        ++$count;
    }
    $html .= "</tr></table>";
    echo $html;//die();
?>