//
// Global map.js variables
//

var $selectExclude
var $selectPokemonNotify
var $selectRarityNotify
var $textPerfectionNotify
var $selectStyle
var $selectIconSize
var $selectOpenGymsOnly
var $selectTeamGymsOnly
var $selectLastUpdateGymsOnly
var $selectMinGymLevel
var $selectMaxGymLevel
var $selectLuredPokestopsOnly
var $selectSearchIconMarker
var $selectGymMarkerStyle
var $selectLocationIconMarker
var $switchGymSidebar
var $timeoutDialog

var language = document.documentElement.lang === '' ? 'en' : document.documentElement.lang
var idToPokemon = {}
var i8lnDictionary = {}
var languageLookups = 0
var languageLookupThreshold = 3

var searchMarkerStyles

var timestamp
var excludedPokemon = []
var notifiedPokemon = []
var notifiedRarity = []
var notifiedMinPerfection = null

var buffer = []
var reincludedPokemon = []
var reids = []

var map
var rawDataIsLoading = false
var locationMarker
var rangeMarkers = ['pokemon', 'pokestop', 'gym']
var searchMarker
var storeZoom = true
var scanPath
var moves

var oSwLat
var oSwLng
var oNeLat
var oNeLng

var lastpokestops
var lastgyms
var lastpokemon
var lastslocs
var lastspawns

var selectedStyle = 'light'

var updateWorker
var lastUpdateTime

var gymTypes = ['Uncontested', 'Mystic', 'Valor', 'Instinct']
var gymPrestige = [2000, 4000, 8000, 12000, 16000, 20000, 30000, 40000, 50000]
var audio = new Audio('static/sounds/ding.mp3')

/*
 text place holders:
 <pkm> - pokemon name
 <prc> - iv in percent without percent symbol
 <atk> - attack as number
 <def> - defense as number
 <sta> - stamnia as number
 */
var notifyIvTitle = '<pkm> <prc>% (<atk>/<def>/<sta>)'
var notifyNoIvTitle = '<pkm>'

/*
 text place holders:
 <dist>  - disappear time
 <udist> - time until disappear
 */
var notifyText = 'disappears at <dist> (<udist>)'

//
// Functions
//

function excludePokemon(id) { // eslint-disable-line no-unused-vars
    $selectExclude.val(
        $selectExclude.val().concat(id)
    ).trigger('change')
}

function notifyAboutPokemon(id) { // eslint-disable-line no-unused-vars
    $selectPokemonNotify.val(
        $selectPokemonNotify.val().concat(id)
    ).trigger('change')
}

function removePokemonMarker(encounterId) { // eslint-disable-line no-unused-vars
    if (mapData.pokemons[encounterId].marker.rangeCircle) {
        mapData.pokemons[encounterId].marker.rangeCircle.setMap(null)
        delete mapData.pokemons[encounterId].marker.rangeCircle
    }
    mapData.pokemons[encounterId].marker.setMap(null)
    mapData.pokemons[encounterId].hidden = true
}

function initMap() { // eslint-disable-line no-unused-vars
    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: centerLat,
            lng: centerLng
        },
        zoom: Store.get('zoomLevel'),
        fullscreenControl: true,
        streetViewControl: false,
        mapTypeControl: false,
        clickableIcons: false,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
            position: google.maps.ControlPosition.RIGHT_TOP,
            mapTypeIds: [
                google.maps.MapTypeId.ROADMAP,
                google.maps.MapTypeId.SATELLITE,
                google.maps.MapTypeId.HYBRID,
                'nolabels_style',
                'dark_style',
                'style_light2',
                'style_pgo',
                'dark_style_nl',
                'style_light2_nl',
                'style_pgo_nl',
                'style_pgo_day',
                'style_pgo_night',
                'style_pgo_dynamic'
            ]
        }
    })

    var styleNoLabels = new google.maps.StyledMapType(noLabelsStyle, {
        name: 'No Labels'
    })
    map.mapTypes.set('nolabels_style', styleNoLabels)

    var styleDark = new google.maps.StyledMapType(darkStyle, {
        name: 'Dark'
    })
    map.mapTypes.set('dark_style', styleDark)

    var styleLight2 = new google.maps.StyledMapType(light2Style, {
        name: 'Light2'
    })
    map.mapTypes.set('style_light2', styleLight2)

    var stylePgo = new google.maps.StyledMapType(pGoStyle, {
        name: 'PokemonGo'
    })
    map.mapTypes.set('style_pgo', stylePgo)

    var styleDarkNl = new google.maps.StyledMapType(darkStyleNoLabels, {
        name: 'Dark (No Labels)'
    })
    map.mapTypes.set('dark_style_nl', styleDarkNl)

    var styleLight2Nl = new google.maps.StyledMapType(light2StyleNoLabels, {
        name: 'Light2 (No Labels)'
    })
    map.mapTypes.set('style_light2_nl', styleLight2Nl)

    var stylePgoNl = new google.maps.StyledMapType(pGoStyleNoLabels, {
        name: 'PokemonGo (No Labels)'
    })
    map.mapTypes.set('style_pgo_nl', stylePgoNl)

    var stylePgoDay = new google.maps.StyledMapType(pGoStyleDay, {
        name: 'PokemonGo Day'
    })
    map.mapTypes.set('style_pgo_day', stylePgoDay)

    var stylePgoNight = new google.maps.StyledMapType(pGoStyleNight, {
        name: 'PokemonGo Night'
    })
    map.mapTypes.set('style_pgo_night', stylePgoNight)

    // dynamic map style chooses stylePgoDay or stylePgoNight depending on client time
    var currentDate = new Date()
    var currentHour = currentDate.getHours()
    var stylePgoDynamic = (currentHour >= 6 && currentHour < 19) ? stylePgoDay : stylePgoNight
    map.mapTypes.set('style_pgo_dynamic', stylePgoDynamic)

    map.addListener('maptypeid_changed', function (s) {
        Store.set('map_style', this.mapTypeId)
    })

    map.setMapTypeId(Store.get('map_style'))
    map.addListener('idle', updateMap)

    map.addListener('zoom_changed', function () {
        if (storeZoom === true) {
            Store.set('zoomLevel', this.getZoom())
        } else {
            storeZoom = true
        }

        redrawPokemon(mapData.pokemons)
        redrawPokemon(mapData.lurePokemons)
    })

    locationMarker = createLocationMarker()
    createMyLocationButton()
    initSidebar()
}

