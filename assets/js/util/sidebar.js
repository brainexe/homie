
$(function () {
    var width = window.innerWidth;

    //Enable sidebar toggle
    var toggle = function (e) {
        e && e.preventDefault();

        //If window is small enough, enable sidebar push menu
        if (width <= 992) {
            var rowOffcanvas = $('.row-offcanvas');
            rowOffcanvas.toggleClass('active');
            $('.left-side').removeClass("collapse-left");
            $(".right-side").removeClass("strech");
            rowOffcanvas.toggleClass("relative");
        } else {
            //Else, enable content streching
            $('.left-side').toggleClass("collapse-left");
            $(".right-side").toggleClass("strech");
        }
    };
    $('.sidebar-menu a').click(function () {
        if (width <= 992) {
            toggle()
        }
    });
    document.getElementById('offcanvas').onclick = toggle;

    /*
     * Make sure that the sidebar is streched full height
     * ---------------------------------------------
     * We are gonna assign a min-height value every time the
     * wrapper gets resized and upon page load. We will use
     * Ben Alman's method for detecting the resize event.
     **/
    function _fix() {
        //Get window height and the wrapper height
        var height  = window.innerHeight - $("body > .header").height();
        var wrapper = document.getElementById('wrapper');
        wrapper.style.minHeight = height + "px";

        var content = $(wrapper).height();
        $(".left-side, html, body").css("min-height", Math.min(height, content) + "px");
    }

    //Fire upon load
    _fix();
    //Fire when wrapper is resized
    window.onresize = function () {
        _fix();
    };
});
