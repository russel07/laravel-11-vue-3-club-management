let mix = require('laravel-mix');

mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
});

mix.webpackConfig(
	{
		module: {
			rules: [
				{
					test: /\.mjs$/, resolve: { fullySpecified: false },
					include: /node_modules/, 
					type: "javascript/auto" 
				}
			]
		},
	}
);

mix.js('resources/js/main.js', 'public/js/app.js').vue({ version: 3 });

// Compile plain CSS
mix.css('resources/css/app.css', 'public/css/app.css');

mix.copy('resources/images', 'public/images')

//Compile Sass to CSS
mix.sass('resources/sass/app.scss', 'public/css/app.css');