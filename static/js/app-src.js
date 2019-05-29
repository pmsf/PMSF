!function(){
// Methods/polyfills.
Element.prototype.matches||(Element.prototype.matches=Element.prototype.matchesSelector||Element.prototype.mozMatchesSelector||Element.prototype.msMatchesSelector||Element.prototype.oMatchesSelector||Element.prototype.webkitMatchesSelector||function(s){for(var matches=(this.document||this.ownerDocument).querySelectorAll(s),i=matches.length;0<=--i&&matches.item(i)!==this;);return-1<i});
// addEventsListener
function addEventsListener(o,t,e){var n,i=t.split(" ");for(n in i)o.addEventListener(i[n],e)}
// classList | (c) @remy | github.com/remy/polyfills | rem.mit-license.org
!function(){function t(t){for(var n=(this.el=t).className.replace(/^\s+|\s+$/g,"").split(/\s+/),i=0;i<n.length;i++)e.call(this,n[i])}
/* eslint-disable no-unused-expressions */
if(!(void 0===window.Element||"classList"in document.documentElement)){var i=Array.prototype,e=i.push,s=i.splice,o=i.join;t.prototype={add:function(t){this.contains(t)||(e.call(this,t),this.el.className=this.toString())},contains:function(t){return-1!==this.el.className.indexOf(t)},item:function(t){return this[t]||null},remove:function(t){if(this.contains(t))for(var n=0;n<this.length&&this[n]!==t;n++)s.call(this,n,1),this.el.className=this.toString()},toString:function(){return o.call(this," ")},toggle:function(t){return this.contains(t)?this.remove(t):this.add(t),this.contains(t)}},window.DOMTokenList=t,function(t,n,i){Object.defineProperty?Object.defineProperty(t,n,{get:i}):t.__defineGetter__(n,i)}(Element.prototype,"classList",function(){return new t(this);// eslint-disable-line new-cap
})}}();
// Vars.
var $body=document.querySelector("body");
// Breakpoints.
skel.breakpoints({xlarge:"(max-width: 1680px)",large:"(max-width: 1280px)",medium:"(max-width: 980px)",small:"(max-width: 736px)",xsmall:"(max-width: 480px)"}),
// Disable animations/transitions until everything's loaded.
$body.classList.add("is-loading"),window.addEventListener("load",function(){$body.classList.remove("is-loading")});
// Nav.
var $navClose,$statsClose,$gymSidebarClose,$nav=document.querySelector("#nav"),$navToggle=document.querySelector('a[href="#nav"]'),$stats=document.querySelector("#stats"),$statsToggle=document.querySelector('a[href="#stats"]'),$gymSidebar=document.querySelector("#gym-details");
// Event: Prevent clicks/taps inside the nav from bubbling.
addEventsListener($nav,"click touchend",function(event){event.stopPropagation()}),$stats&&
// Event: Prevent clicks/taps inside the stats from bubbling.
addEventsListener($stats,"click touchend",function(event){event.stopPropagation()}),$gymSidebar&&
// Event: Prevent clicks/taps inside the gym sidebar from bubbling.
addEventsListener($gymSidebar,"click touchend",function(event){event.stopPropagation()}),
// Event: Hide nav on body click/tap.
addEventsListener($body,"click touchend",function(event){
// on ios safari, when navToggle is clicked,
// this function executes too, so if the target
// is the toggle button, exit this function
event.target.matches('a[href="#nav"]')||$stats&&event.target.matches('a[href="#stats"]')||($nav.classList.remove("visible"),$stats&&$stats.classList.remove("visible"))}),
// Toggle.
// Event: Toggle nav on click.
$navToggle.addEventListener("click",function(event){event.preventDefault(),event.stopPropagation(),$nav.classList.toggle("visible")}),
// Event: Toggle stats on click.
$statsToggle&&$statsToggle.addEventListener("click",function(event){event.preventDefault(),event.stopPropagation(),$stats.classList.toggle("visible")}),(
// Close.
// Create elements.
$navClose=document.createElement("a")).href="#",$navClose.className="close",$navClose.tabIndex=0,$nav.appendChild($navClose),$stats&&(($statsClose=document.createElement("a")).href="#",$statsClose.className="close",$statsClose.tabIndex=0,$stats.appendChild($statsClose)),($gymSidebarClose=document.createElement("a")).href="#",$gymSidebarClose.className="close",$gymSidebarClose.tabIndex=0,$gymSidebar.appendChild($gymSidebarClose),
// Event: Hide on ESC.
window.addEventListener("keydown",function(event){27===event.keyCode&&($nav.classList.remove("visible"),$stats&&$stats.classList.remove("visible"),$gymSidebar&&$gymSidebar.classList.remove("visible"))}),
// Event: Hide nav on click.
$navClose.addEventListener("click",function(event){event.preventDefault(),event.stopPropagation(),$nav.classList.remove("visible")}),$statsClose&&
// Event: Hide stats on click.
$statsClose.addEventListener("click",function(event){event.preventDefault(),event.stopPropagation(),$stats.classList.remove("visible")}),$gymSidebarClose&&
// Event: Hide stats on click.
$gymSidebarClose.addEventListener("click",function(event){event.preventDefault(),event.stopPropagation(),$gymSidebar.classList.remove("visible")})}();