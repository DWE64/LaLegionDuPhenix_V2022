const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('mainReactManagaUser', './assets/mainReactManageUser.js')
    .addEntry('vendor', './assets/styles/themeHyper/js/vendor.js')
    .addEntry('app_template', './assets/styles/themeHyper/js/app.js')
    .addEntry('demo_dashboard','./assets/styles/themeHyper/js/pages/demo.dashboard.js')
    .addEntry('demo_datatable','./assets/styles/themeHyper/js/pages/demo.datatable-init.js')
    .addEntry('demo_profile','./assets/styles/themeHyper/js/pages/demo.profile.js')
    .addEntry('register', './assets/js/app/register/registration.js')

    /*
     * admin user manage
     */
    .addEntry('modalManageUserApp', './assets/js/admin/manageUser/modalManageUserApp.js')
    .addEntry('adminManageUser', './assets/js/admin/manageUser/manageUser.js')

    /*
     * admin game manage
     */
    .addEntry('modalManageGameApp', './assets/js/admin/manageGame/modalManageGameApp.js')
    .addEntry('adminManageGame', './assets/js/admin/manageGame/manageGame.js')

    /*
     * profil manage
     */
    .addEntry('modalProfilApp', './assets/js/app/profil/modalProfilApp.js')
    .addEntry('profilGameApp', './assets/js/app/profil/profilGameApp.js')
    .addEntry('profilWrapper', './assets/js/app/profil/profilWrapper.js')

    /*
     * contact
     */
    .addEntry('contactApp', './assets/js/app/contact/contactApp.js')
    .addEntry('contactWrapper', './assets/js/app/contact/contactWrapper.js')

    /*
     * activity
     */
    .addEntry('activityApp', './assets/js/app/activity/activityApp.js')
    .addEntry('activityWrapper', './assets/js/app/activity/activityWrapper.js')

    //sortie de plusieur fichier style
    .addStyleEntry('app_core','./assets/styles/app_core.scss')
    .addStyleEntry('app-saas','./assets/styles/themeHyper/scss/app.scss')
    .addStyleEntry('app-dark','./assets/styles/themeHyper/scss/app-dark.scss')
    .addStyleEntry('icons','./assets/styles/themeHyper/scss/icons.scss')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

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

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    .enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
