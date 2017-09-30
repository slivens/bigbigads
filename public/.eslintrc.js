module.exports = {
	parser: 'babel-eslint',
	parserOptions: {
		sourceType: 'module'
	},
	"extends": "standard",
	"env": {
		"browser": true,
		"jquery": true,
		"es6": true
	},
	"rules": {
		"indent": ["error", 4],
		"space-before-function-paren": ["error", "never"],
		"quotes": ["off", "double"],
		"curly": ["off", "all"],
		"eqeqeq": ["off"]
	},
	// TODO: 尽量不要有全局对象
	"globals": {
		"angular": true,
		"moment": true,
		"Highcharts": true,
		"Intercom": true,
		"intercomSettings": true
	}
};