function updateLocationMarker(style) {
    if (style in searchMarkerStyles) {
        locationMarker.setIcon(searchMarkerStyles[style].icon)
        Store.set('locationMarkerStyle', style)
    }
    return locationMarker
}

function createLocationMarker() {
    var position = Store.get('followMyLocationPosition')
    var lat = ('lat' in position) ? position.lat : centerLat
    var lng = ('lng' in position) ? position.lng : centerLng

    var locationMarker = new google.maps.Marker({
        map: map,
        animation: google.maps.Animation.DROP,
        position: {
            lat: lat,
            lng: lng
        },
        draggable: true,
        icon: null,
        optimized: false,
        zIndex: google.maps.Marker.MAX_ZINDEX + 2
    })

    locationMarker.infoWindow = new google.maps.InfoWindow({
        content: '<div><b>My Location</b></div>',
        disableAutoPan: true
    })

    addListeners(locationMarker)

    google.maps.event.addListener(locationMarker, 'dragend', function () {
        var newLocation = locationMarker.getPosition()
        Store.set('followMyLocationPosition', {
            lat: newLocation.lat(),
            lng: newLocation.lng()
        })
    })

    return locationMarker
}

function initSidebar() {
    $('#pokemon-switch').prop('checked', Store.get('showPokemon'))
    $('#geoloc-switch').prop('checked', Store.get('geoLocate'))
    $('#lock-marker-switch').prop('checked', Store.get('lockMarker'))
    $('#start-at-user-location-switch').prop('checked', Store.get('startAtUserLocation'))
    $('#follow-my-location-switch').prop('checked', Store.get('followMyLocation'))
    $('#ranges-switch').prop('checked', Store.get('showRanges'))
    $('#sound-switch').prop('checked', Store.get('playSound'))
    $('#pokemon-icon-size').val(Store.get('iconSizeModifier'))
}

function pad(number) {
    return number <= 99 ? ('0' + number).slice(-2) : number
}

function getTypeSpan(type) {
    return `<span style='padding: 2px 5px; text-transform: uppercase; color: white; margin-right: 2px; border-radius: 4px; font-size: 0.8em; vertical-align: text-bottom; background-color: ${type['color']}'>${type['type']}</span>`
}

function openMapDirections(lat, lng) { // eslint-disable-line no-unused-vars
    var url = 'https://www.google.com/maps/?daddr=' + lat + ',' + lng
    window.open(url, '_blank')
}

