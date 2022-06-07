
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_common.php");?>
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/footer_scripts.php");?>

        <script>
            //the actions for each page
            var actions = {
                common: {
                    init: function(){
                        $('button.ship_quote').click(function(e)
                        {
                            e.preventDefault();
                            shippingQuote.getQuotes($(this).data('orderid'), $(this).data('destination'));
                        });
                    }
                },
                'get-quotes': {
                    init: function(){
                        shippingEstimates();
                    }
                },
                'book-courier': {
                    init: function(){
                        autoCompleter.addressAutoComplete($('#address'));
                        autoCompleter.suburbAutoComplete($('#suburb'));
                        /*  */ 
                        $('select#state, #postcode').change(function(e){
                            $(this).valid();
                        });

                        $("a.add-package").click(function(e){
                            e.preventDefault();
                            var package_count = $("div#packages_holder div.apackage").length;
                            //console.log('packages: '+package_count);
                            var data = {
                                i: package_count
                            }
                            $.post('/ajaxfunctions/addQuotePackage', data, function(d){
                                $('div#packages_holder').append(d.html);
                            });
                        });
                        $("a#remove-all-packages").click(function(e){
                            e.preventDefault();
                            $('div#packages_holder div.apackage').not(':first').remove();
                        });
                    }
                }
            }
            //console.log('current page: '+config.curPage);
            //run the script for the current page
            actions[config.curPage].init();
        </script>
        <?php Database::closeConnection(); ?>
    </body>
</html>