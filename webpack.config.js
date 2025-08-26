const Encore = require('@symfony/webpack-encore');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    .addEntry('app', './assets/app.js')

    .addEntry('article_edit', './assets/article_edit.js')

    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader()
    .enableVersioning(Encore.isProduction())
    .enableStimulusBridge('./assets/controllers.json')

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    

    // Copie les fichiers images de assets/images vers public/build/images
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[ext]',
    })

    // Définit l’alias @images pour simplifier les chemins SCSS
    .addAliases({
        '@images': path.resolve(__dirname, 'assets/images'),
    })
;

module.exports = Encore.getWebpackConfig();


