
App.run(function() {
    function toggle(e) {
        if (e) {
            e.preventDefault();
        }

        //If window is small enough, enable sidebar push menu
        if (window.innerWidth <= 992) {
            var rowOffcanvas = document.querySelector('.row-offcanvas');
            rowOffcanvas.classList.toggle('active');
            rowOffcanvas.classList.toggle('relative');
        }

        document.querySelector('.left-side').classList.toggle('collapse-left');
        document.querySelector('.right-side').classList.toggle('strech');
    }

    document.querySelector('.sidebar-menu a').onclick = toggle;
    document.querySelector('#offcanvas').onclick = toggle;
});