function pokemonLabel(name, rarity, types, disappearTime, id, latitude, longitude, encounterId, atk, def, sta, move1, move2) {
    var disappearDate = new Date(disappearTime)
    var rarityDisplay = rarity ? '(' + rarity + ')' : ''
    var typesDisplay = ''
    var pMove1 = (moves[move1] !== undefined) ? moves[move1]['name'] : 'gen/unknown'
    var pMove2 = (moves[move2] !== undefined) ? moves[move2]['name'] : 'gen/unknown'

    $.each(types, function (index, type) {
        typesDisplay += getTypeSpan(type)
    })
    var details = ''
    if (atk != null) {
        var iv = (atk + def + sta) / 45 * 100
        details = `
    <div>
        IV: ${iv.toFixed(1)}% (${atk}/${def}/${sta})
        </div>
        <div>
        Moves: ${pMove1} / ${pMove2}
    </div>
            `
    }
    var contentstring = `
<div>
    <b>${name}</b>
    <span> - </span>
    <small>
    <a href='http://www.pokemon.com/us/pokedex/${id}' target='_blank' title='View in Pokedex'>#${id}</a>
    </small>
    <span> ${rarityDisplay}</span>
    <span> - </span>
    <small>${typesDisplay}</small>
    </div>
    <div>
    Disappears at ${pad(disappearDate.getHours())}:${pad(disappearDate.getMinutes())}:${pad(disappearDate.getSeconds())}
<span class='label-countdown' disappears-at='${disappearTime}'>(00m00s)</span>
    </div>
    <div>
    Location: ${latitude.toFixed(6)}, ${longitude.toFixed(7)}
</div>
        ${details}
    <div>
    <a href='javascript:excludePokemon(${id})'>Exclude</a>&nbsp;&nbsp
    <a href='javascript:notifyAboutPokemon(${id})'>Notify</a>&nbsp;&nbsp
    <a href='javascript:removePokemonMarker("${encounterId}")'>Remove</a>&nbsp;&nbsp
    <a href='javascript:void(0);' onclick='javascript:openMapDirections(${latitude},${longitude});' title='View in Maps'>Get directions</a>
    </div>`
    return contentstring
}

function addRangeCircle(marker, map, type, teamId) {
    var targetmap = null
    var circleCenter = new google.maps.LatLng(marker.position.lat(), marker.position.lng())
    var gymColors = ['#999999', '#0051CF', '#FF260E', '#FECC23'] // 'Uncontested', 'Mystic', 'Valor', 'Instinct']
    var teamColor = gymColors[0]
    if (teamId) teamColor = gymColors[teamId]

    var range
    var circleColor

    // handle each type of marker and be explicit about the range circle attributes
    switch (type) {
        case 'pokemon':
            circleColor = '#C233F2'
            range = 40 // pokemon appear at 40m and then you can move away. still have to be 40m close to see it though, so ignore the further disappear distance
            break
        case 'pokestop':
            circleColor = '#3EB0FF'
            range = 40
            break
        case 'gym':
            circleColor = teamColor
            range = 40
            break
    }

    if (map) targetmap = map

    var rangeCircleOpts = {
        map: targetmap,
        radius: range, // meters
        strokeWeight: 1,
        strokeColor: circleColor,
        strokeOpacity: 0.9,
        center: circleCenter,
        fillColor: circleColor,
        fillOpacity: 0.3
    }
    var rangeCircle = new google.maps.Circle(rangeCircleOpts)
    return rangeCircle
}

function isRangeActive(map) {
    if (map.getZoom() < 16) return false
    return Store.get('showRanges')
}

function getIv(atk, def, stm) {
    if (atk !== null) {
        return 100.0 * (atk + def + stm) / 45
    }

    return false
}

function lpad(str, len, padstr) {
    return Array(Math.max(len - String(str).length + 1, 0)).join(padstr) + str
}

function repArray(text, find, replace) {
    for (var i = 0; i < find.length; i++) {
        text = text.replace(find[i], replace[i])
    }

    return text
}

function getTimeUntil(time) {
    var now = +new Date()
    var tdiff = time - now

    var sec = Math.floor((tdiff / 1000) % 60)
    var min = Math.floor((tdiff / 1000 / 60) % 60)
    var hour = Math.floor((tdiff / (1000 * 60 * 60)) % 24)

    return {
        'total': tdiff,
        'hour': hour,
        'min': min,
        'sec': sec,
        'now': now,
        'ttime': time
    }
}

function getNotifyText(item) {
    var iv = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
    var find = ['<prc>', '<pkm>', '<atk>', '<def>', '<sta>']
    var replace = [((iv) ? iv.toFixed(1) : ''), item['pokemon_name'], item['individual_attack'],
        item['individual_defense'], item['individual_stamina']]
    var ntitle = repArray(((iv) ? notifyIvTitle : notifyNoIvTitle), find, replace)
    var dist = (new Date(item['disappear_time'])).toLocaleString([], {
        hour: '2-digit', minute: '2-digit',
        second: '2-digit', hour12: false})
    var until = getTimeUntil(item['disappear_time'])
    var udist = (until.hour > 0) ? until.hour + ':' : ''
    udist += lpad(until.min, 2, 0) + 'm' + lpad(until.sec, 2, 0) + 's'
    find = ['<dist>', '<udist>']
    replace = [dist, udist]
    var ntext = repArray(notifyText, find, replace)

    return {
        'fav_title': ntitle,
        'fav_text': ntext
    }
}

