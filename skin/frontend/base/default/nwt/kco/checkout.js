/**
 * Klarna Checkout extension
 *
 * @category    NWT
 * @package     NWT_KCO
 * @copyright   Copyright (c) 2015 Nordic Web Team ( http://nordicwebteam.se/ )
 * @license     http://nordicwebteam.se/licenses/nwtcl/1.0.txt  NWT Commercial License (NWTCL 1.0)
 *
 */

/**
 * NWT Klarna (Onepage) Checkout main script
 */

var KlarnakassanCheckout = Class.create();

KlarnakassanCheckout.prototype = {

    initialize: function(settings){



        this.loadWaiting    = false;
        this.failureUrl     = settings.failure;
        this.ctrlkey        = settings.ctrlkey;
        this.ctrlcookie     = settings.ctrlcookie;
        this.cartcookie     = settings.cartcookie;

        Mage.Cookies.set(settings.ctrlcookie,settings.ctrlkey);

        this.checkIfCartWasUpdated = setInterval((function(){
                if(this.loadWaiting) {
                    return true;
                }
                var ctrlkey = Mage.Cookies.get(this.ctrlcookie);
                if(ctrlkey && ctrlkey != this.ctrlkey) {

                    clearInterval(this.checkIfCartWasUpdated);
                    this.setLoadWaiting(true);

                    $('nwt_overlay').update('<span class="error">Cart was updated, please wait to reload the Checkout page...</span>').show();
                    window.location.reload();

                }
            }).bind(this), 1000
        );

    },

    ajaxFailure: function() {
        $('nwt_overlay').update('<span class="error">An error occured, please wait to be redirected...</span>');
        this.loadWaiting = false; //prevent that resetLoadWaiting hiding loader
        window.location.href = this.failureUrl;
    },

    setLoadWaiting: function(flag) {

        if(flag) {
            if (this.loadWaiting) {
                //already loadWaiting
                return false;
            }
            $('nwt_overlay').show();
            if(window._klarnaCheckout) {
                try { window._klarnaCheckout(function (api) {api.suspend();}); } catch(err) {}
            }
        } else {
            $('nwt_overlay').hide();
            if(window._klarnaCheckout) {
                try { window._klarnaCheckout(function (api) {api.resume();}); } catch(err) {}
            }
        }
        this.loadWaiting = flag;
        return true;
    },

    resetLoadWaiting: function(){
        if(this.loadWaiting) {
            this.setLoadWaiting(false);
        }
    },

    removeCartItem: function(url,msg) {
        if(msg && !confirm(msg)) {
            return false;
        }
        this.ajaxFormSubmit(url);
        return false; //preventDefault
    },

    changeShippingMethod: function(opt) {
        var form = $(opt).up('form');
        if(form) {
            this.ajaxFormSubmit(form);
        }
        return true;
    },

    changeCountry: function(opt) {
        if(opt.value) {
            var form = $(opt).up('form');
            if(form) {
                this.setLoadWaiting(true)
                form.submit();
            }
            return true;
        } else {
            return false;
        }
    },

    updateNewsletter: function(opt) {
        var form = $(opt).up('form');
        if(form) {
            this.ajaxFormSubmit(form);
        }
        return true;
    },
    updateComment: function(opt) {
        var form = $(opt).up('form');
        if(form) {
            this.ajaxFormSubmit(form);
        }
        return true;
    },
    ajaxFormSubmit: function(form) {

        if(!this.setLoadWaiting(true)) {
            return false;
        }


        if(typeof form == 'string') {
            form = {action:form,method:'get',parameters:''}
        } else {
            form.parameters = Form.serialize(form);
        }
        if(form.parameters) {
            form.parameters += '&';
        }
        //add current store & currency
        form.parameters += 'ctrlkey='+this.ctrlkey;

        var request = new Ajax.Request(
            form.action,
            {
                method:form.method.toUpperCase(),
                onSuccess: this.showBlocks.bind(this),
                onFailure: this.ajaxFailure.bind(this),
                onComplete: this.resetLoadWaiting.bind(this),
                parameters: form.parameters
            }
        );
    },


    showBlocks: function(transport) {

        try{
            response = transport.responseText.evalJSON();
        } catch(e) {
            response = {}
        }

        if(response.reload || response.redirect) {
            this.loadWaiting = false; //prevent that resetLoadWaiting hiding loader
            if(!response.messages) {
                response.messages = 'An error occured, reloading the checkout, please wait...';
            }
            $('nwt_overlay').update('<span class="error">'+response.messages+'</span>');
            if (response.redirect) {
                window.location.href = response.redirect;
            } else {
                window.location.reload();
            }
            return true;
        }

        if(response.ctrlkey) {
            this.ctrlkey = response.ctrlkey;
            Mage.Cookies.set(this.ctrlcookie,response.ctrlkey);
        }


        if(response.updates) {

            blocks = response.updates;

            for (var block in blocks) {
                if(blocks.hasOwnProperty(block)){
                    div = $('nwtkco_'+block);
                    if(div) {
                        div.replace(blocks[block]);
                    }
                }
            }
        }
    },

    toggleProducts : function(elem){
        var tar = elem.up(1);
        var cv;
        if(tar.hasClassName('toggled')){
            tar.removeClassName('toggled');
            cv = 0;
        } else {
            cv = 1;
            tar.addClassName('toggled');
        }
        Mage.Cookies.set(this.cartcookie,cv);
    },
    toggleSidebar : function(el){
        if( $(el).hasClassName("nwtkco-hide") ){
            $(el).removeClassName("nwtkco-hide")
        } else {
            $(el).addClassName("nwtkco-hide")
        }
    },
    commentAreaInteract : function(e, flag){
        $(e).up(1).addClassName('focused');
    }
}
