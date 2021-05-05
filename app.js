//
// const app = express();
//
// const hostname = '127.0.0.1';
// const port = 3000;

// const server = http.createServer((req, res) => {
//   res.statusCode = 200;
//   res.setHeader('Content-Type', 'text/plain');
//   res.end(' Another change has been made.');
// });
//
// server.listen(port, hostname, () => {
//   console.log(`Server running at http://${hostname}:${port}/`);
// });
//
// app.set("views", path.join(__dirname, "views"));
// app.set("view engine", "pug");
// app.use(express.static(path.join(__dirname, "public")));
//
// app.get("/", (req, res) => {
//   res.render("index", { title: "Home" });
// });

// app.get('/', (req, res) => {
//   res.write(`<!doctype html><html><head>
//    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
//    integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous"/>
//    <style> .link-item { margin-right: 20px} </style>
//    </head><body><div class="container-fluid">`);
//   res.end();
// });



  /**
   * Required External Modules
   */
   const express = require("express");
   const path = require("path");

  /**
   * App Variables
   */
   const app = express();
   const port = process.env.PORT || "8000";

  /**
   *  App Configuration
   */
   app.set("views", path.join(__dirname, "views"));
   app.set("view engine", "pug");
   app.use(express.static(path.join(__dirname, "public")));

  /**
   * Routes Definitions
   */
  //  app.get("/", (req, res) => {
  //     res.render("index", { title: "Home" });
  //  });
  //

  app.get('/', (req, res) => {
    res.render("index", {title: "Home"});
  });

  app.get('*', (req, res) => {
    res.render("error", {title: "404"});
  });

  /**
   * Server Activation
   */
   app.listen(port, () => {
     console.log(`Listening to requests on http://localhost:${port}`);
  });