function customizePokemonMarker(marker, item, skipNotification) {
    marker.addListener('click', function () {
        this.setAnimation(null)
        this.animationDisabled = true
    })

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokemon')
    }

    marker.infoWindow = new google.maps.InfoWindow({
        content: pokemonLabel(item['pokemon_name'], item['pokemon_rarity'], item['pokemon_types'], item['disappear_time'], item['pokemon_id'], item['latitude'], item['longitude'], item['encounter_id'], item['individual_attack'], item['individual_defense'], item['individual_stamina'], item['move_1'], item['move_2']),
        disableAutoPan: true
    })

    if (notifiedPokemon.indexOf(item['pokemon_id']) > -1 || notifiedRarity.indexOf(item['pokemon_rarity']) > -1) {
        if (!skipNotification) {
            if (Store.get('playSound')) {
                audio.play()
            }
            sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, 'static/icons/' + item['pokemon_id'] + '.png', item['latitude'], item['longitude'])
        }
        if (marker.animationDisabled !== true) {
            marker.setAnimation(google.maps.Animation.BOUNCE)
        }
    }

    if (item['individual_attack'] != null) {
        var perfection = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
        if (notifiedMinPerfection > 0 && perfection >= notifiedMinPerfection) {
            if (!skipNotification) {
                if (Store.get('playSound')) {
                    audio.play()
                }
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, 'static/icons/' + item['pokemon_id'] + '.png', item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true) {
                marker.setAnimation(google.maps.Animation.BOUNCE)
            }
        }
    }

    addListeners(marker)
}

function clearSelection() {
    if (document.selection) {
        document.selection.empty()
    } else if (window.getSelection) {
        window.getSelection().removeAllRanges()
    }
}

function addListeners(marker) {
    marker.addListener('click', function () {
        if (!marker.infoWindowIsOpen) {
            marker.infoWindow.open(map, marker)
            clearSelection()
            updateLabelDiffTime()
            marker.persist = true
            marker.infoWindowIsOpen = true
        } else {
            marker.persist = null
            marker.infoWindow.close()
            marker.infoWindowIsOpen = false
        }
    })

    google.maps.event.addListener(marker.infoWindow, 'closeclick', function () {
        marker.persist = null
    })

    if (!isMobileDevice() && !isTouchDevice()) {
        marker.addListener('mouseover', function () {
            marker.infoWindow.open(map, marker)
            clearSelection()
            updateLabelDiffTime()
        })
    }

    marker.addListener('mouseout', function () {
        if (!marker.persist) {
            marker.infoWindow.close()
        }
    })

    return marker
}

function clearStaleMarkers() {
    $.each(mapData.pokemons, function (key, value) {
        if (mapData.pokemons[key]['disappear_time'] < new Date().getTime() ||
            excludedPokemon.indexOf(mapData.pokemons[key]['pokemon_id']) >= 0) {
            if (mapData.pokemons[key].marker.rangeCircle) {
                mapData.pokemons[key].marker.rangeCircle.setMap(null)
                delete mapData.pokemons[key].marker.rangeCircle
            }
            mapData.pokemons[key].marker.setMap(null)
            delete mapData.pokemons[key]
        }
    })

    $.each(mapData.lurePokemons, function (key, value) {
        if (mapData.lurePokemons[key]['lure_expiration'] < new Date().getTime() ||
            excludedPokemon.indexOf(mapData.lurePokemons[key]['pokemon_id']) >= 0) {
            mapData.lurePokemons[key].marker.setMap(null)
            delete mapData.lurePokemons[key]
        }
    })

    $.each(mapData.scanned, function (key, value) {
        // If older than 15mins remove
        if (mapData.scanned[key]['last_modified'] < (new Date().getTime() - 15 * 60 * 1000)) {
            mapData.scanned[key].marker.setMap(null)
            delete mapData.scanned[key]
        }
    })
}

function showInBoundsMarkers(markers, type) {
    $.each(markers, function (key, value) {
        var marker = markers[key].marker
        var show = false
        if (!markers[key].hidden) {
            if (typeof marker.getBounds === 'function') {
                if (map.getBounds().intersects(marker.getBounds())) {
                    show = true
                }
            } else if (typeof marker.getPosition === 'function') {
                if (map.getBounds().contains(marker.getPosition())) {
                    show = true
                }
            }
        }
        // marker has an associated range
        if (show && rangeMarkers.indexOf(type) !== -1) {
            // no range circle yet...let's create one
            if (!marker.rangeCircle) {
                // but only if range is active
                if (isRangeActive(map)) {
                    if (type === 'gym') marker.rangeCircle = addRangeCircle(marker, map, type, markers[key].team_id)
                    else marker.rangeCircle = addRangeCircle(marker, map, type)
                }
            } else { // there's already a range circle
                if (isRangeActive(map)) {
                    marker.rangeCircle.setMap(map)
                } else {
                    marker.rangeCircle.setMap(null)
                }
            }
        }

        if (show && !marker.getMap()) {
            marker.setMap(map)
            // Not all markers can be animated (ex: scan locations)
            if (marker.setAnimation && marker.oldAnimation) {
                marker.setAnimation(marker.oldAnimation)
            }
        } else if (!show && marker.getMap()) {
            // Not all markers can be animated (ex: scan locations)
            if (marker.getAnimation) {
                marker.oldAnimation = marker.getAnimation()
            }
            if (marker.rangeCircle) marker.rangeCircle.setMap(null)
            marker.setMap(null)
        }
    })
}

