/**
 * App.js
 *
 * Use this skeleton to create a JavaScript library for your app. The App
 * object will be available to your views after including app.js.
 *
 * You can add jQuery to the inner scope by passing it as an argument:
 *
 *      var App = (function($)
 *      {
 *          // ... 
 *      }) (jQuery);
 *
 * @author ifcanduela <ifcanduela@gmail.com>
 */
var App = (function () {
    /* Private methods and properties */
    var my_private_property = 42;
    
    /* Public methods and properties */
    return {
        'my_public_property': 3.14,
        'my_public_method': function () {}
    };
})();
