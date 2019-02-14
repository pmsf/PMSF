//
// Global map.js variables
//

var login
var expireTimestamp

var $selectExclude
var $selectExcludeMinIV
var $selectPokemonNotify
var $selectRarityNotify
var $textPerfectionNotify
var $textLevelNotify
var $textMinIV
var $textMinLevel
var $raidNotify
var $selectStyle
var $selectIconSize
var $selectIconNotifySizeModifier
var $switchOpenGymsOnly
var $selectTeamGymsOnly
var $selectLastUpdateGymsOnly
var $switchActiveRaids
var $selectMinGymLevel
var $selectMaxGymLevel
var $selectMinRaidLevel
var $selectMaxRaidLevel
var $selectNewPortalsOnly
var $selectGymMarkerStyle
var $selectLocationIconMarker
var $switchGymSidebar
var $switchTinyRat
var $switchBigKarp
var $selectDirectionProvider
var $switchExEligible
var $questsExcludePokemon
var $questsExcludeItem

var language = document.documentElement.lang === '' ? 'en' : document.documentElement.lang
var languageSite = 'en'
var idToPokemon = {}
var idToItem = {}
var i8lnDictionary = {}
var languageLookups = 0
var languageLookupThreshold = 3

var searchMarkerStyles

var timestamp
var excludedPokemon = []
var excludedMinIV = []
var notifiedPokemon = []
var notifiedRarity = []
var questsExcludedPokemon = []
var questsExcludedItem = []
var notifiedMinPerfection = null
var notifiedMinLevel = null
var minIV = null
var prevMinIV = null
var prevMinLevel = null
var onlyPokemon = 0
var directionProvider

var buffer = []
var reincludedPokemon = []
var reincludedQuestsPokemon = []
var reincludedQuestsItem = []
var reids = []
var qpreids = []
var qireids = []
var dustamount
var reloaddustamount

var numberOfPokemon = 493
var numberOfItem = 1405
var L
var map
var markers
var markersnotify
var _oldlayer = 'openstreetmap'
var rawDataIsLoading = false
var locationMarker
var rangeMarkers = ['pokemon', 'pokestop', 'gym']
var storeZoom = true
var scanPath
var moves
var weather
var boostedMons // eslint-disable-line no-unused-vars

var oSwLat
var oSwLng
var oNeLat
var oNeLng

var lastpokestops
var lastgyms
var lastnests
var lastcommunities
var lastportals
var lastpois
var lastpokemon
var lastslocs
var lastspawns

var markPortalsAsNew

var selectedStyle = 'openstreetmap'

var updateWorker
var lastUpdateTime
var lastWeatherUpdateTime

var token

var cries

var pokeList = []
var raidBoss = {} // eslint-disable-line no-unused-vars
var questList = []
var itemList = []
var rewardList = []
var questtypeList = []
var rewardtypeList = []
var conditiontypeList = []
var gymId

var assetsPath = 'static/sounds/'
var iconpath = null

var gymTypes = ['Uncontested', 'Mystic', 'Valor', 'Instinct']

var triggerGyms = Store.get('triggerGyms')
var onlyTriggerGyms
var noExGyms
var noParkInfo
var toastrOptions = {
    'closeButton': true,
    'debug': false,
    'newestOnTop': true,
    'progressBar': false,
    'positionClass': 'toast-top-right',
    'preventDuplicates': true,
    'onclick': null,
    'showDuration': '300',
    'hideDuration': '1000',
    'timeOut': '25000',
    'extendedTimeOut': '1000',
    'showEasing': 'swing',
    'hideEasing': 'linear',
    'showMethod': 'fadeIn',
    'hideMethod': 'fadeOut'
}

createjs.Sound.registerSound('static/sounds/ding.mp3', 'ding')

