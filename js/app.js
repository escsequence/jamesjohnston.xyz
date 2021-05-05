var app = angular.module("jamesjohnstonApp", ["ngRoute"]);

app.config(function($routeProvider, $locationProvider) {
  $routeProvider
  .when("/", {
    templateUrl : "views/index.html",
    controller: "indexController"
  })
  .when("/blog", {
      templateUrl : "views/blog.html",
      controller: "blogController"
  })

  $locationProvider.html5Mode(true);
  $locationProvider.hashPrefix('');
});

app.controller('navigationController', ['$scope',
function($scope) {
  $scope.isRoot = function() {
    return window.location.pathname.replace("/jamesjohnston.xyz", "") == "/";
  }
}]);

app.controller('indexController', ['$scope',
function($scope) {
  console.log("Index page!")
}]);

app.controller('blogController', ['$scope',
function($scope) {
  console.log("Blog page!")
}]);
