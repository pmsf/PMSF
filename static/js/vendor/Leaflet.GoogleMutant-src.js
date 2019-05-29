// Based on https://github.com/shramov/leaflet-plugins
// GridLayer like https://avinmathew.com/leaflet-and-google-maps/ , but using MutationObserver instead of jQuery
// 🍂class GridLayer.GoogleMutant
// 🍂extends GridLayer
L.GridLayer.GoogleMutant=L.GridLayer.extend({options:{minZoom:0,maxZoom:23,tileSize:256,subdomains:"abc",errorTileUrl:"",attribution:"",// The mutant container will add its own attribution anyways.
opacity:1,continuousWorld:!1,noWrap:!1,
// 🍂option type: String = 'roadmap'
// Google's map type. Valid values are 'roadmap', 'satellite' or 'terrain'. 'hybrid' is not really supported.
type:"roadmap",maxNativeZoom:21},initialize:function(options){L.GridLayer.prototype.initialize.call(this,options),this._ready=!!window.google&&!!window.google.maps&&!!window.google.maps.Map,this._GAPIPromise=this._ready?Promise.resolve(window.google):new Promise(function(resolve,reject){var checkCounter=0,intervalId=null;intervalId=setInterval(function(){return 10<=checkCounter?(clearInterval(intervalId),reject(new Error("window.google not found after 10 attempts"))):window.google&&window.google.maps&&window.google.maps.Map?(clearInterval(intervalId),resolve(window.google)):void checkCounter++},500)}),
// Couple data structures indexed by tile key
this._tileCallbacks={},// Callbacks for promises for tiles that are expected
this._freshTiles={},// Tiles from the mutant which haven't been requested yet
this._imagesPerTile="hybrid"===this.options.type?2:1,this._boundOnMutatedImage=this._onMutatedImage.bind(this)},onAdd:function(map){L.GridLayer.prototype.onAdd.call(this,map),this._initMutantContainer(),this._GAPIPromise.then(function(){if(this._ready=!0,this._map=map,this._initMutant(),map.on("viewreset",this._reset,this),this.options.updateWhenIdle?map.on("moveend",this._update,this):map.on("move",this._update,this),map.on("zoomend",this._handleZoomAnim,this),map.on("resize",this._resize,this),
//handle layer being added to a map for which there are no Google tiles at the given zoom
google.maps.event.addListenerOnce(this._mutant,"idle",function(){this._checkZoomLevels(),this._mutantIsReady=!0}.bind(this)),
//20px instead of 1em to avoid a slight overlap with google's attribution
map._controlCorners.bottomright.style.marginBottom="20px",map._controlCorners.bottomleft.style.marginBottom="20px",this._reset(),this._update(),this._subLayers)
//restore previously added google layers
for(var layerName in this._subLayers)this._subLayers[layerName].setMap(this._mutant)}.bind(this))},onRemove:function(map){L.GridLayer.prototype.onRemove.call(this,map),map._container.removeChild(this._mutantContainer),this._mutantContainer=void 0,google.maps.event.clearListeners(map,"idle"),google.maps.event.clearListeners(this._mutant,"idle"),map.off("viewreset",this._reset,this),map.off("move",this._update,this),map.off("moveend",this._update,this),map.off("zoomend",this._handleZoomAnim,this),map.off("resize",this._resize,this),map._controlCorners&&(map._controlCorners.bottomright.style.marginBottom="0em",map._controlCorners.bottomleft.style.marginBottom="0em")},getAttribution:function(){return this.options.attribution},setElementSize:function(e,size){e.style.width=size.x+"px",e.style.height=size.y+"px"},addGoogleLayer:function(googleLayerName,options){return this._subLayers||(this._subLayers={}),this._GAPIPromise.then(function(){var googleLayer=new google.maps[googleLayerName](options);return googleLayer.setMap(this._mutant),this._subLayers[googleLayerName]=googleLayer}.bind(this))},removeGoogleLayer:function(googleLayerName){var googleLayer=this._subLayers&&this._subLayers[googleLayerName];googleLayer&&(googleLayer.setMap(null),delete this._subLayers[googleLayerName])},_initMutantContainer:function(){this._mutantContainer||(this._mutantContainer=L.DomUtil.create("div","leaflet-google-mutant leaflet-top leaflet-left"),this._mutantContainer.id="_MutantContainer_"+L.Util.stamp(this._mutantContainer),this._mutantContainer.style.zIndex="800",//leaflet map pane at 400, controls at 1000
this._mutantContainer.style.pointerEvents="none",L.DomEvent.off(this._mutantContainer),this._map.getContainer().appendChild(this._mutantContainer)),this.setOpacity(this.options.opacity),this.setElementSize(this._mutantContainer,this._map.getSize()),this._attachObserver(this._mutantContainer)},_initMutant:function(){if(this._ready&&this._mutantContainer){this._mutantCenter=new google.maps.LatLng(0,0);var map=new google.maps.Map(this._mutantContainer,{center:this._mutantCenter,zoom:0,tilt:0,mapTypeId:this.options.type,disableDefaultUI:!0,keyboardShortcuts:!1,draggable:!1,disableDoubleClickZoom:!0,scrollwheel:!1,streetViewControl:!1,styles:this.options.styles||{},backgroundColor:"transparent"});this._mutant=map,google.maps.event.addListenerOnce(map,"idle",function(){for(var nodes=this._mutantContainer.querySelectorAll("a"),i=0;i<nodes.length;i++)nodes[i].style.pointerEvents="auto"}.bind(this)),
// 🍂event spawned
// Fired when the mutant has been created.
this.fire("spawned",{mapObject:map})}},_attachObserver:function(node){
// pass in the target node, as well as the observer options
new MutationObserver(this._onMutations.bind(this)).observe(node,{childList:!0,subtree:!0})},_onMutations:function(mutations){for(var i=0;i<mutations.length;++i)for(var mutation=mutations[i],j=0;j<mutation.addedNodes.length;++j){var node=mutation.addedNodes[j];node instanceof HTMLImageElement?this._onMutatedImage(node):node instanceof HTMLElement&&(Array.prototype.forEach.call(node.querySelectorAll("img"),this._boundOnMutatedImage),
// Check for, and remove, the "Google Maps can't load correctly" div.
// You *are* loading correctly, you dumbwit.
"white"===node.style.backgroundColor&&L.DomUtil.remove(node),
// Check for, and remove, the "For development purposes only" divs on the aerial/hybrid tiles.
0===node.textContent.indexOf("For development purposes only")&&L.DomUtil.remove(node),
// Check for, and remove, the "Sorry, we have no imagery here"
// empty <div>s. The [style*="text-align: center"] selector
// avoids matching the attribution notice.
// This empty div doesn't have a reference to the tile
// coordinates, so it's not possible to mark the tile as
// failed.
Array.prototype.forEach.call(node.querySelectorAll('div[draggable=false][style*="text-align: center"]'),L.DomUtil.remove))}},
// Only images which 'src' attrib match this will be considered for moving around.
// Looks like some kind of string-based protobuf, maybe??
// Only the roads (and terrain, and vector-based stuff) match this pattern
_roadRegexp:/!1i(\d+)!2i(\d+)!3i(\d+)!/,
// On the other hand, raster imagery matches this other pattern
_satRegexp:/x=(\d+)&y=(\d+)&z=(\d+)/,
// On small viewports, when zooming in/out, a static image is requested
// This will not be moved around, just removed from the DOM.
_staticRegExp:/StaticMapService\.GetMapImage/,_onMutatedImage:function(imgNode){
// 		if (imgNode.src) {
// 			console.log('caught mutated image: ', imgNode.src);
// 		}
var coords,match=imgNode.src.match(this._roadRegexp),sublayer=0;if(match?(coords={z:match[1],x:match[2],y:match[3]},1<this._imagesPerTile&&(sublayer=imgNode.style.zIndex=1)):((match=imgNode.src.match(this._satRegexp))&&(coords={x:match[1],y:match[2],z:match[3]}),
// 			imgNode.style.zIndex = 0;
sublayer=0),coords){var tileKey=this._tileCoordsToKey(coords);imgNode.style.position="absolute",imgNode.style.visibility="hidden";var key=tileKey+"/"+sublayer;
// console.log('mutation for tile', key)
//store img so it can also be used in subsequent tile requests
if(this._freshTiles[key]=imgNode,key in this._tileCallbacks&&this._tileCallbacks[key])
// console.log('Fullfilling callback ', key);
//fullfill most recent tileCallback because there maybe callbacks that will never get a 
//corresponding mutation (because map moved to quickly...)
this._tileCallbacks[key].pop()(imgNode),this._tileCallbacks[key].length||delete this._tileCallbacks[key];else if(this._tiles[tileKey]){
//we already have a tile in this position (mutation is probably a google layer being added)
//replace it
var c=this._tiles[tileKey].el,oldImg=0===sublayer?c.firstChild:c.firstChild.nextSibling,cloneImgNode=this._clone(imgNode);c.replaceChild(cloneImgNode,oldImg)}}else imgNode.src.match(this._staticRegExp)&&(imgNode.style.visibility="hidden")},createTile:function(coords,done){var key=this._tileCoordsToKey(coords),tileContainer=L.DomUtil.create("div");tileContainer.dataset.pending=this._imagesPerTile,done=done.bind(this,null,tileContainer);for(var i=0;i<this._imagesPerTile;i++){var key2=key+"/"+i;if(key2 in this._freshTiles){var imgNode=this._freshTiles[key2];tileContainer.appendChild(this._clone(imgNode)),tileContainer.dataset.pending--}else this._tileCallbacks[key2]=this._tileCallbacks[key2]||[],this._tileCallbacks[key2].push(function(c/*, k2*/){return function(imgNode){c.appendChild(this._clone(imgNode)),c.dataset.pending--,parseInt(c.dataset.pending)||done()}.bind(this)}.bind(this)(tileContainer/*, key2*/))}return parseInt(tileContainer.dataset.pending)||L.Util.requestAnimFrame(done),tileContainer},_clone:function(imgNode){var clonedImgNode=imgNode.cloneNode(!0);return clonedImgNode.style.visibility="visible",clonedImgNode},_checkZoomLevels:function(){
//setting the zoom level on the Google map may result in a different zoom level than the one requested
//(it won't go beyond the level for which they have data).
var zoomLevel=this._map.getZoom(),gMapZoomLevel=this._mutant.getZoom();zoomLevel&&gMapZoomLevel&&(gMapZoomLevel!==zoomLevel||//zoom levels are out of sync, Google doesn't have data
gMapZoomLevel>this.options.maxNativeZoom)&&//at current location, Google does have data (contrary to maxNativeZoom)
//Update maxNativeZoom
this._setMaxNativeZoom(gMapZoomLevel)},_setMaxNativeZoom:function(zoomLevel){zoomLevel!=this.options.maxNativeZoom&&(this.options.maxNativeZoom=zoomLevel,this._resetView())},_reset:function(){this._initContainer()},_update:function(){
// zoom level check needs to happen before super's implementation (tile addition/creation)
// otherwise tiles may be missed if maxNativeZoom is not yet correctly determined
if(this._mutant){var center=this._map.getCenter(),_center=new google.maps.LatLng(center.lat,center.lng);this._mutant.setCenter(_center);var zoom=this._map.getZoom(),fractionalLevel=zoom!==Math.round(zoom),mutantZoom=this._mutant.getZoom();
//ignore fractional zoom levels
fractionalLevel||zoom==mutantZoom||(this._mutant.setZoom(zoom),this._mutantIsReady&&this._checkZoomLevels())}L.GridLayer.prototype._update.call(this)},_resize:function(){var size=this._map.getSize();this._mutantContainer.style.width===size.x&&this._mutantContainer.style.height===size.y||(this.setElementSize(this._mutantContainer,size),this._mutant&&google.maps.event.trigger(this._mutant,"resize"))},_handleZoomAnim:function(){if(this._mutant){var center=this._map.getCenter(),_center=new google.maps.LatLng(center.lat,center.lng);this._mutant.setCenter(_center),this._mutant.setZoom(Math.round(this._map.getZoom()))}},
// Agressively prune _freshtiles when a tile with the same key is removed,
// this prevents a problem where Leaflet keeps a loaded tile longer than
// GMaps, so that GMaps makes two requests but Leaflet only consumes one,
// polluting _freshTiles with stale data.
_removeTile:function(key){if(this._mutant)
//give time for animations to finish before checking it tile should be pruned
return setTimeout(this._pruneTile.bind(this,key),1e3),L.GridLayer.prototype._removeTile.call(this,key)},_pruneTile:function(key){for(var gZoom=this._mutant.getZoom(),tileZoom=key.split(":")[2],googleBounds=this._mutant.getBounds(),sw=googleBounds.getSouthWest(),ne=googleBounds.getNorthEast(),gMapBounds=L.latLngBounds([[sw.lat(),sw.lng()],[ne.lat(),ne.lng()]]),i=0;i<this._imagesPerTile;i++){var key2=key+"/"+i;if(key2 in this._freshTiles){var tileBounds=this._map&&this._keyToBounds(key);this._map&&tileBounds.overlaps(gMapBounds)&&tileZoom==gZoom||delete this._freshTiles[key2];
//				console.log('Prunning of ', key, (!stillVisible))
}}}}),
// 🍂factory gridLayer.googleMutant(options)
// Returns a new `GridLayer.GoogleMutant` given its options
L.gridLayer.googleMutant=function(options){return new L.GridLayer.GoogleMutant(options)};