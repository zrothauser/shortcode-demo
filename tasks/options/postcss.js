module.exports = {
	dist: {
		options: {
			processors: [
				require('autoprefixer')({browsers: 'last 2 versions'})
			]
		},
		files: { 
			'assets/css/shortcode-demo.css': [ 'assets/css/shortcode-demo.css' ]
		}
	}
};