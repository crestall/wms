<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <?php if(count($todays_reports)):?>
        <div class="row">
            <div class="col-md-12">
                <h3>Reports Already Sent Today</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table-striped table-hover" width="100%">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Report Type</th>
                            <th>Sent By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($todays_reports as $r):?>
                            <tr>
                                <td data-label="Client"><?php echo $r['client_name'];?></td>
                                <td data-label="Report Type"><?php echo $r['report_type'];?></td>
                                <td data-label="Sent By"><?php echo $this->controller->user->getUserName( $r['entered_by'] );?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif;?>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <div class="col-md-12">
            <form id="client_daily_reports" method="post" action="/form/procClientDailyReports">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                    <div class="col-md-4">
                        <select id="client_id" name="client_id" class="form-control selectpicker"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                        <?php echo Form::displayError('client_id');?>
                    </div>
                </div>
                <div class="form-check row">
                    <label class="form-check-label col-md-3">Select Reports</label>
                    <div class="col-md-4 checkbox checkbox-default">
                        <input class="form-check-input styled" type="checkbox" id="dispatch_report" name="client_reports[]" value="dispatch_report" />
                        <label for="dispatch_report">Dispatch Report</label>
                    </div>
                </div>
                <div class="form-check row">
                    <label class="form-check-label col-md-3"></label>
                    <div class="col-md-2 checkbox checkbox-default">
                        <input disabled class="form-check-input styled" type="checkbox" id="returns_report" name="client_reports[]" value="returns_report" />
                        <label for="returns_report">Returns Report</label>
                        <?php echo Form::displayError('client_reports');?>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">&nbsp;</label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Send Reports</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>