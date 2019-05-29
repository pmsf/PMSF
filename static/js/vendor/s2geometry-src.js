/// S2 Geometry functions
// the regional scoreboard is based on a level 6 S2 Cell
// - https://docs.google.com/presentation/d/1Hl4KapfAENAOf4gv-pSngKwvS_jwNVHRPZTTDzXXn6Q/view?pli=1#slide=id.i22
// at the time of writing there's no actual API for the intel map to retrieve scoreboard data,
// but it's still useful to plot the score cells on the intel map
// the S2 geometry is based on projecting the earth sphere onto a cube, with some scaling of face coordinates to
// keep things close to approximate equal area for adjacent cells
// to convert a lat,lng into a cell id:
// - convert lat,lng to x,y,z
// - convert x,y,z into face,u,v
// - u,v scaled to s,t with quadratic formula
// - s,t converted to integer i,j offsets
// - i,j converted to a position along a Hubbert space-filling curve
// - combine face,position to get the cell id
//NOTE: compared to the google S2 geometry library, we vary from their code in the following ways
// - cell IDs: they combine face and the hilbert curve position into a single 64 bit number. this gives efficient space
//             and speed. javascript doesn't have appropriate data types, and speed is not cricical, so we use
//             as [face,[bitpair,bitpair,...]] instead
// - i,j: they always use 30 bits, adjusting as needed. we use 0 to (1<<level)-1 instead
//        (so GetSizeIJ for a cell is always 1)
!function(exports){"use strict";var S2=("undefined"!==typeof module?module.exports:window).S2={L:{}};S2.L.LatLng=function(/*Number*/rawLat,/*Number*/rawLng,/*Boolean*/noWrap){var lat=parseFloat(rawLat,10),lng=parseFloat(rawLng,10);if(isNaN(lat)||isNaN(lng))throw new Error("Invalid LatLng object: ("+rawLat+", "+rawLng+")");return!0!==noWrap&&(lat=Math.max(Math.min(lat,90),-90),// clamp latitude into -90..90
lng=(lng+180)%360+(lng<-180||180===lng?180:-180)),{lat:lat,lng:lng}},S2.L.LatLng.DEG_TO_RAD=Math.PI/180,S2.L.LatLng.RAD_TO_DEG=180/Math.PI,
/*
    S2.LatLngToXYZ = function(latLng) {
      // http://stackoverflow.com/questions/8981943/lat-long-to-x-y-z-position-in-js-not-working
      var lat = latLng.lat;
      var lon = latLng.lng;
      var DEG_TO_RAD = Math.PI / 180.0;

      var phi = lat * DEG_TO_RAD;
      var theta = lon * DEG_TO_RAD;

      var cosLat = Math.cos(phi);
      var sinLat = Math.sin(phi);
      var cosLon = Math.cos(theta);
      var sinLon = Math.sin(theta);
      var rad = 500.0;

      return [
        rad * cosLat * cosLon
      , rad * cosLat * sinLon
      , rad * sinLat
      ];
    };
    */
S2.LatLngToXYZ=function(latLng){var d2r=S2.L.LatLng.DEG_TO_RAD,phi=latLng.lat*d2r,theta=latLng.lng*d2r,cosphi=Math.cos(phi);return[Math.cos(theta)*cosphi,Math.sin(theta)*cosphi,Math.sin(phi)]},S2.XYZToLatLng=function(xyz){var r2d=S2.L.LatLng.RAD_TO_DEG,lat=Math.atan2(xyz[2],Math.sqrt(xyz[0]*xyz[0]+xyz[1]*xyz[1])),lng=Math.atan2(xyz[1],xyz[0]);return S2.L.LatLng(lat*r2d,lng*r2d)};S2.XYZToFaceUV=function(xyz){var face=function(xyz){var temp=[Math.abs(xyz[0]),Math.abs(xyz[1]),Math.abs(xyz[2])];return temp[1]<temp[0]?temp[2]<temp[0]?0:2:temp[2]<temp[1]?1:2}(xyz);return xyz[face]<0&&(face+=3),[face,function(face,xyz){var u,v;switch(face){case 0:u=xyz[1]/xyz[0],v=xyz[2]/xyz[0];break;case 1:u=-xyz[0]/xyz[1],v=xyz[2]/xyz[1];break;case 2:u=-xyz[0]/xyz[2],v=-xyz[1]/xyz[2];break;case 3:u=xyz[2]/xyz[0],v=xyz[1]/xyz[0];break;case 4:u=xyz[2]/xyz[1],v=-xyz[0]/xyz[1];break;case 5:u=-xyz[1]/xyz[2],v=-xyz[0]/xyz[2];break;default:throw{error:"Invalid face"}}return[u,v]}(face,xyz)]},S2.FaceUVToXYZ=function(face,uv){var u=uv[0],v=uv[1];switch(face){case 0:return[1,u,v];case 1:return[-u,1,v];case 2:return[-u,-v,1];case 3:return[-1,-v,-u];case 4:return[v,-1,-u];case 5:return[v,u,-1];default:throw{error:"Invalid face"}}};function singleSTtoUV(st){return.5<=st?1/3*(4*st*st-1):1/3*(1-4*(1-st)*(1-st))}S2.STToUV=function(st){return[singleSTtoUV(st[0]),singleSTtoUV(st[1])]};function singleUVtoST(uv){return 0<=uv?.5*Math.sqrt(1+3*uv):1-.5*Math.sqrt(1-3*uv)}S2.UVToST=function(uv){return[singleUVtoST(uv[0]),singleUVtoST(uv[1])]},S2.STToIJ=function(st,order){function singleSTtoIJ(st){var ij=Math.floor(st*maxSize);return Math.max(0,Math.min(maxSize-1,ij))}var maxSize=1<<order;return[singleSTtoIJ(st[0]),singleSTtoIJ(st[1])]},S2.IJToST=function(ij,order,offsets){var maxSize=1<<order;return[(ij[0]+offsets[0])/maxSize,(ij[1]+offsets[1])/maxSize]};function rotateAndFlipQuadrant(n,point,rx,ry){if(0==ry){1==rx&&(point.x=n-1-point.x,point.y=n-1-point.y);var x=point.x;point.x=point.y,point.y=x}}
// hilbert space-filling curve
// based on http://blog.notdot.net/2009/11/Damn-Cool-Algorithms-Spatial-indexing-with-Quadtrees-and-Hilbert-Curves
// note: rather then calculating the final integer hilbert position, we just return the list of quads
// this ensures no precision issues whth large orders (S3 cell IDs use up to 30), and is more
// convenient for pulling out the individual bits as needed later
function pointToHilbertQuadList(x,y,order,face){var hilbertMap={a:[[0,"d"],[1,"a"],[3,"b"],[2,"a"]],b:[[2,"b"],[1,"b"],[3,"a"],[0,"c"]],c:[[2,"c"],[3,"d"],[1,"c"],[0,"b"]],d:[[0,"a"],[3,"c"],[1,"d"],[2,"d"]]};"number"!=typeof face&&console.warn(new Error("called pointToHilbertQuadList without face value, defaulting to '0'").stack);for(var currentSquare=face%2?"d":"a",positions=[],i=order-1;0<=i;i--){var mask=1<<i,t=hilbertMap[currentSquare][2*(x&mask?1:0)+(y&mask?1:0)];positions.push(t[0]),currentSquare=t[1]}return positions}
// S2Cell class
S2.S2Cell=function(){},S2.S2Cell.FromHilbertQuadKey=function(hilbertQuadkey){var i,level,bit,rx,ry,val,parts=hilbertQuadkey.split("/"),face=parseInt(parts[0]),position=parts[1],maxLevel=position.length,point={x:0,y:0};for(i=maxLevel-1;0<=i;i--)level=maxLevel-i,ry=rx=0,"1"===(bit=position[i])?ry=1:"2"===bit?ry=rx=1:"3"===bit&&(rx=1),val=Math.pow(2,level-1),rotateAndFlipQuadrant(val,point,rx,ry),point.x+=val*rx,point.y+=val*ry;if(face%2==1){var t=point.x;point.x=point.y,point.y=t}return S2.S2Cell.FromFaceIJ(parseInt(face),[point.x,point.y],level)},
//static method to construct
S2.S2Cell.FromLatLng=function(latLng,level){if(!latLng.lat&&0!==latLng.lat||!latLng.lng&&0!==latLng.lng)throw new Error("Pass { lat: lat, lng: lng } to S2.S2Cell.FromLatLng");var xyz=S2.LatLngToXYZ(latLng),faceuv=S2.XYZToFaceUV(xyz),st=S2.UVToST(faceuv[1]),ij=S2.STToIJ(st,level);return S2.S2Cell.FromFaceIJ(faceuv[0],ij,level)},
/*
    S2.faceIjLevelToXyz = function (face, ij, level) {
      var st = S2.IJToST(ij, level, [0.5, 0.5]);
      var uv = S2.STToUV(st);
      var xyz = S2.FaceUVToXYZ(face, uv);

      return S2.XYZToLatLng(xyz);
      return xyz;
    };
    */
S2.S2Cell.FromFaceIJ=function(face,ij,level){var cell=new S2.S2Cell;return cell.face=face,cell.ij=ij,cell.level=level,cell},S2.S2Cell.prototype.toString=function(){return"F"+this.face+"ij["+this.ij[0]+","+this.ij[1]+"]@"+this.level},S2.S2Cell.prototype.getLatLng=function(){var st=S2.IJToST(this.ij,this.level,[.5,.5]),uv=S2.STToUV(st),xyz=S2.FaceUVToXYZ(this.face,uv);return S2.XYZToLatLng(xyz)},S2.S2Cell.prototype.getCornerLatLngs=function(){for(var result=[],offsets=[[0,0],[0,1],[1,1],[1,0]],i=0;i<4;i++){var st=S2.IJToST(this.ij,this.level,offsets[i]),uv=S2.STToUV(st),xyz=S2.FaceUVToXYZ(this.face,uv);result.push(S2.XYZToLatLng(xyz))}return result},S2.S2Cell.prototype.getFaceAndQuads=function(){var quads=pointToHilbertQuadList(this.ij[0],this.ij[1],this.level,this.face);return[this.face,quads]},S2.S2Cell.prototype.toHilbertQuadkey=function(){var quads=pointToHilbertQuadList(this.ij[0],this.ij[1],this.level,this.face);return this.face.toString(10)+"/"+quads.join("")},S2.latLngToNeighborKeys=S2.S2Cell.latLngToNeighborKeys=function(lat,lng,level){return S2.S2Cell.FromLatLng({lat:lat,lng:lng},level).getNeighbors().map(function(cell){return cell.toHilbertQuadkey()})},S2.S2Cell.prototype.getNeighbors=function(){function fromFaceIJWrap(face,ij,level){var maxSize=1<<level;if(0<=ij[0]&&0<=ij[1]&&ij[0]<maxSize&&ij[1]<maxSize)
// no wrapping out of bounds
return S2.S2Cell.FromFaceIJ(face,ij,level);
// the new i,j are out of range.
// with the assumption that they're only a little past the borders we can just take the points as
// just beyond the cube face, project to XYZ, then re-create FaceUV from the XYZ vector
var st=S2.IJToST(ij,level,[.5,.5]),uv=S2.STToUV(st),xyz=S2.FaceUVToXYZ(face,uv),faceuv=S2.XYZToFaceUV(xyz);return face=faceuv[0],uv=faceuv[1],st=S2.UVToST(uv),ij=S2.STToIJ(st,level),S2.S2Cell.FromFaceIJ(face,ij,level)}var face=this.face,i=this.ij[0],j=this.ij[1],level=this.level;return[fromFaceIJWrap(face,[i-1,j],level),fromFaceIJWrap(face,[i,j-1],level),fromFaceIJWrap(face,[i+1,j],level),fromFaceIJWrap(face,[i,j+1],level)]},
//
// Functional Style
//
S2.FACE_BITS=3,S2.MAX_LEVEL=30,S2.POS_BITS=2*S2.MAX_LEVEL+1,// 61 (60 bits of data, 1 bit lsb marker)
S2.facePosLevelToId=S2.S2Cell.facePosLevelToId=S2.fromFacePosLevel=function(faceN,posS,levelN){var faceB,posB,bin;for(levelN||(levelN=posS.length),posS.length>levelN&&(posS=posS.substr(0,levelN)),
// 3-bit face value
faceB=Long.fromString(faceN.toString(10),!0,10).toString(2);faceB.length<S2.FACE_BITS;)faceB="0"+faceB;
// 60-bit position value
for(posB=Long.fromString(posS,!0,4).toString(2);posB.length<2*levelN;)posB="0"+posB;
// n-bit padding to 64-bits
for(bin=faceB+posB,
// 1-bit lsb marker
bin+="1";bin.length<S2.FACE_BITS+S2.POS_BITS;)bin+="0";return Long.fromString(bin,!0,2).toString(10)},S2.keyToId=S2.S2Cell.keyToId=S2.toId=S2.toCellId=S2.fromKey=function(key){var parts=key.split("/");return S2.fromFacePosLevel(parts[0],parts[1],parts[1].length)},S2.idToKey=S2.S2Cell.idToKey=S2.S2Cell.toKey=S2.toKey=S2.fromId=S2.fromCellId=S2.S2Cell.toHilbertQuadkey=S2.toHilbertQuadkey=function(idS){for(var bin=Long.fromString(idS,!0,10).toString(2);bin.length<S2.FACE_BITS+S2.POS_BITS;)bin="0"+bin;
// MUST come AFTER binstr has been left-padded with '0's
for(var lsbIndex=bin.lastIndexOf("1"),faceB=bin.substring(0,3),posB=bin.substring(3,lsbIndex),levelN=posB.length/2,faceS=Long.fromString(faceB,!0,2).toString(10),posS=Long.fromString(posB,!0,2).toString(4)
// substr(start, len)
// substring(start, end) // includes start, does not include end
;posS.length<levelN;)posS="0"+posS;return faceS+"/"+posS},S2.keyToCornerLatLngs=S2.S2Cell.keyToCornerLatLngs=function(key){return S2.S2Cell.FromHilbertQuadKey(key).getCornerLatLngs()},S2.keyToLatLng=S2.S2Cell.keyToLatLng=function(key){return S2.S2Cell.FromHilbertQuadKey(key).getLatLng()},S2.idToLatLng=S2.S2Cell.idToLatLng=function(id){var key=S2.idToKey(id);return S2.keyToLatLng(key)},S2.idToCornerLatLngs=S2.S2Cell.idToCornerLatLngs=function(id){var key=S2.idToKey(id);return S2.keyToCornerLatLngs(key)},S2.S2Cell.latLngToKey=S2.latLngToKey=S2.latLngToQuadkey=function(lat,lng,level){if(isNaN(level)||level<1||30<level)throw new Error("'level' is not a number between 1 and 30 (but it should be)");
// TODO
//
// S2.idToLatLng(id)
// S2.keyToLatLng(key)
// S2.nextFace(key)     // prevent wrapping on nextKey
// S2.prevFace(key)     // prevent wrapping on prevKey
//
// .toKeyArray(id)  // face,quadtree
// .toKey(id)       // hilbert
// .toPoint(id)     // ij
// .toId(key)       // uint64 (as string)
// .toLong(key)     // long.js
// .toLatLng(id)    // object? or array?, or string (with comma)?
//
// maybe S2.HQ.x, S2.GPS.x, S2.CI.x?
return S2.S2Cell.FromLatLng({lat:lat,lng:lng},level).toHilbertQuadkey()},S2.stepKey=function(key,num){var otherL,parts=key.split("/"),faceS=parts[0],posS=parts[1],level=parts[1].length,posL=Long.fromString(posS,!0,4);0<num?otherL=posL.add(Math.abs(num)):num<0&&(otherL=posL.subtract(Math.abs(num)));var otherS=otherL.toString(4);for("0"===otherS&&console.warning(new Error("face/position wrapping is not yet supported"));otherS.length<level;)otherS="0"+otherS;return faceS+"/"+otherS},S2.S2Cell.prevKey=S2.prevKey=function(key){return S2.stepKey(key,-1)},S2.S2Cell.nextKey=S2.nextKey=function(key){return S2.stepKey(key,1)}}();