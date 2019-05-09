var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('src/Resources/public/')
    .addEntry('contao-tab-control-bundle', './src/Resources/assets/js/contao-tab-control-bundle.js')
    .addEntry('tabcontrol-backend', './src/Resources/assets/js/tabcontrol-backend.js')
    .addEntry('bootstrap-tabs', 'bootstrap/js/dist/tab')
    .setPublicPath('/bundles/contaotabcontrol')
    .setManifestKeyPrefix('bundles/contaotabcontrol')
    .disableSingleRuntimeChunk()
    .addExternals({
        'bootstrap': 'bootstrap',
        'jquery': 'jQuery'
    })
    .enableSourceMaps(!Encore.isProduction())
    // css
    .enableSassLoader()
    .enablePostCssLoader()
;

module.exports = Encore.getWebpackConfig();