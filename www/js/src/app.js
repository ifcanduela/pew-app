let $ = require('jquery');
let ko = require('knockout');

function AppModel() {
    let self = this;

    self.greeting = ko.observable('Welcome');
}

let app = new AppModel();

ko.applyBindings(app);
