const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/assets/')
    .setPublicPath('/assets')
    .addEntry('js/likes', './assets/js/likes.js')
    .addStyleEntry('css/dashboard', ['./assets/css/dashboard.css'])
    .addStyleEntry('css/login', ['./assets/css/login.css'])
    .addStyleEntry('css/likes', ['./assets/css/likes.css'])

    // .cleanupOutputBeforeBuild()
    // .enableSourceMaps(!Encore.isProduction())
    // .enableVersioning(Encore.isProduction())
   ;

module.exports = Encore.getWebpackConfig();