var pokemonTypes = [i8ln('unset'), i8ln('Normal'), i8ln('Fighting'), i8ln('Flying'), i8ln('Poison'), i8ln('Ground'), i8ln('Rock'), i8ln('Bug'), i8ln('Ghost'), i8ln('Steel'), i8ln('Fire'), i8ln('Water'), i8ln('Grass'), i8ln('Electric'), i8ln('Psychic'), i8ln('Ice'), i8ln('Dragon'), i8ln('Dark'), i8ln('Fairy')]
var genderType = ['♂', '♀', '⚲']
var forms = ['unset', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '!', '?', i8ln('Normal'), i8ln('Sunny'), i8ln('Rainy'), i8ln('Snowy'), i8ln('Normal'), i8ln('Attack'), i8ln('Defense'), i8ln('Speed'), i8ln('1'), i8ln('2'), i8ln('3'), i8ln('4'), i8ln('5'), i8ln('6'), i8ln('7'), i8ln('8'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Alola'), i8ln('Normal'), i8ln('Frost'), i8ln('Fan'), i8ln('Mow'), i8ln('Wash'), i8ln('Heat'), i8ln('Plant'), i8ln('Sandy'), i8ln('Trash'), i8ln('Altered'), i8ln('Origin'), i8ln('Sky'), i8ln('Land'), i8ln('Overcast'), i8ln('Sunny'), i8ln('West sea'), i8ln('East sea'), i8ln('West sea'), i8ln('East sea'), i8ln('Arceus Normal'), i8ln('Archeus Fighting'), i8ln('Archeus Flying'), i8ln('Archeus Poison'), i8ln('Archeus Ground'), i8ln('Archeus Rock'), i8ln('Archeus Bug'), i8ln('Archeus Ghost'), i8ln('Archeus Steel'), i8ln('Archeus Fire'), i8ln('Archeus Water'), i8ln('Archeus Grass'), i8ln('Archeus Electric'), i8ln('Archeus Psychic'), i8ln('Archeus Ice'), i8ln('Archeus Dragon'), i8ln('Archeus Dark'), i8ln('Archeus Fairy')]
var cpMultiplier = [0.094, 0.16639787, 0.21573247, 0.25572005, 0.29024988, 0.3210876, 0.34921268, 0.37523559, 0.39956728, 0.42250001, 0.44310755, 0.46279839, 0.48168495, 0.49985844, 0.51739395, 0.53435433, 0.55079269, 0.56675452, 0.58227891, 0.59740001, 0.61215729, 0.62656713, 0.64065295, 0.65443563, 0.667934, 0.68116492, 0.69414365, 0.70688421, 0.71939909, 0.7317, 0.73776948, 0.74378943, 0.74976104, 0.75568551, 0.76156384, 0.76739717, 0.7731865, 0.77893275, 0.7846369, 0.79030001]
var throwType = JSON.parse('{"10": "Nice", "11": "Great", "12": "Excellent"}')
var weatherLayerGroup = new L.LayerGroup()
var weatherArray = []
var weatherPolys = []
var weatherMarkers = []
var weatherColors

var S2
var exLayerGroup = new L.LayerGroup()
var gymLayerGroup = new L.LayerGroup()
var stopLayerGroup = new L.LayerGroup()
var scanAreaGroup = new L.LayerGroup()
var scanAreas = []
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

var OpenStreetMapProvider = window.GeoSearch.OpenStreetMapProvider
var searchProvider = new OpenStreetMapProvider()
//
// Functions
//
if (location.search.indexOf('login=true') > 0) {
    $('#nav').load(window.location.href + '#nav')
    window.location.href = '/'
}
if (location.search.indexOf('login=false') > 0) {
    openAccessDeniedModal()
}
function openAccessDeniedModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    $('.accessdenied-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Your access is denied'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}
function formatDate(date) {
    var monthNames = [
        'January', 'February', 'March',
        'April', 'May', 'June', 'July',
        'August', 'September', 'October',
        'November', 'December'
    ]

    var day = date.getDate()
    var monthIndex = date.getMonth()
    var year = date.getFullYear()
    var hours = date.getHours()
    var minutes = date.getMinutes()
    if (minutes < 10) {
        minutes = '0' + minutes
    } else {
        minutes = minutes + ''
    }

    return day + ' ' + monthNames[monthIndex] + ' ' + year + ' ' + hours + ':' + minutes
}

function excludePokemon(id) { // eslint-disable-line no-unused-vars
    $selectExclude.val(
        $selectExclude.val().split(',').concat(id).join(',')
    ).trigger('change')
    $('label[for="exclude-pokemon"] .pokemon-list .pokemon-icon-sprite[data-value="' + id + '"]').addClass('active')
    clearStaleMarkers()
}

function notifyAboutPokemon(id) { // eslint-disable-line no-unused-vars
    $selectPokemonNotify.val(
        $selectPokemonNotify.val().split(',').concat(id).join(',')
    ).trigger('change')
    $('label[for="notify-pokemon"] .pokemon-list .pokemon-icon-sprite[data-value="' + id + '"]').addClass('active')
}

function removePokemonMarker(encounterId) { // eslint-disable-line no-unused-vars
    if (mapData.pokemons[encounterId].marker.rangeCircle) {
        markers.removeLayer(mapData.pokemons[encounterId].marker.rangeCircle)
        markersnotify.removeLayer(mapData.pokemons[encounterId].marker.rangeCircle)
        delete mapData.pokemons[encounterId].marker.rangeCircle
    }
    markers.removeLayer(mapData.pokemons[encounterId].marker)
    markersnotify.removeLayer(mapData.pokemons[encounterId].marker)
    mapData.pokemons[encounterId].hidden = true
}

function createServiceWorkerReceiver() {
    navigator.serviceWorker.addEventListener('message', function (event) {
        const data = JSON.parse(event.data)
        if (data.action === 'centerMap' && data.lat && data.lon) {
            centerMap(data.lat, data.lon, 20)
        }
    })
}

function initMap() { // eslint-disable-line no-unused-vars
    map = L.map('map', {
        center: [centerLat, centerLng],
        zoom: zoom == null ? Store.get('zoomLevel') : zoom,
        minZoom: minZoom,
        maxZoom: maxZoom,
        zoomControl: false,
        layers: [weatherLayerGroup, exLayerGroup, gymLayerGroup, stopLayerGroup, scanAreaGroup]
    })

    setTileLayer(Store.get('map_style'))
    markers = L.markerClusterGroup({
        disableClusteringAtZoom: disableClusteringAtZoom,
        spiderfyOnMaxZoom: spiderfyOnMaxZoom,
        zoomToBoundsOnClick: zoomToBoundsOnClick,
        showCoverageOnHover: true,
        maxClusterRadius: maxClusterRadius,
        removeOutsideVisibleBounds: true
    })
    L.control.zoom({
        position: 'bottomright'
    }).addTo(map)

    map.addLayer(markers)
    markersnotify = L.layerGroup().addTo(map)
    map.on('zoom', function () {
        if (storeZoom === true) {
            Store.set('zoomLevel', map.getZoom())
        } else {
            storeZoom = true
        }

        redrawPokemon(mapData.pokemons)
        redrawPokemon(mapData.lurePokemons)
        if (this.getZoom() > 13) {
            // hide weather markers
            $.each(weatherMarkers, function (index, marker) {
                markers.removeLayer(marker)
            })
            // show header weather
            $('#currentWeather').fadeIn()
        } else {
            // show weather markers
            $.each(weatherMarkers, function (index, marker) {
                markers.addLayer(marker)
            })
            // hide header weather
            $('#currentWeather').fadeOut()
        }
    })

    map.createPane('portals')
    map.getPane('portals').style.zIndex = 450
    createMyLocationButton()
    initSidebar()

    var locale = window.navigator.userLanguage || window.navigator.language
    moment.locale(locale)

    if (language === 'jp') {
        languageSite = 'ja'
    } else if (language === 'pt_br') {
        languageSite = 'pt-br'
    } else if (language === 'zh_tw') {
        languageSite = 'zh-tw'
    } else {
        languageSite = language
    }

    if (Push._agents.chrome.isSupported()) {
        createServiceWorkerReceiver()
    }

    updateWeatherOverlay()
    updateS2Overlay()
    buildScanPolygons()

    map.on('moveend', function () {
        updateS2Overlay()
    })

    map.on('click', function (e) {
        if ($('.submit-on-off-button').hasClass('on')) {
            $('.submitLatitude').val(e.latlng.lat)
            $('.submitLongitude').val(e.latlng.lng)
            $('.ui-dialog').remove()
            $('.submit-modal').clone().dialog({
                modal: true,
                maxHeight: 600,
                buttons: {},
                title: i8ln('Submit Data to Map'),
                classes: {
                    'ui-dialog': 'ui-dialog submit-widget-popup'
                },
                open: function (event, ui) {
                    $('.submit-widget-popup #submit-tabs').tabs()
                    $('.submit-widget-popup .pokemon-list-cont').each(function (index) {
                        $(this).attr('id', 'pokemon-list-cont-6' + index)
                        var options = {
                            valueNames: ['name', 'types', 'id']
                        }
                        var monList = new List('pokemon-list-cont-6' + index, options) // eslint-disable-line no-unused-vars
                    })
                }
            })
        }
    })
}

function toggleFullscreenMap() { // eslint-disable-line no-unused-vars
    map.toggleFullscreen()
}
var openstreetmap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars

var darkmatter = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png', {attribution: '&copy; <a href="https://carto.com/">Carto</a>'}) // eslint-disable-line no-unused-vars

var styleblackandwhite = L.tileLayer('https://korona.geog.uni-heidelberg.de/tiles/roadsg/x={x}&y={y}&z={z}', {attribution: 'Imagery from <a href="http://giscience.uni-hd.de/">GIScience Research Group @ University of Heidelberg</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars

var styletopo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars

var stylesatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'}) // eslint-disable-line no-unused-vars

var stylewikipedia = L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png', {attribution: '<a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use">Wikimedia</a>'}) // eslint-disable-line no-unused-vars

var googlemapssat = L.gridLayer.googleMutant({type: 'satellite'}) // eslint-disable-line no-unused-vars
var googlemapsroad = L.gridLayer.googleMutant({type: 'roadmap'}) // eslint-disable-line no-unused-vars

function setTileLayer(layername) {
    if (map.hasLayer(window[_oldlayer])) { map.removeLayer(window[_oldlayer]) }
    map.addLayer(window[layername])
    _oldlayer = layername
}

function updateLocationMarker(style) {
    var locationIcon
    if (style in searchMarkerStyles) {
        var url = searchMarkerStyles[style].icon
        if (url) {
            locationIcon = L.icon({
                iconUrl: url,
                iconSize: [24, 24]
            })
            locationMarker.setIcon(locationIcon)
        } else {
            locationIcon = new L.Icon.Default()
            locationMarker.setIcon(locationIcon)
        }
        Store.set('locationMarkerStyle', style)
    }
    return locationMarker
}

function createLocationMarker() {
    var position = Store.get('followMyLocationPosition')
    var lat = 'lat' in position ? position.lat : centerLat
    var lng = 'lng' in position ? position.lng : centerLng

    var locationMarker = L.marker([lat, lng]).addTo(markersnotify).bindPopup('<div><b>My location</b></div>')
    addListeners(locationMarker)

    locationMarker.on('dragend', function () {
        var newLocation = locationMarker.getPosition()
        Store.set('followMyLocationPosition', {
            lat: newLocation.lat,
            lng: newLocation.lng
        })
    })

    return locationMarker
}

function showS2Cells(level, style) {
    const bounds = map.getBounds()
    const size = L.CRS.Earth.distance(bounds.getSouthWest(), bounds.getNorthEast()) / 10000 + 1 | 0
    const count = 2 ** level * size >> 11

    function addPoly(cell) {
        const vertices = cell.getCornerLatLngs()
        const poly = L.polygon(vertices,
            Object.assign({color: 'blue', opacity: 0.5, weight: 2, fillOpacity: 0.0, dashArray: '2 6', dashOffset: '0'}, style))
        if (cell.level === 13) {
            exLayerGroup.addLayer(poly)
        } else if (cell.level === 14) {
            gymLayerGroup.addLayer(poly)
        } else if (cell.level === 17) {
            stopLayerGroup.addLayer(poly)
        }
    }

    // add cells spiraling outward
    let cell = S2.S2Cell.FromLatLng(bounds.getCenter(), level)
    let steps = 1
    let direction = 0
    do {
        for (let i = 0; i < 2; i++) {
            for (let i = 0; i < steps; i++) {
                addPoly(cell)
                cell = cell.getNeighbors()[direction % 4]
            }
            direction++
        }
        steps++
    } while (steps < count)
}

function buildScanPolygons() {
    if (!Store.get(['showScanPolygon'])) {
        return false
    }

    $.getJSON(geoJSONfile, function (data) {
        var geoPolys = L.geoJson(data, {
            onEachFeature: function (features, featureLayer) {
                featureLayer.setStyle({color: features.properties.stroke, fillColor: features.properties.fill})
                featureLayer.bindPopup(features.properties.name)
            }
        })
        scanAreaGroup.addLayer(geoPolys)
    })
}

function initSidebar() {
    $('#gyms-switch').prop('checked', Store.get('showGyms'))
    $('#nests-switch').prop('checked', Store.get('showNests'))
    $('#communities-switch').prop('checked', Store.get('showCommunities'))
    $('#portals-switch').prop('checked', Store.get('showPortals'))
    $('#poi-switch').prop('checked', Store.get('showPoi'))
    $('#s2-switch').prop('checked', Store.get('showCells'))
    $('#s2-switch-wrapper').toggle(Store.get('showCells'))
    $('#s2-level13-switch').prop('checked', Store.get('showExCells'))
    $('#s2-level14-switch').prop('checked', Store.get('showGymCells'))
    $('#s2-level17-switch').prop('checked', Store.get('showStopCells'))
    $('#new-portals-only-switch').val(Store.get('showNewPortalsOnly'))
    $('#new-portals-only-wrapper').toggle(Store.get('showPortals'))
    $('#gym-sidebar-switch').prop('checked', Store.get('useGymSidebar'))
    $('#ex-eligible-switch').prop('checked', Store.get('exEligible'))
    $('#gym-sidebar-wrapper').toggle(Store.get('showGyms') || Store.get('showRaids'))
    $('#gyms-filter-wrapper').toggle(Store.get('showGyms'))
    $('#team-gyms-only-switch').val(Store.get('showTeamGymsOnly'))
    $('#open-gyms-only-switch').prop('checked', Store.get('showOpenGymsOnly'))
    $('#raids-switch').prop('checked', Store.get('showRaids'))
    $('#raids-filter-wrapper').toggle(Store.get('showRaids'))
    $('#active-raids-switch').prop('checked', Store.get('activeRaids'))
    $('#min-level-gyms-filter-switch').val(Store.get('minGymLevel'))
    $('#max-level-gyms-filter-switch').val(Store.get('maxGymLevel'))
    $('#min-level-raids-filter-switch').val(Store.get('minRaidLevel'))
    $('#max-level-raids-filter-switch').val(Store.get('maxRaidLevel'))
    $('#last-update-gyms-switch').val(Store.get('showLastUpdatedGymsOnly'))
    $('#pokemon-switch').prop('checked', Store.get('showPokemon'))
    $('#pokemon-filter-wrapper').toggle(Store.get('showPokemon'))
    $('#big-karp-switch').prop('checked', Store.get('showBigKarp'))
    $('#tiny-rat-switch').prop('checked', Store.get('showTinyRat'))
    $('#pokestops-switch').prop('checked', Store.get('showPokestops'))
    $('#pokestops-filter-wrapper').toggle(Store.get('showPokestops'))
    $('#lures-switch').prop('checked', Store.get('showLures'))
    $('#quests-switch').prop('checked', Store.get('showQuests'))
    $('#quests-filter-wrapper').toggle(Store.get('showQuests'))
    $('#dustvalue').text(Store.get('showDustAmount'))
    $('#dustrange').val(Store.get('showDustAmount'))
    $('#start-at-user-location-switch').prop('checked', Store.get('startAtUserLocation'))
    $('#start-at-last-location-switch').prop('checked', Store.get('startAtLastLocation'))
    $('#follow-my-location-switch').prop('checked', Store.get('followMyLocation'))
    $('#spawn-area-switch').prop('checked', Store.get('spawnArea'))
    $('#spawn-area-wrapper').toggle(Store.get('followMyLocation'))
    $('#scanned-switch').prop('checked', Store.get('showScanned'))
    $('#weather-switch').prop('checked', Store.get('showWeather'))
    $('#spawnpoints-switch').prop('checked', Store.get('showSpawnpoints'))
    $('#direction-provider').val(Store.get('directionProvider'))
    $('#ranges-switch').prop('checked', Store.get('showRanges'))
    $('#scan-area-switch').prop('checked', Store.get('showScanPolygon'))
    $('#sound-switch').prop('checked', Store.get('playSound'))
    $('#cries-switch').prop('checked', Store.get('playCries'))
    $('#cries-switch-wrapper').toggle(Store.get('playSound'))
    $('#cries-type-filter-wrapper').toggle(Store.get('playCries'))
    $('#bounce-switch').prop('checked', Store.get('remember_bounce_notify'))
    $('#notification-switch').prop('checked', Store.get('remember_notification_notify'))

    if (Store.get('showGyms') === true || Store.get('showRaids') === true) {
        $('#gyms-raid-filter-wrapper').toggle(true)
    }
    if (document.getElementById('next-location')) {
        const searchform = document.getElementById('search-places')
        const input = searchform.querySelector('input')
        searchform.addEventListener('input', async (event) => {
            $('#search-places-results li').remove()
            event.preventDefault()
            const results = await searchProvider.search({ query: input.value })
            $.each(results, function (key, val) {
                $('#search-places-results').append('<li class="place-result" data-lat="' + val.y + '" data-lon="' + val.x + '"><span class="place-result" onclick="centerMapOnCoords(event);">' + val.label + '</span></li>')
            })
        })
    }

    $('#pokemon-icon-size').val(Store.get('iconSizeModifier'))
    $('#pokemon-icon-notify-size').val(Store.get('iconNotifySizeModifier'))

    var port = ''
    if (window.location.port.length > 0) {
        port = ':' + window.location.port
    }
    var path = window.location.protocol + '//' + window.location.hostname + port + window.location.pathname
    var r = new RegExp('^(?:[a-z]+:)?//', 'i')
    iconpath = r.test(Store.get('icons')) ? Store.get('icons') : path + Store.get('icons')
}

function getTypeSpan(type) {
    return '<span style="padding: 2px 5px; text-transform: uppercase; color: white; margin-right: 2px; border-radius: 4px; font-size: 0.8em; vertical-align: text-bottom; background-color: ' + type['color'] + ';">' + type['type'] + '</span>'
}

function openMapDirections(lat, lng) { // eslint-disable-line no-unused-vars
    var url = 'https://www.google.com/maps/dir/?api=1&destination=' + lat + ',' + lng
    switch (directionProvider) {
        case 'google_pin':
            url = 'https://maps.google.com/maps?q=' + lat + ',' + lng
            break
        case 'apple':
            url = 'https://maps.apple.com/?daddr=' + lat + ',' + lng
            break
        case 'waze':
            url = 'https://waze.com/ul?ll=' + lat + ',' + lng
            break
        case 'bing':
            url = 'https://www.bing.com/maps/?v=2&where1=' + lat + ',' + lng
            break
    }
    window.open(url, '_blank')
}

// Converts timestamp to readable String
function getDateStr(t) { // eslint-disable-line no-unused-vars
    var dateStr = 'Unknown'
    if (t) {
        dateStr = moment(t).format('L')
    }
    return dateStr
}

// Converts timestamp to readable String
function getTimeStr(t) {
    var dateStr = 'Unknown'
    if (t) {
        dateStr = moment(t).format('LTS')
    }
    return dateStr
}

function toggleOtherPokemon(pokemonId) { // eslint-disable-line no-unused-vars
    onlyPokemon = onlyPokemon === 0 ? pokemonId : 0
    if (onlyPokemon === 0) {
        // reload all Pokemon
        lastpokemon = false
        updateMap()
    } else {
        // remove other Pokemon
        clearStaleMarkers()
    }
}

function isTemporaryHidden(pokemonId) {
    return onlyPokemon !== 0 && pokemonId !== onlyPokemon
}

function pokemonLabel(item) {
    var name = item['pokemon_name']
    var rarityDisplay = item['pokemon_rarity'] ? '(' + item['pokemon_rarity'] + ')' : ''
    var types = item['pokemon_types']
    var typesDisplay = ''
    var encounterId = item['encounter_id']
    var id = item['pokemon_id']
    var latitude = item['latitude']
    var longitude = item['longitude']
    var disappearTime = item['disappear_time']
    var reportTime = disappearTime - 1800000
    var atk = item['individual_attack']
    var def = item['individual_defense']
    var sta = item['individual_stamina']
    var pMove1 = moves[item['move_1']] !== undefined ? i8ln(moves[item['move_1']]['name']) : 'gen/unknown'
    var pMove2 = moves[item['move_2']] !== undefined ? i8ln(moves[item['move_2']]['name']) : 'gen/unknown'
    var weight = item['weight']
    var height = item['height']
    var gender = item['gender']
    var form = item['form']
    var cp = item['cp']
    var cpMultiplier = item['cp_multiplier']
    var weatherBoostedCondition = item['weather_boosted_condition']
    var level = item['level']
    var formStr = ''
    if (form === 0 || form === '0' || form == null) {
        formStr = '00'
    } else {
        formStr = form
    }

    var pokemonidStr = ''
    if (id <= 9) {
        pokemonidStr = '00' + id
    } else if (id <= 99) {
        pokemonidStr = '0' + id
    } else {
        pokemonidStr = id
    }

    $.each(types, function (index, type) {
        typesDisplay += getTypeSpan(type)
    })

    var details = ''
    if (atk != null && def != null && sta != null) {
        var iv = getIv(atk, def, sta)
        details =
            '<div><center>' +
            'IV: ' + iv.toFixed(1) + '% (' + atk + '/' + def + '/' + sta + ')' +
            '</center></div>'

        if (cp != null && (cpMultiplier != null || level != null)) {
            var pokemonLevel
            if (level != null) {
                pokemonLevel = level
            } else {
                pokemonLevel = getPokemonLevel(cpMultiplier)
            }
            details +=
                '<div><center>' +
                i8ln('CP') + ' : ' + cp + ' | ' + i8ln('Level') + ' : ' + pokemonLevel +
                '</center></div>'
        }
        details +=
            '<div><center>' +
            i8ln('Moves') + ' : ' + pMove1 + ' / ' + pMove2 +
            '</center></div>'
    }
    if (weatherBoostedCondition !== 0) {
        details +=
            '<div><center>' +
            i8ln('Weather Boost') + ': ' + i8ln(weather[weatherBoostedCondition]) +
            '</center></div>'
    }
    if (gender != null) {
        details +=
            '<div><center>' +
            i8ln('Gender') + ': ' + genderType[gender - 1]
        if (weight != null) {
            details += ' | ' + i8ln('Weight') + ': ' + weight.toFixed(2) + 'kg'
        }
        if (height != null) {
            details += ' | ' + i8ln('Height') + ': ' + height.toFixed(2) + 'm'
        }
        details +=
            '</center></div>'
    }
    var contentstring =
        '<div><center>' +
        '<b>' + name + '</b>'
    if (form !== null && form > 0 && forms.length > form) {
        // todo: check how rocket map handles this (if at all):
        if (id === 132) {
            contentstring += ' (' + idToPokemon[item['form']].name + ')'
        } else {
            contentstring += ' (' + forms[item['form']] + ')'
        }
    }
    var coordText = latitude.toFixed(6) + ', ' + longitude.toFixed(7)
    if (hidePokemonCoords === true) {
        coordText = i8ln('Directions')
    }
    contentstring += '<span> - </span>' +
        '<small>' +
        '<a href="https://pokemon.gameinfo.io/' + languageSite + '/pokemon/' + id + '" target="_blank" title="' + i8ln('View in Pokedex') + '">#' + id + '</a>' +
        '</small>'
    if (noRarityDisplay === false) {
        contentstring += '<span> ' + rarityDisplay + '</span>'
    }
    contentstring += '<span> - </span>' +
        '<small>' + typesDisplay + '</small>' +
        '</center></div>' +
        '<div><center><img src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png" style="width:50px;margin-top:10px;"/></center></div>' +
        details
    if (pokemonReportTime === true) {
        contentstring += '<div><center><b>' +
            i8ln('Reported at') + ' ' + getTimeStr(reportTime) +
            '</b></center></div>'
    } else {
        contentstring += '<div><center><b>' +
            i8ln('Aprox Despawn Time:') + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            '</b></center></div>'
    }

    contentstring += '<div><center>' +
        i8ln('Location') + ': <a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ', ' + longitude + ')" title="' + i8ln('View in Maps') + '">' + coordText + '</a>' +
        '</center></div>' +
        '<div><center>' +
        '<a href="javascript:excludePokemon(' + id + ')">' + i8ln('Exclude') + '</a>&nbsp&nbsp' +
        '<a href="javascript:notifyAboutPokemon(' + id + ')">' + i8ln('Notify') + '</a>&nbsp&nbsp' +
        '<a href="javascript:removePokemonMarker(\'' + encounterId + '\')">' + i8ln('Remove') + '</a>&nbsp&nbsp' +
        '<a href="javascript:void(0);" onclick="javascript:toggleOtherPokemon(' + id + ');" title="' + i8ln('Toggle display of other Pokemon') + '">' + i8ln('Toggle Others') + '</a>' +
        '</center></div>'
    return contentstring
}

function gymLabel(item) {
    var teamName = gymTypes[item['team_id']]
    var teamId = item['team_id']
    var latitude = item['latitude']
    var longitude = item['longitude']
    var lastScanned = item['last_scanned']
    var lastModified = item['last_modified']
    var name = item['name']
    var url = item['url']
    var members = item['pokemon']
    var form = item['form']

    var raidSpawned = item['raid_level'] != null
    var raidStarted = item['raid_pokemon_id'] != null

    var raidStr = ''
    var raidIcon = ''
    var i = 0
    if (raidSpawned && item.raid_end > Date.now()) {
        var levelStr = ''
        for (i = 0; i < item['raid_level']; i++) {
            levelStr += '★'
        }
        raidStr = '<h3 style="margin-bottom: 0">Raid ' + levelStr
        if (raidStarted) {
            var cpStr = ''
            if (item.raid_pokemon_cp > 0) {
                cpStr = ' CP ' + item.raid_pokemon_cp
            }
            raidStr += '<br>' + item.raid_pokemon_name
            if (form !== null && form > 0 && forms.length > form) {
                // todo: check how rocket map handles this (if at all):
                if (item['raid_pokemon_id'] === 132) {
                    raidStr += ' (' + idToPokemon[item['form']].name + ')'
                } else {
                    raidStr += ' (' + forms[item['form']] + ')'
                }
            }
            raidStr += cpStr
        }
        raidStr += '</h3>'
        if (raidStarted && item.raid_pokemon_move_1 > 0 && item.raid_pokemon_move_1 !== '133' && item.raid_pokemon_move_2 > 0 && item.raid_pokemon_move_2 !== '133') {
            var pMove1 = (moves[item['raid_pokemon_move_1']] !== undefined) ? i8ln(moves[item['raid_pokemon_move_1']]['name']) : 'gen/unknown'
            var pMove2 = (moves[item['raid_pokemon_move_2']] !== undefined) ? i8ln(moves[item['raid_pokemon_move_2']]['name']) : 'gen/unknown'
            raidStr += '<div><b>' + pMove1 + ' / ' + pMove2 + '</b></div>'
        }

        var raidStartStr = getTimeStr(item['raid_start'])
        var raidEndStr = getTimeStr(item['raid_end'])
        raidStr += '<div>' + i8ln('Start') + ': <b>' + raidStartStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_start'] + '" start>(00m00s)</span></div>'
        raidStr += '<div>' + i8ln('End') + ': <b>' + raidEndStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_end'] + '" end>(00m00s)</span></div>'

        var raidForm = item['form']
        var formStr = ''
        if (raidForm <= 10 || raidForm == null || raidForm === '0') {
            formStr = '00'
        } else {
            formStr = raidForm
        }

        var pokemonid = item['raid_pokemon_id']
        var pokemonidStr = ''
        if (pokemonid <= 9) {
            pokemonidStr = '00' + pokemonid
        } else if (pokemonid <= 99) {
            pokemonidStr = '0' + pokemonid
        } else {
            pokemonidStr = pokemonid
        }

        if (raidStarted) {
            raidIcon = '<img style="width: 80px; -webkit-filter: drop-shadow(5px 5px 5px #222); filter: drop-shadow(5px 5px 5px #222);" src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png"/>'
        } else if (item.raid_start <= Date.now()) {
            var hatchedEgg = ''
            if (item['raid_level'] <= 2) {
                hatchedEgg = 'hatched_normal'
            } else if (item['raid_level'] <= 4) {
                hatchedEgg = 'hatched_rare'
            } else {
                hatchedEgg = 'hatched_legendary'
            }
            raidIcon = '<img src="static/raids/egg_' + hatchedEgg + '.png" style="width:60px;height:70">'
        } else {
            var raidEgg = ''
            if (item['raid_level'] <= 2) {
                raidEgg = 'normal'
            } else if (item['raid_level'] <= 4) {
                raidEgg = 'rare'
            } else {
                raidEgg = 'legendary'
            }
            raidIcon = '<img src="static/raids/egg_' + raidEgg + '.png">'
        }
    }
    if (manualRaids && item['scanArea'] === false) {
        raidStr += '<div class="raid-container">' + i8ln('Add raid ') + '<i class="fa fa-binoculars submit-raid" onclick="openRaidModal(event);" data-id="' + item['gym_id'] + '"></i>' +
            '</div>'
    }
    if (!noDeleteGyms) {
        raidStr += '<i class="fa fa-trash-o delete-gym" onclick="deleteGym(event);" data-id="' + item['gym_id'] + '"></i>'
    }
    if (!noToggleExGyms) {
        raidStr += '<i class="fa fa-trophy toggle-ex-gym" onclick="toggleExGym(event);" data-id="' + item['gym_id'] + '"></i>'
    }

    var park = ''
    if ((item['park'] !== '0' && item['park'] !== 'None' && item['park'] !== undefined && item['park']) && (noParkInfo === false)) {
        if (item['park'] === 1) {
            // RM only stores boolean, so just call it "Park Gym"
            park = i8ln('Park Gym')
        } else {
            park = i8ln('Park') + ': ' + item['park']
        }
    }

    var memberStr = ''
    for (i = 0; i < members.length; i++) {
        memberStr +=
            '<span class="gym-member" title="' + members[i].pokemon_name + ' | ' + members[i].trainer_name + ' (Lvl ' + members[i].trainer_level + ')">' +
            '<i class="pokemon-sprite n' + members[i].pokemon_id + '"></i>' +
            '<span class="cp team-' + teamId + '">' + members[i].pokemon_cp + '</span>' +
            '</span>'
    }

    var lastScannedStr = ''
    if (lastScanned != null) {
        lastScannedStr =
            '<div>' +
            i8ln('Last Scanned') + ' : ' + getDateStr(lastScanned) + ' ' + getTimeStr(lastScanned) +
            '</div>'
    }

    var lastModifiedStr = getDateStr(lastModified) + ' ' + getTimeStr(lastModified)

    var nameStr = (name ? '<div>' + name + '</div>' : '')

    var gymColor = ['0, 0, 0, .4', '74, 138, 202, .6', '240, 68, 58, .6', '254, 217, 40, .6']
    var str
    var gymImage = ''
    if (url !== null) {
        gymImage = '<img height="70px" style="padding: 5px;" src="' + url + '">'
    }
    if (teamId === 0) {
        str =
            '<div class="gym-label">' +
            '<center>' +
            nameStr +
            '<div>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/' + teamName + '_large.png">' +
            raidIcon +
            gymImage +
            '</div>' +
            raidStr +
            '<div>' +
            park +
            '</div>' +
            '<div>' +
            i8ln('Location') + ': <a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ' , ' + longitude.toFixed(7) + '</a> - <a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=16">Share link</a>' +
            '</div>' +
            '<div>' +

            i8ln('Last Modified') + ' : ' + lastModifiedStr +
            '</div>' +
            '<div>' +
            lastScannedStr +
            '</div>' +
            '</center>' +
            '</div>'
        if (((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) && (item.raid_pokemon_id > 1 && item.raid_pokemon_id < numberOfPokemon)) {
            str += '<center>' +
                '<div>' +
                '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20' + item.raid_pokemon_name + '%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0AStats:%0Ahttps://pokemongo.gamepress.gg/pokemon/' + item.raid_pokemon_id + '%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
                '</div>' +
                '</center>'
        } else if ((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) {
            str += '<center>' +
                '<div>' +
                '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20egg%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
                '</div>' +
                '</center>'
        }
    } else {
        var freeSlots = item['slots_available']
        var gymCp = ''
        if (item['total_gym_cp'] != null) {
            gymCp = '<div>' + i8ln('Total Gym CP') + ' : <b>' + item.total_gym_cp + '</b></div>'
        }
        str =
            '<div class="gym-label">' +
            '<center>' +
            '<div style="padding-bottom: 2px">' +

            nameStr +
            '</div>' +
            '<div>' +
            '<b style="color:rgba(' + gymColor[teamId] + ')">' + i8ln('Team') + ' ' + i8ln(teamName) + '</b><br>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/' + teamName + '_large.png">' +
            raidIcon +
            '<img height="70px" style="padding: 5px;" src="' + url + '">' +
            '</div>' +
            raidStr +
            '<div><b>' + freeSlots + ' ' + i8ln('Free Slots') + '</b></div>' +
            '<div>' +
            park +
            '</div>' +
            gymCp +
            '<div>' +
            memberStr +
            '</div>' +
            '<div>' +
            i8ln('Location') + ': <a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ' , ' + longitude.toFixed(7) + '</a> - <a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=16">Share link</a>' +
            '</div>' +
            '<div>' +

            i8ln('Last Modified') + ' : ' + lastModifiedStr +
            '</div>' +
            '<div>' +
            lastScannedStr +
            '</div>' +
            '</center>' +
            '</div>'
        if (((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) && (item.raid_pokemon_id > 1 && item.raid_pokemon_id < numberOfPokemon)) {
            str += '<center>' +
                '<div>' +
                '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20' + item.raid_pokemon_name + '%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0AStats:%0Ahttps://pokemongo.gamepress.gg/pokemon/' + item.raid_pokemon_id + '%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
                '</div>' +
                '</center>'
        } else if ((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) {
            str += '<center>' +
                '<div>' +
                '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20egg%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
                '</div>' +
                '</center>'
        }
    }

    return str
}

function getReward(item) {
    var rewardImage
    var pokemonIdStr = ''
    var formStr = ''
    var shinyStr = ''
    if (item['quest_reward_type'] === 7) {
        if (item['quest_pokemon_id'] <= 9) {
            pokemonIdStr = '00' + item['quest_pokemon_id']
        } else if (item['quest_pokemon_id'] <= 99) {
            pokemonIdStr = '0' + item['quest_pokemon_id']
        } else {
            pokemonIdStr = item['quest_pokemon_id']
        }
        if (item['quest_pokemon_formid'] === 0) {
            formStr = '00'
        } else {
            formStr = item['quest_pokemon_formid']
        }
        if (item['quest_pokemon_shiny'] === 'true') {
            shinyStr = '_shiny'
        }
        rewardImage = '<img height="70px" style="padding: 5px;" src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + shinyStr + '.png"/>'
    } else if (item['quest_reward_type'] === 3) {
        rewardImage = '<img height="70px" style="padding: 5px;" src="' + iconpath + 'rewards/reward_stardust.png"/>'
    } else if (item['quest_reward_type'] === 2) {
        rewardImage = '<img height="70px" style="padding: 5px;" src="' + iconpath + 'rewards/reward_' + item['quest_item_id'] + '_1.png"/>'
    }
    return rewardImage
}

function getQuest(item) {
    var str
    var raidLevel
    if (item['quest_condition_type'] !== null) {
        var questinfo = JSON.parse(item['quest_condition_info'])
        var questStr = i8ln(questtypeList[item['quest_type']])
        str = '<center><div>' +
        i8ln('Task:') + ' ' +
        questStr.replace('{0}', item['quest_target']) +
        '</div></center>' +
        '<center><div>'

        if (item['quest_condition_type'] === 1) {
            var tstr = ''
            if (questinfo['pokemon_type_ids'].length > 1) {
                $.each(questinfo['pokemon_type_ids'], function (index, typeId) {
                    tstr += pokemonTypes[typeId] + ' '
                })
            } else {
                tstr = pokemonTypes[questinfo['pokemon_type_ids']]
            }
            str = str.replace('pokémon', tstr + ' type(s)')
        } else if (item['quest_condition_type'] === 2) {
            var pstr = ''
            if (questinfo['pokemon_ids'].length > 1) {
                $.each(questinfo['pokemon_ids'], function (index, id) {
                    pstr += idToPokemon[id].name + ' '
                })
            } else {
                pstr = idToPokemon[questinfo['pokemon_ids']].name
            }
            str = str.replace('pokémon', pstr)
        } else if (item['quest_condition_type'] === 3) {
            str = str.replace('pokémon', 'pokémon with weather boost')
        } else if (item['quest_condition_type'] === 6) {
            str = str.replace('Complete', 'Win')
        } else if (item['quest_condition_type'] === 7) {
            raidLevel = Math.min.apply(null, questinfo['raid_levels'])
            if (raidLevel > 1) {
                str = str.replace('raid battle(s)', 'level ' + raidLevel + ' raid or higher')
            }
            if (item['quest_condition_type_1'] === 6) {
                str = str.replace('Complete', 'Win')
            }
        } else if (item['quest_condition_type'] === 8) {
            str = str.replace('throw(s)', i8ln(throwType[questinfo['throw_type_id']] + ' throw(s)'))
            if (item['quest_condition_type_1'] === 15) {
                str = str.replace('throw(s)', 'curve throw(s)')
            }
        } else if (item['quest_condition_type'] === 9) {
            str = str.replace('Complete', 'Win')
        } else if (item['quest_condition_type'] === 10) {
            str = str.replace('Complete', 'Use a super effective charge move in ')
        } else if (item['quest_condition_type'] === 11 && questinfo !== null) {
            str = str.replace('berrie(s)', i8ln(idToItem[questinfo['item_id']].name))
        } else if (item['quest_condition_type'] === 11) {
            str = str.replace('Evolve', 'Use a evolution item to evolve')
        } else if (item['quest_condition_type'] === 14 && typeof questinfo['throw_type_id'] === 'undefined') {
            str = str.replace('throw(s)', 'throw(s) in a row')
            if (item['quest_condition_type_1'] === 15) {
                str = str.replace('throw(s)', 'curve throw(s)')
            }
        } else if (item['quest_condition_type'] === 14) {
            str = str.replace('throw(s)', i8ln(throwType[questinfo['throw_type_id']] + ' throw(s) in a row'))
            if (item['quest_condition_type_1'] === 15) {
                str = str.replace('throw(s)', 'curve throw(s)')
            }
        } else if (item['quest_condition_type'] !== 0) {
            console.log('Undefined condition type ' + item['quest_condition_type'])
            str += '<div>Undefined condition</div>'
        }
        if (item['quest_reward_type'] === 3) {
            str += '<center><div>' +
            i8ln('Reward Amount:') + ' ' +
            item['quest_dust_amount'] +
            '</div></center>'
        }
        if (item['quest_reward_type'] === 2) {
            str += '<center><div>' +
            i8ln('Reward Amount:') + ' ' +
            item['quest_reward_amount'] +
            '</div></center>'
        }
        str += '</div></center>'
    } else if (item['quest_type'] !== null) {
        questStr = i8ln(questtypeList[item['quest_type']])
        str += '<center><div>' +
        i8ln('Task:') + ' ' +
        questStr.replace('{0}', item['quest_target']) +
        '</div></center>'
    }
    return str
}

function pokestopLabel(item) {
    var str
    if (item['pokestop_name'] === null) {
        item['pokestop_name'] = 'Pokéstop'
    }
    var stopImage = ''
    if (item['url'] !== null) {
        stopImage = '<img height="70px" style="padding: 5px;" src="' + item['url'] + '">'
    }
    str =
        '<center>' + '<div class="pokestop-label">' +
        '<b>' + item['pokestop_name'] + '</b>' +
        '</div>'
    var d = new Date()
    var lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    if (!noQuests && item['quest_type'] !== null && lastMidnight < Number(item['quest_timestamp'])) {
        str +=
            '<div><center>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/Pstop-quest-large.png">' +
            stopImage +
            getReward(item) +
            '</center></div>' +
            getQuest(item)
    } else {
        str =
            '<div class="pokestop-label">' +
            '<center>' +
            '<div>' +
            '<b>' + item['pokestop_name'] + '</b>' +
            '</div>' +
            '<div>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/Pstop-large.png">' +
            stopImage +
            '</div>' +
            '</center>' +
            '</div>'
    }
    if (!noDeletePokestops) {
        str += '<i class="fa fa-trash-o delete-pokestop" onclick="deletePokestop(event);" data-id="' + item['pokestop_id'] + '"></i>'
    }
    if (!noManualQuests && item['scanArea'] === false) {
        str += '<center><div>' + i8ln('Add Quest') + '<i class="fa fa-binoculars submit-quest" onclick="openQuestModal(event);" data-id="' + item['pokestop_id'] + '"></i></div></center>'
    }
    if (!noRenamePokestops) {
        str += '<center><div>' + i8ln('Rename Pokestop') + '<i class="fa fa-edit rename-pokestop" style="margin-top: 2px; vertical-align: middle; font-size: 1.5em;" onclick="openRenamePokestopModal(event);" data-id="' + item['pokestop_id'] + '"></i></div></center>'
    }
    if (!noConvertPokestops) {
        str += '<center><div>' + i8ln('Convert to Gym') + '<i class="fa fa-refresh convert-pokestop" style="margin-top: 2px; vertical-align: middle; font-size: 1.5em;" onclick="openConvertPokestopModal(event);" data-id="' + item['pokestop_id'] + '"></i></div></center>'
    }
    str += '<div>' +
        i8ln('Location:') + ' ' + '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item['latitude'] + ',' + item['longitude'] + ')" title="' + i8ln('View in Maps') + '">' + item['latitude'] + ', ' + item['longitude'] + '</a> - <a href="./?lat=' + item['latitude'] + '&lon=' + item['longitude'] + '&zoom=16">Share link</a>' +
        '</div>'
    if ((!noWhatsappLink) && (item['quest_id'] && item['reward_id'] !== null)) {
        str += '<div>' +
            '<center>' +
            '<a href="whatsapp://send?text=' + encodeURIComponent(item['pokestop_name']) + '%0A%2AQuest:%20' + i8ln(questList[item['quest_id']]) + '%2A%0A%2AReward:%20' + i8ln(rewardList[item['reward_id']]) + '%2A%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item['latitude'] + ',' + item['longitude'] + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
            '</center>' +
            '</div>'
    }
    return str
}

function formatSpawnTime(seconds) {
    // the addition and modulo are required here because the db stores when a spawn disappears
    // the subtraction to get the appearance time will knock seconds under 0 if the spawn happens in the previous hour
    return ('0' + Math.floor((seconds + 3600) % 3600 / 60)).substr(-2) + ':' + ('0' + seconds % 60).substr(-2)
}

function spawnpointLabel(item) {
    var str =
        '<div>' +
        '<b>' + i8ln('Spawn Point') + '</b>' +
        '</div>' +
        '<div>' +
        i8ln('Every hour from') + ' ' + formatSpawnTime(item.time + 1800) + ' ' + i8ln('to') + ' ' + formatSpawnTime(item.time) +
        '</div>'
    if (item.duration === 60 || item.kind === 'ssss') {
        str =
            '<div>' +
            '<b>Spawn Point</b>' +
            '</div>' +
            '<div>' +
            i8ln('Every hour from') + ' ' + formatSpawnTime(item.time) +
            '</div>'
    }
    return str
}

function addRangeCircle(marker, map, type, teamId) {
    var markerPos = marker.getLatLng()
    var lat = markerPos.lat
    var lng = markerPos.lng
    var circleCenter = L.latLng(lat, lng)
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

    var rangeCircleOpts = {
        color: circleColor,
        radius: range, // meters
        strokeWeight: 1,
        strokeColor: circleColor,
        strokeOpacity: 0.9,
        center: circleCenter,
        fillColor: circleColor,
        fillOpacity: 0.4
    }
    var rangeCircle = L.circle(circleCenter, rangeCircleOpts)
    markers.addLayer(rangeCircle)
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

function getPokemonLevel(cpMultiplier) {
    if (cpMultiplier < 0.734) {
        var pokemonLevel = 58.35178527 * cpMultiplier * cpMultiplier - 2.838007664 * cpMultiplier + 0.8539209906
    } else {
        pokemonLevel = 171.0112688 * cpMultiplier - 95.20425243
    }
    pokemonLevel = Math.round(pokemonLevel) * 2 / 2

    return pokemonLevel
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

    var sec = Math.floor(tdiff / 1000 % 60)
    var min = Math.floor(tdiff / 1000 / 60 % 60)
    var hour = Math.floor(tdiff / (1000 * 60 * 60) % 24)

    return {
        'total': tdiff,
        'hour': hour,
        'min': min,
        'sec': sec,
        'now': now,
        'time': time
    }
}

function getNotifyText(item) {
    var iv = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
    var find = ['<prc>', '<pkm>', '<atk>', '<def>', '<sta>']
    var replace = [iv ? iv.toFixed(1) : '', item['pokemon_name'], item['individual_attack'], item['individual_defense'], item['individual_stamina']]
    var ntitle = repArray(iv ? notifyIvTitle : notifyNoIvTitle, find, replace)
    var dist = new Date(item['disappear_time']).toLocaleString([], {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    })
    var until = getTimeUntil(item['disappear_time'])
    var udist = until.hour > 0 ? until.hour + ':' : ''
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
    marker.setBouncingOptions({
        bounceHeight: 20, // height of the bouncing
        bounceSpeed: 80, // bouncing speed coefficient
        elastic: false,
        shadowAngle: null
    })
    marker.on('mouseover', function () {
        this.stopBouncing()
        this.animationDisabled = true
    })

    var pokemonForm = item['form']
    var formStr = ''
    if (pokemonForm === '0' || pokemonForm === null || pokemonForm === 0) {
        formStr = '00'
    } else {
        formStr = pokemonForm
    }

    var pokemonId = item['pokemon_id']
    var pokemonIdStr = ''
    if (pokemonId <= 9) {
        pokemonIdStr = '00' + pokemonId
    } else if (pokemonId <= 99) {
        pokemonIdStr = '0' + pokemonId
    } else {
        pokemonIdStr = pokemonId
    }

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokemon')
    }

    marker.bindPopup(pokemonLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})

    if (notifiedPokemon.indexOf(item['pokemon_id']) > -1 || notifiedRarity.indexOf(item['pokemon_rarity']) > -1) {
        if (!skipNotification) {
            checkAndCreateSound(item['pokemon_id'])
            sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png', item['latitude'], item['longitude'])
        }
        if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
            marker.bounce()
        }
    }

    if (item['individual_attack'] != null) {
        var perfection = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
        if (notifiedMinPerfection > 0 && perfection >= notifiedMinPerfection) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png', item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
                marker.bounce()
            }
        }
    }

    if (item['level'] != null) {
        var level = item['level']
        if (notifiedMinLevel > 0 && level >= notifiedMinLevel) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png', item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
                marker.bounce()
            }
        }
    }

    addListeners(marker)
}

