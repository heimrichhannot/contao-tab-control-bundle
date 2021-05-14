var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('src/Resources/public/')
    .addEntry('contao-tab-control-bundle', './src/Resources/assets/js/contao-tab-control-bundle.js')
    .addEntry('contao-tab-control-bundle-backend', './src/Resources/assets/js/contao-tab-control-bundle-backend.js')
    .addEntry('bootstrap-tabs', 'bootstrap/js/dist/tab')
    .setPublicPath('/bundles/heimrichhannottabcontrol')
    .setManifestKeyPrefix('bundles/heimrichhannottabcontrol')
    .disableSingleRuntimeChunk()
    .addExternals({
        'bootstrap': 'bootstrap'
    })
    .enableSourceMaps(!Encore.isProduction())
    // css
    .enableSassLoader()
    .enablePostCssLoader()
;

module.exports = Encore.getWebpackConfig();
