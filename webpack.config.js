const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
  mode: 'development',
  entry: {
    'js/app' : './src/js/app.js',
    'js/inicio' : './src/js/inicio.js',
    'js/usuarios/index' : './src/js/usuarios/index.js',
    'js/armamento/index' : './src/js/armamento/index.js',
    'js/login/index' : './src/js/login/index.js',
    'js/personal/index' : './src/js/personal/index.js',    
    'js/asignaciones/index' : './src/js/asignaciones/index.js',
    'js/mapas/index' : './src/js/mapas/index.js',
    'js/estadisticas/index': './src/js/estadisticas/index.js',
    'js/permisos/index' : './src/js/permisos/index.js',
    'js/historial/index' : './src/js/historial/index.js',
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'public/build')
  },
  plugins: [
    new MiniCssExtractPlugin({
        filename: 'styles.css'
    })
  ],
  module: {
    rules: [
      {
        test: /\.(c|sc|sa)ss$/,
        use: [
            {
                loader: MiniCssExtractPlugin.loader
            },
            'css-loader',
            'sass-loader'
        ]
      },
      {
        test: /\.(png|svg|jpe?g|gif)$/,
        type: 'asset/resource',
      },
    ]
  }
};