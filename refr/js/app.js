var app = angular.module('refrApp', ['datatables']);

app.controller('refrController', function($scope, $http){
  $scope.dtOptions = {searching:false, bPaginate: false}

  // Wip.
  $scope.canEdit = true;
  $scope.canDelete = true;
  $scope.canDownload = true;
  $scope.success = false;
  $scope.successMessage = "";

  $http.get('./api/fetch.php?t=a').success(function(data){
     $scope.fileData = data;
    });

  $scope.editFile = function(f) {
    alert("Edit file")
  }

  $scope.deleteFile = function(f) {
    alert("Delete file");
  }

  $scope.downloadFile = function(f) {
    //alert("Download file");
    //console.log(f.obf);
    //console.log(CryptoJS.MD5(f.obf).toString());
    //alert("Download file");
    // $scope.modalTitle = 'Add Data';
    // $scope.submit_button = 'Insert';

    // Set the specific file
    $scope.file = f;
    //console.log(f.file_id);
    $http.get('./api/fetch.php?t=v&fid=' + f.file_id).success(function(data){
      $scope.file.versions = data;
    });
    $scope.openDLModal();
  }



  $scope.downloadFileVersion = function(fv) {
    //console.log(fv);
    //tst = './api/download.php?obfr=' + fv.obf + '&fv=' + fv.version + '&fn=' + $scope.file.location;
    //console.log(tst);
    $http.get('./api/download.php?obfr=' + fv.obf + '&fv=' + fv.version + '&fn=' + $scope.file.location).success(function(data){
      // console.log(data);
      // $.fileDownload(data, {
      //     preparingMessageHtml: "Downloading...",
      //     failMessageHtml: "Error, please try again."
      // });
      //e.preventDefault();  //stop the browser from following
      window.location.href = data;
      //window.location.href = data;
    });
  }

  $scope.addNewFile = function() {
    $scope.openAddNewModal();
    //alert("yo!");
  }


   $scope.openDLModal = function(){
    var modal_popup = angular.element('#downloadModal');
    modal_popup.modal('show');
   };

   $scope.closeDLModal = function(){
    var modal_popup = angular.element('#downloadModal');
    modal_popup.modal('hide');
   };

   $scope.openAddNewModal = function() {
     var modal_popup = angular.element("#addNewModal");
     $(document).on('change', '.file-input', function() {
        var filesCount = $(this)[0].files.length;

        var textbox = $(this).prev();

        if (filesCount === 1) {
        var fileName = $(this).val().split('\\').pop();
        textbox.text(fileName);
        } else {
        textbox.text(filesCount + ' files selected');
        }
    });
     modal_popup.modal('show');
   }

   $scope.closeAddNewModal = function() {
     var modal_popup = angular.element("#addNewModal");
     modal_popup.modal('hide');
   }
});
