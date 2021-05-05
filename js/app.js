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
  .when("/blog", {
      templateUrl : "views/blog.html"
  })
});

app.controller('NavigationController', ['$scope',
function($scope) {
  $scope.isRoot = function() {
    return window.location.pathname.replace("/jamesjohnston.xyz", "") == "/";
  }
}]);