function loadRawData() {
    var loadPokemon = Store.get('showPokemon')
    var loadGyms = Store.get('showGyms')
    var loadPokestops = Store.get('showPokestops')
    var loadScanned = Store.get('showScanned')
    var loadSpawnpoints = Store.get('showSpawnpoints')
    var loadLuredOnly = Boolean(Store.get('showLuredPokestopsOnly'))

    var bounds = map.getBounds()
    var swPoint = bounds.getSouthWest()
    var nePoint = bounds.getNorthEast()
    var swLat = swPoint.lat()
    var swLng = swPoint.lng()
    var neLat = nePoint.lat()
    var neLng = nePoint.lng()

    return $.ajax({
        url: '../../raw_data.php',
        type: 'GET',
        data: {
            'timestamp': timestamp,
            'pokemon': loadPokemon,
            'lastpokemon': lastpokemon,
            'pokestops': loadPokestops,
            'lastpokestops': lastpokestops,
            'luredonly': loadLuredOnly,
            'gyms': loadGyms,
            'lastgyms': lastgyms,
            'scanned': loadScanned,
            'lastslocs': lastslocs,
            'spawnpoints': loadSpawnpoints,
            'lastspawns': lastspawns,
            'swLat': swLat,
            'swLng': swLng,
            'neLat': neLat,
            'neLng': neLng,
            'oSwLat': oSwLat,
            'oSwLng': oSwLng,
            'oNeLat': oNeLat,
            'oNeLng': oNeLng,
            'reids': String(reincludedPokemon),
            'eids': String(excludedPokemon)
        },
        dataType: 'json',
        cache: false,
        beforeSend: function () {
            if (rawDataIsLoading) {
                return false
            } else {
                rawDataIsLoading = true
            }
        },
        error: function () {
            /*if (!$timeoutDialog) {
                var opts = {
                    title: 'Reduce marker settings'
                }

                $timeoutDialog = $('<div>Hmm... we\'re having problems getting data for your criteria. Try reducing what you\'re showing and zooming in to limit what\'s returned.</div>').dialog(opts)
                $timeoutDialog.dialog('open')
            } else if (!$timeoutDialog.dialog('isOpen')) {
                $timeoutDialog.dialog('open')
            }*/
        },
        complete: function () {
            rawDataIsLoading = false
        }
    })
}

function processPokemons(i, item) {
    if (!Store.get('showPokemon')) {
        return false // in case the checkbox was unchecked in the meantime.
    }

    if (!(item['encounter_id'] in mapData.pokemons) &&
        excludedPokemon.indexOf(item['pokemon_id']) < 0 && item['disappear_time'] > Date.now()) {
        // add marker to map and item to dict
        if (item.marker) {
            item.marker.setMap(null)
        }
        if (!item.hidden) {
            item.marker = setupPokemonMarker(item, map)
            customizePokemonMarker(item.marker, item)
            mapData.pokemons[item['encounter_id']] = item
        }
    }
}

function updateMap() {
    loadRawData().done(function (result) {
        $.each(result.pokemons, processPokemons)
        showInBoundsMarkers(mapData.pokemons, 'pokemon')
        //      drawScanPath(result.scanned);
        clearStaleMarkers()

        if ($('#stats').hasClass('visible')) {
            countMarkers(map)
        }

        oSwLat = result.oSwLat
        oSwLng = result.oSwLng
        oNeLat = result.oNeLat
        oNeLng = result.oNeLng

        lastgyms = result.lastgyms
        lastpokestops = result.lastpokestops
        lastpokemon = result.lastpokemon
        lastslocs = result.lastslocs
        lastspawns = result.lastspawns

        reids = result.reids
        if (reids instanceof Array) {
            reincludedPokemon = reids.filter(function (e) {
                return this.indexOf(e) < 0
            }, reincludedPokemon)
        }
        timestamp = result.timestamp
        lastUpdateTime = Date.now()
    })
}

function drawScanPath(points) { // eslint-disable-line no-unused-vars
    var scanPathPoints = []
    $.each(points, function (idx, point) {
        scanPathPoints.push({
            lat: point['latitude'],
            lng: point['longitude']
        })
    })
    if (scanPath) {
        scanPath.setMap(null)
    }
    scanPath = new google.maps.Polyline({
        path: scanPathPoints,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2,
        map: map
    })
}

function redrawPokemon(pokemonList) {
    var skipNotification = true
    $.each(pokemonList, function (key, value) {
        var item = pokemonList[key]
        if (!item.hidden) {
            if (item.marker.rangeCircle) item.marker.rangeCircle.setMap(null)
            var newMarker = setupPokemonMarker(item, map, this.marker.animationDisabled)
            customizePokemonMarker(newMarker, item, skipNotification)
            item.marker.setMap(null)
            pokemonList[key].marker = newMarker
        }
    })
}

