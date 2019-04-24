var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableVueLoader()
    .enableSingleRuntimeChunk()
    .configureBabel(() => {}, {
        useBuiltIns: 'entry',
        corejs: 3
    })

    .addEntry('dashboard', './assets/js/dashboard.js')
;

module.exports = Encore.getWebpackConfig();
