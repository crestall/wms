        <!-- Jquery JavaScript -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
        <!-- Validation JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/additional-methods.min.js" ></script>
        <script src="/scripts/form_validators.js?t=<?php echo time();?>" ></script>
        <!-- Bootstrap JavaScript -->
        <script src="/scripts/bootstrap.3.3.4.min.js"></script>
        <!--script src="/scripts/bootstrap.3.3.7.min.js"></script-->
        <!-- Bootstrap Select Styling >
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.2/js/bootstrap-select.min.js"></script>
        <!-- Menu JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/metisMenu/2.7.8/metisMenu.min.js"></script>
        <!-- DataTables JavaScript -->
        <script src="//cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/1.10.18/js/dataTables.bootstrap.min.js"></script>
        <script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
        <!-- Block UI JavaScript -->
        <script src="/scripts/jquery.blockUI.js"></script>
        <!-- Print Area Javascript -->
        <script src="/scripts/jquery.PrintArea.js"></script> 
        <!-- Live Filter JavaScript -->
        <!--script src="/scripts/jquery.liveFilter.js"></script-->
        <script src="/scripts/jquery.filtertable.min.js"></script>
        <!-- File download JavaScript -->
        <script src="/scripts/jquery.filedownload.js"></script>
        <!-- Sacnner detection -->
        <script src="/scripts/jquery.scannerdetection.js"></script>
        <!-- Sweet alerts JavaScript -->
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <!-- Sound PlayingJavaScript -->
        <script src='https://cdn.rawgit.com/admsev/jquery-play-sound/master/jquery.playSound.js'></script>
        <!-- Google Charts JavaScript -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <!-- Sticky table headers -->
        <script src="https://unpkg.com/sticky-table-headers"></script>
        <!-- WMS JavaScript -->
        <script src="/scripts/common.js?t=<?php echo time();?>"></script>



        <!-- Assign CSRF Token to JS variable -->
        <?php Config::setJsConfig('csrfToken', Session::generateCsrfToken()); ?>
        <!-- Assign pages for menu highlighting -->
        <?php
        if(Session::getIsLoggedIn()):
            $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
            $user_role = str_replace(" ","_", $user_role);
            $pages = Config::get(strtoupper($user_role."_PAGES"));
            Config::setJsConfig('allPages', $pages);
        else:
            Config::setJsConfig('allPages', '');
        endif;
        //Assign courier ids to javascript
        Config::setJsConfig('eParcelId', $this->controller->courier->eParcelId);
        Config::setJsConfig('eParcelExpressId', $this->controller->courier->eParcelExpressId);
        Config::setJsConfig('huntersId', $this->controller->courier->huntersId);
        Config::setJsConfig('huntersPluId', $this->controller->courier->huntersPluId);
        Config::setJsConfig('huntersPalId', $this->controller->courier->huntersPalId);
        Config::setJsConfig('threePlTruckId', $this->controller->courier->threePlTruckId);
        Config::setJsConfig('localId', $this->controller->courier->localId);
        Config::setJsConfig('vicLocalId', $this->controller->courier->vicLocalId);
        Config::setJsConfig('directFreightId', $this->controller->courier->directFreightId);
        Config::setJsConfig('cometLocalId', $this->controller->courier->cometLocalId);
        Config::setJsConfig('sydneyCometId', $this->controller->courier->sydneyCometId);
        //assign solar ids to javascript
        Config::setJsConfig('TLJSolarId', $this->controller->solarordertype->TLJSolarId);
        Config::setJsConfig('OriginId', $this->controller->solarordertype->OriginId);
        Config::setJsConfig('SolargainId', $this->controller->solarordertype->SolargainId);
        Config::setJsConfig('BeyondId', $this->controller->solarordertype->BeyondId);
        ?>
        <!-- Assign all configuration variables -->
        <script>config = <?php echo json_encode(Config::getJsConfig()); ?>;</script>