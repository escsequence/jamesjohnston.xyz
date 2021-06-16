var app = angular.module("jamesjohnstonApp", ["ngRoute"]);

app.config(function($routeProvider, $locationProvider) {
  $routeProvider
  .when("/", {
    templateUrl : "views/index.html",
    controller: "indexController"
  })
  .when("/404", {
    templateUrl : "views/error.html",
    controller: "errorController"
  })
  .when("/blog", {
      templateUrl : "views/blog.html",
      controller: "blogController"
  })
  .when("/blog/:pid", {
      templateUrl : "views/blog-post.html",
      controller: "blogPostController"
  })
  .when("/quizem", {
    redirectTo: function(obj,path,search) {
        window.location.href=path;
    }
  })
  .otherwise({redirectTo:'/404'});
  $locationProvider.html5Mode(true);
  $locationProvider.hashPrefix('');
});


app.controller('navigationController', ['$scope', function($scope) {
  $scope.isRoot = function() {
    return window.location.pathname.replace("/jamesjohnston.xyz", "") == "/";
  }

  $(".bk_t_p").on("click", function() {
    console.log("bk")
  });

  $('a.js-scroll-trigger[href*="#"]:not([href="#"])').click(function () {
      if (location.pathname.replace(/^\//, "") == this.pathname.replace(/^\//, "") && location.hostname == this.hostname) {
          var target = $(this.hash);
          var hash_target = this.hash.replace("#", "")
          target = target.length ? target : $("[name=" + this.hash.slice(1) + "]");
          if (target.length) {
              $("html, body").animate({scrollTop: target.offset().top,},
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

app.controller('errorController', ['$scope', function($scope) {
  // Error random titles for fun. :)
  var random_titles = ["Uh-oh.", "Whoops.", "Oh-noes.", "No bueno.", "Its gone.", "This is embarrassing.", "Uh-oh.", "Uh-oh.", "Uh-oh.", "Uh-oh.", "Uh-oh.", "Uh-oh."];
  $scope.error_title = random_titles[Math.floor((Math.random() * random_titles.length))];
}]);

app.controller('indexController', ['$scope', function($scope) {

  //window.location = "#about";
  $(".nav-link[href='#about']").addClass("active");
}]);

app.controller('blogController', ['$scope', '$http', function($scope, $http) {
  $(".navbar-collapse").collapse("hide");
  $http.get("../php/blog_api.php?q=p").then(function (data){
      $scope.pts = data.data;
       data.data.forEach(function(e, i){
        $http.get("../php/blog_api.php?q=pt&id=" + e.pid).then(function (data){
          $scope.pts[i].tags = data.data;
        });
      });
  });
}]);

app.controller('blogPostController', ['$scope', '$http', '$routeParams', '$location', '$sce', function($scope, $http, $routeParams, $location, $sce) {
  $scope.data_loaded = false;
  $http.get("../php/blog_api.php?q=p&id=" + $routeParams.pid).then(function (data){
      $scope.pt = data.data[0];
      $scope.pt.content =  $sce.trustAsHtml($scope.pt.content);

      // Check if anyting was found..
      if (data.data.length > 0) {
        $scope.data_loaded = true;

        $http.get("../php/blog_api.php?q=pt&id=" + $scope.pt.pid).then(function (data){
          $scope.pt.tags = data.data;
        });

      } else {
        $scope.data_loaded = false;
        $location.url('/404');
      }
  });
}]);
