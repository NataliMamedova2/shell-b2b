var Encore = require('@symfony/webpack-encore');
var CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('../../public/build/admin/')
    // public path used by the web server to access the output path
    .setPublicPath('/build/admin')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('js/app', ['./scripts/app.js'])
    .addEntry('js/api', ['./scripts/api.js'])
    .addEntry('js/crud/read', './scripts/modules/crud/read.js')
    .addEntry('js/crud/list', './scripts/modules/crud/list.js')
    .addEntry('js/modules/translations', './scripts/modules/translations/index.js')
    .addEntry('js/modules/site-config', './scripts/modules/site-config/index.js')
    .addEntry('js/modules/cards/block-card', './scripts/modules/cards/block-card.js')
    .addEntry('js/modules/clients/users/send-restore-pass-link', './scripts/modules/clients/users/send-restore-pass-link.js')
    .addEntry('js/modules/clients/users/change-status', './scripts/modules/clients/users/change-status.js')
    .addEntry('js/modules/clients/transaction/export', './scripts/modules/clients/transaction/export.js')

    .addStyleEntry('css/app', ['./style/app.scss'])
    .addStyleEntry('css/api', ['./style/api.scss'])
    .copyFiles({
            from: './themes/images',
            to: 'images/[path][name].[ext]',
        },
    )
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    // .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()
    .disableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    // .enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .autoProvideVariables({
        "window.jQuery": "jquery",
        "jQuery": "jquery",
        "$": "jquery",

    })
    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')
;

module.exports = Encore.getWebpackConfig();
