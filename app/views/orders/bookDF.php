<?php

?>
<div id="page-wrapper">
    <div id="page_container" class="container-xl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <form id="df_collection" method="post" action="/form/procDFColection">
            <div class="form-group row">
                <div class="col-12">
                    <h5>Cartons</h5>
                </div>
            </div>
            <div class="form-group row ml-4">
                <label class="col-md-1 col-sm-2 mb-3">Count</label>
                <div class="col-md-2 col-sm-4 mb-3">
                    <input type="text" class="form-control number" name="carton_count" id="carton_count" value="<?php echo Form::value('carton_count');?>" />
                </div>
                <label class="col-md-1 col-sm-2 mb-3">Width</label>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="carton_width" id="carton_width" value="<?php echo Form::value('carton_width');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-md-1 col-sm-2 mb-3">Length</label>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="carton_length" id="carton_length" value="<?php echo Form::value('carton_length');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
                <label class="col-md-1 col-sm-2 mb-3">Height</label>
                <div class="col-md-2 col-sm-4 mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control number" name="carton_height" id="carton_height" value="<?php echo Form::value('carton_length');?>" />
                        <div class="input-group-append">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>