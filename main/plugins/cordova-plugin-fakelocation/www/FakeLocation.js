cordova.define("cordova-plugin-fakelocation.FakeLocation", function(require, exports, module) { var fakeLocation = {
  check: function (successCallback, errorCallback) {
    cordova.exec(successCallback, errorCallback, 'FakeLocation', 'check', []);
  }
}

if (!window.plugins) { window.plugins = {}; }

window.plugins.fakeLocation = fakeLocation;
return window.plugins.fakeLocation;
});
