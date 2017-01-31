//!
//! App.js
//!
//! Use this skeleton to create a KnockoutJS view-model for your app.
//!

function App()
{
    var self = this;

    this.greeting = ko.observable('Welcome');
}

var app = new App();

ko.applyBindings(app);
