var banner = window.banner || {};

banner.main = {
    init: function () {
        banner.debug = true;
        if (banner.debug) {

            console.log('banner.main');
        }
        banner.main.blackFriday = this.getCookie('blackfriday');
        this.clearBlackFridayCookie();
        this.closeBlackModal();
        this.changeBannerBackground();

    },
    setCookie: function (name, value, hours) {
        var expires = "";
        if (hours) {
            var date = new Date();
            date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    },
    getCookie: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    },
    eraseCookie: function (name) {
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;"
    },

    clearBlackFridayCookie: function () {
        $j(".opc-btn-checkout").on("click", function () {
            banner.main.eraseCookie("blackfriday");
        });
    },

    closeBlackModal: function () {
        $j("#blackFriday .closeBlack").on('click', function () {
            $j('#blackFriday').modal('hide');
        });
    },

    changeBannerBackground: function() {
        $j(".banner-background").each(function() {
            var image_desktop = $j(this).attr('data-desktop');
            var image_mobile = $j(this).attr('data-mobile');
            if ($j(window).width() < 768) {
                $j(this).attr('data-lazy-background',image_mobile);
            }
            else {
                $j(this).attr('data-lazy-background',image_desktop);
            }
        });
    }

};

$j(document).ready(function () {
    banner.main.init();

    $j('#blackFriday').on('hidden.bs.modal', function (e) {
        banner.main.setCookie("blackfriday", "1", 1);
    })
});

$j(window).resize(function () {
    banner.main.init();

});
$j(window).on('load', function () {

    if (!banner.main.blackFriday) {
        $j('#blackFriday').modal('show');
    }
});

