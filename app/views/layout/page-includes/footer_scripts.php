        <!-- Assign CSRF Token to JS variable -->
        <?php Config::setJsConfig('csrfToken', Session::generateCsrfToken()); ?>
        <!-- Assign pages for menu highlighting -->
        <?php
        if(Session::getIsLoggedIn()):
            $user_role = (Session::isAdminUser())? 'admin' : Session::getUserRole();
            $user_role = str_replace(" ","_", $user_role);
            $pages = Config::getPages(strtoupper($user_role."_PAGES"));
            Config::setJsConfig('allPages', $pages);
        else:
            Config::setJsConfig('allPages', '');
        endif;
        Config::setJsConfig('isDeliveryClient', Session::isDeliveryClientUser());
        //Assign courier ids to javascript
        Config::setJsConfig('eParcelId', $this->controller->courier->eParcelId);
        Config::setJsConfig('eParcelExpressId', $this->controller->courier->eParcelExpressId);
        Config::setJsConfig('localId', $this->controller->courier->localId);
        Config::setJsConfig('directFreightId', $this->controller->courier->directFreightId);
        ?>
        <!-- Assign all configuration variables -->
        <script>config = <?php echo json_encode(Config::getJsConfig()); ?>;</script>
        <!-- Jquery JavaScript -->
        <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
        <!--script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script-->
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <!-- Validation JavaScript -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" ></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js" ></script>
        <script src="/scripts/form_validators.js?t=<?php echo time();?>" ></script>
        <!-- Bootstrap JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
        <!-- Bootstrap Select Styling -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
        <!-- CKEditor JavaScript -->
        <script src="/scripts/ckeditor5/build/ckeditor.js"></script>
        <!-- DataTables JavaScript -->
        <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
        <!--script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script-->
        <script src="//cdn.jsdelivr.net/mark.js/8.6.0/jquery.mark.min.js"></script>
        <script src="//cdn.jsdelivr.net/datatables.mark.js/2.0.0/datatables.mark.min.js"></script>
        <!-- Block UI JavaScript -->
        <script src="/scripts/jquery.blockUI.js"></script>
        <!-- Print Area Javascript -->
        <script src="/scripts/jquery.PrintArea.js"></script> 
        <!-- Live Filter JavaScript -->
        <!--script src="/scripts/jquery.liveFilter.js"></script-->
        <!-- Filter Tables JavaScript -->
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
        <!-- FontAwesome Pro Kit>
        <script src="https://kit.fontawesome.com/cc79da085a.js"></script-->
        <script src="https://kit.fontawesome.com/f8feb6c33e.js"></script>
        <!-- Bootstrap Colour Picker -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js" integrity="sha512-INXxqXxcP6zawSei7i47Xmu+6ZIBRbsYN1LHAy5H1gzl1XIfTbI/OLjUcvBnDD8F3ZSVB6mf8asEPTMxz4VNjw==" crossorigin="anonymous"></script>