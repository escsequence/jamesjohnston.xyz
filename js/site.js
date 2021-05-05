$(document).ready(function() {
  function isRoot() {
    return window.location.pathname.replace("/jamesjohnston.xyz", "") == "/";
  }
  // Random fun
  // $(".fancy-h1").each((i, e) => {
  //   $(e).children().each((ix, ex) => {
  //     $ex = $(ex);
  //     let nm = ex.innerHTML;
  //     let flip = true;
  //     $ex.empty();
  //     for (var ic = 0; ic < nm.length; ++ic) {
  //       let charizard = nm.charAt(ic);
  //
  //       if (flip)
  //         $spanx = $("<span class='fun-char'></span>").append(charizard);
  //       else
  //         $spanx = $("<span class='fun-char-invert'></span>").append(charizard);
  //
  //       flip = !flip;
  //       $ex.append($spanx);
  //     }
  //   })
  // })
});
