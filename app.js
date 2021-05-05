const http = require('http');
const express = require('express');
const app = express();

const hostname = '127.0.0.1';
const port = 3000;

// const server = http.createServer((req, res) => {
//   res.statusCode = 200;
//   res.setHeader('Content-Type', 'text/plain');
//   res.end(' Another change has been made.');
// });
//
// server.listen(port, hostname, () => {
//   console.log(`Server running at http://${hostname}:${port}/`);
// });

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "pug");

app.get("/", (req, res) => {
  res.render("index", { title: "Home" });
});

// app.get('/', (req, res) => {
//   res.write(`<!doctype html><html><head>
//    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"
//    integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous"/>
//    <style> .link-item { margin-right: 20px} </style>
//    </head><body><div class="container-fluid">`);
//   res.end();
// });

app.listen(port, () => console.log(`Example app listening at http://localhost:${port}`));
