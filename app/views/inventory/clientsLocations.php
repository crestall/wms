<div id="page-wrapper">
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
    <div class="row">
        <div class="col-lg-12">
            <h3>Add a New Client Location</h3>
        </div>
    </div>
    <?php include(Config::get('VIEWS_PATH')."layout/page-includes/form-top.php");?>
    <div class="row">
        <form id="add_client_location" method="post" action="/form/procAddClientLocation">
            <div class='form-group row'>
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Location</label>
                <div class="col-md-4">
                    <select id="location" name="location" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->location->getSelectClientLocations(Form::value('location'));?></select>
                    <?php echo Form::displayError('location');?>
                </div>
            </div>
            <div class='form-group row'>
                <label class="col-md-3 col-form-label"><sup><small><i class="fas fa-asterisk text-danger"></i></small></sup> Client</label>
                <div class="col-md-4">
                    <select id="client_id" name="client_id" class="form-control selectpicker" data-live-search="true"><option value="0">--Select One--</option><?php echo $this->controller->client->getSelectClients(Form::value('client_id'));?></select>
                    <?php echo Form::displayError('client_id');?>
                </div>
            </div>
            <div class='form-group row'>
                <label class="col-md-3 col-form-label">Notes</label>
                <div class="col-md-4">
                    <textarea class="form-control" name="notes" id="notes"><?php echo Form::value("notes");?></textarea>
                </div>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo Session::generateCsrfToken(); ?>" />
            <div class="form-group row">
                <label class="col-md-3 col-form-label">&nbsp;</label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Add Location</button>
                </div>
            </div>
        </form>
    </div>
    <?php if(count($locations)):?>
    <hr/>
        <div class="row">
            <div class="col-lg-12">
                <h3>Current Client Locations</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table id="client_locations_table" class="table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Client</th>
                            <th>Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($locations as $cl):
                            $client = $this->controller->client->getClientName($cl['client_id']);?>
                            <tr>
                                <td data-label="Location"><?php echo $cl['location'];?></td>
                                <td data-label="Client"><?php echo $client;?></td>
                                <td data-label="Notes"><?php echo nl2br($cl['notes']);?></td>
                                <td><button class="deletebutton btn btn-danger" data-allocationid="<?php echo $cl['id'];?>">Delete Allocation</button></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else:?>
        <div class="row">
            <div class="col-lg-12">
                <div class="errorbox">
                    <h2>No Client Locations Listed</h2>
                    <p>There are not any client locations currently listed in the system</p>
                </div>
            </div>
        </div>
    <?php endif;?>
</div>