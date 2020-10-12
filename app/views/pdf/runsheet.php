<div id="runsheet_body">
    <div class="landscape">
        <div id="page_top">
            <table>
                <tr>
                    <td><img width="130" src="https://wms.fsg.com.au/images/FSG_logo_white@130px.png" alt="FSG Logo"> </td>
                    <td style="width: 18cm"></td>
                    <td style="text-transform: uppercase; color: #fff;">Driver RunSheet</td>
                </tr>
            </table>
        </div>
        <div id="page_labels">
            <table align="right">
                <tr>
                    <td style="text-align: right">
                        Driver:
                    </td>
                    <td style="border-bottom: 1px dotted black; width: 75mm; text-align: center"><?php echo $driver;?></td>
                </tr>
                <tr>
                    <td style="text-align: right">
                        Date:
                    </td>
                    <td style="border-bottom: 1px dotted black; width: 75mm; text-align: center"><?php echo date("d/M/Y");?></td>
                </tr>
            </table>
        </div>
        <div id="page_body">
            <table>
                <thead>
                    <tr>
                        <th style="width: 16mm">Job Id/Order Number</th>
                        <th style="width: 38mm">Customer - Client</th>
                        <th style="width: 41mm">Description</th>
                        <th style="width: 74mm">Address</th>
                        <th style="width: 12mm">Units</th>
                        <th style="width: 27mm">FSG Contact</th>
                        <th style="width: 27mm">Received By</th>
                        <th style="width: 25mm">Time of Drop</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $table_body;?>
                </tbody>
            </table>
        </div>
        <div id="page_foot">
            Please return this run sheet and signed delivery dockets to FSG
        </div>
    </div>
</div>