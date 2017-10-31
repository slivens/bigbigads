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
        "space-before-function-paren":["error", "never"],
        "quotes": ["off", "double"],
        "curly": ["off", "all"],
        "eqeqeq": ["off"]
    }
};