function getGymMarkerIcon(item) {
    var park = item['park']
    var level = 6 - item['slots_available']
    var raidForm = item['form']
    var formStr = ''
    if (raidForm <= 10 || raidForm == null || raidForm === '0') {
        formStr = '00'
    } else {
        formStr = raidForm
    }
    var pokemonid = item['raid_pokemon_id']
    var pokemonidStr = ''
    if (pokemonid <= 9) {
        pokemonidStr = '00' + pokemonid
    } else if (pokemonid <= 99) {
        pokemonidStr = '0' + pokemonid
    } else {
        pokemonidStr = pokemonid
    }
    var team = item.team_id
    var teamStr = ''
    if (team === 0 || level === null) {
        teamStr = gymTypes[item['team_id']]
    } else {
        teamStr = gymTypes[item['team_id']] + '_' + level
    }
    var exIcon = ''
    var fortMarker = ''
    if ((((park !== '0' && park !== 'None' && park !== undefined && onlyTriggerGyms === false && park) || (item['sponsor'] !== undefined && item['sponsor'] > 0) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false))) {
        exIcon = '<img src="static/images/ex.png" style="position:absolute;right:25px;bottom:2px;"/>'
    }
    var smallExIcon = ''
    if ((((park !== '0' && park !== 'None' && park !== undefined && onlyTriggerGyms === false && park) || (item['sponsor'] !== undefined && item['sponsor'] > 0) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false))) {
        smallExIcon = '<img src="static/images/ex.png" style="width:26px;position:absolute;right:35px;bottom:13px;"/>'
    }
    var html = ''
    if (item['raid_pokemon_id'] != null && item.raid_end > Date.now()) {
        html = '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:50px;height:auto;"/>' +
            exIcon +
            '<img src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png" style="width:50px;height:auto;position:absolute;top:-15px;right:0px;"/>' +
            '</div>'
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -70],
            className: 'raid-marker',
            html: html
        })
    } else if (item['raid_level'] !== null && item.raid_start <= Date.now() && item.raid_end > Date.now()) {
        var hatchedEgg = ''
        if (item['raid_level'] <= 2) {
            hatchedEgg = 'hatched_normal'
        } else if (item['raid_level'] <= 4) {
            hatchedEgg = 'hatched_rare'
        } else {
            hatchedEgg = 'hatched_legendary'
        }
        html = '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:50px;height:auto;"/>' +
            exIcon +
            '<img src="static/raids/egg_' + hatchedEgg + '.png" style="width:35px;height:auto;position:absolute;top:-11px;right:18px;"/>' +
            '</div>'
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -40],
            className: 'active-egg-marker',
            html: html
        })
    } else if (item['raid_level'] !== null && item.raid_end > Date.now()) {
        var raidEgg = ''
        if (item['raid_level'] <= 2) {
            raidEgg = 'normal'
        } else if (item['raid_level'] <= 4) {
            raidEgg = 'rare'
        } else {
            raidEgg = 'legendary'
        }
        html = '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:50px;height:auto;"/>' +
            exIcon +
            '<img src="static/raids/egg_' + raidEgg + '.png" style="width:25px;height:auto;position:absolute;top:6px;right:18px;"/>' +
            '</div>'
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -40],
            className: 'egg-marker',
            html: html
        })
    } else {
        html = '<div>' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:35px;height:auto;"/>' +
            smallExIcon +
            '</div>'
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [17, 30],
            popupAnchor: [0, -35],
            className: 'egg-marker',
            html: html
        })
    }
    return fortMarker
}

function setupGymMarker(item) {
    var marker = L.marker([item['latitude'], item['longitude']], {icon: getGymMarkerIcon(item), zIndexOffset: 1060})
    markers.addLayer(marker)
    updateGymMarker(item, marker)

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'gym', item['team_id'])
    }


    var raidLevel = item.raid_level
    if (raidLevel >= Store.get('remember_raid_notify') && item.raid_end > Date.now() && Store.get('remember_raid_notify') !== 0) {
        var title = 'Raid level: ' + raidLevel

        var raidStartStr = getTimeStr(item['raid_start'])
        var raidEndStr = getTimeStr(item['raid_end'])
        var text = raidStartStr + ' - ' + raidEndStr

        var raidStarted = item['raid_pokemon_id'] != null
        var icon
        if (raidStarted) {
            var raidForm = item['form']
            var formStr = ''
            if (raidForm <= 10 || raidForm == null || raidForm === '0') {
                formStr = '00'
            } else {
                formStr = raidForm
            }
            var pokemonid = item.raid_pokemon_id
            var pokemonidStr = ''
            if (pokemonid <= 9) {
                pokemonidStr = '00' + pokemonid
            } else if (pokemonid <= 99) {
                pokemonidStr = '0' + pokemonid
            } else {
                pokemonidStr = pokemonid
            }

            icon = iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png'
            checkAndCreateSound(item.raid_pokemon_id)
        } else if (item.raid_start <= Date.now()) {
            var hatchedEgg = ''
            if (item['raid_level'] <= 2) {
                hatchedEgg = 'hatched_normal'
            } else if (item['raid_level'] <= 4) {
                hatchedEgg = 'hatched_rare'
            } else {
                hatchedEgg = 'hatched_legendary'
            }
            icon = 'static/raids/egg_' + hatchedEgg + '.png'
        } else {
            var raidEgg = ''
            if (item['raid_level'] <= 2) {
                raidEgg = 'normal'
            } else if (item['raid_level'] <= 4) {
                raidEgg = 'rare'
            } else {
                raidEgg = 'legendary'
            }
            icon = 'static/raids/egg_' + raidEgg + '.png'
            checkAndCreateSound()
        }
        sendNotification(title, text, icon, item['latitude'], item['longitude'])
    }

    if (Store.get('useGymSidebar')) {
        marker.on('click', function () {
            var gymSidebar = document.querySelector('#gym-details')
            if (gymSidebar.getAttribute('data-id') === item['gym_id'] && gymSidebar.classList.contains('visible')) {
                gymSidebar.classList.remove('visible')
            } else {
                gymSidebar.setAttribute('data-id', item['gym_id'])
                showGymDetails(item['gym_id'])
            }
        })


        if (!isMobileDevice() && !isTouchDevice()) {
            marker.bindPopup(gymLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
            marker.on('mouseover', function () {
                marker.openPopup()
                clearSelection()
                updateLabelDiffTime()
            })
        }

        marker.on('mouseout', function () {
            if (!marker.persist) {
                marker.closePopup()
            }
        })
    } else {
        marker.bindPopup(gymLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
        addListeners(marker)
    }
    return marker
}

function updateGymMarker(item, marker) {
    marker.setIcon(getGymMarkerIcon(item))
    marker.setPopupContent(gymLabel(item))
    var raidLevel = item.raid_level
    if (raidLevel >= Store.get('remember_raid_notify') && item.raid_end > Date.now() && Store.get('remember_raid_notify') !== 0) {
        if (item.last_scanned > (Date.now() - 5 * 60)) {
            var title = 'Raid level: ' + raidLevel

            var raidStartStr = getTimeStr(item['raid_start'])
            var raidEndStr = getTimeStr(item['raid_end'])
            var text = raidStartStr + ' - ' + raidEndStr

            var raidStarted = item['raid_pokemon_id'] != null
            var icon
            if (raidStarted) {
                var raidForm = item['form']
                var formStr = ''
                if (raidForm <= 10 || raidForm == null || raidForm === '0') {
                    formStr = '00'
                } else {
                    formStr = raidForm
                }
                var pokemonid = item.raid_pokemon_id
                var pokemonidStr = ''
                if (pokemonid <= 9) {
                    pokemonidStr = '00' + pokemonid
                } else if (pokemonid <= 99) {
                    pokemonidStr = '0' + pokemonid
                } else {
                    pokemonidStr = pokemonid
                }
                icon = iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png'
                checkAndCreateSound(item.raid_pokemon_id)
            } else if (item.raid_start <= Date.now()) {
                var hatchedEgg = ''
                if (item['raid_level'] <= 2) {
                    hatchedEgg = 'hatched_normal'
                } else if (item['raid_level'] <= 4) {
                    hatchedEgg = 'hatched_rare'
                } else {
                    hatchedEgg = 'hatched_legendary'
                }
                icon = 'static/raids/egg_' + hatchedEgg + '.png'
            } else {
                checkAndCreateSound()
                var raidEgg = ''
                if (item['raid_level'] <= 2) {
                    raidEgg = 'normal'
                } else if (item['raid_level'] <= 4) {
                    raidEgg = 'rare'
                } else {
                    raidEgg = 'legendary'
                }
                icon = 'static/raids/egg_' + raidEgg + '.png'
            }
            sendNotification(title, text, icon, item['latitude'], item['longitude'])
        }
    }

    return marker
}

function updateGymIcons() {
    $.each(mapData.gyms, function (key, value) {
        mapData.gyms[key]['marker'].setIcon(getGymMarkerIcon(mapData.gyms[key]))
    })
}
function getPokestopMarkerIcon(item) {
    var stopMarker = ''
    var html = ''
    var d = new Date()
    var lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    if (!noQuests && item['quest_reward_type'] !== null && lastMidnight < Number(item['quest_timestamp'])) {
        if (item['quest_reward_type'] === 7) {
            var pokemonIdStr = ''
            if (item['quest_pokemon_id'] <= 9) {
                pokemonIdStr = '00' + item['quest_pokemon_id']
            } else if (item['quest_pokemon_id'] <= 99) {
                pokemonIdStr = '0' + item['quest_pokemon_id']
            } else {
                pokemonIdStr = item['quest_pokemon_id']
            }
            var formStr = ''
            if (item['quest_pokemon_formid'] === 0) {
                formStr = '00'
            } else {
                formStr = item['quest_pokemon_formid']
            }
            var shinyStr = ''
            if (item['quest_pokemon_shiny'] === 'true') {
                shinyStr = '_shiny'
            }
            html = '<div style="position:relative;">' +
                '<img src="static/forts/Pstop-quest-small.png" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + shinyStr + '.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [24, 38],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 3) {
            html = '<div style="position:relative;">' +
                '<img src="static/forts/Pstop-quest-small.png" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'rewards/reward_stardust.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [24, 38],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 2) {
            html = '<div style="position:relative;">' +
                '<img src="static/forts/Pstop-quest-small.png" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'rewards/reward_' + item['quest_item_id'] + '_1.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [24, 38],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else {
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [15, 28],
                popupAnchor: [0, -35],
                className: 'stop-marker',
                html: '<div>' +
                '<img src="static/forts/Pstop.png"' +
                '</div>'
            })
        }
    } else {
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [15, 28],
            popupAnchor: [0, -35],
            className: 'stop-marker',
            html: '<div>' +
            '<img src="static/forts/Pstop.png"' +
            '</div>'
        })
    }
    return stopMarker
}

function setupPokestopMarker(item) {
    var pokestopMarkerIcon = getPokestopMarkerIcon(item)
    var marker
    if (item['quest_pokemon_shiny'] === 'true') {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper shiny', autoPan: false, closeOnClick: false, autoClose: false})
    } else {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper normal', autoPan: false, closeOnClick: false, autoClose: false})
    }
    markers.addLayer(marker)

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokestop')
    }

    addListeners(marker)

    return marker
}
function setupNestMarker(item) {
    var getNestMarkerIcon = ''
    if (item.pokemon_id > 0) {
        var pokemonIdStr = ''
        if (item.pokemon_id <= 9) {
            pokemonIdStr = '00' + item.pokemon_id
        } else if (item.pokemon_id <= 99) {
            pokemonIdStr = '0' + item.pokemon_id
        } else {
            pokemonIdStr = item.pokemon_id
        }
        getNestMarkerIcon = '<div class="marker-nests">' +
            '<img src="static/images/nest-' + item.english_pokemon_types[0].type.toLowerCase() + '.png" style="width:45px;height: auto;"/>' +
            '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png" style="position:absolute;width:40px;height:40px;top:6px;left:3px"/>' +
            '</div>'
    } else {
        getNestMarkerIcon = '<div class="marker-nests">' +
            '<img src="static/images/nest-empty.png" style="width:36px;height:auto;"/>' +
            '</div>'
    }
    var nestMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [20, 45],
        popupAnchor: [0, -45],
        className: 'marker-nests',
        html: getNestMarkerIcon
    })
    var marker = L.marker([item['lat'], item['lon']], {icon: nestMarkerIcon, zIndexOffset: 1020}).bindPopup(nestLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function nestLabel(item) {
    var str = '<div>'
    if (item.pokemon_id > 0) {
        var types = item['pokemon_types']
        var typesDisplay = ''
        $.each(types, function (index, type) {
            typesDisplay += getTypeSpan(type)
        })
        var pokemonIdStr = ''
        if (item.pokemon_id <= 9) {
            pokemonIdStr = '00' + item.pokemon_id
        } else if (item.pokemon_id <= 99) {
            pokemonIdStr = '0' + item.pokemon_id
        } else {
            pokemonIdStr = item.pokemon_id
        }
        str += '<center><b>' + item.pokemon_name + '</b></center>' +
                '</div>' +
                '<center>' +
                '<div class="marker-nests">' +
                '<img src="static/images/nest-' + item.english_pokemon_types[0].type.toLowerCase() + '.png" style="width:80px;height:auto;"/>' +
                '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png" style="position:absolute;width:65px;height:65px;top:44px;left:103px;"/>' +
                '<br>' +
                '<div>' +
                typesDisplay +
                '</div>' +
                '</center>' +
                '</div>'
    } else {
        str += '<div align="center" class="marker-nests">' +
            '<img src="static/images/nest-empty.png" align"middle" style="width:36px;height: auto;"/>' +
            '</div>' +
            '<b>' + i8ln('No Pokemon - Assign One Below') + '</b>'
    }
    if (item.type === 1) {
        str += '<center><div style="margin-bottom:5px; margin-top:5px;">' + i8ln('As found on thesilphroad.com') + '</div></center>'
    }
    if (!noDeleteNests) {
        str += '<i class="fa fa-trash-o delete-nest" onclick="deleteNest(event);" data-id="' + item['nest_id'] + '"></i>'
    }
    if (!noManualNests) {
        str += '<center><div>' + i8ln('Add Nest') + '<i class="fa fa-binoculars submit-nest" onclick="openNestModal(event);" data-id="' + item['nest_id'] + '"></i></div></center>'
    }
    str += '<div>' +
        'Location: <a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ')" title="' + i8ln('View in Maps') + '">' + item.lat.toFixed(6) + ', ' + item.lon.toFixed(7) + '</a> - <a href="./?lat=' + item.lat + '&lon=' + item.lon + '&zoom=16">Share link</a>' +
        '</div>'
    if ((!noWhatsappLink) && (item.pokemon_id > 0)) {
        str += '<div>' +
            '<center>' +
            '<a href="whatsapp://send?text=%2A' + encodeURIComponent(item.pokemon_name) + '%2A%20nest has been found.%0A%0ALocation:%20https://www.google.com/maps/search/?api=1%26query=' + item.lat + ',' + item.lon + '" data-action="share/whatsapp/share">Whatsapp Link</a>' +
            '</center>' +
            '</div>'
    }
    return str
}

function setupCommunityMarker(item) {
    var icon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [18, 24],
        popupAnchor: [0, -35],
        className: 'marker-community',
        html: '<img src="static/images/marker-' + item.type + '.png" style="width:36px;height: auto;"/>'
    })

    var marker = L.marker([item['lat'], item['lon']], {icon: icon, zIndexOffset: 1030}).bindPopup(communityLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)

    addListeners(marker)

    return marker
}

function communityLabel(item) {
    var str = '<div align="center" class="marker-community">' +
        '<img src="static/images/marker-' + item.type + '.png" align"middle" style="width:30px;height: auto;"/>'
    if (item.image_url != null) {
        str +=
        '<img src="' + item.image_url + '" align"middle" style="width:36px;height: auto;"/>'
    } else {
        str +=
        '<img src="static/images/community_ball.png" align"middle" style="width:36px;height: auto;"/>'
    }
    str +=
        '</div>' +
        '<center><h4><div>' + item.title + '</div></h4></center>' +
        '<center><div>' + item.description.slice(0, 40) + '</div></center>'
    if (item.team_instinct === 1 || item.team_mystic === 1 || item.team_valor === 1) {
        str += '<center><div>Welcome to Teams:<br>'
        if (item.team_instinct === 1) {
            str +=
            '<img src="static/images/communities/instinct.png" align"middle" style="width:18px;height: auto;"/>'
        }
        if (item.team_mystic === 1) {
            str +=
            '<img src="static/images/communities/mystic.png" align"middle" style="width:18px;height: auto;"/>'
        }
        if (item.team_valor === 1) {
            str +=
            '<img src="static/images/communities/valor.png" align"middle" style="width:18px;height: auto;"/>'
        }
        str += '</center></div>'
    }
    if (item.size >= 10) {
        str +=
        '<center><div>' + item.size + ' Members</div></center>'
    }
    if (item.has_invite_url === 1 && (item.invite_url !== '#' || item.invite_url !== undefined)) {
        str +=
        '<center><div class="button-container">' +
            '<a class="button" href="' + item.invite_url + '">' + i8ln('Join Now') + '<i class="fa fa-comments" style="margin-left:10px;"></i>' +
            '</a>' +
        '</div></center>'
    }
    if (!noEditCommunity) {
        str +=
        '<center><div class="button-container">' +
        '<a class="button" onclick="openEditCommunityModal(event);" data-id="' + item.community_id + '" data-title="' + item.title + '" data-description="' + item.description + '" data-invite="' + item.invite_url + '">' + i8ln('Edit Community') + '<i class="fa fa-edit style="margin-left:10px;"></i></center>' +
            '</a>' +
        '</div></center>'
    }
    if (item.source === 2) {
        str += '<center><div style="margin-bottom:5px; margin-top:5px;">' + i8ln('Join on  <a href="https://thesilphroad.com/map#18/' + item.lat + '/' + item.lon + '">thesilphroad.com</a>') + '</div></center>'
    }
    if (!noDeleteCommunity) {
        str += '<i class="fa fa-trash-o delete-community" onclick="deleteCommunity(event);" data-id="' + item.community_id + '"></i>'
    }
    return str
}

function setupPortalMarker(item) {
    var ts = Math.round(new Date().getTime() / 1000)
    var yesterday = ts - markPortalsAsNew
    if (item.checked === '1') {
        var circle = {
            color: 'red',
            radius: 10,
            fillOpacity: 0.4,
            fillColor: '#f00',
            weight: 1,
            pane: 'portals'
        }
    } else if (item.imported > yesterday) {
        circle = {
            color: 'green',
            radius: 10,
            fillOpacity: 0.4,
            fillColor: '#9f3',
            weight: 1,
            pane: 'portals'
        }
    } else {
        circle = {
            color: 'blue',
            radius: 10,
            fillOpacity: 0.4,
            fillColor: '#00f',
            weight: 1,
            pane: 'portals'
        }
    }
    var marker = L.circleMarker([item['lat'], item['lon']], circle).bindPopup(portalLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)

    addListeners(marker)

    return marker
}

function setupPoiMarker(item) {
    if (item.status === '1') {
        var circle = {
            color: '#FFA500',
            radius: 5,
            fillOpacity: 1,
            fillColor: '#FFA500',
            weight: 1,
            pane: 'portals'
        }
    } else if (item.status === '2') {
        circle = {
            color: '#0000FF',
            radius: 5,
            fillOpacity: 1,
            fillColor: '#0000FF',
            weight: 1,
            pane: 'portals'
        }
    } else if (item.status === '3') {
        circle = {
            color: '#FF0000',
            radius: 5,
            fillOpacity: 1,
            fillColor: '#FF0000',
            weight: 1,
            pane: 'portals'
        }
    }
    var marker = L.circleMarker([item['lat'], item['lon']], circle).bindPopup(poiLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)

    addListeners(marker)

    return marker
}

function portalLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var imported = formatDate(new Date(item.imported * 1000))
    var str = '<img src="' + item.url + '" align"middle" style="width:175px;height:auto;margin-left:25px;"/>' +
        '<center><h4><div>' + item.name + '</div></h4></center>'
    if (!noConvertPortal) {
        str += '<center><div>Convert this portal<i class="fa fa-refresh convert-portal" style="margin-top: 2px; margin-left: 5px; vertical-align: middle; font-size: 1.5em;" onclick="openConvertPortalModal(event);" data-id="' + item.external_id + '"></i></div></center>'
    }
    str += '<center><div>Last updated: ' + updated + '</div></center>' +
        '<center><div>Date imported: ' + imported + '</div></center>'
    if (!noDeletePortal) {
        str += '<i class="fa fa-trash-o delete-portal" onclick="deletePortal(event);" data-id="' + item.external_id + '"></i>'
    }
    return str
}

function poiLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var str = '<center><h3><div>' + item.name + '</div></h3></center>' +
        '<center><h4><div>' + item.description + '</div></h4></center>' +
        '<center><div>Added: ' + updated + '</div></center>' +
        '<center><div>Submitted by: ' + item.submitted_by + '</div></center>'
    if (!noDeletePoi) {
        str += '<i class="fa fa-trash-o delete-poi" onclick="deletePoi(event);" data-id="' + item.poi_id + '"></i>'
    }
    if (!noMarkPoi) {
        str += '<center><div>Mark this poi <i class="fa fa-refresh convert-poi" style="margin-top: 2px; margin-left: 5px; vertical-align: middle; font-size: 1.5em;" onclick="openMarkPoiModal(event);" data-id="' + item.poi_id + '"></i></div></center>'
    }
    return str
}

function deletePortal(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var portalid = button.data('id')
    if (portalid && portalid !== '') {
        if (confirm(i8ln('I confirm that this portal does not longer exist. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-portal',
                    'portalId': portalid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting portal'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="portals-switch"]').click()
                    jQuery('label[for="portals-switch"]').click()
                }
            })
        }
    }
}

function deletePoi(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var poiid = button.data('id')
    if (poiid && poiid !== '') {
        if (confirm(i8ln('I confirm that this poi has been accepted through niantic or is not eligible as POI. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-poi',
                    'poiId': poiid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting poi'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="poi-switch"]').click()
                    jQuery('label[for="poi-switch"]').click()
                }
            })
        }
    }
}

function getColorByDate(value) {
    // Changes the color from red to green over 15 mins
    var diff = (Date.now() - value) / 1000 / 60 / 15

    if (diff > 1) {
        diff = 1
    }

    // value from 0 to 1 - Green to Red
    var hue = ((1 - diff) * 120).toString(10)
    return ['hsl(', hue, ',100%,50%)'].join('')
}

function setupScannedMarker(item) {
    var circleCenter = new google.maps.LatLng(item['latitude'], item['longitude'])

    var marker = new google.maps.Circle({
        map: map,
        clickable: false,
        center: circleCenter,
        radius: 70, // metres
        fillColor: getColorByDate(item['last_modified']),
        fillOpacity: 0.1,
        strokeWeight: 1,
        strokeOpacity: 0.5
    })

    return marker
}

function getColorBySpawnTime(value) {
    var now = new Date()
    var seconds = now.getMinutes() * 60 + now.getSeconds()
    // account for hour roll-over
    if (seconds < 900 && value > 2700) {
        seconds += 3600
    } else if (seconds > 2700 && value < 900) {
        value += 3600
    }

    var diff = seconds - value
    var hue = 275 // light purple when spawn is neither about to spawn nor active
    if (diff >= 0 && diff <= 900) {
        // green to red over 15 minutes of active spawn
        hue = (1 - diff / 60 / 15) * 120
    } else if (diff < 0 && diff > -300) {
        // light blue to dark blue over 5 minutes til spawn
        hue = (1 - -diff / 60 / 5) * 50 + 200
    }

    hue = Math.round(hue / 5) * 5

    return colourConversion.hsvToHex(hue, 1.0, 1.0)
}
var colourConversion = (function () {
    var self = {}
    self.hsvToHex = function (hue, sat, val) {
        if (hue > 360 || hue < 0 || sat > 1 || sat < 0 || val > 1 || val < 0) {
            console.log('{colourConverion.hsvToHex} illegal input')
            return '#000000'
        }
        let rgbArray = hsvToRgb(hue, sat, val)
        return rgbArrayToHexString(rgbArray)
    }
    function rgbArrayToHexString(rgbArray) {
        let hexString = '#'
        for (var i = 0; i < rgbArray.length; i++) {
            let hexOfNumber = rgbArray[i].toString(16)
            if (hexOfNumber.length === 1) {
                hexOfNumber = '0' + hexOfNumber
            }
            hexString += hexOfNumber
        }
        if (hexString.length !== 7) {
            console.log('Hexstring not complete for colours...')
        }
        return hexString
    }
    function hsvToRgb(hue, sat, val) {
        let hder = Math.floor(hue / 60)
        let f = hue / 60 - hder
        let p = val * (1 - sat)
        let q = val * (1 - sat * f)
        let t = val * (1 - sat * (1 - f))
        var rgb
        if (sat === 0) {
            rgb = [val, val, val]
        } else if (hder === 0 || hder === 6) {
            rgb = [val, t, p]
        } else if (hder === 1) {
            rgb = [q, val, p]
        } else if (hder === 2) {
            rgb = [p, val, t]
        } else if (hder === 3) {
            rgb = [p, q, val]
        } else if (hder === 4) {
            rgb = [t, p, val]
        } else if (hder === 5) {
            rgb = [val, p, q]
        } else {
            console.log('Failed converting HSV to RGB')
        }
        for (var i = 0; i < rgb.length; i++) {
            rgb[i] = Math.round(rgb[i] * 255)
        }
        return rgb
    }
    return self
})()

function setupSpawnpointMarker(item) {
    var hue = getColorBySpawnTime(item.time)

    var rangeCircleOpts = {
        radius: 4,
        weight: 1,
        color: hue,
        opacity: 1,
        center: [item['latitude'], item['longitude']],
        fillColor: hue,
        fillOpacity: 0.4
    }
    var circle = L.circle([item['latitude'], item['longitude']], rangeCircleOpts).bindPopup(spawnpointLabel(item), {autoPan: false, closeOnclick: false, autoClose: false})
    markersnotify.addLayer(circle)
    addListeners(circle)

    return circle
}

function clearSelection() {
    if (document.selection) {
        document.selection.empty()
    } else if (window.getSelection) {
        window.getSelection().removeAllRanges()
    }
}

function addListeners(marker) {
    marker.on('click', function () {
        if (!marker.infoWindowIsOpen) {
            marker.openPopup()
            clearSelection()
            updateLabelDiffTime()
            marker.persist = true
            marker.infoWindowIsOpen = true
        } else {
            marker.persist = null
            marker.closePopup()
            marker.infoWindowIsOpen = false
        }
    })


    if (!isMobileDevice() && !isTouchDevice()) {
        marker.on('mouseover', function () {
            marker.openPopup()
            clearSelection()
            updateLabelDiffTime()
        })
    }

    marker.on('mouseout', function () {
        if (!marker.persist) {
            marker.closePopup()
        }
    })

    return marker
}

function clearStaleMarkers() {
    $.each(mapData.pokemons, function (key, value) {
        if (((mapData.pokemons[key]['disappear_time'] < new Date().getTime() || ((excludedPokemon.indexOf(mapData.pokemons[key]['pokemon_id']) >= 0 || isTemporaryHidden(mapData.pokemons[key]['pokemon_id']) || ((((mapData.pokemons[key]['individual_attack'] + mapData.pokemons[key]['individual_defense'] + mapData.pokemons[key]['individual_stamina']) / 45 * 100 < minIV) || ((mapType === 'monocle' && mapData.pokemons[key]['level'] < minLevel) || (mapType === 'rm' && !isNaN(minLevel) && (mapData.pokemons[key]['cp_multiplier'] < cpMultiplier[minLevel - 1])))) && !excludedMinIV.includes(mapData.pokemons[key]['pokemon_id'])) || (Store.get('showBigKarp') === true && mapData.pokemons[key]['pokemon_id'] === 129 && (mapData.pokemons[key]['weight'] < 13.14 || mapData.pokemons[key]['weight'] === null)) || (Store.get('showTinyRat') === true && mapData.pokemons[key]['pokemon_id'] === 19 && (mapData.pokemons[key]['weight'] > 2.40 || mapData.pokemons[key]['weight'] === null))) && encounterId !== mapData.pokemons[key]['encounter_id'])) || (encounterId && encounterId === mapData.pokemons[key]['encounter_id'] && mapData.pokemons[key]['disappear_time'] < new Date().getTime()))) {
            if (mapData.pokemons[key].marker.rangeCircle) {
                markers.removeLayer(mapData.pokemons[key].marker.rangeCircle)
                markersnotify.removeLayer(mapData.pokemons[key].marker.rangeCircle)
                delete mapData.pokemons[key].marker.rangeCircle
            }
            markers.removeLayer(mapData.pokemons[key].marker)
            markersnotify.removeLayer(mapData.pokemons[key].marker)
            delete mapData.pokemons[key]
        }
    })

    $.each(mapData.lurePokemons, function (key, value) {
        if (mapData.lurePokemons[key]['lure_expiration'] < new Date().getTime() || (excludedPokemon.indexOf(mapData.lurePokemons[key]['pokemon_id']) >= 0 && ((encounterId && encounterId !== mapData.pokemons[key]['encounter_id']) || !encounterId))) {
            markers.removeLayer(mapData.lurePokemons[key].marker)
            markersnotify.removeLayer(mapData.lurePokemons[key].marker)
            delete mapData.lurePokemons[key]
        }
    })

    $.each(mapData.scanned, function (key, value) {
        // If older than 15mins remove
        if (mapData.scanned[key]['last_modified'] < new Date().getTime() - 15 * 60 * 1000) {
            markers.removeLayer(mapData.scanned[key].marker)
            markersnotify.removeLayer(mapData.scanned[key].marker)
            delete mapData.scanned[key]
        }
    })
}

function showInBoundsMarkers(markersInput, type) {
    $.each(markersInput, function (key, value) {
        var marker = markersInput[key].marker
        var show = false
        if (!markersInput[key].hidden) {
            if (typeof marker.getLatLng === 'function') {
                if (map.getBounds().contains(marker.getLatLng())) {
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
                    if (type === 'gym') marker.rangeCircle = addRangeCircle(marker, map, type, markersInput[key].team_id)
                    else marker.rangeCircle = addRangeCircle(marker, map, type)
                }
            } else {
                // there's already a range circle
                if (isRangeActive(map)) {
                    markers.addLayer(marker.rangeCircle)
                } else {
                    markers.removeLayer(marker.rangeCircle)
                    markersnotify.removeLayer(marker.rangeCircle)
                    delete marker.rangeCircle
                }
            }
        }
    })
}

function loadRawData() {
    var loadPokemon = Store.get('showPokemon')
    var loadGyms = (Store.get('showGyms') || Store.get('showRaids')) ? 'true' : 'false'
    var loadPokestops = Store.get('showPokestops')
    var loadLures = Store.get('showLures')
    var loadQuests = Store.get('showQuests')
    var loadDustamount = Store.get('showDustAmount')
    var loadNests = Store.get('showNests')
    var loadCommunities = Store.get('showCommunities')
    var loadPortals = Store.get('showPortals')
    var loadPois = Store.get('showPoi')
    var loadNewPortalsOnly = Store.get('showNewPortalsOnly')
    var loadScanned = Store.get('showScanned')
    var loadSpawnpoints = Store.get('showSpawnpoints')
    var loadMinIV = Store.get('remember_text_min_iv')
    var loadMinLevel = Store.get('remember_text_min_level')
    var bigKarp = Boolean(Store.get('showBigKarp'))
    var tinyRat = Boolean(Store.get('showTinyRat'))
    var exEligible = Boolean(Store.get('exEligible'))
    var bounds = map.getBounds()
    var swPoint = bounds.getSouthWest()
    var nePoint = bounds.getNorthEast()
    var swLat = swPoint.lat
    var swLng = swPoint.lng
    var neLat = nePoint.lat
    var neLng = nePoint.lng
    return $.ajax({
        url: 'raw_data',
        type: 'POST',
        timeout: 300000,
        data: {
            'timestamp': timestamp,
            'login': login,
            'expireTimestamp': expireTimestamp,
            'pokemon': loadPokemon,
            'lastpokemon': lastpokemon,
            'pokestops': loadPokestops,
            'lures': loadLures,
            'quests': loadQuests,
            'dustamount': loadDustamount,
            'reloaddustamount': reloaddustamount,
            'nests': loadNests,
            'lastnests': lastnests,
            'communities': loadCommunities,
            'lastcommunities': lastcommunities,
            'portals': loadPortals,
            'pois': loadPois,
            'lastpois': lastpois,
            'newportals': loadNewPortalsOnly,
            'lastportals': lastportals,
            'lastpokestops': lastpokestops,
            'gyms': loadGyms,
            'lastgyms': lastgyms,
            'exEligible': exEligible,
            'scanned': loadScanned,
            'lastslocs': lastslocs,
            'spawnpoints': loadSpawnpoints,
            'lastspawns': lastspawns,
            'minIV': loadMinIV,
            'prevMinIV': prevMinIV,
            'minLevel': loadMinLevel,
            'prevMinLevel': prevMinLevel,
            'bigKarp': bigKarp,
            'tinyRat': tinyRat,
            'swLat': swLat,
            'swLng': swLng,
            'neLat': neLat,
            'neLng': neLng,
            'oSwLat': oSwLat,
            'oSwLng': oSwLng,
            'oNeLat': oNeLat,
            'oNeLng': oNeLng,
            'reids': String(reincludedPokemon),
            'eids': String(excludedPokemon),
            'exMinIV': String(excludedMinIV),
            'qpreids': String(reincludedQuestsPokemon),
            'qpeids': String(questsExcludedPokemon),
            'qireids': String(reincludedQuestsItem),
            'qieids': String(questsExcludedItem),
            'token': token,
            'encId': encounterId
        },
        dataType: 'json',
        cache: false,
        beforeSend: function beforeSend() {
            if (maxLatLng > 0 && (((neLat - swLat) > maxLatLng) || ((neLng - swLng) > maxLatLng))) {
                toastr['error'](i8ln('Please zoom in to get data.'), i8ln('Max zoom'))
                toastr.options = toastrOptions
                return false
            }
            if (rawDataIsLoading) {
                return false
            } else {
                rawDataIsLoading = true
            }
        },
        error: function error() {
            // Display error toast
            toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error getting data'))
            toastr.options = toastrOptions
        },
        complete: function complete() {
            rawDataIsLoading = false
        }
    })
}

function loadWeather() {
    return $.ajax({
        url: 'weather_data?all',
        type: 'POST',
        timeout: 300000,
        dataType: 'json',
        cache: false,
        error: function error() {
            // Display error toast
            toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error getting weather'))
            toastr.options = toastrOptions
        },
        complete: function complete() {

        }
    })
}

function loadWeatherCellData(cell) { // eslint-disable-line no-unused-vars
    return $.ajax({
        url: 'weather_data?cell',
        type: 'POST',
        timeout: 300000,
        dataType: 'json',
        cache: false,
        data: {
            'cell_id': cell
        },
        error: function error() {
            // Display error toast
            toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error getting weather'))
            toastr.options = toastrOptions
        },
        complete: function complete() {

        }
    })
}
function searchForItem(lat, lon, term, type, field) {
    if (term !== '') {
        $.ajax({
            url: 'search',
            type: 'POST',
            timeout: 300000,
            dataType: 'json',
            cache: false,
            data: {
                'action': type,
                'term': term,
                'lat': lat,
                'lon': lon
            },
            error: function error() {
                // Display error toast
                toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error searching'))
                toastr.options = toastrOptions
            }
        }).done(function (data) {
            if (data) {
                var par = field.parent()
                var sr = par.find('.search-results')
                sr.html('')
                $.each(data.reward, function (i, element) {
                    var pokemonIdStr = ''
                    if (element.quest_pokemon_id <= 9) {
                        pokemonIdStr = '00' + element.quest_pokemon_id
                    } else if (element.quest_pokemon_id <= 99) {
                        pokemonIdStr = '0' + element.quest_pokemon_id
                    } else {
                        pokemonIdStr = element.quest_pokemon_id
                    }
                    var scanArea
                    var latlng = turf.point([element.lon, element.lat])
                    $.each(scanAreas, function (index, poly) {
                        var insideScan = turf.booleanPointInPolygon(latlng, poly)
                        if (insideScan) {
                            scanArea = insideScan
                            return false
                        }
                    })
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (sr.hasClass('reward-results')) {
                        if (element.quest_pokemon_id !== 0) {
                            html += '<span style="background:url(' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png) no-repeat;" class="i-icon" ></span>'
                        }
                        if (element.quest_item_id !== 0) {
                            html += '<span style="background:url(' + iconpath + 'rewards/reward_' + element.quest_item_id + '_1.png) no-repeat;" class="i-icon" ></span>'
                        }
                    }
                    html += '<div class="cont">'
                    if (sr.hasClass('reward-results')) {
                        if (element.pokemon_name !== null) {
                            html += '<span class="reward" style="font-weight:bold">' + element.pokemon_name + '</span><span>&nbsp;-&#32;</span>'
                        }
                        if (element.item_name !== null) {
                            html += '<span class="reward" style="font-weight:bold">' + element.item_name + '</span><span>&nbsp;-&#32;</span>'
                        }
                    }
                    html += '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>'
                    html += '</div></div>'
                    if (sr.hasClass('pokestop-results') && !noManualQuests && !scanArea) {
                        html += '<div class="right-column"><i class="fa fa-binoculars submit-quests"  onClick="openQuestModal(event);" data-id="' + element.external_id + '"></i></div>'
                    } else {
                        html += '<div class="right-column" onClick="centerMapOnCoords(event);"><span style="background:url(' + element.url + ') no-repeat;" class="i-icon" ></span></div>'
                    }
                    html += '</li>'
                    sr.append(html)
                })
                $.each(data.forts, function (i, element) {
                    var scanArea
                    var latlng = turf.point([element.lon, element.lat])
                    $.each(scanAreas, function (index, poly) {
                        var insideScan = turf.booleanPointInPolygon(latlng, poly)
                        if (insideScan) {
                            scanArea = insideScan
                            return false
                        }
                    })
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (sr.hasClass('gym-results')) {
                        html += '<span style="background:url(' + element.url + ') no-repeat;" class="i-icon" ></span>'
                    }
                    html += '<div class="cont">' +
                    '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>' +
                    '</div></div>'
                    if (sr.hasClass('gym-results') && manualRaids && !scanArea) {
                        html += '<div class="right-column"><i class="fa fa-binoculars submit-raid"  onClick="openRaidModal(event);" data-id="' + element.external_id + '"></i></div>'
                    }
                    html += '</li>'
                    sr.append(html)
                })
                $.each(data.pokestops, function (i, element) {
                    var scanArea
                    var latlng = turf.point([element.lon, element.lat])
                    $.each(scanAreas, function (index, poly) {
                        var insideScan = turf.booleanPointInPolygon(latlng, poly)
                        if (insideScan) {
                            scanArea = insideScan
                            return false
                        }
                    })
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (sr.hasClass('pokestop-results')) {
                        html += '<span style="background:url(' + element.url + ') no-repeat;" class="i-icon" ></span>'
                    }
                    html += '<div class="cont">' +
                    '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>' +
                    '</div></div>'
                    if (sr.hasClass('pokestop-results') && !noManualQuests && !scanArea) {
                        html += '<div class="right-column"><i class="fa fa-binoculars submit-quests"  onClick="openQuestModal(event);" data-id="' + element.external_id + '"></i></div>'
                    }
                    html += '</li>'
                    sr.append(html)
                })
                $.each(data.portals, function (i, element) {
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (sr.hasClass('portals-results')) {
                        html += '<span style="background:url(' + element.url + ') no-repeat;" class="i-icon" ></span>'
                    }
                    html += '<div class="cont">' +
                    '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>' +
                    '</div></div>' +
                    '</li>'
                    sr.append(html)
                })
                $.each(data.nests, function (i, element) {
                    var pokemonIdStr = ''
                    if (element.pokemon_id <= 9) {
                        pokemonIdStr = '00' + element.pokemon_id
                    } else if (element.pokemon_id <= 99) {
                        pokemonIdStr = '0' + element.pokemon_id
                    } else {
                        pokemonIdStr = element.pokemon_id
                    }
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (sr.hasClass('nest-results')) {
                        html += '<span style="background:url(' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png) no-repeat;" class="i-icon" ></span>'
                    }
                    html += '<div class="cont">' +
                    '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>' +
                    '</div></div>' +
                    '</li>'
                    sr.append(html)
                })
            }
        })
    }
}

function searchAjax(field) { // eslint-disable-line no-unused-vars
    var term = field.val()
    var type = field.data('type')
    navigator.geolocation.getCurrentPosition(function (position) {
        searchForItem(position.coords.latitude, position.coords.longitude, term, type, field)
    }, function (err) {
        if (err) {
            var center = map.getCenter()
            searchForItem(center.lat, center.lng, term, type, field)
        }
    })
}

function centerMapOnCoords(event) { // eslint-disable-line no-unused-vars
    var point = $(event.target)
    var zoom
    if (point.hasClass('place-result')) {
        point = point.parent()
        zoom = 15
    } else if (point.hasClass('left-column')) {
        point = point.parent()
        zoom = 18
    } else if (point.hasClass('cont')) {
        point = point.parent().parent().parent()
        zoom = 18
    } else if (point.hasClass('name') || point.hasClass('reward')) {
        point = point.parent().parent().parent()
        zoom = 16
    } else if (point.hasClass('pokemon-icon')) {
        point = point.parent().parent().parent()
        zoom = 18
    } else if (point.hasClass('distance')) {
        point = point.parent().parent().parent()
        zoom = 17
    } else if (!point.hasClass('search-result')) {
        point = point.parent().parent()
        zoom = 17
    } else {
        point = point.parent().parent().parent()
        zoom = 17
    }
    var latlng = new L.LatLng(point.data('lat'), point.data('lon'))
    map.setView(latlng, zoom)
    $('.ui-dialog-content').dialog('close')
}

function manualPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopName = form.find('[name="pokestop-name"]').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    var scanArea
    var latlng = turf.point([lon, lat])
    $.each(scanAreas, function (index, poly) {
        var insideScan = turf.booleanPointInPolygon(latlng, poly)
        if (insideScan) {
            scanArea = insideScan
            return false
        }
    })
    if (pokestopName && pokestopName !== '' && !scanArea) {
        if (confirm(i8ln('I confirm this is an accurate reporting of a new pokestop'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'pokestop',
                    'pokestopName': pokestopName,
                    'lat': lat,
                    'lon': lon
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Pokestop'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a Pokéstop inside the scan area is not allowed'))) {
            $('.ui-dialog-content').dialog('close')
        }
    }
}

