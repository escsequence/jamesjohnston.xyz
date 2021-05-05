var app = angular.module("jamesjohnstonApp", ["ngRoute"]);

app.config(['$locationProvider', function($locationProvider) {
  $locationProvider.html5Mode(true);
  $locationProvider.hashPrefix('');
}]);

app.config(function($routeProvider) {
  $routeProvider
  .when("/", {
    templateUrl : "views/index.html"
  })
});