var updateLabelDiffTime = function () {
    $('.label-countdown').each(function (index, element) {
        var disappearsAt = getTimeUntil(parseInt(element.getAttribute('disappears-at')))

        var hours = disappearsAt.hour
        var minutes = disappearsAt.min
        var seconds = disappearsAt.sec
        var timestring = ''

        if (disappearsAt.ttime < disappearsAt.now) {
            timestring = '(expired)'
        } else {
            timestring = '('
            if (hours > 0) {
                timestring = hours + 'h'
            }

            timestring += lpad(minutes, 2, 0) + 'm'
            timestring += lpad(seconds, 2, 0) + 's'
            timestring += ')'
        }

        $(element).text(timestring)
    })
}

function getPointDistance(pointA, pointB) {
    return google.maps.geometry.spherical.computeDistanceBetween(pointA, pointB)
}

function sendNotification(title, text, icon, lat, lng) {
    if (!('Notification' in window)) {
        return false // Notifications are not present in browser
    }

    if (Notification.permission !== 'granted') {
        Notification.requestPermission()
    } else {
        var notification = new Notification(title, {
            icon: icon,
            body: text,
            sound: 'sounds/ding.mp3'
        })

        notification.onclick = function () {
            window.focus()
            notification.close()

            centerMap(lat, lng, 20)
        }
    }
}

function createMyLocationButton() {
    var locationContainer = document.createElement('div')

    var locationButton = document.createElement('button')
    locationButton.style.backgroundColor = '#fff'
    locationButton.style.border = 'none'
    locationButton.style.outline = 'none'
    locationButton.style.width = '28px'
    locationButton.style.height = '28px'
    locationButton.style.borderRadius = '2px'
    locationButton.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)'
    locationButton.style.cursor = 'pointer'
    locationButton.style.marginRight = '10px'
    locationButton.style.padding = '0px'
    locationButton.title = 'My Location'
    locationContainer.appendChild(locationButton)

    var locationIcon = document.createElement('div')
    locationIcon.style.margin = '5px'
    locationIcon.style.width = '18px'
    locationIcon.style.height = '18px'
    locationIcon.style.backgroundImage = 'url(static/mylocation-sprite-1x.png)'
    locationIcon.style.backgroundSize = '180px 18px'
    locationIcon.style.backgroundPosition = '0px 0px'
    locationIcon.style.backgroundRepeat = 'no-repeat'
    locationIcon.id = 'current-location'
    locationButton.appendChild(locationIcon)

    locationButton.addEventListener('click', function () {
        centerMapOnLocation()
    })

    locationContainer.index = 1
    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(locationContainer)

    google.maps.event.addListener(map, 'dragend', function () {
        var currentLocation = document.getElementById('current-location')
        currentLocation.style.backgroundPosition = '0px 0px'
    })
}

function centerMapOnLocation() {
    var currentLocation = document.getElementById('current-location')
    var imgX = '0'
    var animationInterval = setInterval(function () {
        if (imgX === '-18') {
            imgX = '0'
        } else {
            imgX = '-18'
        }
        currentLocation.style.backgroundPosition = imgX + 'px 0'
    }, 500)
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude)
            locationMarker.setPosition(latlng)
            map.setCenter(latlng)
            Store.set('followMyLocationPosition', {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            })
            clearInterval(animationInterval)
            currentLocation.style.backgroundPosition = '-144px 0px'
        })
    } else {
        clearInterval(animationInterval)
        currentLocation.style.backgroundPosition = '0px 0px'
    }
}

function changeLocation(lat, lng) {
    var loc = new google.maps.LatLng(lat, lng)
    changeSearchLocation(lat, lng).done(function () {
        map.setCenter(loc)
        searchMarker.setPosition(loc)
    })
}

function centerMap(lat, lng, zoom) {
    var loc = new google.maps.LatLng(lat, lng)

    map.setCenter(loc)

    if (zoom) {
        storeZoom = false
        map.setZoom(zoom)
    }
}

function i8ln(word) {
    if ($.isEmptyObject(i8lnDictionary) && language !== 'en' && languageLookups < languageLookupThreshold) {
        $.ajax({
            url: 'static/dist/locales/' + language + '.min.json',
            dataType: 'json',
            async: false,
            success: function (data) {
                i8lnDictionary = data
            },
            error: function (jqXHR, status, error) {
                console.log('Error loading i8ln dictionary: ' + error)
                languageLookups++
            }
        })
    }
    if (word in i8lnDictionary) {
        return i8lnDictionary[word]
    } else {
        // Word doesn't exist in dictionary return it as is
        return word
    }
}

