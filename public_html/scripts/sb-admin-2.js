// Sets the min-height of #page-wrapper to window size or sidebar height
function fixPageWrapperHeight()
{
    var topOffset = 50;
    var height = ( (window.innerHeight > 0) ? window.innerHeight : window.screen.height ) - topOffset;
    var fheight = $('footer#the_footer').height();
    height = height - fheight;
    var mheight = $('ul#side-menu').height();
    height = Math.max(height, mheight);
    //console.log("height: "+height);
    if (height < 1) height = 1;
    if (height > topOffset) {
        $("#page-wrapper").css("min-height", (height) + "px");
    }
}

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.

$(function() {
    $('#side-menu').metisMenu().on('shown.metisMenu', function(e){
        fixPageWrapperHeight();
    }).on('hidden.metisMenu', function(e){
        fixPageWrapperHeight();
    });

    $(window).bind("load resize", function() {

        var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        fixPageWrapperHeight();
    });

    var url = window.location;
    // var element = $('ul.nav a').filter(function() {
    //     return this.href == url;
    // }).addClass('active').parent().parent().addClass('in').parent();
    /*
    var element = $('ul.nav a').filter(function() {
        return this.href == url;
    }).addClass('active').parent();


    while (true) {
        if (element.is('li')) {
            element = element.parent().parent();
            element.find(">:first-child").addClass('active');
            //console.log('added in class');
        } else {
            break;
        }
    }
     */
    var foundpage = false
    for(var cat in config.allPages)
    {
        //console.log("1 doing: "+cat);
        for(var page in config.allPages[cat])
        {
            //console.log("2 doing: "+page);
            if(config.curPage == page)
            {
                //console.log("found: "+page);
                $("li#"+cat+" > a").addClass("active");
                foundpage = true;
                break;
            }
        }
        if(foundpage)
            break;
    }

});

