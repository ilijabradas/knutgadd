var KnutgaddProduct = {

    toggle: {
        switchers: '.block-related .block-title h2',
        relatedProducts: '#block-related li.product',
        description: '#block-related li.product-description',
        condition: '.block-related .block-title h2.additional-items',
        videoControls: '.play-pause',
        customerNavigationTitle: '#customer-navigation-title',
        customerNavigation: '#customer-navigation',
        currentItemImages: '.current-product-image'

    },
    init: function() {
        $j(this.toggle.switchers).on('click', jQuery.proxy(this.onClick, this));
        $j(this.toggle.relatedProducts).on('mouseover', jQuery.proxy(this.onMouseover, this));
        $j(this.toggle.relatedProducts).on('mouseout', jQuery.proxy(this.onMouseout, this));
        $j(this.toggle.videoControls).on('click', jQuery.proxy(this.playPause, this));
        $j(this.toggle.customerNavigationTitle).on('click', jQuery.proxy(this.showHide, this));
    },

    playPause: function(e) {
        var video = $j(e.currentTarget).next('video');

        if ($j(e.currentTarget).hasClass('play')) {
            video[0].play();
            $j(e.currentTarget).removeClass('play').addClass('pause');
        } else {
            video[0].pause();
            $j(e.currentTarget).removeClass('pause').addClass('play');
        }
    },

    onClick: function(e) {
        $j(this.toggle.switchers).removeClass('current');
        $j(e.currentTarget).addClass('current');
        if ($j(this.toggle.condition).hasClass('current')) {
            $j(this.toggle.description).hide();
            $j(this.toggle.currentItemImages).hide();
            $j(this.toggle.relatedProducts).show().css('display', 'inline-block');
            $j(this.toggle.relatedProducts).addClass('current-view');
        } else {
            $j(this.toggle.description).show();
            $j(this.toggle.relatedProducts).removeClass('current-view');
            $j(this.toggle.relatedProducts).hide();
            $j(this.toggle.currentItemImages).show();
        }
    },

    onMouseover: function(e) {
        if ($j(this.toggle.condition).hasClass('current')) {
            $j(e.currentTarget).addClass('hovered');
        }
    },

    onMouseout: function(e) {
        if ($j(this.toggle.condition).hasClass('current')) {
            $j(e.currentTarget).removeClass('hovered');
        }
    },

    showHide: function(e) {
        $j(this.toggle.customerNavigation).toggle();
    },
};

