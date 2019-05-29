/**
 * Push v1.0-beta
 * ==============
 * A compact, cross-browser solution for the JavaScript Notifications API
 *
 * Credits
 * -------
 * Tsvetan Tsvetkov (ttsvetko)
 * Alex Gibson (alexgibson)
 *
 * License
 * -------
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2015-2017 Tyler Nickerson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/* global clients */
"use strict";function isFunction(obj){return obj&&"[object Function]"==={}.toString.call(obj)}function runFunctionString(funcStr){if(0<funcStr.trim().length){
// eslint-disable-next-line no-new-func
var func=new Function(funcStr);isFunction(func)&&func()}}function findValidClient(clients,origin){for(var validClient=null,i=0;i<clients.length;i++){var client=clients[i];if(client.url===origin&&"focused"in(validClient=client)&&client.focused)return client}return validClient}function sendCenterMapMessage(client,lat,lon){client.postMessage(JSON.stringify({action:"centerMap",lat:lat,lon:lon}))}self.addEventListener("message",function(event){self.client=event.source}),self.onnotificationclick=function(event){event.notification.close(),event.waitUntil(clients.matchAll({includeUncontrolled:!0,type:"window"}).then(function(clientList){var client=findValidClient(clientList,event.notification.data.origin);client&&"focus"in client?client.focus().then(function(client){sendCenterMapMessage(client,event.notification.data.lat,event.notification.data.lon)}):"openWindow"in clients&&clients.openWindow("/").then(function(client){sendCenterMapMessage(client,event.notification.data.lat,event.notification.data.lon)})}))},self.onnotificationclose=function(event){runFunctionString(event.notification.data.onClose)
/* Tell Push to execute close callback */,self.client.postMessage(JSON.stringify({id:event.notification.data.id,action:"close"}))};