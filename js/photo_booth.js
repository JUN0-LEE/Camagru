function store_src(src) {
  if (
    document.getElementById("video-canvas-container").style.display === "none"
  ) {
    if (!document.getElementById("frame").src) {
      return;
    }
    let elem = document.createElement("img");
    let set = 0;
    elem.setAttribute("src", src);
    elem.setAttribute("class", "filter");
    if (!document.getElementById("place-here").hasChildNodes()) {
      document.getElementById("place-here").appendChild(elem);
      set = 1;
    } else {
      let currentSrc = document.getElementById("place-here").firstChild.src;
      if (currentSrc == src) {
        let list = document.getElementById("place-here");
        list.removeChild(list.childNodes[0]);
        set = 0;
      } else {
        let item = document.getElementById("place-here").childNodes[0];
        let parent = document.getElementById("place-here");
        parent.replaceChild(elem, item);
        set = 1;
      }
    }
    if (set)
      document.getElementById("filter-path").value = src.replace(
        /^.*[\\\/]/,
        ""
      );
    else document.getElementById("filter-path").value = "";
  } else {
    let elem = document.createElement("img");
    let set = 0;
    elem.setAttribute("src", src);
    elem.setAttribute("src", src);
    elem.setAttribute("class", "filter");
    if (!document.getElementsByClassName("filter")[0]) {
      document.getElementById("canvas-container").appendChild(elem);
      set = 1;
    } else {
      let currentSrc = document.getElementsByClassName("filter")[0].src;
      if (currentSrc != src) {
        let item = document.getElementsByClassName("filter");
        let parent = document.getElementById("canvas-container");
        parent.replaceChild(elem, item[0]);
        set = 1;
      } else {
        let parent = document.getElementById("canvas-container");
        let child = document.getElementsByClassName("filter")[0];
        parent.removeChild(child);
        set = 0;
      }
    }
    if (document.getElementsByClassName("filter")[0]) {
      let filter = document.getElementsByClassName("filter")[0];
      filter.style.top = document.getElementById("y").value + "px";
      filter.style.left = document.getElementById("x").value + "px";
      filter.style.width = document.getElementById("w").value + "px";
      filter.style.height = document.getElementById("h").value + "px";
    }
    if (set)
      document.getElementById("filter-path").value = src.replace(
        /^.*[\\\/]/,
        ""
      );
    else document.getElementById("filter-path").value = "";
  }
}

function changeX(n) {
  if (
    !document.getElementById("place-here").hasChildNodes() &&
    !document.getElementsByClassName("filter")[0]
  )
    return;
  else if (document.getElementById("place-here").hasChildNodes()) {
    let elem = document.getElementsByClassName("filter");
    elem[0].style.left = n + "px";
  } else if (document.getElementsByClassName("filter")[0]) {
    let elem = document.getElementsByClassName("filter")[0];
    elem.style.left = n + "px";
  }
}
function changeY(n) {
  if (
    !document.getElementById("place-here").hasChildNodes() &&
    !document.getElementsByClassName("filter")[0]
  )
    return;
  else if (document.getElementById("place-here").hasChildNodes()) {
    let elem = document.getElementsByClassName("filter");
    elem[0].style.top = n + "px";
  } else if (document.getElementsByClassName("filter")[0]) {
    let elem = document.getElementsByClassName("filter")[0];
    elem.style.top = n + "px";
  }
}
function changeW(n) {
  if (
    !document.getElementById("place-here").hasChildNodes() &&
    !document.getElementsByClassName("filter")[0]
  )
    return;
  else if (document.getElementById("place-here").hasChildNodes()) {
    let elem = document.getElementsByClassName("filter");
    elem[0].style.width = n + "px";
  } else if (document.getElementsByClassName("filter")[0]) {
    let elem = document.getElementsByClassName("filter")[0];
    elem.style.width = n + "px";
  }
}

function changeH(n) {
  if (
    !document.getElementById("place-here").hasChildNodes() &&
    !document.getElementsByClassName("filter")[0]
  )
    return;
  else if (document.getElementById("place-here").hasChildNodes()) {
    let elem = document.getElementsByClassName("filter");
    elem[0].style.height = n + "px";
  } else if (document.getElementsByClassName("filter")[0]) {
    let elem = document.getElementsByClassName("filter")[0];
    elem.style.height = n + "px";
  }
}

function get_img_infos() {
  const elem = document.getElementById("frame");
  document.getElementById("img-width").value = parseInt(
    window.getComputedStyle(elem).width
  );
  document.getElementById("img-height").value = parseInt(
    window.getComputedStyle(elem).height
  );
}

window.onload = function() {
  let camera = document.querySelector("#video-canvas-container");
  let upload = document.querySelector("#upload-img-container");

  let cameraTab = document.querySelector("#camera-tab");
  let uploadTab = document.querySelector("#upload-tab");
  let genPhoto = document.getElementById("generate-photo");

  uploadTab.addEventListener("click", function() {
    camera.style.display = "none";
    upload.style.display = "flex";
    genPhoto.style.display = "block";
  });
  cameraTab.addEventListener("click", function() {
    camera.style.display = "flex";
    upload.style.display = "none";
    genPhoto.style.display = "none";
  });
};
