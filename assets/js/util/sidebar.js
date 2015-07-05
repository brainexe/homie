var width = window.innerWidth;

//Enable sidebar toggle
var toggle = function (e) {
    e && e.preventDefault();

    //If window is small enough, enable sidebar push menu
    if (width <= 992) {
        var rowOffcanvas = document.getElementsByClassName('row-offcanvas');
        rowOffcanvas.classList.toggle('active');
        rowOffcanvas.classList.toggle("relative");
    }
    document.querySelector('.left-side').classList.toggle("collapse-left");
    document.querySelector('.right-side').classList.toggle("strech");
};
document.querySelector('.sidebar-menu a').onclick = function() {
    if (width <= 992) {
        toggle()
    }
};
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
    var height  = window.innerHeight - document.querySelector("body > .header").offsetHeight;
    var wrapper = document.getElementById('wrapper');
    wrapper.style.minHeight = height + "px";

    document.querySelector(".left-side, html, body").style.minHeight = Math.min(height, wrapper.offsetHeight) + "px";
}

//Fire upon load
_fix();
//Fire when wrapper is resized
window.onresize = function () {
    _fix();
};
