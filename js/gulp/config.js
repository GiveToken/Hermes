var p = require('./util/PathHelper').create('css', 'js', 'vendor'),
    path = require('path');

module.exports = {
    output: {
        js_build: path.join('public', 'js', 'dist'),
        js_mnm: path.join('public', 'js'),
        css_build: path.join('public', 'css', 'dist'),
        css_mnm: path.join('public', 'css')
    },
    builds: {
        /**
         * MAIN SIZZLE APPLICATION BUILD
         */
        sizzle: {
            vendor_js: [
            /** TODO: These should generally be pulled from one of the package manager directories **/
                p.vendor_js('bootstrap.min'),
                p.vendor_js('smoothscroll'),
                p.vendor_js('jquery.scrollTo.min'),
                p.vendor_js('jquery.localScroll.min'),
                p.vendor_js('jquery-ui.min'),
                p.vendor_js('simple-expand.min'),
                p.vendor_js('wow'),
                p.vendor_js('jquery.stellar.min'),
                p.vendor_js('retina-1.1.0.min'),
                p.vendor_js('matchMedia'),
                p.vendor_js('jquery.ajaxchimp.min')
            ],
            vendor_css: [
                p.css('owl.theme'),
                p.css('owl.carousel'),
                p.css('font-awesome.min'),
                p.css('animate')
            ],
            js: [
                p.js('Sizzle'),
                p.component('Form'),
                p.component('Slider'),
                p.controller('controller_factory'),
                p.controller('Pricing'),
                p.js('custom'),
                p.js('account')
            ],
            css: [
                // Vanilla css
                p.css('styles'),

                // Stylus
                p.styl('variables'),
                p.styl('Pricing')
            ]
        }
    },

    minify_and_migrate: {
        js: [
            p.vendor_js('react.min'),
            p.vendor_js('JSXTransformer'),
            p.js('create_common'),
            p.js('create_recruiting'),
            p.js('free-trial.min'),
            p.js('contact'),
            p.js('login'),
            p.js('signup')
        ],

        json: [
            path.join('js', 'api-v1.json')
        ],

        css: [
            p.css('create_recruiting'),
            p.css('datatables'),
            p.css('at-at'),
            p.css('ball-robot')
        ]
    }
};