$j(document).ready(function() {
    KnutgaddProduct.init();
    topActiveLink();
    // billingSelect();
    // checkoutLanguageSetcurrency();
    // if (Mage.Cookies.get('first') == '') {
    //     var data = '';
    //     if (window.location.href.indexOf('/se') > -1) {
    //         data = jQuery("#select-currency option:contains('SEK')").val();
    //     } else {
    //         var data = jQuery("#select-currency option:contains('USD')").val();
    //     }

    //     jQuery('#select-currency').val(data).trigger('change');
    //     Mage.Cookies.set('first', 'no');
    // }
    var countryNamesEnglish = {
        AF: 'Afghanistan',
        AX: 'Åland Islands',
        AL: 'Albania',
        DZ: 'Algeria',
        AS: 'American Samoa',
        AD: 'Andorra',
        AO: 'Angola',
        AI: 'Anguilla',
        AQ: 'Antarctica',
        AG: 'Antigua and Barbuda',
        AR: 'Argentina',
        AM: 'Armenia',
        AW: 'Aruba',
        AU: 'Australia',
        AT: 'Austria',
        AZ: 'Azerbaijan',
        BS: 'Bahamas',
        BH: 'Bahrain',
        BD: 'Bangladesh',
        BB: 'Barbados',
        BY: 'Belarus',
        BE: 'Belgium',
        BZ: 'Belize',
        BJ: 'Benin',
        BM: 'Bermuda',
        BT: 'Bhutan',
        BO: 'Bolivia',
        BA: 'Bosnia and Herzegovina',
        BW: 'Botswana',
        BV: 'Bouvet Island',
        BR: 'Brazil',
        IO: 'British Indian Ocean Territory',
        VG: 'British Virgin Islands',
        BN: 'Brunei',
        BG: 'Bulgaria',
        BF: 'Burkina Faso',
        BI: 'Burundi',
        KH: 'Cambodia',
        CM: 'Cameroon',
        CA: 'Canada',
        CV: 'Cape Verde',
        KY: 'Cayman Islands',
        CF: 'Central African Republic',
        TD: 'Chad',
        CL: 'Chile',
        CN: 'China',
        CX: 'Christmas Island',
        CC: 'Cocos (Keeling) Islands',
        CO: 'Colombia',
        KM: 'Comoros',
        CG: 'Congo - Brazzaville',
        CD: 'Congo - Kinshasa',
        CK: 'Cook Islands',
        CR: 'Costa Rica',
        CI: 'Côte d’Ivoire',
        HR: 'Croatia',
        CU: 'Cuba',
        CY: 'Cyprus',
        CZ: 'Czech Republic',
        DK: 'Denmark',
        DJ: 'Djibouti',
        DM: 'Dominica',
        DO: 'Dominican Republic',
        EC: 'Ecuador',
        EG: 'Egypt',
        SV: 'El Salvador',
        GQ: 'Equatorial Guinea',
        ER: 'Eritrea',
        EE: 'Estonia',
        ET: 'Ethiopia',
        FK: 'Falkland Islands',
        FO: 'Faroe Islands',
        FJ: 'Fiji',
        FI: 'Finland',
        FR: 'France',
        GF: 'French Guiana',
        PF: 'French Polynesia',
        TF: 'French Southern Territories',
        GA: 'Gabon',
        GM: 'Gambia',
        GE: 'Georgia',
        DE: 'Germany',
        GH: 'Ghana',
        GI: 'Gibraltar',
        GR: 'Greece',
        GL: 'Greenland',
        GD: 'Grenada',
        GP: 'Guadeloupe',
        GU: 'Guam',
        GT: 'Guatemala',
        GG: 'Guernsey',
        GN: 'Guinea',
        GW: 'Guinea-Bissau',
        GY: 'Guyana',
        HT: 'Haiti',
        HM: 'Heard &amp; McDonald Islands',
        HN: 'Honduras',
        HK: 'Hong Kong SAR China',
        HU: 'Hungary',
        IS: 'Iceland',
        IN: 'India',
        ID: 'Indonesia',
        IR: 'Iran',
        IQ: 'Iraq',
        IE: 'Ireland',
        IM: 'Isle of Man',
        IL: 'Israel',
        IT: 'Italy',
        JM: 'Jamaica',
        JP: 'Japan',
        JE: 'Jersey',
        JO: 'Jordan',
        KZ: 'Kazakhstan',
        KE: 'Kenya',
        KI: 'Kiribati',
        KW: 'Kuwait',
        KG: 'Kyrgyzstan',
        LA: 'Laos',
        LV: 'Latvia',
        LB: 'Lebanon',
        LS: 'Lesotho',
        LR: 'Liberia',
        LY: 'Libya',
        LI: 'Liechtenstein',
        LT: 'Lithuania',
        LU: 'Luxembourg',
        MO: 'Macau SAR China',
        MK: 'Macedonia',
        MG: 'Madagascar',
        MW: 'Malawi',
        MY: 'Malaysia',
        MV: 'Maldives',
        ML: 'Mali',
        MT: 'Malta',
        MH: 'Marshall Islands',
        MQ: 'Martinique',
        MR: 'Mauritania',
        MU: 'Mauritius',
        YT: 'Mayotte',
        MX: 'Mexico',
        FM: 'Micronesia',
        MD: 'Moldova',
        MC: 'Monaco',
        MN: 'Mongolia',
        ME: 'Montenegro',
        MS: 'Montserrat',
        MA: 'Morocco',
        MZ: 'Mozambique',
        MM: 'Myanmar (Burma)',
        NA: 'Namibia',
        NR: 'Nauru',
        NP: 'Nepal',
        NL: 'Netherlands',
        AN: 'Netherlands Antilles',
        NC: 'New Caledonia',
        NZ: 'New Zealand',
        NI: 'Nicaragua',
        NE: 'Niger',
        NG: 'Nigeria',
        NU: 'Niue',
        NF: 'Norfolk Island',
        MP: 'Northern Mariana Islands',
        KP: 'North Korea',
        NO: 'Norway',
        OM: 'Oman',
        PK: 'Pakistan',
        PW: 'Palau',
        PS: 'Palestinian Territories',
        PA: 'Panama',
        PG: 'Papua New Guinea',
        PY: 'Paraguay',
        PE: 'Peru',
        PH: 'Philippines',
        PN: 'Pitcairn Islands',
        PL: 'Poland',
        PT: 'Portugal',
        PR: 'Puerto Rico',
        QA: 'Qatar',
        RE: 'Réunion',
        RO: 'Romania',
        RU: 'Russia',
        RW: 'Rwanda',
        BL: 'Saint Barthélemy',
        SH: 'Saint Helena',
        KN: 'Saint Kitts and Nevis',
        LC: 'Saint Lucia',
        MF: 'Saint Martin',
        PM: 'Saint Pierre and Miquelon',
        WS: 'Samoa',
        SM: 'San Marino',
        ST: 'São Tomé and Príncipe',
        SA: 'Saudi Arabia',
        SN: 'Senegal',
        RS: 'Serbia',
        SC: 'Seychelles',
        SL: 'Sierra Leone',
        SG: 'Singapore',
        SK: 'Slovakia',
        SI: 'Slovenia',
        SB: 'Solomon Islands',
        SO: 'Somalia',
        ZA: 'South Africa',
        GS: 'South Georgia &amp; South Sandwich Islands',
        KR: 'South Korea',
        ES: 'Spain',
        LK: 'Sri Lanka',
        VC: 'St. Vincent &amp; Grenadines',
        SD: 'Sudan',
        SR: 'Suriname',
        SJ: 'Svalbard and Jan Mayen',
        SZ: 'Swaziland',
        SE: 'Sweden',
        CH: 'Switzerland',
        SY: 'Syria',
        TW: 'Taiwan',
        TJ: 'Tajikistan',
        TZ: 'Tanzania',
        TH: 'Thailand',
        TL: 'Timor-Leste',
        TG: 'Togo',
        TK: 'Tokelau',
        TO: 'Tonga',
        TT: 'Trinidad and Tobago',
        TN: 'Tunisia',
        TR: 'Turkey',
        TM: 'Turkmenistan',
        TC: 'Turks and Caicos Islands',
        TV: 'Tuvalu',
        UG: 'Uganda',
        UA: 'Ukraine',
        AE: 'United Arab Emirates',
        GB: 'United Kingdom',
        US: 'United States',
        UY: 'Uruguay',
        UM: 'U.S. Outlying Islands',
        VI: 'U.S. Virgin Islands',
        UZ: 'Uzbekistan',
        VU: 'Vanuatu',
        VA: 'Vatican City',
        VE: 'Venezuela',
        VN: 'Vietnam',
        WF: 'Wallis and Futuna',
        EH: 'Western Sahara',
        YE: 'Yemen',
        ZM: 'Zambia',
        ZW: 'Zimbabwe'
    };
    var countryNamesSwedish = {
        AF: 'Afghanistan',
        AL: 'Albanien',
        DZ: 'Algeriet',
        VI: 'Amerikanska Jungfruöarna',
        AS: 'Amerikanska Samoa',
        AD: 'Andorra',
        AO: 'Angola',
        AI: 'Anguilla',
        AQ: 'Antarktis',
        AG: 'Antigua och Barbuda',
        AR: 'Argentina',
        AM: 'Armenien',
        AW: 'Aruba',
        AU: 'Australien',
        AZ: 'Azerbajdzjan',
        BS: 'Bahamas',
        BH: 'Bahrain',
        BD: 'Bangladesh',
        BB: 'Barbados',
        BE: 'Belgien',
        BZ: 'Belize',
        BJ: 'Benin',
        BM: 'Bermuda',
        BT: 'Bhutan',
        BO: 'Bolivia',
        BA: 'Bosnien och Hercegovina',
        BW: 'Botswana',
        BV: 'Bouvetön',
        BR: 'Brasilien',
        VG: 'Brittiska Jungfruöarna',
        IO: 'Brittiska territoriet i Indiska oceanen',
        BN: 'Brunei',
        BG: 'Bulgarien',
        BF: 'Burkina Faso',
        BI: 'Burundi',
        KY: 'Caymanöarna',
        CF: 'Centralafrikanska republiken',
        CL: 'Chile',
        CO: 'Colombia',
        CK: 'Cooköarna',
        CR: 'Costa Rica',
        CY: 'Cypern',
        DK: 'Danmark',
        DJ: 'Djibouti',
        DM: 'Dominica',
        DO: 'Dominikanska republiken',
        EC: 'Ecuador',
        EG: 'Egypten',
        GQ: 'Ekvatorialguinea',
        SV: 'El Salvador',
        CI: 'Elfenbenskusten',
        ER: 'Eritrea',
        EE: 'Estland',
        ET: 'Etiopien',
        FK: 'Falklandsöarna',
        FJ: 'Fiji',
        PH: 'Filippinerna',
        FI: 'Finland',
        FR: 'Frankrike',
        GF: 'Franska Guyana',
        PF: 'Franska Polynesien',
        TF: 'Franska sydterritorierna',
        FO: 'Färöarna',
        AE: 'Förenade Arabemiraten',
        GA: 'Gabon',
        GM: 'Gambia',
        GE: 'Georgien',
        GH: 'Ghana',
        GI: 'Gibraltar',
        GR: 'Grekland',
        GD: 'Grenada',
        GL: 'Grönland',
        GP: 'Guadeloupe',
        GU: 'Guam',
        GT: 'Guatemala',
        GG: 'Guernsey',
        GN: 'Guinea',
        GW: 'Guinea-Bissau',
        GY: 'Guyana',
        HT: 'Haiti',
        HM: 'Heardön och McDonaldöarna',
        HN: 'Honduras',
        HK: 'Hongkong (S.A.R. Kina)',
        IN: 'Indien',
        ID: 'Indonesien',
        IQ: 'Irak',
        IR: 'Iran',
        IE: 'Irland',
        IS: 'Island',
        IM: 'Isle of Man',
        IL: 'Israel',
        IT: 'Italien',
        JM: 'Jamaica',
        JP: 'Japan',
        YE: 'Jemen',
        JE: 'Jersey',
        JO: 'Jordanien',
        CX: 'Julön',
        KH: 'Kambodja',
        CM: 'Kamerun',
        CA: 'Kanada',
        CV: 'Kap Verde',
        KZ: 'Kazakstan',
        KE: 'Kenya',
        CN: 'Kina',
        KG: 'Kirgizistan',
        KI: 'Kiribati',
        CC: 'Kokosöarna',
        KM: 'Komorerna',
        CG: 'Kongo-Brazzaville',
        CD: 'Kongo-Kinshasa',
        HR: 'Kroatien',
        CU: 'Kuba',
        KW: 'Kuwait',
        LA: 'Laos',
        LS: 'Lesotho',
        LV: 'Lettland',
        LB: 'Libanon',
        LR: 'Liberia',
        LY: 'Libyen',
        LI: 'Liechtenstein',
        LT: 'Litauen',
        LU: 'Luxemburg',
        MO: 'Macao (S.A.R. Kina)',
        MG: 'Madagaskar',
        MK: 'Makedonien',
        MW: 'Malawi',
        MY: 'Malaysia',
        MV: 'Maldiverna',
        ML: 'Mali',
        MT: 'Malta',
        MA: 'Marocko',
        MH: 'Marshallöarna',
        MQ: 'Martinique',
        MR: 'Mauretanien',
        MU: 'Mauritius',
        YT: 'Mayotte',
        MX: 'Mexiko',
        FM: 'Mikronesien',
        MD: 'Moldavien',
        MC: 'Monaco',
        MN: 'Mongoliet',
        ME: 'Montenegro',
        MS: 'Montserrat',
        MZ: 'Moçambique',
        MM: 'Myanmar (Burma)',
        NA: 'Namibia',
        NR: 'Nauru',
        NL: 'Nederländerna',
        AN: 'Nederländska Antillerna',
        NP: 'Nepal',
        NI: 'Nicaragua',
        NE: 'Niger',
        NG: 'Nigeria',
        NU: 'Niue',
        KP: 'Nordkorea',
        MP: 'Nordmarianerna',
        NF: 'Norfolkön',
        NO: 'Norge',
        NC: 'Nya Kaledonien',
        NZ: 'Nya Zeeland',
        OM: 'Oman',
        PK: 'Pakistan',
        PW: 'Palau',
        PS: 'Palestinska territorierna',
        PA: 'Panama',
        PG: 'Papua Nya Guinea',
        PY: 'Paraguay',
        PE: 'Peru',
        PN: 'Pitcairnöarna',
        PL: 'Polen',
        PT: 'Portugal',
        PR: 'Puerto Rico',
        QA: 'Qatar',
        RO: 'Rumänien',
        RW: 'Rwanda',
        RU: 'Ryssland',
        RE: 'Réunion',
        BL: 'S:t Barthélemy',
        SH: 'S:t Helena',
        KN: 'S:t Kitts och Nevis',
        LC: 'S:t Lucia',
        MF: 'S:t Martin',
        PM: 'S:t Pierre och Miquelon',
        VC: 'S:t Vincent och Grenadinerna',
        SB: 'Salomonöarna',
        WS: 'Samoa',
        SM: 'San Marino',
        SA: 'Saudiarabien',
        CH: 'Schweiz',
        SN: 'Senegal',
        RS: 'Serbien',
        SC: 'Seychellerna',
        SL: 'Sierra Leone',
        SG: 'Singapore',
        SK: 'Slovakien',
        SI: 'Slovenien',
        SO: 'Somalia',
        ES: 'Spanien',
        LK: 'Sri Lanka',
        GB: 'Storbritannien',
        SD: 'Sudan',
        SR: 'Surinam',
        SJ: 'Svalbard och Jan Mayen',
        SE: 'Sverige',
        SZ: 'Swaziland',
        ZA: 'Sydafrika',
        GS: 'Sydgeorgien och Sydsandwichöarna',
        KR: 'Sydkorea',
        SY: 'Syrien',
        ST: 'São Tomé och Príncipe',
        TJ: 'Tadzjikistan',
        TW: 'Taiwan',
        TZ: 'Tanzania',
        TD: 'Tchad',
        TH: 'Thailand',
        CZ: 'Tjeckien',
        TG: 'Togo',
        TK: 'Tokelau',
        TO: 'Tonga',
        TT: 'Trinidad och Tobago',
        TN: 'Tunisien',
        TR: 'Turkiet',
        TM: 'Turkmenistan',
        TC: 'Turks- och Caicosöarna',
        TV: 'Tuvalu',
        DE: 'Tyskland',
        US: 'USA',
        UM: 'USA:s yttre öar',
        UG: 'Uganda',
        UA: 'Ukraina',
        HU: 'Ungern',
        UY: 'Uruguay',
        UZ: 'Uzbekistan',
        VU: 'Vanuatu',
        VA: 'Vatikanstaten',
        VE: 'Venezuela',
        VN: 'Vietnam',
        BY: 'Vitryssland',
        EH: 'Västsahara',
        WF: 'Wallis- och Futunaöarna',
        ZM: 'Zambia',
        ZW: 'Zimbabwe',
        AX: 'Åland',
        AT: 'Österrike',
        TL: 'Östtimor'
    };

    //   var countryCode = Mage.Cookies.get('countryCode');

    // jQuery('select[name="billing[country_id]"] option:selected').val(countryCode);

    // if (window.location.href.indexOf('/se') > -1) {
    //     jQuery('select[name="billing[country_id]"] option:selected').text(countryNamesSwedish[countryCode]);
    // } else {
    //     jQuery('select[name="billing[country_id]"] option:selected').text(countryNamesEnglish[countryCode]);
    // }
});