function updateGeoLocation() {
    if (navigator.geolocation && (Store.get('geoLocate') || Store.get('followMyLocation'))) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var lat = position.coords.latitude
            var lng = position.coords.longitude
            var center = new google.maps.LatLng(lat, lng)

            if (Store.get('geoLocate')) {
                // the search function makes any small movements cause a loop. Need to increase resolution
                if ((typeof searchMarker !== 'undefined') && (getPointDistance(searchMarker.getPosition(), center) > 40)) {
                    $.post('next_loc?lat=' + lat + '&lon=' + lng).done(function () {
                        map.panTo(center)
                        searchMarker.setPosition(center)
                    })
                }
            }
            if (Store.get('followMyLocation')) {
                if ((typeof locationMarker !== 'undefined') && (getPointDistance(locationMarker.getPosition(), center) >= 5)) {
                    map.panTo(center)
                    locationMarker.setPosition(center)
                    Store.set('followMyLocationPosition', {
                        lat: lat,
                        lng: lng
                    })
                }
            }
        })
    }
}

//
// Page Ready Exection
//

$(function () {
    try {
        if (!Notification) {
            console.log('could not load notifications')
            return
        }

        if (Notification.permission !== 'granted') {
            Notification.requestPermission()
        }
    }
    catch(err) {
        console.log('Something went wrong with notifications!')
    }
})

$(function () {
    // populate Navbar Style menu
    $selectStyle = $('#map-style')

    // Load Stylenames, translate entries, and populate lists
    $.getJSON('static/dist/data/mapstyle.min.json').done(function (data) {
        var styleList = []

        $.each(data, function (key, value) {
            styleList.push({
                id: key,
                text: i8ln(value)
            })
        })

        // setup the stylelist
        $selectStyle.select2({
            placeholder: 'Select Style',
            data: styleList,
            minimumResultsForSearch: Infinity
        })

        // setup the list change behavior
        $selectStyle.on('change', function (e) {
            selectedStyle = $selectStyle.val()
            map.setMapTypeId(selectedStyle)
            Store.set('map_style', selectedStyle)
        })

        // recall saved mapstyle
        $selectStyle.val(Store.get('map_style')).trigger('change')
    })

    $selectIconSize = $('#pokemon-icon-size')

    $selectIconSize.select2({
        placeholder: 'Select Icon Size',
        minimumResultsForSearch: Infinity
    })

    $selectIconSize.on('change', function () {
        Store.set('iconSizeModifier', this.value)
        redrawPokemon(mapData.pokemons)
        redrawPokemon(mapData.lurePokemons)
    })

    $selectSearchIconMarker = $('#iconmarker-style')
    $selectLocationIconMarker = $('#locationmarker-style')

    $.getJSON('static/dist/data/searchmarkerstyle.min.json').done(function (data) {
        searchMarkerStyles = data
        var searchMarkerStyleList = []

        $.each(data, function (key, value) {
            searchMarkerStyleList.push({
                id: key,
                text: value.name
            })
        })

        $selectLocationIconMarker.select2({
            placeholder: 'Select Location Marker',
            data: searchMarkerStyleList,
            minimumResultsForSearch: Infinity
        })

        $selectLocationIconMarker.on('change', function (e) {
            Store.set('locationMarkerStyle', this.value)
            updateLocationMarker(this.value)
        })

        $selectLocationIconMarker.val(Store.get('locationMarkerStyle')).trigger('change')
    })
})