function manualGymData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var gymName = form.find('[name="gym-name"]').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    var scanArea
    var latlng = turf.point([lon, lat])
    $.each(scanAreas, function (index, poly) {
        var insideScan = turf.booleanPointInPolygon(latlng, poly)
        if (insideScan) {
            scanArea = insideScan
            return false
        }
    })
    if (gymName && gymName !== '' && !scanArea) {
        if (confirm(i8ln('I confirm this is an accurate reporting of a new gym'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'gym',
                    'gymName': gymName,
                    'lat': lat,
                    'lon': lon
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Gym'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgyms = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a Gym inside the scan area is not allowed'))) {
            $('.ui-dialog-content').dialog('close')
        }
    }
}
function manualPokemonData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent().parent()
    var pokemonId = form.find('.pokemonID').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    var scanArea
    var latlng = turf.point([lon, lat])
    $.each(scanAreas, function (index, poly) {
        var insideScan = turf.booleanPointInPolygon(latlng, poly)
        if (insideScan) {
            scanArea = insideScan
            return false
        }
    })
    if (pokemonId && pokemonId !== '' && !scanArea) {
        if (confirm(i8ln('I confirm this is an accurate reporting of a new pokemon'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'pokemon',
                    'pokemonId': pokemonId,
                    'lat': lat,
                    'lon': lon
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Pokemon'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpokemon = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a wild spawn inside the scan area is not allowed'))) {
            $('.ui-dialog-content').dialog('close')
        }
    }
}
function deleteGym(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var gymId = button.data('id')
    if (gymId && gymId !== '') {
        if (confirm(i8ln('I confirm that I want to delete this gym. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-gym',
                    'gymId': gymId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Deleting Gym'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('#gym-details').removeClass('visible')
                }
            })
        }
    }
}
function toggleExGym(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var gymId = button.data('id')
    if (gymId && gymId !== '') {
        if (confirm(i8ln('I confirm that this gym is EX eligible.'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'toggle-ex-gym',
                    'gymId': gymId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error marking as EX Gym'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('#gym-details').removeClass('visible')
                }
            })
        }
    }
}
function deletePokestop(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var pokestopId = button.data('id')
    if (pokestopId && pokestopId !== '') {
        if (confirm(i8ln('I confirm that I want to delete this pokestop. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-pokestop',
                    'pokestopId': pokestopId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Deleting Pokestop'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                }
            })
        }
    }
}
function renamePokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopId = form.find('.renamepokestopid').val()
    var pokestopName = form.find('[name="pokestop-name"]').val()
    if (pokestopName && pokestopName !== '') {
        if (confirm(i8ln('I confirm this is an accurate new name for this pokestop'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'renamepokestop',
                    'pokestopId': pokestopId,
                    'pokestopName': pokestopName
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error changing Pokestop name'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function convertPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopId = form.find('.convertpokestopid').val()
    if (pokestopId && pokestopId !== '') {
        if (confirm(i8ln('I confirm this pokestop is now a gym'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertpokestop',
                    'pokestopId': pokestopId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Pokestop ID got lost somewhere.'), i8ln('Error converting Pokestop'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgyms = false
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function convertPortalToPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is a pokestop'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportalpokestop',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error converting to Pokestop'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgyms = false
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function convertPortalToGymData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is a gym'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportalgym',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error converting to Gym'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgyms = false
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function markPortalChecked(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is not a Pokestop or Gym'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'markportal',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error marking portal'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastportals = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function deleteNest(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var nestid = button.data('id')
    if (nestid && nestid !== '') {
        if (confirm(i8ln('I confirm that I want to delete this nest. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-nest',
                    'nestId': nestid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Deleting nest'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="nests-switch"]').click()
                    jQuery('label[for="nests-switch"]').click()
                }
            })
        }
    }
}

function submitNewNest(event) { // eslint-disable-line no-unused-vars
    var cont = $(event.target).parent().parent()
    var pokemonId = cont.find('.pokemonID').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    if (lat && lat !== '' && lon && lon !== '') {
        if (confirm(i8ln('I confirm this is an new nest'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'new-nest',
                    'lat': lat,
                    'lon': lon,
                    'pokemonId': pokemonId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Nest'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastnests = false
                    updateMap()
                    jQuery('label[for="nests-switch"]').click()
                    jQuery('label[for="nests-switch"]').click()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function manualNestData(event) { // eslint-disable-line no-unused-vars
    var cont = $(event.target).parent().parent().parent()
    var nestId = cont.find('.submitting-nests').data('nest')
    var pokemonId = cont.find('.pokemonID').val()
    if (nestId && nestId !== '' && pokemonId && pokemonId !== '') {
        if (confirm(i8ln('I confirm this is an accurate sighting of a quest'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'nest',
                    'nestId': nestId,
                    'pokemonId': pokemonId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Nest'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastnests = false
                    updateMap()
                    jQuery('label[for="nests-switch"]').click()
                    jQuery('label[for="nests-switch"]').click()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function manualQuestData(event) { // eslint-disable-line no-unused-vars
    var cont = $(event.target).parent().parent()
    var questType = cont.find('.questTypeList').val()
    var questTarget = cont.find('.questAmountList').val()
    var conditionType = cont.find('.conditionTypeList').val()
    var catchPokemon = cont.find('.pokeCatchList').val()
    var catchPokemonCategory = cont.find('.typeCatchList').val()
    var raidLevel = cont.find('.raidLevelList').val()
    var throwType = cont.find('.throwTypeList').val()
    var curveThrow = cont.find('.curveThrow').val()
    var rewardType = cont.find('.rewardTypeList').val()
    var encounter = cont.find('.pokeQuestList').val()
    var item = cont.find('.itemQuestList').val()
    var itemamount = cont.find('.itemAmountList').val()
    var dust = cont.find('.dustQuestList').val()
    var pokestopId = cont.find('.questPokestop').val()
    if (pokestopId && pokestopId !== '') {
        if (confirm(i8ln('I confirm this is an accurate sighting of a quest'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'quest',
                    'questType': questType,
                    'questTarget': questTarget,
                    'conditionType': conditionType,
                    'catchPokemon': catchPokemon,
                    'catchPokemonCategory': catchPokemonCategory,
                    'raidLevel': raidLevel,
                    'throwType': throwType,
                    'curveThrow': curveThrow,
                    'rewardType': rewardType,
                    'encounter': encounter,
                    'item': item,
                    'itemamount': itemamount,
                    'dust': dust,
                    'pokestopId': pokestopId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Quest'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpokestops = false
                    updateMap()
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function manualRaidData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokemonId = form.find('[name="pokemonId"]').val()
    gymId = form.find('[name="gymId"]').val()
    var monTime = form.find('[name="mon_time"]').val()
    var eggTime = form.find('[name="egg_time"]').val()
    if (pokemonId && pokemonId !== '' && gymId && gymId !== '' && eggTime && eggTime !== '' && monTime && monTime !== '') {
        if (confirm(i8ln('I confirm this is an accurate sighting of a raid'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'raid',
                    'pokemonId': pokemonId,
                    'gymId': gymId,
                    'monTime': monTime,
                    'eggTime': eggTime
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error Submitting Raid'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgyms = false
                    updateMap()
                    if (Store.get('useGymSidebar')) {
                        showGymDetails(form.find('[name="gymId"]').val())
                    }
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function submitNewCommunity(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    var communityName = form.find('[name="community-name"]').val()
    var communityDescription = form.find('[name="community-description"]').val()
    var communityInvite = form.find('[name="community-invite"]').val()
    if (communityName && communityName !== '' && communityDescription && communityDescription !== '' && communityInvite && communityInvite !== '') {
        if (confirm(i8ln('I confirm this is an active community'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'community-add',
                    'lat': lat,
                    'lon': lon,
                    'communityName': communityName,
                    'communityDescription': communityDescription,
                    'communityInvite': communityInvite
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Make sure all fields are filled and the invite link is a valid Discord, Telegram or Whatsapp link.'), i8ln('Error Submitting community'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastcommunities = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function deleteCommunity(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var communityid = button.data('id')
    if (communityid && communityid !== '') {
        if (confirm(i8ln('I confirm that I want to delete this community. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-community',
                    'communityId': communityid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting community'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="communities-switch"]').click()
                    jQuery('label[for="communities-switch"]').click()
                }
            })
        }
    }
}
function editCommunityData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var communityId = form.find('.editcommunityid').val()
    var communityName = form.find('[name="community-name"]').val()
    var communityDescription = form.find('[name="community-description"]').val()
    var communityInvite = form.find('[name="community-invite"]').val()
    if ((communityName && communityName !== '') && (communityDescription && communityDescription !== '') && (communityInvite && communityInvite !== '')) {
        if (confirm(i8ln('I confirm this new info accurate for this community'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'editcommunity',
                    'communityId': communityId,
                    'communityName': communityName,
                    'communityDescription': communityDescription,
                    'communityInvite': communityInvite
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('No fields are filled or an invalid url is found, please check the form.'), i8ln('Error changing community'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="communities-switch"]').click()
                    jQuery('label[for="communities-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function submitPoi(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lon = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    var poiName = form.find('[name="poi-name"]').val()
    var poiDescription = form.find('[name="poi-description"]').val()
    if (poiName && poiName !== '' && poiDescription && poiDescription !== '') {
        if (confirm(i8ln('I confirm this is an eligible POI location'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'poi-add',
                    'lat': lat,
                    'lon': lon,
                    'poiName': poiName,
                    'poiDescription': poiDescription
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Make sure all fields are filled.'), i8ln('Error Submitting poi'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpois = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function markPoiSubmitted(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('.markpoiid').val()
    if (poiId && poiId !== '') {
        if (confirm(i8ln('I confirm this POI is submitted to OPR'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'markpoisubmitted',
                    'poiId': poiId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('POI id got lost somewhere.'), i8ln('Error marking portal'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpois = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function markPoiDeclined(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('.markpoiid').val()
    if (poiId && poiId !== '') {
        if (confirm(i8ln('I confirm this POI is declined by OPR'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'markpoideclined',
                    'poiId': poiId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('POI id got lost somewhere.'), i8ln('Error marking portal'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpois = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}

function openNestModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.submitting-nests').attr('data-nest', val)
    $('.global-nest-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        title: i8ln('Submit a Nest'),
        buttons: {},
        classes: {
            'ui-dialog': 'ui-dialog nest-widget-popup'
        },
        open: function (event, ui) {
            $('.nest-widget-popup .pokemon-list-cont').each(function (index) {
                $(this).attr('id', 'pokemon-list-cont-7' + index)
                var options = {
                    valueNames: ['name', 'types', 'id']
                }
                var monList = new List('pokemon-list-cont-7' + index, options) // eslint-disable-line no-unused-vars
            })
        }
    })
}
function openRaidModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('#raidModalGymId').val(val)
    $('.raid-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openQuestModal(event) { // eslint-disable-line no-unused-vars
    $(function () {
        var $questTypeList = $('.quest-modal #questTypeList')
        $questTypeList.select2({
            placeholder: i8ln('Quest type'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })
        $questTypeList.change(function () {
            var questType = Number($(this).find('option:selected').val())
            if (questType > 0) {
                $('.quest-modal #questAmountList').show()
            } else {
                $('.quest-modal #questAmountList').hide()
            }
        })

        var $questAmountList = $('.quest-modal #questAmountList')
        $questAmountList.select2({
            placeholder: i8ln('Quest target amount'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })

        var $pokeCatchList = $('.quest-modal #pokeCatchList')
        $pokeCatchList.select2({
            placeholder: i8ln('Pokemon'),
            data: pokeList,
            multiple: true,
            maximumSelectionSize: 2
        })

        var $pokemonTypes = $('.quest-modal #typeCatchList')
        $pokemonTypes.select2({
            placeholder: i8ln('Pokemon type'),
            minimumResultsForSearch: Infinity,
            multiple: true,
            maximumSelectionSize: 3
        })

        var $raidLevelList = $('.quest-modal #raidLevelList')
        $raidLevelList.select2({
            placeholder: i8ln('Raid level'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            multiple: true,
            maximumSelectionSize: 1
        })

        var $throwTypes = $('.quest-modal #throwTypeList')
        $throwTypes.select2({
            placeholder: i8ln('Throw type'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })
        var $curveThrow = $('.quest-modal #curveThrow')
        $curveThrow.select2({
            placeholder: i8ln('Curve throw'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })
        var $conditionTypeList = $('.quest-modal #conditionTypeList')
        $conditionTypeList.select2({
            placeholder: i8ln('Condition type'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })
        $('.quest-modal #pokeCatchList').next('.select2-container').hide()
        $('.quest-modal #typeCatchList').next('.select2-container').hide()
        $('.quest-modal #raidLevelList').next('.select2-container').hide()
        $('.quest-modal #throwTypeList').next('.select2-container').hide()
        $('.quest-modal #curveThrow').next('.select2-container').hide()
        $conditionTypeList.change(function () {
            var conditionType = Number($(this).find('option:selected').val())
            if (conditionType === 1) {
                $('.quest-modal #pokeCatchList').next('.select2-container').hide()
                $('.quest-modal #typeCatchList').next('.select2-container').show()
                $('.quest-modal #raidLevelList').next('.select2-container').hide()
                $('.quest-modal #throwTypeList').next('.select2-container').hide()
                $('.quest-modal #curveThrow').next('.select2-container').hide()
            } else if (conditionType === 2) {
                $('.quest-modal #pokeCatchList').next('.select2-container').show()
                $('.quest-modal #typeCatchList').next('.select2-container').hide()
                $('.quest-modal #raidLevelList').next('.select2-container').hide()
                $('.quest-modal #throwTypeList').next('.select2-container').hide()
                $('.quest-modal #curveThrow').next('.select2-container').hide()
            } else if (conditionType === 7) {
                $('.quest-modal #pokeCatchList').next('.select2-container').hide()
                $('.quest-modal #typeCatchList').next('.select2-container').hide()
                $('.quest-modal #raidLevelList').next('.select2-container').show()
                $('.quest-modal #throwTypeList').next('.select2-container').hide()
                $('.quest-modal #curveThrow').next('.select2-container').hide()
            } else if (conditionType === 8 || conditionType === 14) {
                $('.quest-modal #pokeCatchList').next('.select2-container').hide()
                $('.quest-modal #typeCatchList').next('.select2-container').hide()
                $('.quest-modal #raidLevelList').next('.select2-container').hide()
                $('.quest-modal #throwTypeList').next('.select2-container').show()
                $('.quest-modal #curveThrow').next('.select2-container').show()
            } else {
                $('.quest-modal #pokeCatchList').next('.select2-container').hide()
                $('.quest-modal #typeCatchList').next('.select2-container').hide()
                $('.quest-modal #raidLevelList').next('.select2-container').hide()
                $('.quest-modal #throwTypeList').next('.select2-container').hide()
                $('.quest-modal #curveThrow').next('.select2-container').hide()
            }
        })
        var $rewardTypeList = $('.quest-modal #rewardTypeList')
        $rewardTypeList.select2({
            placeholder: i8ln('Reward type'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })

        var $itemQuestList = $('.quest-modal #itemQuestList')
        $itemQuestList.select2({
            placeholder: i8ln('Reward Item'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })

        var $itemAmountList = $('.quest-modal #itemAmountList')
        $itemAmountList.select2({
            placeholder: i8ln('Reward Amount'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })

        var $dustQuestList = $('.quest-modal #dustQuestList')
        $dustQuestList.select2({
            placeholder: i8ln('Stardust amount'),
            closeOnSelect: true,
            minimumResultsForSearch: Infinity,
            maximumSelectionSize: 1
        })

        var $pokeQuestList = $('.quest-modal #pokeQuestList')
        $pokeQuestList.select2({
            placeholder: i8ln('Pokemon encounter'),
            closeOnSelect: true,
            maximumSelectionSize: 1
        })
        $('.quest-modal #itemQuestList').next('.select2-container').hide()
        $('.quest-modal #itemAmountList').next('.select2-container').hide()
        $('.quest-modal #dustQuestList').next('.select2-container').hide()
        $('.quest-modal #pokeQuestList').next('.select2-container').hide()

        $rewardTypeList.change(function () {
            var rewardType = $(this).find('option:selected').val()
            if (rewardType === '2') {
                $('.quest-modal #itemQuestList').next('.select2-container').show()
                $('.quest-modal #itemAmountList').next('.select2-container').show()
                $('.quest-modal #dustQuestList').next('.select2-container').hide()
                $('.quest-modal #pokeQuestList').next('.select2-container').hide()
            } else if (rewardType === '3') {
                $('.quest-modal #itemQuestList').next('.select2-container').hide()
                $('.quest-modal #itemAmountList').next('.select2-container').hide()
                $('.quest-modal #dustQuestList').next('.select2-container').show()
                $('.quest-modal #pokeQuestList').next('.select2-container').hide()
            } else if (rewardType === '7') {
                $('.quest-modal #itemQuestList').next('.select2-container').hide()
                $('.quest-modal #itemAmountList').next('.select2-container').hide()
                $('.quest-modal #dustQuestList').next('.select2-container').hide()
                $('.quest-modal #pokeQuestList').next('.select2-container').show()
            } else {
                $('.quest-modal #itemQuestList').next('.select2-container').hide()
                $('.quest-modal #itemAmountList').next('.select2-container').hide()
                $('.quest-modal #dustQuestList').next('.select2-container').hide()
                $('.quest-modal #pokeQuestList').next('.select2-container').hide()
            }
        })
    })
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.questPokestop').val(val)
    $('.quest-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Submit a Quest'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openRenamePokestopModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.renamepokestopid').val(val)
    $('.rename-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Rename Pokéstop'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openConvertPokestopModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.convertpokestopid').val(val)
    $('.convert-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Convert Pokéstop to Gym'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openConvertPortalModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.convertportalid').val(val)
    $('.convert-portal-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Convert to Pokestop/Gym'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openMarkPoiModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.markpoiid').val(val)
    $('.mark-poi-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Mark POI'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openEditCommunityModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    var title = $(event.target).data('title')
    var description = $(event.target).data('description')
    var invite = $(event.target).data('invite')
    $('.editcommunityid').val(val)
    $('#community-name').val(title)
    $('#community-description').val(description)
    $('#community-invite').val(invite)
    $('.editcommunity-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Edit Community'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function generateRaidModal() {
    var raidStr = '<form class="raid-modal" style="display:none;" title="' + i8ln('Submit a Raid Report') + '">'
    raidStr += '<input type="hidden" value="" id="raidModalGymId" name="gymId" autofocus>'
    raidStr += '<div class=" switch-container">' +
        generateRaidBossList() +
        '</div>' +
        '<div class="mon-name" style="display:none;"></div>' +
        '<div class="switch-container timer-cont" style="text-align:center;display:none">' +
        '<h5 class="timer-name" style="margin-bottom:0;"></h5>' +
        generateTimerLists() +
        '</div>' +
        '<button type="button" onclick="manualRaidData(event);" class="submitting-raid"><i class="fa fa-binoculars" style="margin-right:10px;"></i>' + i8ln('Submit Raid') + '</button>' +
        '<button type="button" onclick="$(\'.ui-dialog-content\').dialog(\'close\');" class="close-modal"><i class="fa fa-times" aria-hidden="true"></i></button>' +
        '</form>'
    return raidStr
}

function generateTimerLists() {
    var html = '<select name="egg_time" class="egg_time" style="display:none;">' +
        '<option value="60" selected>60</option>' +
        '<option value="59">59</option>' +
        '<option value="58">58</option>' +
        '<option value="57">57</option>' +
        '<option value="56">56</option>' +
        '<option value="55">55</option>' +
        '<option value="54">54</option>' +
        '<option value="53">53</option>' +
        '<option value="52">52</option>' +
        '<option value="51">51</option>' +
        '<option value="50">50</option>' +
        '<option value="49">49</option>' +
        '<option value="48">48</option>' +
        '<option value="47">47</option>' +
        '<option value="46">46</option>' +
        '<option value="45">45</option>' +
        '<option value="44">44</option>' +
        '<option value="43">43</option>' +
        '<option value="42">42</option>' +
        '<option value="41">41</option>' +
        '<option value="40">40</option>' +
        '<option value="39">39</option>' +
        '<option value="38">38</option>' +
        '<option value="37">37</option>' +
        '<option value="36">36</option>' +
        '<option value="35">35</option>' +
        '<option value="34">34</option>' +
        '<option value="33">33</option>' +
        '<option value="32">32</option>' +
        '<option value="31">31</option>' +
        '<option value="30">30</option>' +
        '<option value="29">29</option>' +
        '<option value="28">28</option>' +
        '<option value="27">27</option>' +
        '<option value="26">26</option>' +
        '<option value="25">25</option>' +
        '<option value="24">24</option>' +
        '<option value="23">23</option>' +
        '<option value="22">22</option>' +
        '<option value="21">21</option>' +
        '<option value="20">20</option>' +
        '<option value="19">19</option>' +
        '<option value="18">18</option>' +
        '<option value="17">17</option>' +
        '<option value="16">16</option>' +
        '<option value="15">15</option>' +
        '<option value="14">14</option>' +
        '<option value="13">13</option>' +
        '<option value="12">12</option>' +
        '<option value="11">11</option>' +
        '<option value="10">10</option>' +
        '<option value="9">9</option>' +
        '<option value="8">8</option>' +
        '<option value="7">7</option>' +
        '<option value="6">6</option>' +
        '<option value="5">5</option>' +
        '<option value="4">4</option>' +
        '<option value="3">3</option>' +
        '<option value="2">2</option>' +
        '<option value="1">1</option>' +
        '</select>' +
        '<select name="mon_time" class="mon_time" style="display:none;">' +
        '<option value="45" selected>45</option>' +
        '<option value="44">44</option>' +
        '<option value="43">43</option>' +
        '<option value="42">42</option>' +
        '<option value="41">41</option>' +
        '<option value="40">40</option>' +
        '<option value="39">39</option>' +
        '<option value="38">38</option>' +
        '<option value="37">37</option>' +
        '<option value="36">36</option>' +
        '<option value="35">35</option>' +
        '<option value="34">34</option>' +
        '<option value="33">33</option>' +
        '<option value="32">32</option>' +
        '<option value="31">31</option>' +
        '<option value="30">30</option>' +
        '<option value="29">29</option>' +
        '<option value="28">28</option>' +
        '<option value="27">27</option>' +
        '<option value="26">26</option>' +
        '<option value="25">25</option>' +
        '<option value="24">24</option>' +
        '<option value="23">23</option>' +
        '<option value="22">22</option>' +
        '<option value="21">21</option>' +
        '<option value="20">20</option>' +
        '<option value="19">19</option>' +
        '<option value="18">18</option>' +
        '<option value="17">17</option>' +
        '<option value="16">16</option>' +
        '<option value="15">15</option>' +
        '<option value="14">14</option>' +
        '<option value="13">13</option>' +
        '<option value="12">12</option>' +
        '<option value="11">11</option>' +
        '<option value="10">10</option>' +
        '<option value="9">9</option>' +
        '<option value="8">8</option>' +
        '<option value="7">7</option>' +
        '<option value="6">6</option>' +
        '<option value="5">5</option>' +
        '<option value="4">4</option>' +
        '<option value="3">3</option>' +
        '<option value="2">2</option>' +
        '<option value="1">1</option>' +
        '</select>'
    return html
}
function openSearchModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var modal = $('.search-modal')
    var wwidth = $(window).width()
    var width = 300
    if (wwidth > 768) {
        width = 500
    }
    modal.clone().dialog({
        autoOpen: true,
        resizable: false,
        draggable: false,
        modal: true,
        title: i8ln('Search...'),
        classes: {
            'ui-dialog': 'ui-dialog search-widget-popup'
        },
        width: width,
        buttons: {},
        open: function (event, ui) {
            jQuery('input[name="gym-search"], input[name="pokestop-search"], input[name="reward-search"], input[name="nest-search"], input[name="portals-search"]').bind('input', function () {
                searchAjax($(this))
            })
            $('.search-widget-popup #search-tabs').tabs()
        }
    })
}

function processPokemons(i, item) {
    if (!Store.get('showPokemon')) {
        return false // in case the checkbox was unchecked in the meantime.
    }
    if (!(item['encounter_id'] in mapData.pokemons) && item['disappear_time'] > Date.now() && ((encounterId && encounterId === item['encounter_id']) || (excludedPokemon.indexOf(item['pokemon_id']) < 0 && !isTemporaryHidden(item['pokemon_id'])))) {
        // add marker to map and item to dict
        if (item.marker) {
            markers.removeLayer(item.marker)
            markersnotify.removeLayer(item.marker)
        }
        if (!item.hidden) {
            item.marker = setupPokemonMarker(item, map)
            customizePokemonMarker(item.marker, item)
            mapData.pokemons[item['encounter_id']] = item
        }
        if (encounterId && encounterId === item['encounter_id']) {
            if (!item.marker.infoWindowIsOpen) {
                item.marker.openPopup()
                clearSelection()
                updateLabelDiffTime()
                item.marker.persist = true
                item.marker.infoWindowIsOpen = true
            } else {
                item.marker.persist = null
                item.marker.closePopup()
                item.marker.infoWindowIsOpen = false
            }
        }
    }
}
function processNests(i, item) {
    if (!Store.get('showNests')) {
        return false
    }

    if (!mapData.nests[item['nest_id']]) {
        // new pokestop, add marker to map and item to dict
        if (item.marker && item.marker.rangeCircle) {
            markers.removeLayer(item.marker.rangeCircle)
        }
        if (item.marker) {
            markers.removeLayer(item.marker)
        }
        item.marker = setupNestMarker(item)
        mapData.nests[item['nest_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.nests[item['nest_id']]
        markers.removeLayer(item2.marker)
        item.marker = setupNestMarker(item)
        mapData.nests[item['nest_id']] = item
    }
}
function processCommunities(i, item) {
    if (!Store.get('showCommunities')) {
        return false
    }

    if (!mapData.communities[item['community_id']]) {
        // new pokestop, add marker to map and item to dict
        if (item.marker && item.marker.rangeCircle) {
            markers.removeLayer(item.marker.rangeCircle)
        }
        if (item.marker) {
            markers.removeLayer(item.marker)
        }
        item.marker = setupCommunityMarker(item)
        mapData.communities[item['community_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.communities[item['community_id']]
        markers.removeLayer(item2.marker)
        item.marker = setupCommunityMarker(item)
        mapData.communities[item['community_id']] = item
    }
}
function processPortals(i, item) {
    if (!Store.get('showPortals')) {
        return false
    }

    if (Store.get('showNewPortalsOnly') === 1 && !item['imported']) {
        return true
    }

    if (!mapData.portals[item['external_id']]) {
        // new portal, add marker to map and item to dict
        if (item.marker) {
            markers.removeLayer(item.marker)
        }
        item.marker = setupPortalMarker(item)
        mapData.portals[item['external_id']] = item
    } else {
        // change existing portal marker to old/new
        var item2 = mapData.portals[item['external_id']]
        if (!!item['imported'] !== !!item2['imported']) {
            markers.removeLayer(item2.marker)
            item.marker = setupPortalMarker(item)
            mapData.portals[item['external_id']] = item
        }
    }
}
function updatePortals() {
    if (!Store.get('showPortals')) {
        return false
    }

    var removePortals = []
    var ts = Math.round(new Date().getTime() / 1000)
    var diffTime = ts - markPortalsAsNew
    if (Store.get('showNewPortalsOnly') === 1) {
        $.each(mapData.portals, function (key, value) {
            if (value['imported'] < diffTime) {
                removePortals.push(key)
            }
        })
        $.each(removePortals, function (key, value) {
            if (mapData.portals[value] && mapData.portals[value].marker) {
                markers.removeLayer(mapData.portals[value].marker)
                markersnotify.removeLayer(mapData.portals[value].marker)
                delete mapData.portals[value]
            }
        })
    }
}
function processPois(i, item) {
    if (!Store.get('showPoi')) {
        return false
    }
    if (!mapData.pois[item['poi_id']]) {
        if (item.marker && item.marker.rangeCircle) {
            markers.removeLayer(item.marker.rangeCircle)
        }
        if (item.marker) {
            markers.removeLayer(item.marker)
        }
        item.marker = setupPoiMarker(item)
        mapData.pois[item['poi_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.pois[item['poi_id']]
        markers.removeLayer(item2.marker)
        item.marker = setupPoiMarker(item)
        mapData.pois[item['poi_id']] = item
    }
}
function processPokestops(i, item) {
    if (!Store.get('showPokestops')) {
        return false
    }

    if (Store.get('showLures') && !item['lure_expiration']) {
        return true
    }
    if (!mapData.pokestops[item['pokestop_id']]) {
        // new pokestop, add marker to map and item to dict
        if (item.marker && item.marker.rangeCircle) {
            markers.removeLayer(item.marker.rangeCircle)
        }
        if (item.marker) {
            markers.removeLayer(item.marker)
        }
        var latlng = turf.point([item['longitude'], item['latitude']])
        $.each(scanAreas, function (index, poly) {
            var insideScan = turf.booleanPointInPolygon(latlng, poly)
            if (insideScan) {
                item.scanArea = insideScan
                return false
            } else {
                item.scanArea = insideScan
            }
        })
        item.marker = setupPokestopMarker(item)
        mapData.pokestops[item['pokestop_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.pokestops[item['pokestop_id']]
        if (!!item['lure_expiration'] !== !!item2['lure_expiration']) {
            if (item2.marker && item2.marker.rangeCircle) {
                markers.removeLayer(item2.marker.rangeCircle)
            }
            markers.removeLayer(item2.marker)
            item.marker = setupPokestopMarker(item)
            mapData.pokestops[item['pokestop_id']] = item
        }
    }
}

function updatePokestops() {
    if (!Store.get('showPokestops')) {
        return false
    }
    var removeStops = []
    var currentTime = Math.round(new Date().getTime() / 1000)
    var d = new Date()
    var lastMidnight = d.setHours(0, 0, 0, 0) / 1000

    // change lured pokestop marker to unlured when expired
    $.each(mapData.pokestops, function (key, value) {
        if (value['lure_expiration'] !== '0' && value['lure_expiration'] < currentTime) {
            if (value.marker && value.marker.rangeCircle) {
                markers.removeLayer(value.marker.rangeCircle)
                markersnotify.removeLayer(value.marker.rangeCircle)
            }
            markers.removeLayer(value.marker)
            markersnotify.removeLayer(value.marker)
            value.marker = setupPokestopMarker(value)
        }
    })
    // remove unlured stops if show lured only is selected
    if (Store.get('showLures')) {
        $.each(mapData.pokestops, function (key, value) {
            if (value['lure_expiration'] < currentTime) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                    markersnotify.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
                markersnotify.removeLayer(mapData.pokestops[value].marker)
                delete mapData.pokestops[value]
            }
        })
    }
    if (Store.get('showQuests')) {
        $.each(mapData.pokestops, function (key, value) {
            if (value['quest_type'] === 0 || lastMidnight > Number(value['quest_timestamp']) || ((value['quest_pokemon_id'] > 0 && questsExcludedPokemon.indexOf(value['quest_pokemon_id']) > -1) || (value['quest_item_id'] > 0 && questsExcludedItem.indexOf(value['quest_item_id']) > -1) || ((value['quest_reward_type'] === 3 && (Number(value['quest_dust_amount']) < Number(Store.get('showDustAmount')))) || (value['quest_reward_type'] === 3 && Store.get('showDustAmount') === 0)))) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                    markersnotify.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
                markersnotify.removeLayer(mapData.pokestops[value].marker)
                delete mapData.pokestops[value]
            }
        })
    }
}

function processGyms(i, item) {
    if (!Store.get('showGyms') && !Store.get('showRaids')) {
        return false // in case the checkbox was unchecked in the meantime.
    }
    var latlng = turf.point([item['longitude'], item['latitude']])
    $.each(scanAreas, function (index, poly) {
        var insideScan = turf.booleanPointInPolygon(latlng, poly)
        if (insideScan) {
            item.scanArea = insideScan
            return false
        } else {
            item.scanArea = insideScan
        }
    })
    var gymLevel = item.slots_available
    var raidLevel = item.raid_level
    var removeGymFromMap = function removeGymFromMap(gymid) {
        if (mapData.gyms[gymid] && mapData.gyms[gymid].marker) {
            if (mapData.gyms[gymid].marker.rangeCircle) {
                markers.removeLayer(mapData.gyms[gymid].marker.rangeCircle)
                markersnotify.removeLayer(mapData.gyms[gymid].marker.rangeCircle)
            }
            markers.removeLayer(mapData.gyms[gymid].marker)
            markersnotify.removeLayer(mapData.gyms[gymid].marker)
            delete mapData.gyms[gymid]
        }
    }

    if (!Store.get('showGyms') && Store.get('showRaids')) {
        if (item.raid_end === undefined) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (!Store.get('showGyms') && Store.get('showRaids')) {
        if (item.raid_end < Date.now()) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (Store.get('showGyms') && !Store.get('showRaids')) {
        item.raid_end = 0
        item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
    }

    if (Store.get('activeRaids') && item.raid_end > Date.now()) {
        if ((item.raid_pokemon_id === undefined) || (item.raid_pokemon_id === null)) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (raidLevel < Store.get('minRaidLevel') && item.raid_end > Date.now()) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (raidLevel > Store.get('maxRaidLevel') && item.raid_end > Date.now()) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (Store.get('exEligible') && (item.park === null || item.park === 0) && (item.sponsor === 0 || item.sponsor === undefined)) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (Store.get('showOpenGymsOnly')) {
        if (item.slots_available === 0 && (item.raid_end === undefined || item.raid_end < Date.now())) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (Store.get('showTeamGymsOnly') && Store.get('showTeamGymsOnly') !== item.team_id && (item.raid_end === undefined || item.raid_end < Date.now())) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (Store.get('showLastUpdatedGymsOnly')) {
        var now = new Date()
        if (item.last_scanned == null) {
            if (Store.get('showLastUpdatedGymsOnly') * 3600 * 1000 + item.last_modified < now.getTime() && (item.raid_end === undefined || item.raid_end < Date.now())) {
                removeGymFromMap(item['gym_id'])
                return true
            }
        } else {
            if (Store.get('showLastUpdatedGymsOnly') * 3600 * 1000 + item.last_scanned < now.getTime() && (item.raid_end === undefined || item.raid_end < Date.now())) {
                removeGymFromMap(item['gym_id'])
                return true
            }
        }
    }

    if (gymLevel < Store.get('minGymLevel') && (item.raid_end === undefined || item.raid_end < Date.now())) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (gymLevel > Store.get('maxGymLevel') && (item.raid_end === undefined || item.raid_end < Date.now())) {
        removeGymFromMap(item['gym_id'])
        return true
    }
    if (item['gym_id'] in mapData.gyms) {
        item.marker = updateGymMarker(item, mapData.gyms[item['gym_id']].marker)
    } else {
        // add marker to map and item to dict
        item.marker = setupGymMarker(item)
    }
    if (item.raid_start !== undefined && item.raid_start > Date.now()) {
        var delayStart = item.raid_start - Date.now()
        setTimeOut(item['gym_id'], item, delayStart)
    } else if (item.raid_end !== undefined && item.raid_end > Date.now()) {
        var delayEnd = item.raid_end - Date.now()
        setTimeOut(item['gym_id'], item, delayEnd)
    }
    mapData.gyms[item['gym_id']] = item
}

var timeoutHandles = []

function setTimeOut(id, item, time) {
    if (id in timeoutHandles) {
        clearTimeout(timeoutHandles[id])
    }
    timeoutHandles[id] = setTimeout(function () {
        processGyms(null, item)
    }, time + 1000)
}

function processScanned(i, item) {
    if (!Store.get('showScanned')) {
        return false
    }

    var scanId = item['latitude'] + '|' + item['longitude']

    if (!(scanId in mapData.scanned)) {
        // add marker to map and item to dict
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupScannedMarker(item)
        mapData.scanned[scanId] = item
    } else {
        mapData.scanned[scanId].last_modified = item['last_modified']
    }
}

function updateScanned() {
    if (!Store.get('showScanned')) {
        return false
    }

    $.each(mapData.scanned, function (key, value) {
        if (map.getBounds().intersects(value.marker.getBounds())) {
            value.marker.setOptions({
                fillColor: getColorByDate(value['last_modified'])
            })
        }
    })
}

function processSpawnpoints(i, item) {
    if (!Store.get('showSpawnpoints')) {
        return false
    }

    var id = item['spawnpoint_id']

    if (!(id in mapData.spawnpoints)) {
        // add marker to map and item to dict
        if (item.marker) {
            markersnotify.removeLayer(item.marker)
        }
        item.marker = setupSpawnpointMarker(item)
        mapData.spawnpoints[id] = item
    }
}

function updateSpawnPoints() {
    if (!Store.get('showSpawnpoints')) {
        return false
    }

    $.each(mapData.spawnpoints, function (key, value) {
        if (map.getBounds().contains(value.marker.getLatLng())) {
            var hue = getColorBySpawnTime(value['time'])
            value.marker.setStyle({color: hue, fillColor: hue})
        }
    })
}

function updateMap() {
    var position = map.getCenter()
    Store.set('startAtLastLocationPosition', {
        lat: position.lat,
        lng: position.lng
    })
    // lets try and get the s2 cell id in the middle
    if (mapType !== 'rdm') {
        var s2CellCenter = S2.keyToId(S2.latLngToKey(position.lat, position.lng, 10))
        if ((s2CellCenter) && (String(s2CellCenter) !== $('#currentWeather').data('current-cell')) && (map.getZoom() > 13)) {
            loadWeatherCellData(s2CellCenter).done(function (cellWeather) {
                var currentWeather = cellWeather.weather
                var currentCell = $('#currentWeather').data('current-cell')
                if ((currentWeather) && (currentCell !== currentWeather.s2_cell_id)) {
                    $('#currentWeather').data('current-cell', currentWeather.s2_cell_id)
                    $('#currentWeather').html('<img src="static/weather/' + currentWeather.condition + '.png" alt="" height="55px"">')
                } else if (!currentWeather) {
                    $('#currentWeather').data('current-cell', '')
                    $('#currentWeather').html('')
                }
            })
        }
    }

    loadRawData().done(function (result) {
        $.each(result.pokemons, processPokemons)
        $.each(result.pokestops, processPokestops)
        $.each(result.gyms, processGyms)
        $.each(result.scanned, processScanned)
        $.each(result.spawnpoints, processSpawnpoints)
        $.each(result.nests, processNests)
        $.each(result.communities, processCommunities)
        $.each(result.portals, processPortals)
        $.each(result.pois, processPois)
        showInBoundsMarkers(mapData.pokemons, 'pokemon')
        showInBoundsMarkers(mapData.lurePokemons, 'pokemon')
        showInBoundsMarkers(mapData.gyms, 'gym')
        showInBoundsMarkers(mapData.pokestops, 'pokestop')
        showInBoundsMarkers(mapData.scanned, 'scanned')
        showInBoundsMarkers(mapData.spawnpoints, 'inbound')
        // drawScanPath(result.scanned)

        clearStaleMarkers()

        updateScanned()
        updateSpawnPoints()
        updatePokestops()
        updatePortals()

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
        lastnests = result.lastnests
        lastcommunities = result.lastcommunities
        lastportals = result.lastportals
        lastpois = result.lastpois

        prevMinIV = result.preMinIV
        prevMinLevel = result.preMinLevel
        reids = result.reids
        qpreids = result.qpreids
        qireids = result.qireids
        if (reids instanceof Array) {
            reincludedPokemon = reids.filter(function (e) {
                return this.indexOf(e) < 0
            }, reincludedPokemon)
        }
        if (qpreids instanceof Array) {
            reincludedQuestsPokemon = qpreids.filter(function (e) {
                return this.indexOf(e) < 0
            }, reincludedQuestsPokemon)
        }
        if (qireids instanceof Array) {
            reincludedQuestsItem = qireids.filter(function (e) {
                return this.indexOf(e) < 0
            }, reincludedQuestsItem)
        }
        reloaddustamount = false
        timestamp = result.timestamp
        lastUpdateTime = Date.now()
        token = result.token
    })
}

function updateWeatherOverlay() {
    if (Store.get('showWeather')) {
        loadWeather().done(function (result) {
            if (weatherPolys.length === 0) {
                drawWeatherOverlay(result.weather)
            } else {
                // update layers
                destroyWeatherOverlay()
                drawWeatherOverlay(result.weather)
            }
            lastWeatherUpdateTime = Date.now()
        })
    }
}

function updateS2Overlay() {
    if ((Store.get('showCells'))) {
        if (Store.get('showExCells') && (map.getZoom() > 12)) {
            exLayerGroup.clearLayers()
            showS2Cells(13, {color: 'red', weight: 6, dashOffset: '8'})
        } else if (Store.get('showExCells') && (map.getZoom() <= 12)) {
            exLayerGroup.clearLayers()
            toastr['error'](i8ln('This is to much zoom.'), i8ln('EX cells are currently hidden'))
        }
        if (Store.get('showGymCells') && (map.getZoom() > 13)) {
            gymLayerGroup.clearLayers()
            showS2Cells(14, {color: 'green', weight: 4, dashOffset: '4'})
        } else if (Store.get('showGymCells') && (map.getZoom() <= 13)) {
            gymLayerGroup.clearLayers()
            toastr['error'](i8ln('This is to much zoom.'), i8ln('Gym cells are currently hidden'))
        }
        if (Store.get('showStopCells') && (map.getZoom() > 16)) {
            stopLayerGroup.clearLayers()
            showS2Cells(17, {color: 'blue'})
        } else if (Store.get('showStopCells') && (map.getZoom() <= 16)) {
            stopLayerGroup.clearLayers()
            toastr['error'](i8ln('This is to much zoom.'), i8ln('Pokestop cells are currently hidden'))
        }
    }
}

function drawWeatherOverlay(weather) {
    if (weather) {
        $.each(weather, function (idx, item) {
            weatherArray.push(S2.idToCornerLatLngs(item.s2_cell_id))
            var poly = L.polygon(weatherArray, {
                color: weatherColors[item.condition],
                opacity: 1,
                weight: 1,
                fillOpacity: 0
            })
            var bounds = new L.LatLngBounds()
            var i, center

            for (i = 0; i < weatherArray[0].length; i++) {
                bounds.extend(weatherArray[0][i])
            }
            center = bounds.getCenter()
            var icon = L.icon({
                iconSize: [30, 30],
                iconAnchor: [15, 15],
                iconUrl: 'static/weather/i-' + item.condition + '.png'
            })
            var marker = L.marker([center.lat, center.lng], {icon})
            weatherPolys.push(poly)
            weatherMarkers.push(marker)
            weatherLayerGroup.addLayer(poly)
            weatherArray = []
        })
    }
}

function destroyWeatherOverlay() {
    weatherLayerGroup.clearLayers()
    $.each(weatherMarkers, function (idx, marker) {
        markers.removeLayer(marker)
    })
    weatherPolys = []
    weatherMarkers = []
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
            if (item.marker.rangeCircle) markers.removeLayer(item.marker.rangeCircle)
            var newMarker = setupPokemonMarker(item, map, this.marker.animationDisabled)
            customizePokemonMarker(newMarker, item, skipNotification)
            markers.removeLayer(item.marker)
            markersnotify.removeLayer(item.marker)
            pokemonList[key].marker = newMarker
        }
    })
}

var updateLabelDiffTime = function updateLabelDiffTime() {
    $('.label-countdown').each(function (index, element) {
        var disappearsAt = getTimeUntil(parseInt(element.getAttribute('disappears-at')))

        var hours = disappearsAt.hour
        var minutes = disappearsAt.min
        var seconds = disappearsAt.sec
        var timestring = ''

        if (disappearsAt.time < disappearsAt.now) {
            if (element.hasAttribute('start')) {
                timestring = '(' + i8ln('started') + ')'
            } else if (element.hasAttribute('end')) {
                timestring = '(' + i8ln('ended') + ')'
            } else {
                timestring = '(' + i8ln('expired') + ')'
            }
        } else {
            timestring = '('
            if (hours > 0) {
                timestring += hours + 'h'
            }

            timestring += lpad(minutes, 2, 0) + 'm'
            timestring += lpad(seconds, 2, 0) + 's'
            timestring += ')'
        }

        $(element).text(timestring)
    })
}

function sendNotification(title, text, icon, lat, lon) {
    if (Store.get('remember_notification_notify')) {
        var notificationDetails = {
            icon: icon,
            body: text,
            data: {
                lat: lat,
                lon: lon
            }
        }

        if (Push._agents.desktop.isSupported()) {
            /* This will only run in browsers which support the old
             * Notifications API. Browsers supporting the newer Push API
             * are handled by serviceWorker.js. */
            notificationDetails.onClick = function (event) {
                if (Push._agents.desktop.isSupported()) {
                    window.focus()
                    event.currentTarget.close()
                    map.setView(new L.LatLng(lat, lon), 20)
                }
            }
        }

        /* Push.js requests the Notification permission automatically if
         * necessary. */
        Push.create(title, notificationDetails).catch(function () {
            sendToastrPokemonNotification(title, text, icon, lat, lon)
        })
    }
}

function sendToastrPokemonNotification(title, text, icon, lat, lon) {
    var notification = toastr.info(text, title, {
        closeButton: true,
        positionClass: 'toast-top-right',
        preventDuplicates: true,
        onclick: function () {
            centerMap(lat, lon, 20)
        },
        showDuration: '300',
        hideDuration: '500',
        timeOut: '6000',
        extendedTimeOut: '1500',
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    })
    notification.removeClass('toast-info')
    notification.css({
        'padding-left': '74px',
        'background-image': `url('${icon}')`,
        'background-size': '48px',
        'background-color': '#0c5952'
    })
}

//
// Page Ready Execution
//

$(function () {
    /* If push.js is unsupported or disabled, fall back to toastr
     * notifications. */
    Push.config({
        serviceWorker: 'serviceWorker.min.js',
        fallback: function (notification) {
            sendToastrPokemonNotification(
                notification.title,
                notification.body,
                notification.icon,
                notification.data.lat,
                notification.data.lon
            )
        }
    })
})

function createMyLocationButton() {
    var _locationMarker = L.control({position: 'bottomright'})
    var locationContainer

    _locationMarker.onAdd = function (map) {
        locationContainer = L.DomUtil.create('div', '_locationMarker')

        var locationButton = document.createElement('button')
        locationButton.style.backgroundColor = '#fff'
        locationButton.style.border = 'none'
        locationButton.style.outline = 'none'
        locationButton.style.width = '28px'
        locationButton.style.height = '28px'
        locationButton.style.borderRadius = '15px'
        locationButton.style.boxShadow = '0 1px 4px rgba(0,0,0,0.3)'
        locationButton.style.cursor = 'pointer'
        locationButton.style.marginRight = '3px'
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

        return locationContainer
    }

    _locationMarker.addTo(map)
    locationContainer.index = 1

    map.on('dragend', function () {
        var currentLocation = document.getElementById('current-location')
        currentLocation.style.backgroundPosition = '0px 0px'
    })
}

function centerMapOnLocation() {
    var currentLocation = document.getElementById('current-location')
    if (currentLocation !== null) {
        var imgX = '0'
        var animationInterval = setInterval(function () {
            if (imgX === '-18') {
                imgX = '0'
            } else {
                imgX = '-18'
            }
            currentLocation.style.backgroundPosition = imgX + 'px 0'
        }, 500)
    }
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var latlng = new L.LatLng(position.coords.latitude, position.coords.longitude)
            locationMarker.setLatLng(latlng)
            map.setView(latlng)
            Store.set('followMyLocationPosition', {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            })
            clearInterval(animationInterval)
            if (currentLocation !== null) {
                currentLocation.style.backgroundPosition = '-144px 0px'
            }
        })
    } else {
        clearInterval(animationInterval)
        if (currentLocation !== null) {
            currentLocation.style.backgroundPosition = '0px 0px'
        }
    }
}

function centerMap(lat, lng, zoom) {
    var loc = new L.LatLng(lat, lng)

    map.setView(loc)

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
            success: function success(data) {
                i8lnDictionary = data
            },
            error: function error(jqXHR, status, _error) {
                console.log('Error loading i8ln dictionary: ' + _error)
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
    if (navigator.geolocation && Store.get('followMyLocation')) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var lat = position.coords.latitude
            var lng = position.coords.longitude
            var center = new L.LatLng(lat, lng)

            if (Store.get('followMyLocation')) {
                if (typeof locationMarker !== 'undefined') {
                    map.setView(center)
                    locationMarker.setLatLng(center)
                    if (Store.get('spawnArea')) {
                        if (locationMarker.rangeCircle) {
                            markers.removeLayer(locationMarker.rangeCircle)
                            markersnotify.removeLayer(locationMarker.rangeCircle)
                            delete locationMarker.rangeCircle
                        }
                        var rangeCircleOpts = {
                            color: '#FF9200',
                            radius: 35, // meters
                            center: center,
                            fillColor: '#FF9200',
                            fillOpacity: 0.4,
                            weight: 1
                        }
                        locationMarker.rangeCircle = L.circle(center, rangeCircleOpts)
                        markers.addLayer(locationMarker.rangeCircle)
                    }
                    Store.set('followMyLocationPosition', {
                        lat: lat,
                        lng: lng
                    })
                }
            }
        })
    }
}
function createUpdateWorker() {
    try {
        if (isMobileDevice() && window.Worker) {
            var updateBlob = new Blob(["onmessage = function(e) {\n                var data = e.data\n                if (data.name === 'backgroundUpdate') {\n                    self.setInterval(function () {self.postMessage({name: 'backgroundUpdate'})}, 5000)\n                }\n            }"])

            var updateBlobURL = window.URL.createObjectURL(updateBlob)

            updateWorker = new Worker(updateBlobURL)

            updateWorker.onmessage = function (e) {
                var data = e.data
                if (document.hidden && data.name === 'backgroundUpdate' && Date.now() - lastUpdateTime > 2500) {
                    updateMap()
                    updateGeoLocation()
                }
                if (document.hidden && data.name === 'backgroundUpdate' && Date.now() - lastWeatherUpdateTime > 60000) {
                    updateWeatherOverlay()
                }
            }

            updateWorker.postMessage({
                name: 'backgroundUpdate'
            })
        }
    } catch (ex) {
        console.log('Webworker error: ' + ex.message)
    }
}

function showGymDetails(id) { // eslint-disable-line no-unused-vars
    var sidebar = document.querySelector('#gym-details')
    var sidebarClose

    sidebar.classList.add('visible')

    var data = $.ajax({
        url: 'gym_data',
        type: 'POST',
        timeout: 300000,
        data: {
            'id': id,
            'token': token
        },
        dataType: 'json',
        cache: false
    })

    data.done(function (result) {
        var lastModifiedStr = getDateStr(result.last_modified) + ' ' + getTimeStr(result.last_modified)
        var lastScannedStr = ''
        if (result.last_scanned != null) {
            lastScannedStr =
                '<div style="font-size: .7em">' +
                i8ln('Last Scanned') + ' : ' + getDateStr(result.last_scanned) + ' ' + getTimeStr(result.last_scanned) +
                '</div>'
        }

        var pokemon = result.pokemon !== undefined ? result.pokemon : []
        var freeSlots = result.slots_available
        var gymLevelStr = ''
        if (result.team_id !== 0) {
            gymLevelStr =
                '<center class="team-' + result.team_id + '-text">' +
                '<b class="team-' + result.team_id + '-text">' + freeSlots + ' ' + i8ln('Free Slots') + '</b>' +
                '</center>'
        }

        var park = ''
        if (((result['park'] !== '0' && result['park'] !== 'None' && result['park'] !== undefined && result['park']) && (noParkInfo === false))) {
            if (result['park'] === 1) {
                // RM only stores boolean, so just call it "Park Gym"
                park = i8ln('Park Gym')
            } else {
                park = i8ln('Park') + ': ' + result['park']
            }
        }

        var raidSpawned = result['raid_level'] != null
        var raidStarted = result['raid_pokemon_id'] != null
        var form = result['form']

        var raidStr = ''
        var raidIcon = ''
        if (manualRaids) {
            var rbList = generateRaidBossList()
        }
        if (raidSpawned && result.raid_end > Date.now()) {
            var levelStr = ''
            for (var i = 0; i < result['raid_level']; i++) {
                levelStr += '★'
            }
            raidStr = '<h3 style="margin-bottom: 0">Raid ' + levelStr
            if (raidStarted) {
                var cpStr = ''
                if (result.raid_pokemon_cp > 0) {
                    cpStr = ' CP ' + result.raid_pokemon_cp
                }
                raidStr += '<br>' + result.raid_pokemon_name
                if (form !== null && form > 0 && forms.length > form) {
                    // todo: check how rocket map handles this (if at all):
                    if (result['raid_pokemon_id'] === 132) {
                        raidStr += ' (' + idToPokemon[result['form']].name + ')'
                    } else {
                        raidStr += ' (' + forms[result['form']] + ')'
                    }
                }
                raidStr += cpStr
            }
            raidStr += '</h3>'
            if (raidStarted && result.raid_pokemon_move_1 > 0 && result.raid_pokemon_move_1 !== '133' && result.raid_pokemon_move_2 > 0 && result.raid_pokemon_move_2 !== '133') {
                var pMove1 = (moves[result['raid_pokemon_move_1']] !== undefined) ? i8ln(moves[result['raid_pokemon_move_1']]['name']) : 'gen/unknown'
                var pMove2 = (moves[result['raid_pokemon_move_2']] !== undefined) ? i8ln(moves[result['raid_pokemon_move_2']]['name']) : 'gen/unknown'
                raidStr += '<div><b>' + pMove1 + ' / ' + pMove2 + '</b></div>'
            }

            var raidStartStr = getTimeStr(result['raid_start'])
            var raidEndStr = getTimeStr(result['raid_end'])
            raidStr += '<div>' + i8ln('Start') + ': <b>' + raidStartStr + '</b> <span class="label-countdown" disappears-at="' + result['raid_start'] + '" start>(00m00s)</span></div>'
            raidStr += '<div>' + i8ln('End') + ': <b>' + raidEndStr + '</b> <span class="label-countdown" disappears-at="' + result['raid_end'] + '" end>(00m00s)</span></div>'

            if (raidStarted) {
                var raidForm = result['form']
                var formStr = ''
                if (raidForm <= 10 || raidForm == null || raidForm === '0') {
                    formStr = '00'
                } else {
                    formStr = raidForm
                }
                var pokemonid = result['raid_pokemon_id']
                var pokemonidStr = ''
                if (pokemonid <= 9) {
                    pokemonidStr = '00' + pokemonid
                } else if (pokemonid <= 99) {
                    pokemonidStr = '0' + pokemonid
                } else {
                    pokemonidStr = pokemonid
                }
                raidIcon = '<img style="width: 80px; -webkit-filter: drop-shadow(5px 5px 5px #222); filter: drop-shadow(5px 5px 5px #222);" src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png"/>'
            } else if (result.raid_start <= Date.now()) {
                var hatchedEgg = ''
                if (result['raid_level'] <= 2) {
                    hatchedEgg = 'hatched_normal'
                } else if (result['raid_level'] <= 4) {
                    hatchedEgg = 'hatched_rare'
                } else {
                    hatchedEgg = 'hatched_legendary'
                }
                raidIcon = '<img style="width: 80px; -webkit-filter: drop-shadow(5px 5px 5px #222); filter: drop-shadow(5px 5px 5px #222);" src="static/raids/egg_' + hatchedEgg + '.png">'
            } else {
                var raidEgg = ''
                if (result['raid_level'] <= 2) {
                    raidEgg = 'normal'
                } else if (result['raid_level'] <= 4) {
                    raidEgg = 'rare'
                } else {
                    raidEgg = 'legendary'
                }
                raidIcon = '<img src="static/raids/egg_' + raidEgg + '.png">'
            }
        }
        if (!noDeleteGyms) {
            raidStr += '<i class="fa fa-trash-o delete-gym" onclick="deleteGym(event);" data-id="' + id + '"></i>'
        }
        if (!noToggleExGyms) {
            raidStr += '<i class="fa fa-trophy toggle-ex-gym" onclick="toggleExGym(event);" data-id="' + id + '"></i>'
        }
        if (manualRaids) {
            raidStr += '<i class="fa fa-binoculars submit-raid" onclick="$(this).toggleClass(\'open\');$(\'.raid-report\').slideToggle()" ></i>'
            raidStr += '<div class="raid-report">'
            raidStr += '<div style="margin:0px 10px;"><form>'
            raidStr += '<input type="hidden" value="' + id + '" id="gymId" name="gymId">'
            raidStr += '<div class=" switch-container">' +
                rbList +
                '</div>' +
                '<div class="mon-name" style="display:none;"></div>' +
                '<div class="switch-container timer-cont" style="display:none;">' +
                '<h5 class="timer-name" style="margin-bottom:0;"></h5>' +
                generateTimerLists() +
                '</div>' +
                '<button type="button" onclick="manualRaidData(event);" class="submitting-raid"><i class="fa fa-binoculars" style="margin-right:10px;"></i> ' + i8ln('Submit Raid') + '</button>' +
                '</form>' +
                '</div>' +
                '</div>'
        }

        var pokemonHtml = ''
        var gymImage = ''
        if (result.url !== null) {
            gymImage = '<img height="140px" style="padding: 5px;" src="' + result.url + '">'
        }
        var headerHtml =
            '<center class="team-' + result.team_id + '-text">' +
            '<div>' +
            '<b class="team-' + result.team_id + '-text">' + (result.name || '') + '</b>' +
            '</div>' +
            '<div>' +
            gymImage +
            '</div>' +
            '<div>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/' + gymTypes[result.team_id] + '_large.png">' +
            raidIcon +
            '</div>' +
            raidStr +
            gymLevelStr +
            '<div>' +
            park +
            '</div>' +
            '<div style="font-size: .7em">' +
            i8ln('Last Modified') + ' : ' + lastModifiedStr +
            '</div>' +
            lastScannedStr +
            '<div>' +
            '<a href=\'javascript:void(0)\' onclick=\'javascript:openMapDirections(' + result.latitude + ',' + result.longitude + ')\' title=\'' + i8ln('View in Maps') + '\'>' + i8ln('Get directions') + '</a> - <a href="./?lat=' + result.latitude + '&lon=' + result.longitude + '&zoom=16">Share link</a>' +
            '</div>' +
            '</center>'

        if (pokemon.length) {
            $.each(pokemon, function (i, pokemon) {
                var perfectPercent = getIv(pokemon.iv_attack, pokemon.iv_defense, pokemon.iv_stamina)
                var moveEnergy = Math.round(100 / pokemon.move_2_energy)

                var pokemonIdStr = ''
                if (pokemon.pokemon_id <= 9) {
                    pokemonIdStr = '00' + pokemon.pokemon_id
                } else if (pokemon.pokemon_id <= 99) {
                    pokemonIdStr = '0' + pokemon.pokemon_id
                } else {
                    pokemonIdStr = pokemon.pokemon_id
                }
                var formStr = ''
                if (pokemon.form === '0' || pokemon.form === null || pokemon.form === 0 || pokemon.form === undefined) {
                    formStr = '00'
                } else {
                    formStr = pokemon.form
                }
                pokemonHtml +=
                    '<tr onclick=toggleGymPokemonDetails(this)>' +
                    '<td width="30px">' +
                    '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png"/>' +
                    '</td>' +
                    '<td class="team-' + result.team_id + '-text">' +
                    '<div style="line-height:1em">' + pokemon.pokemon_name + '</div>' +
                    '<div class="cp">CP ' + pokemon.pokemon_cp + '</div>' +
                    '</td>' +
                    '<td width="190" class="team-' + result.team_id + '-text" align="center">'
                if (pokemon.trainer_level) {
                    pokemonHtml +=
                        '<div class="trainer-level">' + pokemon.trainer_level + '</div>'
                }
                if (pokemon.trainer_name) {
                    pokemonHtml +=
                        '<div style="line-height: 1em">' + pokemon.trainer_name + '</div>'
                }
                pokemonHtml +=
                    '</td>' +
                    '<td width="10">' +
                    '<!--<a href="#" onclick="toggleGymPokemonDetails(this)">-->' +
                    '<i class="team-' + result.team_id + '-text fa fa-angle-double-down"></i>' +
                    '<!--</a>-->' +
                    '</td>' +
                    '</tr>' +
                    '<tr class="details">' +
                    '<td colspan="2">' +
                    '<div class="ivs">' +
                    '<div class="iv">' +
                    '<div class="type">ATK</div>' +
                    '<div class="value">' +
                    pokemon.iv_attack +
                    '</div>' +
                    '</div>' +
                    '<div class="iv">' +
                    '<div class="type">DEF</div>' +
                    '<div class="value">' +
                    pokemon.iv_defense +
                    '</div>' +
                    '</div>' +
                    '<div class="iv">' +
                    '<div class="type">STA</div>' +
                    '<div class="value">' +
                    pokemon.iv_stamina +
                    '</div>' +
                    '</div>' +
                    '<div class="iv" style="width: 36px">' +
                    '<div class="type">PERFECT</div>' +
                    '<div class="value">' +
                    perfectPercent.toFixed(0) + '' +
                    '<span style="font-size: .6em">%</span>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '<td colspan="2">' +
                    '<div class="moves">' +
                    '<div class="move">' +
                    '<div class="name">' +
                    pokemon.move_1_name +
                    ' <div class="type ' + pokemon.move_1_type.type_en.toLowerCase() + '">' + pokemon.move_1_type.type + '</div>' +
                    '</div>' +
                    '<div class="damage">' +
                    pokemon.move_1_damage +
                    '</div>' +
                    '</div>' +
                    '<br>' +
                    '<div class="move">' +
                    '<div class="name">' +
                    pokemon.move_2_name +
                    ' <div class="type ' + pokemon.move_2_type.type_en.toLowerCase() + '">' + pokemon.move_2_type.type + '</div>' +
                    '<div>' +
                    '<i class="move-bar-sprite move-bar-sprite-' + moveEnergy + '"></i>' +
                    '</div>' +
                    '</div>' +
                    '<div class="damage">' +
                    pokemon.move_2_damage +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</td>' +
                    '</tr>'
            })

            pokemonHtml = '<table><tbody>' + pokemonHtml + '</tbody></table>'
        } else if (result.team_id === 0) {
            pokemonHtml = ''
        } else {
            var pokemonIdStr = ''
            if (result.guard_pokemon_id <= 9) {
                pokemonIdStr = '00' + result.guard_pokemon_id
            } else if (result.guard_pokemon_id <= 99) {
                pokemonIdStr = '0' + result.guard_pokemon_id
            } else {
                pokemonIdStr = result.guard_pokemon_id
            }
            var guardFormStr = ''
            if (result.guard_pokemon_form === '0' || result.guard_pokemon_form === null || result.guard_pokemon_form === 0 || result.guard_pokemon_form === undefined) {
                guardFormStr = '00'
            } else {
                guardFormStr = result.guard_pokemon_form
            }
            pokemonHtml =
                '<center class="team-' + result.team_id + '-text">' +
                'Gym Leader:<br>' +
                '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + guardFormStr + '.png"/><br>' +
                '<b class="team-' + result.team_id + '-text">' + result.guard_pokemon_name + '</b>' +
                '</center>'
        }

        sidebar.innerHTML = headerHtml + pokemonHtml

        sidebarClose = document.createElement('a')
        sidebarClose.href = '#'
        sidebarClose.className = 'close'
        sidebarClose.tabIndex = 0
        sidebar.appendChild(sidebarClose)

        sidebarClose.addEventListener('click', function (event) {
            event.preventDefault()
            event.stopPropagation()
            sidebar.classList.remove('visible')
        })
        token = result.token
    })
}

function toggleGymPokemonDetails(e) { // eslint-disable-line no-unused-vars
    e.lastElementChild.firstElementChild.classList.toggle('fa-angle-double-up')
    e.lastElementChild.firstElementChild.classList.toggle('fa-angle-double-down')
    e.nextElementSibling.classList.toggle('visible')
}

function fetchCriesJson() {
    $.ajax({
        'global': false,
        'url': 'static/dist/data/cries.min.json',
        'dataType': 'json',
        'success': function (data) {
            cries = data
            createjs.Sound.alternateExtensions = ['mp3']
            createjs.Sound.registerSounds(cries, assetsPath)
        }
    })
}

function pokemonSubmitFilter(event) { // eslint-disable-line no-unused-vars
    var img = $(event.target).parent()
    var cont = img.parent().parent().parent()
    var select = cont.find('input.pokemonID')
    var id = img.data('value').toString()
    select.val(id)
    cont.find('.pokemon-icon-sprite').removeClass('active')
    img.addClass('active')
}

function pokemonRaidFilter(event) { // eslint-disable-line no-unused-vars
    var img = $(event.target).parent()
    var label = img.data('label')
    var cont = img.parent().parent()
    var select = cont.find('input')
    var id = img.data('value').toString()
    var par = cont.parent()
    par.find('.mon-name').text(label).show()
    par.find('.timer-cont').show()
    var text = i8ln('Time Remaining (mins)')
    if (id.includes('egg')) {
        text = i8ln('Time Until Hatch (mins)')
        par.find('.mon_time').hide()
        par.find('.egg_time').show()
    } else {
        par.find('.mon_time').show()
        par.find('.egg_time').hide()
    }
    par.find('.timer-name').text(i8ln(text))
    select.val(id)
    cont.find('.pokemon-icon-sprite').removeClass('active')
    img.addClass('active')
}
function generateRaidBossList() {
    var boss = raidBossActive
    var data = '<div class="pokemon-list raid-submission">'
    data += '<input type="hidden" name="pokemonId" value="">'
    data += '<span class="pokemon-icon-sprite" data-value="egg_1" data-label="Level 1" onclick="pokemonRaidFilter(event);"><span class="egg_1 inner-bg" style="background: url(\'static/raids/egg_normal.png\');background-size:100%"></span><span class="egg-number">1</span></span>'
    data += '<span class="pokemon-icon-sprite" data-value="egg_2" data-label="Level 2" onclick="pokemonRaidFilter(event);"><span class="egg_2 inner-bg" style="background: url(\'static/raids/egg_normal.png\');background-size:100%"></span><span class="egg-number">2</span></span>'
    data += '<span class="pokemon-icon-sprite" data-value="egg_3" data-label="Level 3" onclick="pokemonRaidFilter(event);"><span class="egg_3 inner-bg" style="background: url(\'static/raids/egg_rare.png\');background-size:100%"></span><span class="egg-number">3</span></span>'
    data += '<span class="pokemon-icon-sprite" data-value="egg_4" data-label="Level 4" onclick="pokemonRaidFilter(event);"><span class="egg_4 inner-bg" style="background: url(\'static/raids/egg_rare.png\');background-size:100%"></span><span class="egg-number">4</span></span>'
    data += '<span class="pokemon-icon-sprite" data-value="egg_5" data-label="Level 5" onclick="pokemonRaidFilter(event);"><span class="egg_5 inner-bg" style="background: url(\'static/raids/egg_legendary.png\');background-size:100%"></span><span class="egg-number">5</span></span>'
    boss.forEach(function (element) {
        var pokemonIdStr = ''
        if (element <= 9) {
            pokemonIdStr = '00' + element
        } else if (element <= 99) {
            pokemonIdStr = '0' + element
        } else {
            pokemonIdStr = element
        }
        data += '<span class="pokemon-icon-sprite" data-value="' + element + '" data-label="' + pokeList[element - 1].name + '" onclick="pokemonRaidFilter(event);"><img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png" style="width:48px;height:48px;"/></span>'
    })
    data += '</div>'
    return data
}


function pokemonSpritesFilter() {
    jQuery('.pokemon-list').parent().find('.select2').hide()
    loadDefaultImages()
    jQuery('#nav .pokemon-list .pokemon-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.select2-hidden-accessible')
        var value = select.val().split(',')
        var id = img.data('value').toString()
        if (img.hasClass('active')) {
            select.val(value.filter(function (elem) {
                return elem !== id
            }).join(',')).trigger('change')
            img.removeClass('active')
        } else {
            select.val((value.concat(id).join(','))).trigger('change')
            img.addClass('active')
        }
    })
}

function itemSpritesFilter() {
    jQuery('.item-list').parent().find('.select2').hide()
    loadDefaultImages()
    jQuery('#nav .item-list .item-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.select2-hidden-accessible')
        var value = select.val().split(',')
        var id = img.data('value').toString()
        if (img.hasClass('active')) {
            select.val(value.filter(function (elem) {
                return elem !== id
            }).join(',')).trigger('change')
            img.removeClass('active')
        } else {
            select.val((value.concat(id).join(','))).trigger('change')
            img.addClass('active')
        }
    })
}

function loadDefaultImages() {
    var ep = Store.get('remember_select_exclude')
    var eminiv = Store.get('remember_select_exclude_min_iv')
    var en = Store.get('remember_select_notify')
    var eqp = Store.get('remember_quests_exclude_pokemon')
    var eqi = Store.get('remember_quests_exclude_item')
    $('label[for="exclude-pokemon"] .pokemon-icon-sprite').each(function () {
        if (ep.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('label[for="exclude-min-iv"] .pokemon-icon-sprite').each(function () {
        if (eminiv.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('label[for="notify-pokemon"] .pokemon-icon-sprite').each(function () {
        if (en.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('label[for="exclude-quests-pokemon"] .pokemon-icon-sprite').each(function () {
        if (eqp.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('label[for="exclude-quests-item"] .item-icon-sprite').each(function () {
        if (eqi.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
}

//
// Page Ready Exection
//

$(function () {
    if (Push.Permission.has()) {
        console.log('Push has notification permission')
        return
    }

    Push.Permission.request()
})

$(function () {
    if (Store.get('playCries')) {
        fetchCriesJson()
    }
    // load MOTD, if set
    if (motd) {
        $.ajax({
            url: 'motd_data',
            type: 'POST',
            dataType: 'json',
            cache: false,
            success: function (data) {
                // set content of motd banner
                $('#motd').attr('title', data.title).html(data.content).dialog()
            },
            fail: function () {
                return false
            }
        })
    }
})

$(function () {
    // populate Navbar Style menu
    $selectStyle = $('#map-style')
    $selectDirectionProvider = $('#direction-provider')

    // Load Stylenames, translate entries, and populate lists
    $.getJSON('static/dist/data/mapstyle.min.json').done(function (data) {
        var styleList = []

        $.each(data, function (key, value) {
            var googleMaps
            if (gmapsKey === '') {
                googleMaps = false
            } else {
                googleMaps = true
            }
            var googleStyle = value.includes('Google')
            if (!googleMaps && !googleStyle) {
                styleList.push({
                    id: key,
                    text: i8ln(value)
                })
            } else if (googleMaps) {
                styleList.push({
                    id: key,
                    text: i8ln(value)
                })
            }
        })

        // setup the stylelist
        $selectStyle.select2({
            placeholder: 'Select Style',
            data: styleList,
            minimumResultsForSearch: Infinity
        })
        $selectStyle.on('change', function (e) {
            selectedStyle = $selectStyle.val()
            setTileLayer(selectedStyle)
            Store.set('map_style', selectedStyle)
        })

        // recall saved mapstyle
        $selectStyle.val(Store.get('map_style')).trigger('change')
    })
    $selectDirectionProvider.select2({
        placeholder: 'Select Provider',
        minimumResultsForSearch: Infinity
    })

    $selectDirectionProvider.on('change', function () {
        directionProvider = $selectDirectionProvider.val()
        Store.set('directionProvider', directionProvider)
    })

    $selectDirectionProvider.val(Store.get('directionProvider')).trigger('change')

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

    $selectIconNotifySizeModifier = $('#pokemon-icon-notify-size')

    $selectIconNotifySizeModifier.select2({
        placeholder: 'Increase Size Of Notified',
        minimumResultsForSearch: Infinity
    })

    $selectIconNotifySizeModifier.on('change', function () {
        Store.set('iconNotifySizeModifier', this.value)
        redrawPokemon(mapData.pokemons)
        redrawPokemon(mapData.lurePokemons)
    })

    $switchOpenGymsOnly = $('#open-gyms-only-switch')

    $switchOpenGymsOnly.on('change', function () {
        Store.set('showOpenGymsOnly', this.checked)
        lastgyms = false
        updateMap()
    })

    $selectTeamGymsOnly = $('#team-gyms-only-switch')

    $selectTeamGymsOnly.select2({
        placeholder: 'Only Show Gyms For Team',
        minimumResultsForSearch: Infinity
    })

    $selectTeamGymsOnly.on('change', function () {
        Store.set('showTeamGymsOnly', this.value)
        lastgyms = false
        updateMap()
    })

    $selectLastUpdateGymsOnly = $('#last-update-gyms-switch')

    $selectLastUpdateGymsOnly.select2({
        placeholder: 'Only Show Gyms Last Updated',
        minimumResultsForSearch: Infinity
    })

    $selectLastUpdateGymsOnly.on('change', function () {
        Store.set('showLastUpdatedGymsOnly', this.value)
        lastgyms = false
        updateMap()
    })

    $selectMinGymLevel = $('#min-level-gyms-filter-switch')

    $selectMinGymLevel.select2({
        placeholder: 'Minimum Gym Level',
        minimumResultsForSearch: Infinity
    })

    $selectMinGymLevel.on('change', function () {
        Store.set('minGymLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $selectMaxGymLevel = $('#max-level-gyms-filter-switch')

    $selectMaxGymLevel.select2({
        placeholder: 'Maximum Gym Level',
        minimumResultsForSearch: Infinity
    })

    $selectMaxGymLevel.on('change', function () {
        Store.set('maxGymLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $switchActiveRaids = $('#active-raids-switch')

    $switchActiveRaids.on('change', function () {
        Store.set('activeRaids', this.checked)
        lastgyms = false
        updateMap()
    })

    $selectMinRaidLevel = $('#min-level-raids-filter-switch')

    $selectMinRaidLevel.select2({
        placeholder: 'Minimum Raid Level',
        minimumResultsForSearch: Infinity
    })

    $selectMinRaidLevel.on('change', function () {
        Store.set('minRaidLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $selectMaxRaidLevel = $('#max-level-raids-filter-switch')

    $selectMaxRaidLevel.select2({
        placeholder: 'Maximum Raid Level',
        minimumResultsForSearch: Infinity
    })

    $selectMaxRaidLevel.on('change', function () {
        Store.set('maxRaidLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $selectNewPortalsOnly = $('#new-portals-only-switch')

    $selectNewPortalsOnly.select2({
        placeholder: 'Only Show New Portals',
        minimumResultsForSearch: Infinity
    })

    $selectNewPortalsOnly.on('change', function () {
        Store.set('showNewPortalsOnly', this.value)
        lastportals = false
        updateMap()
    })

    $switchGymSidebar = $('#gym-sidebar-switch')

    $switchGymSidebar.on('change', function () {
        Store.set('useGymSidebar', this.checked)
        lastgyms = false
        $.each(['gyms'], function (d, dType) {
            $.each(mapData[dType], function (key, value) {
                // for any marker you're turning off, you'll want to wipe off the range
                if (mapData[dType][key].marker.rangeCircle) {
                    markers.removeLayer(mapData[dType][key].marker.rangeCircle)
                    delete mapData[dType][key].marker.rangeCircle
                }
                markers.removeLayer(mapData[dType][key].marker)
            })
            mapData[dType] = {}
        })
        updateMap()
    })
    $switchExEligible = $('#ex-eligible-switch')

    $switchExEligible.on('change', function () {
        Store.set('exEligible', this.checked)
        lastgyms = false
        $.each(['gyms'], function (d, dType) {
            $.each(mapData[dType], function (key, value) {
                // for any marker you're turning off, you'll want to wipe off the range
                if (mapData[dType][key].marker.rangeCircle) {
                    markers.removeLayer(mapData[dType][key].marker.rangeCircle)
                    delete mapData[dType][key].marker.rangeCircle
                }
                markers.removeLayer(mapData[dType][key].marker)
            })
            mapData[dType] = {}
        })
        updateMap()
    })

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

        locationMarker = createLocationMarker()

        if (Store.get('startAtUserLocation') && !locationSet) {
            centerMapOnLocation()
        }

        if (Store.get('startAtLastLocation') && !locationSet) {
            var position = Store.get('startAtLastLocationPosition')
            var lat = 'lat' in position ? position.lat : centerLat
            var lng = 'lng' in position ? position.lng : centerLng

            var latlng = new L.LatLng(lat, lng)
            locationMarker.setLatLng(latlng)
            map.setView(latlng)
        }

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

    $selectGymMarkerStyle = $('#gym-marker-style')

    $selectGymMarkerStyle.select2({
        placeholder: 'Select Style',
        minimumResultsForSearch: Infinity
    })

    $selectGymMarkerStyle.on('change', function (e) {
        Store.set('gymMarkerStyle', this.value)
        updateGymIcons()
    })

    $selectGymMarkerStyle.val(Store.get('gymMarkerStyle')).trigger('change')
    pokemonSpritesFilter()
    itemSpritesFilter()
})

$(function () {
    function formatState(state) {
        if (!state.id) {
            return state.text
        }
        var $state = $('<span><i class="pokemon-raid-sprite n' + state.element.value.toString() + '" style="display: inline-block;position: relative;top: 6px; right: 0px;"></i> ' + state.text + '</span>')
        return $state
    }

    $.getJSON('static/dist/data/moves.min.json').done(function (data) {
        moves = data
    })

    $.getJSON('static/dist/data/weather.min.json').done(function (data) {
        weather = data.weather
        boostedMons = data.boosted_mons
    })

    $.getJSON('static/dist/data/questtype.min.json').done(function (data) {
        $.each(data, function (key, value) {
            questtypeList[key] = value['text']
        })
    })

    $.getJSON('static/dist/data/rewardtype.min.json').done(function (data) {
        $.each(data, function (key, value) {
            rewardtypeList[key] = value['text']
        })
    })

    $.getJSON('static/dist/data/conditiontype.min.json').done(function (data) {
        $.each(data, function (key, value) {
            conditiontypeList[key] = value['text']
        })
    })

    $.getJSON(geoJSONfile).done(function (data) {
        $.each(data.features, function (key, value) {
            scanAreas.push(value)
        })
    })
    $selectExclude = $('#exclude-pokemon')
    $selectExcludeMinIV = $('#exclude-min-iv')
    $selectPokemonNotify = $('#notify-pokemon')
    $selectRarityNotify = $('#notify-rarity')
    $textPerfectionNotify = $('#notify-perfection')
    $textMinIV = $('#min-iv')
    $textMinLevel = $('#min-level')
    $textLevelNotify = $('#notify-level')
    $raidNotify = $('#notify-raid')
    $switchTinyRat = $('#tiny-rat-switch')
    $switchBigKarp = $('#big-karp-switch')
    $questsExcludePokemon = $('#exclude-quests-pokemon')
    $questsExcludeItem = $('#exclude-quests-item')

    $.getJSON('static/dist/data/items.min.json').done(function (data) {
        $.each(data, function (key, value) {
            itemList.push({
                id: key,
                name: i8ln(value['name'])
            })
            value['name'] = i8ln(value['name'])
            idToItem[key] = value
        })
        $questsExcludeItem.select2({
            placeholder: i8ln('Select Item'),
            data: itemList,
            templateResult: formatState,
            multiple: true,
            maximumSelectionSize: 1
        })
        $questsExcludeItem.on('change', function (e) {
            buffer = questsExcludedItem
            questsExcludedItem = $questsExcludeItem.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, questsExcludedItem)
            reincludedQuestsItem = reincludedQuestsItem.concat(buffer).map(String)
            updateMap()
            Store.set('remember_quests_exclude_item', questsExcludedItem)
        })
    })

    $.getJSON('static/dist/data/pokemon.min.json').done(function (data) {
        $.each(data, function (key, value) {
            if (key > numberOfPokemon) {
                return false
            }
            var _types = []
            pokeList.push({
                id: key,
                text: i8ln(value['name']) + ' - #' + key,
                name: i8ln(value['name']),
                level: value['level'] !== undefined ? value['level'] : 1,
                cp: value['cp'] !== undefined ? value['cp'] : 1
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
        $questsExcludeItem.val(Store.get('remember_quests_exclude_item')).trigger('change')

        // setup the filter lists
        $selectExclude.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState,
            multiple: true,
            maximumSelectionSize: 1
        })
        $selectPokemonNotify.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState,
            multiple: true,
            maximumSelectionSize: 1
        })

        $selectRarityNotify.select2({
            placeholder: i8ln('Select Rarity'),
            data: [i8ln('Common'), i8ln('Uncommon'), i8ln('Rare'), i8ln('Very Rare'), i8ln('Ultra Rare')],
            templateResult: formatState
        })
        $selectExcludeMinIV.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState
        })
        $questsExcludePokemon.select2({
            placeholder: i8ln('Select Pokémon'),
            data: pokeList,
            templateResult: formatState,
            multiple: true,
            maximumSelectionSize: 1
        })
        // setup list change behavior now that we have the list to work from
        $selectExclude.on('change', function (e) {
            buffer = excludedPokemon
            excludedPokemon = $selectExclude.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, excludedPokemon)
            reincludedPokemon = reincludedPokemon.concat(buffer).map(String)
            clearStaleMarkers()
            Store.set('remember_select_exclude', excludedPokemon)
        })
        $selectExcludeMinIV.on('change', function (e) {
            buffer = excludedMinIV
            excludedMinIV = $selectExcludeMinIV.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = excludedMinIV.filter(function (e) {
                return this.indexOf(e) < 0
            }, buffer)
            reincludedPokemon = reincludedPokemon.concat(buffer).map(String)
            clearStaleMarkers()
            Store.set('remember_select_exclude_min_iv', excludedMinIV)
        })
        $textMinIV.on('change', function (e) {
            minIV = parseInt($textMinIV.val(), 10)
            if (isNaN(minIV) || minIV < 0) {
                minIV = ''
            }
            if (minIV > 100) {
                minIV = 100
            }
            $textMinIV.val(minIV)
            Store.set('remember_text_min_iv', minIV)
        })
        $textMinLevel.on('change', function (e) {
            minLevel = parseInt($textMinLevel.val(), 10)
            if (isNaN(minLevel) || minLevel < 0) {
                minLevel = ''
            }
            if (minLevel > 35) {
                minLevel = 35
            }
            $textMinLevel.val(minLevel)
            Store.set('remember_text_min_level', minLevel)
        })
        $switchTinyRat.on('change', function (e) {
            Store.set('showTinyRat', this.checked)
            lastpokemon = false
            updateMap()
        })
        $switchBigKarp.on('change', function (e) {
            Store.set('showBigKarp', this.checked)
            lastpokemon = false
            updateMap()
        })
        $selectPokemonNotify.on('change', function (e) {
            notifiedPokemon = $selectPokemonNotify.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
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
        $textLevelNotify.on('change', function (e) {
            notifiedMinLevel = parseInt($textLevelNotify.val(), 10)
            if (isNaN(notifiedMinLevel) || notifiedMinLevel <= 0) {
                notifiedMinLevel = ''
            }
            if (notifiedMinLevel > 35) {
                notifiedMinLevel = 35
            }
            $textLevelNotify.val(notifiedMinLevel)
            Store.set('remember_text_level_notify', notifiedMinLevel)
        })
        $questsExcludePokemon.on('change', function (e) {
            buffer = questsExcludedPokemon
            questsExcludedPokemon = $questsExcludePokemon.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, questsExcludedPokemon)
            reincludedQuestsPokemon = reincludedQuestsPokemon.concat(buffer).map(String)
            updateMap()
            Store.set('remember_quests_exclude_pokemon', questsExcludedPokemon)
        })
        // recall saved lists
        $selectExclude.val(Store.get('remember_select_exclude')).trigger('change')
        $selectExcludeMinIV.val(Store.get('remember_select_exclude_min_iv')).trigger('change')
        $selectPokemonNotify.val(Store.get('remember_select_notify')).trigger('change')
        $selectRarityNotify.val(Store.get('remember_select_rarity_notify')).trigger('change')
        $textPerfectionNotify.val(Store.get('remember_text_perfection_notify')).trigger('change')
        $textLevelNotify.val(Store.get('remember_text_level_notify')).trigger('change')
        $textMinIV.val(Store.get('remember_text_min_iv')).trigger('change')
        $textMinLevel.val(Store.get('remember_text_min_level')).trigger('change')
        $raidNotify.val(Store.get('remember_raid_notify')).trigger('change')
        $questsExcludePokemon.val(Store.get('remember_quests_exclude_pokemon')).trigger('change')

        if (isTouchDevice() && isMobileDevice()) {
            $('.select2-search input').prop('readonly', true)
        }
        $('#tabs').tabs()
        $('#quests-tabs').tabs()
        if (manualRaids) {
            $('.global-raid-modal').html(generateRaidModal())
        }
    })

    $('.select-all').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.pokemon-list .pokemon-icon-sprite').addClass('active')
        parent.find('input').val(Array.from(Array(numberOfPokemon + 1).keys()).slice(1).join(',')).trigger('change')
    })

    $('.hide-all').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.pokemon-list .pokemon-icon-sprite').removeClass('active')
        parent.find('input').val('').trigger('change')
    })
    $('.select-all-item').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.item-list .item-icon-sprite').addClass('active')
        parent.find('input').val(Array.from(Array(numberOfItem + 1).keys()).slice(1).join(',')).trigger('change')
    })

    $('.hide-all-item').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.item-list .item-icon-sprite').removeClass('active')
        parent.find('input').val('').trigger('change')
    })
    $('.area-go-to').on('click', function (e) {
        e.preventDefault()
        var lat = $(this).data('lat')
        var lng = $(this).data('lng')
        var zoom = $(this).data('zoom')
        map.setView(new L.LatLng(lat, lng), zoom)
    })

    $raidNotify.select2({
        placeholder: 'Minimum raid level',
        minimumResultsForSearch: Infinity
    })

    $raidNotify.on('change', function () {
        Store.set('remember_raid_notify', this.value)
    })

    $('#dialog_edit').on('click', '#closeButtonId', function () {
        $(this).closest('#dialog_edit').dialog('close')
    })

    // run interval timers to regularly update map and timediffs
    window.setInterval(updateLabelDiffTime, 1000)
    window.setInterval(updateMap, 5000)
    window.setInterval(updateWeatherOverlay, 60000)
    window.setInterval(updateGeoLocation, 1000)

    createUpdateWorker()

    // Wipe off/restore map icons when switches are toggled
    function buildSwitchChangeListener(data, dataType, storageKey) {
        return function () {
            Store.set(storageKey, this.checked)
            if (this.checked) {
                // When switch is turned on we asume it has been off, makes sure we dont end up in limbo
                // Without this there could've been a situation where no markers are on map and only newly modified ones are loaded
                if (storageKey === 'showPokemon') {
                    lastpokemon = false
                } else if (storageKey === 'showRaids') {
                    lastgyms = false
                } else if (storageKey === 'showGyms') {
                    lastgyms = false
                } else if (storageKey === 'showPokestops') {
                    lastpokestops = false
                } else if (storageKey === 'showLures') {
                    lastpokestops = false
                } else if (storageKey === 'showQuests') {
                    lastpokestops = false
                } else if (storageKey === 'showPortals') {
                    lastportals = false
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
                            markers.removeLayer(data[dType][key].marker.rangeCircle)
                            markersnotify.removeLayer(data[dType][key].marker.rangeCircle)
                            delete data[dType][key].marker.rangeCircle
                        }
                        if (storageKey !== 'showRanges') {
                            markers.removeLayer(data[dType][key].marker)
                            markersnotify.removeLayer(data[dType][key].marker)
                        }
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
    $('#raids-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#raids-filter-wrapper')
        var gymSidebarWrapper = $('#gym-sidebar-wrapper')
        var gymRaidsFilterWrapper = $('#gyms-raid-filter-wrapper')
        if (this.checked) {
            lastgyms = false
            wrapper.show(options)
            gymSidebarWrapper.show(options)
            gymRaidsFilterWrapper.show(options)
        } else {
            lastgyms = false
            wrapper.hide(options)
            if (!Store.get('showGyms')) {
                gymSidebarWrapper.hide(options)
                gymRaidsFilterWrapper.hide(options)
            }
        }
        buildSwitchChangeListener(mapData, ['gyms'], 'showRaids').bind(this)()
    })
    if (Store.get('showGyms') === true || Store.get('showRaids') === true) {
        $('#gyms-raid-filter-wrapper').toggle(true)
    }
    $('#gyms-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#gyms-filter-wrapper')
        var gymSidebarWrapper = $('#gym-sidebar-wrapper')
        var gymRaidsFilterWrapper = $('#gyms-raid-filter-wrapper')
        if (this.checked) {
            lastgyms = false
            wrapper.show(options)
            gymSidebarWrapper.show(options)
            gymRaidsFilterWrapper.show(options)
        } else {
            lastgyms = false
            wrapper.hide(options)
            if (!Store.get('showRaids')) {
                gymSidebarWrapper.hide(options)
                gymRaidsFilterWrapper.hide(options)
            }
        }
        buildSwitchChangeListener(mapData, ['gyms'], 'showGyms').bind(this)()
    })
    $('#nests-switch').change(function () {
        lastnests = false
        buildSwitchChangeListener(mapData, ['nests'], 'showNests').bind(this)()
    })
    $('#communities-switch').change(function () {
        lastcommunities = false
        buildSwitchChangeListener(mapData, ['communities'], 'showCommunities').bind(this)()
    })
    $('#poi-switch').change(function () {
        lastpois = false
        buildSwitchChangeListener(mapData, ['pois'], 'showPoi').bind(this)()
    })
    $('#portals-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#new-portals-only-wrapper')
        if (this.checked) {
            lastportals = false
            wrapper.show(options)
        } else {
            lastportals = false
            wrapper.hide(options)
        }
        return buildSwitchChangeListener(mapData, ['portals'], 'showPortals').bind(this)()
    })

    $('#s2-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#s2-switch-wrapper')
        if (this.checked) {
            wrapper.show(options)
            if (Store.get('showExCells')) {
                showS2Cells(13, {color: 'red', weight: 6, dashOffset: '8'})
            }
            if (Store.get('showGymCells')) {
                showS2Cells(14, {color: 'green', weight: 4, dashOffset: '4'})
            }
            if (Store.get('showStopCells')) {
                showS2Cells(17, {color: 'blue'})
            }
        } else {
            wrapper.hide(options)
            exLayerGroup.clearLayers()
            gymLayerGroup.clearLayers()
            stopLayerGroup.clearLayers()
        }
        return buildSwitchChangeListener(mapData, ['s2cells'], 'showCells').bind(this)()
    })

    $('#s2-level13-switch').change(function () {
        Store.set('showExCells', this.checked)
        if (this.checked) {
            showS2Cells(13, {color: 'red', weight: 6, dashOffset: '8'})
        } else {
            exLayerGroup.clearLayers()
        }
    })

    $('#s2-level14-switch').change(function () {
        Store.set('showGymCells', this.checked)
        if (this.checked) {
            showS2Cells(14, {color: 'green', weight: 4, dashOffset: '4'})
        } else {
            gymLayerGroup.clearLayers()
        }
    })

    $('#s2-level17-switch').change(function () {
        Store.set('showStopCells', this.checked)
        if (this.checked) {
            showS2Cells(17, {color: 'blue'})
        } else {
            stopLayerGroup.clearLayers()
        }
    })

    $('#pokemon-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#pokemon-filter-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
        buildSwitchChangeListener(mapData, ['pokemons'], 'showPokemon').bind(this)()
    })
    $('#scanned-switch').change(function () {
        buildSwitchChangeListener(mapData, ['scanned'], 'showScanned').bind(this)()
    })

    $('#weather-switch').change(function () {
        Store.set('showWeather', this.checked)
        if (this.checked) {
            updateWeatherOverlay()
        } else {
            destroyWeatherOverlay()
        }
    })

    $('#spawnpoints-switch').change(function () {
        buildSwitchChangeListener(mapData, ['spawnpoints'], 'showSpawnpoints').bind(this)()
    })
    $('#ranges-switch').change(buildSwitchChangeListener(mapData, ['gyms', 'pokemons', 'pokestops'], 'showRanges'))

    $('#scan-area-switch').change(function () {
        Store.set('showScanPolygon', this.checked)
        if (this.checked) {
            buildScanPolygons()
        } else {
            scanAreaGroup.clearLayers()
        }
    })
    $('#pokestops-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#pokestops-filter-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
        buildSwitchChangeListener(mapData, ['pokestops'], 'showPokestops').bind(this)()
    })

    $('#lures-switch').change(function () {
        Store.set('showLures', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            $('#quests-switch').prop('checked', false)
        }
        if (this.checked) {
            lastpokestops = false
            updateMap()
        } else {
            lastpokestops = false
            updateMap()
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showLures').bind(this)()
    })

    $('#quests-switch').change(function () {
        Store.set('showQuests', this.checked)
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        var options = {
            'duration': 500
        }
        var wrapper = $('#quests-filter-wrapper')
        if (this.checked) {
            lastpokestops = false
            wrapper.show(options)
            updateMap()
        } else {
            lastpokestops = false
            wrapper.hide(options)
            updateMap()
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showQuests').bind(this)()
    })

    $('#dustrange').on('input', function () {
        dustamount = $(this).val()
        Store.set('showDustAmount', dustamount)
        if (dustamount === '0') {
            $('#dustvalue').text('Off')
            setTimeout(function () { updateMap() }, 2000)
        } else {
            $('#dustvalue').text(i8ln('above') + ' ' + dustamount)
            reloaddustamount = true
            setTimeout(function () { updateMap() }, 2000)
        }
    })

    $('#sound-switch').change(function () {
        Store.set('playSound', this.checked)
        var options = {
            'duration': 500
        }
        var wrapper = $('#cries-switch-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
    })

    $('#cries-switch').change(function () {
        var wrapper = $('#cries-type-filter-wrapper')
        var options = {
            'duration': 500
        }
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
        Store.set('playCries', this.checked)
        if (this.checked) {
            fetchCriesJson()
        }
    })

    $('#bounce-switch').change(function () {
        Store.set('remember_bounce_notify', this.checked)
    })

    $('#notification-switch').change(function () {
        Store.set('remember_notification_notify', this.checked)
    })

    $('#start-at-user-location-switch').change(function () {
        Store.set('startAtUserLocation', this.checked)
        if (this.checked === true && Store.get('startAtLastLocation') === true) {
            Store.set('startAtLastLocation', false)
            $('#start-at-last-location-switch').prop('checked', false)
        }
    })

    $('#start-at-last-location-switch').change(function () {
        Store.set('startAtLastLocation', this.checked)
        if (this.checked === true && Store.get('startAtUserLocation') === true) {
            Store.set('startAtUserLocation', false)
            $('#start-at-user-location-switch').prop('checked', false)
        }
    })

    $('#follow-my-location-switch').change(function () {
        if (!navigator.geolocation) {
            this.checked = false
        } else {
            Store.set('followMyLocation', this.checked)

            var options = {
                'duration': 500
            }
            var wrapper = $('#spawn-area-wrapper')
            if (this.checked) {
                wrapper.show(options)
            } else {
                wrapper.hide(options)
            }
        }
    })

    $('#spawn-area-switch').change(function () {
        Store.set('spawnArea', this.checked)
        if (locationMarker.rangeCircle) {
            markers.removeLayer(locationMarker.rangeCircle)
            markersnotify.removeLayer(locationMarker.rangeCircle)
            delete locationMarker.rangeCircle
        }
    })

    if ($('#nav-accordion').length) {
        $('#nav-accordion').accordion({
            active: false,
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
        'columns': [{'orderable': false}, null, null, null]
    }).order([1, 'asc'])
})

function download(filename, text) { // eslint-disable-line no-unused-vars
    var element = document.createElement('a')
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text))
    element.setAttribute('download', filename + '_' + moment().format('DD-MM-YYYY HH:mm'))

    element.style.display = 'none'
    document.body.appendChild(element)

    element.click()

    document.body.removeChild(element)
}

function upload(fileText) {
    var data = JSON.parse(JSON.parse(fileText))
    Object.keys(data).forEach(function (k) {
        localStorage.setItem(k, data[k])
    })
    window.location.reload()
}

function openFile(event) { // eslint-disable-line no-unused-vars
    var input = event.target
    var reader = new FileReader()
    reader.onload = function () {
        console.log(reader.result)
        upload(reader.result)
    }
    reader.readAsText(input.files[0])
}

function checkAndCreateSound(pokemonId = 0) {
    if (Store.get('playSound')) {
        if (!Store.get('playCries') || pokemonId === 0) {
            createjs.Sound.play('ding')
        } else {
            createjs.Sound.play(pokemonId)
        }
    }
}
//
