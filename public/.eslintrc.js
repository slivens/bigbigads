module.exports = {
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
    },
    "globals": {
        "angular": true,
        "moment": true,
        "Highcharts": true // TODO: 尽量不会将该对象设置为global
    }
};
