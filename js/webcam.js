var player = document.getElementById("player");
var snapshotCanvas = document.getElementById("snapshot");
var captureButton = document.getElementById("capture");

var handleSuccess = function(stream) {
  player.srcObject = stream;
  // player.play();
};
let form = document.getElementById("input-form");

captureButton.addEventListener("click", function() {
  //   console.log(snapshot);a
  let snapshot = document.createElement("canvas");
  snapshot.width = 400;
  snapshot.height = 300;
  var context = snapshot.getContext("2d");
  context.drawImage(player, 0, 0, 400, 300);
  var dataURL = snapshot.toDataURL("image/png");
  document.getElementById("hidden_data").value = dataURL;
  // console.log(document.getElementById("x").value);
  document.getElementById("hidden_x").value = document.getElementById(
    "x"
  ).value;
  document.getElementById("hidden_y").value = document.getElementById(
    "y"
  ).value;
  document.getElementById("hidden_h").value = document.getElementById(
    "h"
  ).value;
  document.getElementById("hidden_w").value = document.getElementById(
    "w"
  ).value;
  document.getElementById("hidden_f").value = document.getElementById(
    "filter-path"
  ).value;
  document.getElementById("face-width").value = parseInt(
    window.getComputedStyle(player).width
  );
  document.getElementById("face-height").value = parseInt(
    window.getComputedStyle(player).height
  );
  //console.log(window.getComputedStyle(player).height);
  var fd = new FormData(document.forms["form1"]);
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "../photo_booth.php", true);
  xhr.upload.onprogress = function(e) {
    if (e.lengthComputable) {
      var percentComplete = (e.loaded / e.total) * 100;
      console.log(percentComplete + "% uploaded");
      alert("Successfully uploaded");
    }
  };
  // xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  xhr.onload = function() {};
  xhr.send(fd);
});

navigator.mediaDevices.getUserMedia({ video: true }).then(handleSuccess);

function getDataUri(url, callback) {
  let image = new Image(400, (this.naturalHeight / this.naturalWidth) * 400);
  image.onload = function() {
    let canvas = document.createElement("canvas");
    canvas.width = 400;
    canvas.height = (this.naturalHeight / this.naturalWidth) * 400;

    canvas.getContext("2d").drawImage(this, 0, 0, 400, canvas.height);

    callback(canvas.toDataURL("image/png"));
  };
  image.src = url;
}

let preview = document.getElementById("preview");
let frame = document.getElementById("frame");

preview.addEventListener("change", function(e) {
  let file = e.target.files[0];
  frame.src = URL.createObjectURL(file);
  getDataUri(URL.createObjectURL(file), function(dataUri) {
    document.getElementById("hidden_data2").value = dataUri;
    let fd = new FormData(document.forms["form2"]);

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "../photo_booth.php", true);
    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        var percentComplete = (e.loaded / e.total) * 100;
        console.log(percentComplete + "% uploaded");
        alert("Successfully uploaded");
      }
    };
    xhr.onload = function() {};
    xhr.send(fd);
  });
});

document.getElementById("video-canvas-container").style.display = "none";