function topActiveLink() {
    var url = window.location.pathname;
    var activePage = url.substring(url.lastIndexOf('/') + 1);
    jQuery('.top-menu li a').each(function() {
        var linkPage = this.href.substring(this.href.lastIndexOf('/') + 1);

        if (activePage == linkPage) {
            jQuery(this).parent().addClass('active');
        }
    });
}

function billingSelect() {
    if (window.cancel == false) {
    jQuery('select[name="billing[country_id]"]').on('change', function(e) {
        var countryCode = jQuery('select[name="billing[country_id]"]').val();
        console.log(countryCode);
     
     
        if ( Mage.Cookies.get('countryCode') !== null) {
             Mage.Cookies.clear('countryCode');
        }
        Mage.Cookies.set('countryCode', countryCode);

        var uniqueCurrencies = ['CN', 'JP', 'KP', 'SE'];
        var euroCurrency = ['AX', 'AL', 'AD', 'AM', 'AZ',
            'BY', 'BE', 'BA', 'BG', 'HR',
            'CY', 'CZ', 'DK', 'FO', 'GE',
            'GI', 'GR', 'GL', 'HU', 'IS',
            'IE', 'IM', 'IL', 'IT', 'JE',
            'KZ', 'LI', 'LU', 'MK', 'MD',
            'MC', 'ME', 'NL', 'NO', 'PL',
            'PT', 'RU', 'SM', 'RS', 'SK',
            'SI', 'SJ', 'TR', 'UA', 'GB',
            'VA','AT', 'EE', 'FI', 'FR', 
            'DE', 'LV', 'LT', 'ES','RO', 'CH'
        ];

        var data = '';
        if (countryCode == 'CN') {
            // data = jQuery("#select-currency option:contains('CNY')").val();
            data = jQuery("#select-currency option:contains('USD')").val();
        jQuery('#select-currency').val(data).trigger('change');
        }
        if (jQuery.inArray(countryCode, euroCurrency) != '-1') { //if in array
            data = jQuery("#select-currency option:contains('EUR')").val();
        jQuery('#select-currency').val(data).trigger('change');
        }
        if (countryCode == 'JP') {
            // data = jQuery("#select-currency option:contains('JPY')").val();
            data = jQuery("#select-currency option:contains('USD')").val();
        jQuery('#select-currency').val(data).trigger('change');
        }
        if (countryCode == 'KP') {
            data = jQuery("#select-currency option:contains('KRW')").val();
        jQuery('#select-currency').val(data).trigger('change');
        }
        if (countryCode == 'SE') {
            data = jQuery("#select-currency option:contains('SEK')").val();
       jQuery('#select-currency').val(data).trigger('change');
        }
        if (jQuery.inArray(countryCode, euroCurrency) == '-1' && jQuery.inArray(countryCode, uniqueCurrencies) == '-1') {
            data = jQuery("#select-currency option:contains('USD')").val();
       jQuery('#select-currency').val(data).trigger('change');
        }
    });
}

}

function checkoutLanguageSetcurrency() {
    jQuery('.btn-checkout.opc-btn-checkout').on('click', function() {
         Mage.Cookies.clear('first');
         Mage.Cookies.clear('countryCode');
    });

}