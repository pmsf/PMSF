var useLoc=document.getElementById("use-loc");useLoc.checked="true"===localStorage.useLoc,useLoc.onchange=function(){localStorage.useLoc=useLoc.checked};var navBtn=document.querySelector("#nav button");function updateTimes(){for(
// server tells us how many seconds are left we note the
// pageload time and count down from there.
// Yes, this could be a smidge innaccurate, but not by
// more than 1 second or so which doesn't matter.
// And now we don't have to deal with timestamps and dates!
var remains=document.querySelectorAll("div.remain"),i=0;i<remains.length;++i){var element=remains[i],now=(new Date).getTime(),secondsPassed=Math.floor((now-pageLoaded)/1e3),remain=element.getAttribute("disappear")-secondsPassed,min=Math.floor(remain/60),sec=remain%60;element.innerText=0<remain?min+" min "+sec+" sec":"(expired)"}}navBtn.onclick=function(){if("true"!==localStorage.useLoc)return navBtn.disabled=!0,location.href="mobile";"geolocation"in navigator?(
// Getting the GPS position can be very slow on some devices
navBtn.disabled=!0,navBtn.innerText="Locating...",
// Get location and use it!
navigator.geolocation.getCurrentPosition(function(p){navBtn.innerText="Reloading...",location.href="mobile?lat="+p.coords.latitude+"&lon="+p.coords.longitude},function(err){navBtn.innerText="Reload",navBtn.disabled=!1,alert("Failed to get location: "+err.message)},{enableHighAccuracy:!0,timeout:5e3,maximumAge:5e3})):alert("Your device does not support web geolocation")},setInterval(updateTimes,1e3),document.querySelectorAll("li").forEach(function(listItem){listItem.onclick=function(){window.document.location=this.getAttribute("href")}});