var Encore = require('@symfony/webpack-encore');

Encore
.setOutputPath('src/Resources/public/js/')
.addEntry('contao-tab-control-bundle', './src/Resources/assets/js/contao-tab-control-bundle.js')
.addEntry('bootstrap-tabs', 'bootstrap/js/dist/tab')
.setPublicPath('/bundles/contaotabcontrol/js')
.setManifestKeyPrefix('bundles/contaotabcontrol/js')
.disableSingleRuntimeChunk()
.addExternals({
    'bootstrap': 'bootstrap',
    'jquery': 'jQuery'
})
.enableSourceMaps(!Encore.isProduction())
;

module.exports = Encore.getWebpackConfig();