<div id="page-wrapper">
    <div id="page_container" class="container-xxl">
        <?php include(Config::get('VIEWS_PATH')."layout/page-includes/page_top.php");?>
        <div class="mt-2 mb-3 py-5 border border-secondary rounded bg-fsg">
        <div id="help-centre-sm-head"  class="row">
            <div class="col text-center">
                <p>What do you need help with?</p>
            </div>
        </div>
        <div id="help-centre-top" class="row">
            <div id="help-centre-searchbar" class="col-lg-6 offset-lg-3 col-md-8 offset-md-2  col-sm-10 offset-sm-1">
                <i class='fa-light fa-magnifying-glass'></i>
                <input type="search" class="form-control" id="help-centre-search" placeholder="Search for help">
            </div>
        </div>
        <div class="row">
            <div class="my-3 p-3 col-md-8 offset-md-2  col-sm-10 offset-sm-1 small">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-2 ">
                        Type a question in the search box above. EG &ldquo;How do I place an order?&rdquo;
                    </li>
                    <li class="list-group-item py-2">
                        Choose a help topic from the drop down list that appears.
                    </li>
                    <li class="list-group-item py-2">
                        Return to the <a href="/help-centre">Help Centre Home</a> to search anew
                    </li>
                    <li class="list-group-item py-2">
                        Alternatively. Choose a general topic from the menu above to browse all relevant help topics
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>