$(function () {
    function formatState(state) {
        if (!state.id) {
            return state.text
        }
        var $state = $(
            '<span><i class="pokemon-sprite n' + state.element.value.toString() + '"></i> ' + state.text + '</span>'
        )
        return $state
    }

    if (Store.get('startAtUserLocation')) {
        centerMapOnLocation()
    }

    $.getJSON('static/dist/data/moves.min.json').done(function (data) {
        moves = data
    })

    $selectExclude = $('#exclude-pokemon')
    $selectPokemonNotify = $('#notify-pokemon')
    $selectRarityNotify = $('#notify-rarity')
    $textPerfectionNotify = $('#notify-perfection')
    var numberOfPokemon = 493

    // Load pokemon names and populate lists
    $.getJSON('static/dist/data/pokemon.min.json').done(function (data) {
        var pokeList = []

        $.each(data, function (key, value) {
            if (key > numberOfPokemon) {
                return false
            }
            var _types = []
            pokeList.push({
                id: key,
                text: i8ln(value['name']) + ' - #' + key
            })
            value['name'] = i8ln(value['name'])
            value['rarity'] = i8ln(value['rarity'])
            $.each(value['types'], function (key, pokemonType) {
                _types.push({
                    'type': i8ln(pokemonType['type']),
                    'color': pokemonType['color']
                })
            })
            value['types'] = _types
            idToPokemon[key] = value
        })

        // setup the filter lists
        $selectExclude.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState
        })
        $selectPokemonNotify.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState
        })
        $selectRarityNotify.select2({
            placeholder: i8ln('Select Rarity'),
            data: [i8ln('Common'), i8ln('Uncommon'), i8ln('Rare'), i8ln('Very Rare'), i8ln('Ultra Rare')],
            templateResult: formatState
        })

        // setup list change behavior now that we have the list to work from
        $selectExclude.on('change', function (e) {
            buffer = excludedPokemon
            excludedPokemon = $selectExclude.val().map(Number)
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, excludedPokemon)
            reincludedPokemon = reincludedPokemon.concat(buffer)
            clearStaleMarkers()
            Store.set('remember_select_exclude', excludedPokemon)
        })
        $selectPokemonNotify.on('change', function (e) {
            notifiedPokemon = $selectPokemonNotify.val().map(Number)
            Store.set('remember_select_notify', notifiedPokemon)
        })
        $selectRarityNotify.on('change', function (e) {
            notifiedRarity = $selectRarityNotify.val().map(String)
            Store.set('remember_select_rarity_notify', notifiedRarity)
        })
        $textPerfectionNotify.on('change', function (e) {
            notifiedMinPerfection = parseInt($textPerfectionNotify.val(), 10)
            if (isNaN(notifiedMinPerfection) || notifiedMinPerfection <= 0) {
                notifiedMinPerfection = ''
            }
            if (notifiedMinPerfection > 100) {
                notifiedMinPerfection = 100
            }
            $textPerfectionNotify.val(notifiedMinPerfection)
            Store.set('remember_text_perfection_notify', notifiedMinPerfection)
        })

        // recall saved lists
        $selectExclude.val(Store.get('remember_select_exclude')).trigger('change')
        $selectPokemonNotify.val(Store.get('remember_select_notify')).trigger('change')
        $selectRarityNotify.val(Store.get('remember_select_rarity_notify')).trigger('change')
        $textPerfectionNotify.val(Store.get('remember_text_perfection_notify')).trigger('change')

        if (isTouchDevice() && isMobileDevice()) {
            $('.select2-search input').prop('readonly', true)
        }
    })

    // run interval timers to regularly update map and timediffs
    window.setInterval(updateLabelDiffTime, 1000)
    window.setInterval(updateMap, 10000)
    window.setInterval(updateGeoLocation, 1000)

    // Wipe off/restore map icons when switches are toggled
    function buildSwitchChangeListener(data, dataType, storageKey) {
        return function () {
            Store.set(storageKey, this.checked)
            if (this.checked) {
                // When switch is turned on we asume it has been off, makes sure we dont end up in limbo
                // Without this there could've been a situation where no markers are on map and only newly modified ones are loaded
                if (storageKey === 'showPokemon') {
                    lastpokemon = false
                } else if (storageKey === 'showGyms') {
                    lastgyms = false
                } else if (storageKey === 'showPokestops') {
                    lastpokestops = false
                } else if (storageKey === 'showScanned') {
                    lastslocs = false
                } else if (storageKey === 'showSpawnpoints') {
                    lastspawns = false
                }
                updateMap()
            } else {
                $.each(dataType, function (d, dType) {
                    $.each(data[dType], function (key, value) {
                        // for any marker you're turning off, you'll want to wipe off the range
                        if (data[dType][key].marker.rangeCircle) {
                            data[dType][key].marker.rangeCircle.setMap(null)
                            delete data[dType][key].marker.rangeCircle
                        }
                        if (storageKey !== 'showRanges') data[dType][key].marker.setMap(null)
                    })
                    if (storageKey !== 'showRanges') data[dType] = {}
                })
                if (storageKey === 'showRanges') {
                    updateMap()
                }
            }
        }
    }

    // Setup UI element interactions
    $('#pokemon-switch').change(function () {
        buildSwitchChangeListener(mapData, ['pokemons'], 'showPokemon').bind(this)()
    })

    $('#ranges-switch').change(buildSwitchChangeListener(mapData, ['gyms', 'pokemons', 'pokestops'], 'showRanges'))


    $('#sound-switch').change(function () {
        Store.set('playSound', this.checked)
    })

    $('#geoloc-switch').change(function () {
        $('#next-location').prop('disabled', this.checked)
        $('#next-location').css('background-color', this.checked ? '#e0e0e0' : '#ffffff')
        if (!navigator.geolocation) {
            this.checked = false
        } else {
            Store.set('geoLocate', this.checked)
        }
    })

    $('#lock-marker-switch').change(function () {
        Store.set('lockMarker', this.checked)
        searchMarker.setDraggable(!this.checked)
    })

    $('#start-at-user-location-switch').change(function () {
        Store.set('startAtUserLocation', this.checked)
    })

    $('#follow-my-location-switch').change(function () {
        if (!navigator.geolocation) {
            this.checked = false
        } else {
            Store.set('followMyLocation', this.checked)
        }
        locationMarker.setDraggable(!this.checked)
    })

    if ($('#nav-accordion').length) {
        $('#nav-accordion').accordion({
            active: 0,
            collapsible: true,
            heightStyle: 'content'
        })
    }

    // Initialize dataTable in statistics sidebar
    //   - turn off sorting for the 'icon' column
    //   - initially sort 'name' column alphabetically

    $('#pokemonList_table').DataTable({
        paging: false,
        searching: false,
        info: false,
        errMode: 'throw',
        'language': {
            'emptyTable': ''
        },
        'columns': [
            { 'orderable': false },
            null,
            null,
            null
        ]
    }).order([1, 'asc'])
})