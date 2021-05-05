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

  // Smooth scrolling using jQuery easing
  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function () {
      if (
          location.pathname.replace(/^\//, "") ==
              this.pathname.replace(/^\//, "") &&
          location.hostname == this.hostname
      ) {
          var target = $(this.hash);
          target = target.length
              ? target
              : $("[name=" + this.hash.slice(1) + "]");
          if (target.length) {
              $("html, body").animate(
                  {
                      scrollTop: target.offset().top,
                  },
                  1000,
                  "easeInOutExpo"
              );
              return false;
          }
      }
  });

  // Closes responsive menu when a scroll trigger link is clicked
  $(".js-scroll-trigger").click(function () {
      $(".navbar-collapse").collapse("hide");
  });

  // Activate scrollspy to add active class to navbar items on scroll
  $("body").scrollspy({
      target: "#sideNav",
  });
}]);

app.controller('blogController', ['$scope',
function($scope) {
  console.log("Blog page!")
}]);
