//
// Global map.js variables
//

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
var $switchTinyRat
var $switchBigKarp
var $selectDirectionProvider
var $switchExEligible
var $questsExcludePokemon
var $questsExcludeItem
var $selectIconStyle

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

var numberOfPokemon = 649
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
var moves
var weather // eslint-disable-line no-unused-vars
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
var lastinns
var lastfortresses
var lastgreenhouses
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
var cpMultiplier = [0.094, 0.16639787, 0.21573247, 0.25572005, 0.29024988, 0.3210876, 0.34921268, 0.37523559, 0.39956728, 0.42250001, 0.44310755, 0.46279839, 0.48168495, 0.49985844, 0.51739395, 0.53435433, 0.55079269, 0.56675452, 0.58227891, 0.59740001, 0.61215729, 0.62656713, 0.64065295, 0.65443563, 0.667934, 0.68116492, 0.69414365, 0.70688421, 0.71939909, 0.7317, 0.73776948, 0.74378943, 0.74976104, 0.75568551, 0.76156384, 0.76739717, 0.7731865, 0.77893275, 0.7846369, 0.79030001]
var throwType = JSON.parse('{"10": "Nice", "11": "Great", "12": "Excellent"}')
var weatherLayerGroup = new L.LayerGroup()
var weatherArray = []
var weatherPolys = []
var weatherMarkers = []
var weatherColors
var s2Colors

var S2
var exLayerGroup = new L.LayerGroup()
var gymLayerGroup = new L.LayerGroup()
var stopLayerGroup = new L.LayerGroup()
var scanAreaGroup = new L.LayerGroup()
var liveScanGroup = new L.LayerGroup()
var scanAreas = []
var nestLayerGroup = new L.LayerGroup()
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
var notifyText = i8ln('disappears at') + ' <dist> (<udist>)'

var OpenStreetMapProvider = window.GeoSearch.OpenStreetMapProvider
var searchProvider = new OpenStreetMapProvider()
//
// Extras
//

L.Marker.addInitHook(function () {
    if (this.options.virtual) {
        this.on('add', function () {
            this._updateIconVisibility = function () {
                if (!this._map) {
                    return
                }
                var map = this._map
                var isVisible = map.getBounds().contains(this.getLatLng())
                var wasVisible = this._wasVisible
                var icon = this._icon
                var iconParent = this._iconParent
                var shadow = this._shadow
                var shadowParent = this._shadowParent

                if (!iconParent) {
                    iconParent = this._iconParent = icon.parentNode
                }
                if (shadow && !shadowParent) {
                    shadowParent = this._shadowParent = shadow.parentNode
                }

                if (isVisible !== wasVisible) {
                    if (isVisible) {
                        iconParent.appendChild(icon)
                        if (shadow) {
                            shadowParent.appendChild(shadow)
                        }
                    } else {
                        iconParent.removeChild(icon)
                        if (shadow) {
                            shadowParent.removeChild(shadow)
                        }
                    }
                    this._wasVisible = isVisible
                }
            }

            this._map.on('resize moveend zoomend', this._updateIconVisibility, this)
            this._updateIconVisibility()
        }, this)
    }
})

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
function previewPoiImage(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var input = event.target
    var reader = new FileReader()
    var fileLoaded = function (event) {
        var base64 = event.target.result
        form.find('[name="preview-poi-image"]').attr('src', base64)
    }
    reader.readAsDataURL(input.files[0])
    reader.onload = fileLoaded
}
function previewPoiSurrounding(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var input = event.target
    var reader = new FileReader()
    var fileLoaded = function (event) {
        var base64 = event.target.result
        form.find('[name="preview-poi-surrounding"]').attr('src', base64)
    }
    reader.readAsDataURL(input.files[0])
    reader.onload = fileLoaded
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

function removePokestopMarker(pokestopId) { // eslint-disable-line no-unused-vars
    if (mapData.pokestops[pokestopId].marker.rangeCircle) {
        markers.removeLayer(mapData.pokestops[pokestopId].marker.rangeCircle)
        delete mapData.pokestops[pokestopId].marker.rangeCircle
    }
    markers.removeLayer(mapData.pokestops[pokestopId].marker)
    mapData.pokestops[pokestopId].hidden = true
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
        preferCanvas: true,
        worldCopyJump: true,
        updateWhenZooming: false,
        updateWhenIdle: true,
        layers: [weatherLayerGroup, exLayerGroup, gymLayerGroup, stopLayerGroup, scanAreaGroup, liveScanGroup, nestLayerGroup]
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

    // If you see this: Don't tell anyone.
    var d = new Date()
    if (d.getMonth() === 11 && d.getDate() >= 24) {
        const snow = '<div class="winter-is-coming">\n' +
            '<div class="snow snow--near"></div>\n' +
            '<div class="snow snow--near snow--alt"></div>\n' +
            '<div class="snow snow--mid"></div>\n' +
            '<div class="snow snow--mid snow--alt"></div>\n' +
            '<div class="snow snow--far"></div>\n' +
            '<div class="snow snow--far snow--alt"></div>\n' +
            '</div>'
        $('#map').append(snow)
    }

    map.addLayer(markers)
    markersnotify = L.layerGroup().addTo(map)
    map.on('zoom', function () {
        updateS2Overlay()
        if (storeZoom === true) {
            Store.set('zoomLevel', map.getZoom())
        } else {
            storeZoom = true
        }
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
    map.getPane('portals').style.zIndex = 300
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
    buildNestPolygons()

    map.on('moveend', function () {
        updateS2Overlay()
    })

    map.on('click', function (e) {
        if ($('.submit-on-off-button').hasClass('on')) {
            updateS2Overlay()
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
    $selectIconSize = $('#pokemon-icon-size')

    $selectIconSize.select2({
        placeholder: 'Select Icon Size',
        minimumResultsForSearch: Infinity
    })

    $selectIconSize.on('change', function () {
        Store.set('iconSizeModifier', this.value)
        redrawPokemon(mapData.pokemons)
    })

    $selectIconNotifySizeModifier = $('#pokemon-icon-notify-size')

    $selectIconNotifySizeModifier.select2({
        placeholder: 'Increase Size Of Notified',
        minimumResultsForSearch: Infinity
    })

    $selectIconNotifySizeModifier.on('change', function () {
        Store.set('iconNotifySizeModifier', this.value)
        redrawPokemon(mapData.pokemons)
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

    $selectLocationIconMarker = $('#locationmarker-style')

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
}

function toggleFullscreenMap() { // eslint-disable-line no-unused-vars
    map.toggleFullscreen()
}
var openstreetmap = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars
var darkmatter = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png', {attribution: '&copy; <a href="https://carto.com/">Carto</a>'}) // eslint-disable-line no-unused-vars
var styletopo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars
var stylesatellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'}) // eslint-disable-line no-unused-vars
var stylewikipedia = L.tileLayer('https://maps.wikimedia.org/osm-intl/{z}/{x}/{y}{r}.png', {attribution: '<a href="https://wikimediafoundation.org/wiki/Maps_Terms_of_Use">Wikimedia</a>'}) // eslint-disable-line no-unused-vars
var mapbox = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/256/{z}/{x}/{y}?access_token=' + mBoxKey, {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars
var mapboxPogo = L.tileLayer('https://api.mapbox.com/styles/v1/skoodat/cjpuer4j702ph2sl3ktkrkkqa/tiles/256/{z}/{x}/{y}@2x?access_token=' + mBoxKey, {attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'}) // eslint-disable-line no-unused-vars
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

function pointInPolygon(x, y, cornersX, cornersY) {
    var i
    var j = cornersX.length - 1
    var odd = 0
    var pX = cornersX
    var pY = cornersY
    for (i = 0; i < cornersX.length; i++) {
        if (((pY[i] < y && pY[j] >= y) || (pY[j] < y && pY[i] >= y)) && (pX[i] <= x || pX[j] <= x)) {
            odd ^= (pX[i] + (y - pY[i]) * (pX[j] - pX[i]) / (pY[j] - pY[i])) < x
        }
        j = i
    }
    return odd === 1
}

function showS2Cells(level, style) {
    const bounds = map.getBounds()
    const swPoint = bounds.getSouthWest()
    const nePoint = bounds.getNorthEast()
    const swLat = swPoint.lat
    const swLng = swPoint.lng
    const neLat = nePoint.lat
    const neLng = nePoint.lng

    function addPoly(cell) {
        const vertices = cell.getCornerLatLngs()
        var s2Lats = []
        var s2Lons = []
        for (let j = 0; j < vertices.length; j++) {
            s2Lats[j] = vertices[j]['lat']
            s2Lons[j] = vertices[j]['lng']
        }
        var stopCount = 0
        var sponsoredStopCount = 0
        var sponsoredGymCount = 0
        var gymCount = 0
        var totalCount = 0

        var possibleCandidatePoiCount = 0
        var submittedPoiCount = 0
        var declinedPoiCount = 0
        var resubmittedPoiCount = 0
        var notEligiblePoiCount = 0
        var totalPoiCount = 0

        if (cell.level === 14 || cell.level === 17) {
            $.each(mapData.pokestops, function (key, value) {
                if (pointInPolygon(value['latitude'], value['longitude'], s2Lats, s2Lons)) {
                    if (value['pokestop_id'].includes('.')) {
                        stopCount++
                        totalCount++
                    } else {
                        sponsoredStopCount++
                    }
                }
            })
            $.each(mapData.gyms, function (key, value) {
                if (pointInPolygon(value['latitude'], value['longitude'], s2Lats, s2Lons)) {
                    if (value['gym_id'].includes('.')) {
                        gymCount++
                        totalCount++
                    } else {
                        sponsoredGymCount++
                    }
                }
            })
            $.each(mapData.pois, function (key, item) {
                if (pointInPolygon(item['lat'], item['lon'], s2Lats, s2Lons)) {
                    if (item['status'] === '1') {
                        possibleCandidatePoiCount++
                    } else if (item['status'] === '2') {
                        submittedPoiCount++
                    } else if (item['status'] === '3') {
                        declinedPoiCount++
                    } else if (item['status'] === '4') {
                        resubmittedPoiCount++
                    } else if (item['status'] === '5') {
                        notEligiblePoiCount++
                    }
                    totalPoiCount++
                }
            })
        }

        var html = ''
        var filledStyle = {color: 'black', fillOpacity: 0.0}
        if ((cell.level === 14) && (totalCount === 1 || totalCount === 5 || totalCount === 19)) {
            filledStyle = {fillColor: s2Colors[1], fillOpacity: 0.3}
            html += '<div><center><b>' + i8ln('1 more Pokéstop until new gym') + '</b></center></div>'
        } else if ((cell.level === 14) && (totalCount === 4 || totalCount === 18)) {
            filledStyle = {fillColor: s2Colors[2], fillOpacity: 0.3}
            html += '<div><center><b>' + i8ln('2 more Pokéstops until new gym') + '</b></center></div>'
        } else if (cell.level === 14 && totalCount >= 20) {
            filledStyle = {fillColor: s2Colors[3], fillOpacity: 0.3}
            html += '<div><center><b>' + i8ln('Max amount of Gyms reached') + '</b></center></div>'
        }

        if (cell.level === 17) {
            $.each(mapData.pokestops, function (key, value) {
                if (pointInPolygon(value['latitude'], value['longitude'], s2Lats, s2Lons) && value['pokestop_id'].includes('.')) {
                    filledStyle = {fillColor: s2Colors[0], fillOpacity: 0.3}
                }
            })
            $.each(mapData.gyms, function (key, value) {
                if (pointInPolygon(value['latitude'], value['longitude'], s2Lats, s2Lons) && value['gym_id'].includes('.')) {
                    filledStyle = {fillColor: s2Colors[0], fillOpacity: 0.3}
                }
            })
        }
        const poly = L.polygon(vertices, Object.assign({color: 'black', opacity: 0.5, weight: 0.5, fillOpacity: 0.0}, style, filledStyle))
        if (cell.level === 14) {
            html += '<div>' + i8ln('Gyms in cell') + ': <b>' + gymCount + '</b></div>' +
                '<div>' + i8ln('Pokéstops in cell') + ': <b>' + stopCount + '</b></div>'
            if (sponsoredStopCount > 0) {
                html += '<div>' + i8ln('Sponsored Pokéstops in cell') + ': <b>' + sponsoredStopCount + '</b></div>'
            }
            if (sponsoredGymCount > 0) {
                html += '<div>' + i8ln('Sponsored Gyms in cell') + ': <b>' + sponsoredGymCount + '</b></div>'
            }
            if (sponsoredStopCount > 0 || sponsoredGymCount > 0) {
                html += '<div>' + i8ln('Total (excluding sponsored)') + ': <b>' + totalCount + '</b></div>'
            } else {
                html += '<div>' + i8ln('Total') + ': <b>' + totalCount + '</b></div>'
            }
            if (!noPoi && totalPoiCount > 0) {
                html += '<br>'
                if (possibleCandidatePoiCount > 0) {
                    html += '<div>' + i8ln('POI possible candidate') + ': <b>' + possibleCandidatePoiCount + '</b></div>'
                }
                if (submittedPoiCount > 0) {
                    html += '<div>' + i8ln('POI submitted') + ': <b>' + submittedPoiCount + '</b></div>'
                }
                if (declinedPoiCount > 0) {
                    html += '<div>' + i8ln('POI declined') + ': <b>' + declinedPoiCount + '</b></div>'
                }
                if (resubmittedPoiCount > 0) {
                    html += '<div>' + i8ln('POI resubmitted') + ': <b>' + resubmittedPoiCount + '</b></div>'
                }
                if (notEligiblePoiCount > 0) {
                    html += '<div>' + i8ln('POI not eligible') + ': <b>' + notEligiblePoiCount + '</b></div>'
                }
                html += '<div>' + i8ln('Total POI') + ': <b>' + totalPoiCount + '</b></div>'
            }
            if (!$('.submit-on-off-button').hasClass('on')) {
                poly.bindPopup(html, {autoPan: false, closeOnClick: false, autoClose: false})
            }
        }
        if (cell.level === 13) {
            exLayerGroup.addLayer(poly)
        } else if (cell.level === 14) {
            gymLayerGroup.addLayer(poly)
        } else if (cell.level === 17) {
            stopLayerGroup.addLayer(poly)
        }
    }

    let processedCells = {}
    let stack = []

    const centerCell = S2.S2Cell.FromLatLng(bounds.getCenter(), level)
    processedCells[centerCell.toString()] = true
    stack.push(centerCell)
    addPoly(centerCell)

    // Find all cells within view with a slighty modified version of the BFS algorithm.
    while (stack.length > 0) {
        const cell = stack.pop()
        const neighbors = cell.getNeighbors()
        neighbors.forEach(function (ncell, index) {
            if (processedCells[ncell.toString()] !== true) {
                const cornerLatLngs = ncell.getCornerLatLngs()
                for (let i = 0; i < 4; i++) {
                    const item = cornerLatLngs[i]
                    if (item.lat >= swLat && item.lng >= swLng &&
                            item.lat <= neLat && item.lng <= neLng) {
                        processedCells[ncell.toString()] = true
                        stack.push(ncell)
                        addPoly(ncell)
                        break
                    }
                }
            }
        })
    }
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

function buildNestPolygons() {
    if (!Store.get(['showNestPolygon'])) {
        return false
    }

    $.getJSON(nestGeoJSONfile, function (data) {
        var nestGeoPolys = L.geoJson(data, {
            onEachFeature: function (features, featureLayer) {
                featureLayer.bindPopup(features.properties.name)
            }
        })
        nestLayerGroup.addLayer(nestGeoPolys)
    })
}

function initSidebar() {
    $('#gyms-switch').prop('checked', Store.get('showGyms'))
    $('#nests-switch').prop('checked', Store.get('showNests'))
    $('#communities-switch').prop('checked', Store.get('showCommunities'))
    $('#portals-switch').prop('checked', Store.get('showPortals'))
    $('#inns-switch').prop('checked', Store.get('showInns'))
    $('#fortresses-switch').prop('checked', Store.get('showFortresses'))
    $('#greenhouses-switch').prop('checked', Store.get('showGreenhouses'))
    $('#poi-switch').prop('checked', Store.get('showPoi'))
    $('#s2-switch').prop('checked', Store.get('showCells'))
    $('#s2-switch-wrapper').toggle(Store.get('showCells'))
    $('#s2-level13-switch').prop('checked', Store.get('showExCells'))
    $('#s2-level14-switch').prop('checked', Store.get('showGymCells'))
    $('#s2-level17-switch').prop('checked', Store.get('showStopCells'))
    $('#new-portals-only-switch').val(Store.get('showNewPortalsOnly'))
    $('#new-portals-only-wrapper').toggle(Store.get('showPortals'))
    $('#ex-eligible-switch').prop('checked', Store.get('exEligible'))
    $('#gyms-filter-wrapper').toggle(Store.get('showGyms'))
    $('#team-gyms-only-switch').val(Store.get('showTeamGymsOnly'))
    $('#open-gyms-only-switch').prop('checked', Store.get('showOpenGymsOnly'))
    $('#raids-switch').prop('checked', Store.get('showRaids'))
    $('#raids-filter-wrapper').toggle(Store.get('showRaids'))
    $('#rocket-wrapper').toggle(Store.get('showRocket'))
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
    $('#rocket-switch').prop('checked', Store.get('showRocket'))
    $('#quests-switch').prop('checked', Store.get('showQuests'))
    $('#quests-filter-wrapper').toggle(Store.get('showQuests'))
    $('#dustvalue').text(Store.get('showDustAmount'))
    $('#dustrange').val(Store.get('showDustAmount'))
    $('#start-at-user-location-switch').prop('checked', Store.get('startAtUserLocation'))
    $('#start-at-last-location-switch').prop('checked', Store.get('startAtLastLocation'))
    $('#follow-my-location-switch').prop('checked', Store.get('followMyLocation'))
    $('#spawn-area-switch').prop('checked', Store.get('spawnArea'))
    $('#spawn-area-wrapper').toggle(Store.get('followMyLocation'))
    $('#weather-switch').prop('checked', Store.get('showWeather'))
    $('#spawnpoints-switch').prop('checked', Store.get('showSpawnpoints'))
    $('#direction-provider').val(Store.get('directionProvider'))
    $('#ranges-switch').prop('checked', Store.get('showRanges'))
    $('#scan-area-switch').prop('checked', Store.get('showScanPolygon'))
    $('#scan-location-switch').prop('checked', Store.get('showScanLocation'))
    $('#nest-polygon-switch').prop('checked', Store.get('showNestPolygon'))
    $('#raid-timer-switch').prop('checked', Store.get('showRaidTimer'))
    $('#rocket-timer-switch').prop('checked', Store.get('showRocketTimer'))
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
    var pMove1 = moves[item['move_1']] !== undefined ? i8ln(moves[item['move_1']]['name']) : 'unknown'
    var pMove2 = moves[item['move_2']] !== undefined ? i8ln(moves[item['move_2']]['name']) : 'unknown'
    var pMoveType1 = ''
    var pMoveType2 = ''
    var weight = item['weight'] !== null ? item['weight'].toFixed(2) + 'kg' : '??'
    var height = item['height'] !== null ? item['height'].toFixed(2) + 'm' : '??'
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
        typesDisplay += '<img src="static/types/' + type['type'] + '.png" style="height:20px;">'
    })

    var details = ''
    if (atk != null && def != null && sta != null) {
        var iv = getIv(atk, def, sta)
        var pokemonLevel
        if (level != null) {
            pokemonLevel = level
        } else {
            pokemonLevel = getPokemonLevel(cpMultiplier)
        }
        if (pMove1 !== 'unknown') {
            pMoveType1 = '<img style="position:relative;top:3px;left:2px;height:15px;" src="static/types/' + moves[item['move_1']]['type'] + '.png">'
        }
        if (pMove2 !== 'unknown') {
            pMoveType2 = '<img style="position:relative;top:3px;left:2px;height:15px;" src="static/types/' + moves[item['move_2']]['type'] + '.png">'
        }
        details +=
            '<div style="position:absolute;top:90px;left:80px;"><div>' +
            i8ln('IV') + ': <b>' + iv.toFixed(1) + '%</b> (<b>' + atk + '</b>/<b>' + def + '</b>/<b>' + sta + '</b>)' +
            '</div>' +
            '<div>' + i8ln('CP') + ': <b>' + cp + '</b> | ' + i8ln('Level') + ': <b>' + pokemonLevel + '</b></div>' +
            '</div><br>' +
            '<div style="position:absolute;top:125px;">' +
            '<div>' + i8ln('Quick') + ': <b>' + pMove1 + '</b>' + pMoveType1 + '</div>' +
            '<div>' + i8ln('Charge') + ': <b>' + pMove2 + '</b>' + pMoveType2 + '</div>' +
            '<div>' + i8ln('Weight') + ': <b>' + weight + '</b>' + ' | ' + i8ln('Height') + ': <b>' + height + '</b></div>' +
            '</div>'
    }

    if (weatherBoostedCondition !== 0) {
        details +=
            '<img style="height:25px;position:absolute;top:25px;left:5px;" src="static/weather/a-' + weatherBoostedCondition + '.png"></div>'
    }

    var contentstring =
        '<div><center>' +
        '<b>' + name + '</b>'
    if (form !== null && form > 0 && item['form_name'] !== 'Normal') {
        contentstring += ' (' + i8ln(item['form_name']) + ')'
    }

    if (gender != null) {
        contentstring += ' ' + genderType[gender - 1]
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
    contentstring +=
        '</center></div>' +
        '<div><img src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr
    if (item['costume'] > 0 && noCostumeIcons === false) {
        contentstring += '_' + item['costume']
    }
    contentstring += '.png" style="width:50px;margin-top:10px;"/>'
    if (item['expire_timestamp_verified'] > 0) {
        contentstring += '<b style="top:-20px;position:relative;">' +
            '<i class="far fa-clock"></i>' + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            ' <i class="fas fa-check-square" style="color:#28b728;" title="' + i8ln('Despawntime verified') + '"></i>' +
            '</b></div>'
    } else if (pokemonReportTime === true) {
        contentstring += '<b style="top:-20px;position:relative;">' +
            ' <i class="far fa-clock"></i>' + ' ' + getTimeStr(reportTime) +
            '</b></div>'
    } else {
        contentstring += '<b style="top:-20px;position:relative;">' +
            ' <i class="far fa-clock"></i>' + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            ' <i class="fas fa-question" style="color:red;" title="' + i8ln('Despawntime not verified') + '"></i>' +
            '</b></div>'
    }

    contentstring += '<small>' + typesDisplay + '</small>' + '<br>' + details
    if (atk != null && def != null && sta != null) {
        contentstring += '<center><div style="position:relative;top:55px;">'
    } else {
        contentstring += '<center><div style="position:relative;">'
    }
    contentstring += '<a href="javascript:excludePokemon(' + id + ')"  title="' + i8ln('Exclude this Pokémon') + '"><i class="fas fa-minus-circle" style="font-size:15px;width:20px;"></i></a>' +
    ' | <a href="javascript:notifyAboutPokemon(' + id + ')" title="' + i8ln('Notify about this Pokémon') + '"><i class="fas fa-bell" style="font-size:15px;width:20px;"></i></a>' +
    ' | <a href="javascript:removePokemonMarker(\'' + encounterId + '\')" title="' + i8ln('Remove this Pokémon from the map') + '"><i class="fas fa-trash-alt" style="font-size:15px;width:20px;"></i></a>' +
    ' | <a href="javascript:void(0);" onclick="javascript:toggleOtherPokemon(' + id + ');" title="' + i8ln('Toggle display of other Pokémon') + '"><i class="fas fa-search-plus" style="font-size:15px;width:20px;"></i></a>' +
    '</div></center>'
    if (atk != null && def != null && sta != null) {
        contentstring += '<div style="position:relative;top:55px;"><center>'
    } else {
        contentstring += '<div style="position:relative;"><center>'
    }
    contentstring +=
    '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ', ' + longitude + ')" title="' + i8ln('View in Maps') + '">' +
    '<i class="fas fa-road"></i> ' + coordText + '</a> - ' +
    '<a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=16">' +
    '<i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;margin-bottom:10px;font-size:18px;"></i>' +
    '</a></center></div>'
    if (atk != null && def != null && sta != null) {
        contentstring += '<br><br><br>'
    }
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
    var form = item['form']
    var freeSlots = item['slots_available']
    var gender = item['raid_pokemon_gender']

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
            if (form !== null && form > 0 && item['form_name'] !== 'Normal') {
                raidStr += ' (' + i8ln(item['form_name']) + ')'
            }
            if (gender > 0) {
                raidStr += ' ' + genderType[gender - 1]
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
            raidIcon = '<img style="width: 70px;" src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png"/>'
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
        raidStr += '<div class="raid-container">' + i8ln('Add raid ') + '<i class="fas fa-binoculars submit-raid" onclick="openRaidModal(event);" data-id="' + item['gym_id'] + '"></i>' +
            '</div>'
    }
    if (!noDeleteGyms) {
        raidStr += '<i class="fas fa-trash-alt delete-gym" onclick="deleteGym(event);" data-id="' + item['gym_id'] + '"></i>'
    }
    if (!noRenameGyms) {
        raidStr += '<center><div><i class="fas fa-edit rename-gym" onclick="openRenameGymModal(event);" data-id="' + item['gym_id'] + '">' + i8ln('Rename Gym') + '</i></div></center>'
    }
    if (!noToggleExGyms) {
        raidStr += '<i class="fas fa-trophy toggle-ex-gym" onclick="toggleExGym(event);" data-id="' + item['gym_id'] + '"></i>'
    }

    var lastScannedStr = ''
    if (lastScanned != null) {
        lastScannedStr =
            '<div>' +
            i8ln('Last Scanned') + ': ' + getDateStr(lastScanned) + ' ' + getTimeStr(lastScanned) +
            '</div>'
    }

    var lastModifiedStr = getDateStr(lastModified) + ' ' + getTimeStr(lastModified)

    var nameStr = (name ? '<div style="font-weight:900">' + name + '</div>' : '')

    var gymColor = ['0, 0, 0, .4', '6, 119, 239', '255, 45, 33', '251, 210, 8']
    var str
    var gymImage = ''
    if (url !== null) {
        gymImage = '<img id="' + item['gym_id'] + '"class="gym-image" style="border:3px solid rgba(' + gymColor[teamId] + ')" src="' + url + '" onclick="openFullscreenModal(document.getElementById(\'' + item['gym_id'] + '\').src)">'
    }
    var teamStr = ''
    if (teamId === 0) {
        teamStr = i8ln('Uncontested Gym')
    } else {
        teamStr = '<b style="color:rgba(' + gymColor[teamId] + ')">' + i8ln('Team') + ' ' + i8ln(teamName) + '</b><br>'
    }
    var whatsappLink = ''
    if (((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) && (item.raid_pokemon_id > 1 && item.raid_pokemon_id < numberOfPokemon)) {
        whatsappLink = '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20' + item.raid_pokemon_name + '%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0AStats:%0Ahttps://pokemongo.gamepress.gg/pokemon/' + item.raid_pokemon_id + '%0AMoves:%0A' + pMove1 + ' / ' + pMove2 + '%0A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share"><i class="fab fa-whatsapp" style="position:relative;top:3px;left:5px;color:#26c300;font-size:20px;"></i></a>'
    } else if ((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) {
        whatsappLink = '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + '%0ALevel%20' + item.raid_level + '%20egg%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share"><i class="fab fa-whatsapp" style="position:relative;top:3px;left:5px;color:#26c300;font-size:20px;"></i></a>'
    }
    var coordText = latitude.toFixed(6) + ', ' + longitude.toFixed(7)
    if (hideGymCoords === true) {
        coordText = i8ln('Directions')
    }
    str =
        '<div class="gym-label">' +
        '<center>' +
        nameStr +
        '<div>' +
        teamStr +
        '</div>' +
        '<div>' +
        gymImage +
        raidIcon +
        '</div>' +
        '<div><b>' + freeSlots + ' ' + i8ln('Free Slots') + '</b></div>' +
        raidStr +
        '<div>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '"><i class="fas fa-road"></i> ' + coordText + '</a> - <a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        whatsappLink +
        '</div>' +
        '<div>' +
        i8ln('Last Modified') + ': ' + lastModifiedStr +
        '</div>' +
        '<div>' +
        lastScannedStr +
        '</div>' +
        '</center>' +
        '</div>'

    return str
}

function getReward(item) {
    var rewardImage
    var pokemonIdStr = ''
    var formStr = ''
    var shinyStr = ''
    var styleStr = ''
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        styleStr = 'position:absolute;height:50px;top:60px;'
    } else if (item['quest_reward_type'] === 2) {
        styleStr = 'position:absolute;height:35px;right:55%;top:85px;filter:drop-shadow(1px 0 0 black)drop-shadow(-1px 0 0 black);'
    } else {
        styleStr = 'position:absolute;height:35px;right:55%;top:85px;'
    }
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
        rewardImage = '<img style="' + styleStr + '" src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + shinyStr + '.png"/>'
    } else if (item['quest_reward_type'] === 3) {
        rewardImage = '<img style="' + styleStr + '" src="' + iconpath + 'rewards/reward_stardust_' + item['quest_dust_amount'] + '.png"/>'
    } else if (item['quest_reward_type'] === 2) {
        rewardImage = '<img style="' + styleStr + '" src="' + iconpath + 'rewards/reward_' + item['quest_item_id'] + '_' + item['quest_reward_amount'] + '.png"/>'
    }
    return rewardImage
}

function getQuest(item) {
    var str
    var raidLevel
    if (mapFork === 'mad') {
        str = item['quest_task']
    } else {
        var questinfo = JSON.parse(item['quest_condition_info'])
        var questStr = questtypeList[item['quest_type']]
        str = questStr
        if (item['quest_condition_type'] > 0) {
            switch (item['quest_condition_type']) {
                case 1:
                    var tstr = ''
                    if (questinfo['pokemon_type_ids'].length > 1) {
                        $.each(questinfo['pokemon_type_ids'], function (index, typeId) {
                            if (index === questinfo['pokemon_type_ids'].length - 2) {
                                tstr += pokemonTypes[typeId] + ' or '
                            } else if (index === questinfo['pokemon_type_ids'].length - 1) {
                                tstr += pokemonTypes[typeId]
                            } else {
                                tstr += pokemonTypes[typeId] + ', '
                            }
                        })
                    } else {
                        tstr = pokemonTypes[questinfo['pokemon_type_ids']]
                    }
                    if (item['quest_condition_type_1'] === 21) {
                        str = str.replace('Catch {0}', 'Catch {0} different species of')
                    }
                    str = str.replace('pokémon', tstr + '-type Pokémon')
                    str = str.replace('Snapshot(s)', 'Snapshot(s) of ' + tstr + '-type Pokémon')
                    break
                case 2:
                    var pstr = ''
                    if (questinfo['pokemon_ids'].length > 1) {
                        $.each(questinfo['pokemon_ids'], function (index, id) {
                            if (index === questinfo['pokemon_ids'].length - 2) {
                                pstr += idToPokemon[id].name + ' or '
                            } else if (index === questinfo['pokemon_ids'].length - 1) {
                                pstr += idToPokemon[id].name
                            } else {
                                pstr += idToPokemon[id].name + ', '
                            }
                        })
                    } else {
                        pstr = idToPokemon[questinfo['pokemon_ids']].name
                    }
                    str = str.replace('pokémon', pstr)
                    str = str.replace('Snapshot(s)', 'Snapshot(s) of ' + pstr)
                    break
                case 3:
                    str = str.replace('pokémon', 'Pokémon with weather boost')
                    break
                case 6:
                    str = str.replace('Complete', 'Win')
                    break
                case 7:
                    raidLevel = Math.min.apply(null, questinfo['raid_levels'])
                    if (raidLevel > 1) {
                        str = str.replace('raid battle(s)', 'level ' + raidLevel + ' or higher raid')
                    }
                    if (item['quest_condition_type_1'] === 6) {
                        str = str.replace('Complete', 'Win')
                    }
                    break
                case 8:
                    str = str.replace('Land', 'Make')
                    str = str.replace('throw(s)', throwType[questinfo['throw_type_id']] + ' Throw(s)')
                    if (item['quest_condition_type_1'] === 15) {
                        str = str.replace('Throw(s)', 'Curveball Throw(s)')
                    }
                    break
                case 9:
                    str = str.replace('Complete', 'Win')
                    break
                case 10:
                    str = str.replace('Complete', 'Use a super effective charged attack in')
                    break
                case 11:
                    if (item['quest_type'] === 13) {
                        str = str.replace('Catch', 'Use').replace('pokémon with berrie(s)', 'berrie(s) to help catch Pokémon')
                    }
                    if (questinfo !== null) {
                        str = str.replace('berrie(s)', idToItem[questinfo['item_id']].name)
                    } else {
                        str = str.replace('Evolve', 'Use a item to evolve')
                    }
                    break
                case 12:
                    str = str.replace('pokéstop(s)', "pokéstop(s) you haven't visited before")
                    break
                case 14:
                    str = str.replace('Land', 'Make')
                    if (typeof questinfo['throw_type_id'] === 'undefined') {
                        str = str.replace('throw(s)', 'Throw(s) in a row')
                    } else {
                        str = str.replace('throw(s)', throwType[questinfo['throw_type_id']] + ' Throw(s) in a row')
                    }
                    if (item['quest_condition_type_1'] === 15) {
                        str = str.replace('Throw(s)', 'Curveball Throw(s)')
                    }
                    break
                case 22:
                    str = str.replace('Win', 'Battle a Team Leader').replace('pvp battle(s)', 'times')
                    break
                case 23:
                    str = str.replace('Win', 'Battle Another Trainer').replace('pvp battle(s)', 'times')
                    break
                case 25:
                    str = str.replace('{0} pokémon', 'pokémon caught ' + questinfo['distance'] + 'km apart')
                    break
            }
        } else if (item['quest_type'] > 0) {
            switch (item['quest_type']) {
                case 7:
                    str = str.replace('Complete', 'Battle in a gym').replace('gym battle(s)', 'times')
                    break
                case 8:
                    str = str.replace('Complete', 'Battle in a raid').replace('raid battle(s)', 'times')
                    break
                case 13:
                    str = str.replace('Catch', 'Use').replace('pokémon with berrie(s)', 'berries to help catch Pokémon')
                    break
                case 17:
                    str = str.replace('Walk your buddy to earn', 'Earn').replace('candy', 'candy walking with your buddy')
                    break
            }
        }
        str = str.replace('{0}', item['quest_target'])
        if (item['quest_target'] === 1) {
            str = str.replace('(s)', '').replace('1 ', 'a ').replace(' a times', '').replace('friends', 'friend')
        } else {
            str = str.replace('(s)', 's')
        }
        str = str.replace('pokémon', 'Pokémon')
    }
    return str
}

function pokestopLabel(item) {
    var str
    var stopImage = ''
    var lureEndStr = ''
    var incidentEndStr = ''
    var stopName = ''
    var gruntReward = ''
    var lastMidnight = ''
    if (item['pokestop_name'] === null) {
        item['pokestop_name'] = 'Pokéstop'
    }
    var d = new Date()
    if (mapFork === 'mad') {
        lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    } else {
        lastMidnight = 0
    }
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        stopName = '<b class="pokestop-rocket-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-rocket-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>' +
            '<img src="static/forts/teamRocket.png" style="position:absolute;height:30px;left:55%;">' +
            '<img src="static/grunttype/' + item['grunt_type'] + '.png" style="position:absolute;height:35px;right:55%;top:85px;">'
        }
    } else if (!noQuests && item['quest_type'] !== 0 && lastMidnight < Number(item['quest_timestamp'])) {
        stopName = '<b class="pokestop-quest-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-quest-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>' +
            '<img src="static/images/reward.png" style="position:absolute;height:30px;left:55%;">'
        }
    } else if (!noLures && item['lure_expiration'] > Date.now()) {
        stopName = '<b class="pokestop-lure-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-lure-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>'
        }
    } else {
        stopName = '<b class="pokestop-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>'
        }
    }
    str =
        '<div class="pokestop-label">' +
        '<center>' +
        '<div>' + stopName + '</div>' +
        '<div>' + stopImage

    if (!noQuests && item['quest_type'] !== null && typeof questtypeList[item['quest_type']] !== 'undefined' && lastMidnight < Number(item['quest_timestamp'])) {
        var questStr = getQuest(item)
        str += getReward(item) + '</div>' +
            '<div>' +
            i8ln('Quest') + ': <b>' +
            i8ln(questStr) +
            '</b></div>'
        if (item['quest_reward_type'] === 2) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['quest_reward_amount'] + ' ' +
            item['quest_item_name'] +
            '</b></div>'
        } else if (item['quest_reward_type'] === 3) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['quest_dust_amount'] + ' ' +
            i8ln('Stardust') +
            '</b></div>'
        } else if (item['quest_reward_type'] === 7) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['quest_pokemon_name'] +
            '</b></div>'
        }
        str += '<a href="javascript:removePokestopMarker(\'' + item['pokestop_id'] + '\')" title="' + i8ln('Hide this Pokéstop') + '"><i class="fas fa-trash-alt" style="font-size:15px;"></i></a>'
    } else {
        str += '</div>'
    }
    if (!noQuests && item['quest_type'] !== null && typeof questtypeList[item['quest_type']] === 'undefined' && lastMidnight < Number(item['quest_timestamp'])) {
        console.log('Undefined Quest Type: ' + item['quest_type'])
        str += '<div>' + i8ln('Error: Undefined Quest Type') + ': ' + item['quest_type'] + '</div>'
    }
    if (!noLures && item['lure_expiration'] > Date.now()) {
        var lureType = '<img style="padding:5px;position:relative;left:0px;top:12px;height:40px;" src="static/forts/LureModule_' + item['lure_id'] + '.png"/>'
        if (item['lure_id'] === 1) {
            lureType += i8ln('Normal')
        } else if (item['lure_id'] === 2) {
            lureType += i8ln('Glacial')
        } else if (item['lure_id'] === 3) {
            lureType += i8ln('Mossy')
        } else if (item['lure_id'] === 4) {
            lureType += i8ln('Magnetic')
        }
        lureEndStr = getTimeStr(item['lure_expiration'])
        str +=
        '<div>' + i8ln('Lure Type') + ': <b>' + lureType + '</b></div>' +
        '<div>' + i8ln('Lure expiration') + ': <b>' + lureEndStr +
        ' <span class="label-countdown" disappears-at="' + item['lure_expiration'] + '">(00m00s)</span>' +
        '</b></div>'
    }
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        str += '<br><div><b>' + i8ln('Team Rocket') + ':</b></div>'
        if (item['grunt_type'] > 0) {
            if (item['grunt_type_name'] !== '') {
                str += '<div>' + i8ln('Grunt-Type') + ': <b>' + item['grunt_type_name'] + '</b></div>'
            }
            if (item['grunt_type_gender'] !== '') {
                str += '<div>' + i8ln('Grunt-Gender') + ': <b>' + item['grunt_type_gender'] + '</b></div>'
            }
        }
        incidentEndStr = getTimeStr(item['incident_expiration'])
        str += '<div>' + i8ln('Expiration Time') + ': <b>' + incidentEndStr +
        ' <span class="label-countdown" disappears-at="' + item['incident_expiration'] + '">(00m00s)</span>' +
        '</b></div>'
        if (!noInvasionEncounterData && item['encounters'] !== null) {
            gruntReward +=
            '<input class="button" name="button" type="button" onclick="showHideGruntEncounter()" value="' + i8ln('Show / Hide Possible Rewards') + '" style="margin-top:2px;outline:none;font-size:9pt">' +
            '<div class="grunt-encounter-wrapper" style="display:none;background-color:#ccc;border-radius:10px;border:1px solid black">'
            if (item['second_reward'] === 'false') {
                gruntReward += '<center>' +
                '<div>100% ' + i8ln('encounter chance for') + ':<br>'
                item['encounters']['first'].forEach(function (data) {
                    gruntReward += '<img src="' + iconpath + 'pokemon_icon_' + data + '.png" style="width:30px;height:auto;position:absolute;"/>' +
                    '<img src="static/images/shadow_icon.png" style="width:30px;height:30px;"/>'
                })
                gruntReward += '</div></div></center>'
            } else if (item['second_reward'] === 'true') {
                gruntReward += '<center>' +
                '<div>85% ' + i8ln('encounter chance for') + ':<br>'
                item['encounters']['first'].forEach(function (data) {
                    gruntReward += '<img src="' + iconpath + 'pokemon_icon_' + data + '.png" style="width:30px;height:auto;position:absolute;"/>' +
                    '<img src="static/images/shadow_icon.png" style="width:30px;height:30px;"/>'
                })
                gruntReward += '</div>' +
                '<div>15% ' + i8ln('encounter chance for') + ':<br>'
                item['encounters']['second'].forEach(function (data) {
                    gruntReward += '<img src="' + iconpath + 'pokemon_icon_' + data + '.png" style="width:30px;height:auto;position:absolute;"/>' +
                    '<img src="static/images/shadow_icon.png" style="width:30px;height:30px;"/>'
                })
                gruntReward += '</div></div><center>'
            }
            str += '<center>' + gruntReward + '</center>'
        }
    }
    str += '</center></div>'
    if (!noDeletePokestops) {
        str += '<i class="fas fa-trash-alt delete-pokestop" onclick="deletePokestop(event);" data-id="' + item['pokestop_id'] + '"></i>'
    }
    if (!noManualQuests && item['scanArea'] === false) {
        str += '<center><div><i class="fas fa-binoculars submit-quest" onclick="openQuestModal(event);" data-id="' + item['pokestop_id'] + '">' + i8ln('Add Quest') + '</i></div></center>'
    }
    if (!noRenamePokestops) {
        str += '<center><div><i class="fas fa-edit rename-pokestop" onclick="openRenamePokestopModal(event);" data-id="' + item['pokestop_id'] + '">' + i8ln('Rename Pokestop') + '</i></div></center>'
    }
    if (!noConvertPokestops) {
        str += '<center><div><i class="fas fa-sync-alt convert-pokestop" onclick="openConvertPokestopModal(event);" data-id="' + item['pokestop_id'] + '">' + i8ln('Convert to Gym') + '</i></div></center>'
    }
    var coordText = item['latitude'] + ', ' + item['longitude']
    if (hidePokestopCoords === true) {
        coordText = i8ln('Directions')
    }
    str += '<div><center>' +
        '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item['latitude'] + ',' + item['longitude'] + ')" title="' + i8ln('View in Maps') + '"><i class="fas fa-road"></i> ' + coordText + '</a> - <a href="./?lat=' + item['latitude'] + '&lon=' + item['longitude'] + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center></div>'
    if ((!noWhatsappLink) && (item['quest_id'] && item['reward_id'] !== null)) {
        str += '<div>' +
            '<center>' +
            '<a href="whatsapp://send?text=' + encodeURIComponent(item['pokestop_name']) + '%0A%2AQuest:%20' + i8ln(questList[item['quest_id']]) + '%2A%0A%2AReward:%20' + i8ln(rewardList[item['reward_id']]) + '%2A%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item['latitude'] + ',' + item['longitude'] + '" data-action="share/whatsapp/share">' + i8ln('Whatsapp Link') + '</a>' +
            '</center>' +
            '</div>'
    }
    return str
}

function showHideGruntEncounter() { // eslint-disable-line no-unused-vars
    var x = document.getElementsByClassName('grunt-encounter-wrapper')
    var i
    for (i = 0; i < x.length; i++) {
        if (x[i].style.display === 'none') {
            x[i].style.display = 'block'
        } else {
            x[i].style.display = 'none'
        }
    }
}

function formatSpawnTime(seconds) {
    return ('0' + Math.floor((seconds + 3600) % 3600 / 60)).substr(-2) + ':' + ('0' + seconds % 60).substr(-2)
}

function spawnpointLabel(item) {
    var str = ''

    // 60 minute
    if (item.time > 1800) {
        str += '<div><b>' + i8ln('Spawn Point (60)') + '</b></div>' +
            '<div>' + i8ln('Spawn time') + ': xx:' + formatSpawnTime(item.time) + '</div>' +
            '<div>' + i8ln('Despawn time') + ': xx:' + formatSpawnTime(item.time) + '</div>'
    // 30 minute
    } else if (item.time > 0) {
        str += '<div><b>' + i8ln('Spawn Point (30)') + '</b></div>' +
        '<div>' + i8ln('Spawn time') + ': xx:' + formatSpawnTime(item.time + 1800) + '</div>' +
        '<div>' + i8ln('Despawn time') + ': xx:' + formatSpawnTime(item.time) + '</div>'
    } else {
        str += '<div>' + i8ln('Unknown spawnpoint info') + '</div>'
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
    if (((park !== '0' && onlyTriggerGyms === false && park) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false)) {
        exIcon = '<img src="static/images/ex.png" style="position:absolute;right:25px;bottom:2px;"/>'
    }
    var smallExIcon = ''
    if (((park !== '0' && onlyTriggerGyms === false && park) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false)) {
        smallExIcon = '<img src="static/images/ex.png" style="width:26px;position:absolute;right:35px;bottom:13px;"/>'
    }
    var html = ''
    if (item['raid_pokemon_id'] != null && item.raid_end > Date.now()) {
        html = '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:50px;height:auto;"/>' +
            exIcon +
            '<img src="' + iconpath + 'pokemon_icon_' + pokemonidStr + '_' + formStr + '.png" style="width:50px;height:auto;position:absolute;top:-15px;right:0px;"/>' +
            '</div>'
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div><span class="raid-countdown gym-icon-countdown" disappears-at="' + item['raid_end'] + '" end>' + generateRemainingTimer(item['raid_end'], 'end') + '</span></div>'
        }
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
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div><span class="raid-countdown gym-icon-countdown" disappears-at="' + item['raid_end'] + '" end>' + generateRemainingTimer(item['raid_end'], 'end') + '</span></div>'
        }
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
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div><span class="raid-countdown gym-icon-countdown" disappears-at="' + item['raid_start'] + '" end>' + generateRemainingTimer(item['raid_start'], 'end') + '</span></div>'
        }
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
    var marker = L.marker([item['latitude'], item['longitude']], {icon: getGymMarkerIcon(item), zIndexOffset: 1060, virtual: true})
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
    marker.bindPopup(gymLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    addListeners(marker)
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
    var lastMidnight = ''
    if (mapFork === 'mad') {
        lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    } else {
        lastMidnight = 0
    }
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        var lureStr = ''
        if (!noLures && item['lure_expiration'] > Date.now()) {
            lureStr = 'Lured_' + item['lure_id']
        }
        html = '<div style="position:relative;"><img src="static/forts/Pstop' + lureStr + '_rocket.png" style="width:50px;height:72;top:-35px;right:10px;"/>'
        if (item['grunt_type'] > 0) {
            html += '<img src="static/grunttype/' + item['grunt_type'] + '.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
        } else {
            html += '</div>'
        }
        if (noRocketTimer === false && Store.get(['showRocketTimer'])) {
            html += '<div><span class="raid-countdown gym-icon-countdown" disappears-at="' + item['incident_expiration'] + '"> </span></div>'
        }
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-rocket-marker',
            html: html
        })
    } else if (!noQuests && item['quest_reward_type'] !== null && lastMidnight < Number(item['quest_timestamp'])) {
        var stopQuestIcon = 'PstopQuest.png'
        if (!noLures && item['lure_expiration'] > Date.now()) {
            stopQuestIcon = 'PstopLured_' + item['lure_id'] + '.png'
        }
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
                '<img src="static/forts/' + stopQuestIcon + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + shinyStr + '.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 3) {
            html = '<div style="position:relative;">' +
                '<img src="static/forts/' + stopQuestIcon + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'rewards/reward_stardust_' + item['quest_dust_amount'] + '.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 2) {
            html = '<div style="position:relative;">' +
                '<img src="static/forts/' + stopQuestIcon + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + iconpath + 'rewards/reward_' + item['quest_item_id'] + '_' + item['quest_reward_amount'] + '.png" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        }
    } else if (!noLures && item['lure_expiration'] > Date.now()) {
        html = '<div><img src="static/forts/PstopLured_' + item['lure_id'] + '.png" style="width:50px;height:72;top:-35px;right:10px;"/><div>'
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-lured-marker',
            html: html
        })
    } else {
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-marker',
            html: '<div><img src="static/forts/Pstop.png" style="width:50px;height:72;top:-35px;right:10px;"/></div>'
        })
    }
    return stopMarker
}

function setupPokestopMarker(item) {
    var pokestopMarkerIcon = getPokestopMarkerIcon(item)
    var marker
    if (item['quest_pokemon_shiny'] === 'true') {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050, virtual: true}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper shiny', autoPan: false, closeOnClick: false, autoClose: false})
    } else {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050, virtual: true}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper normal', autoPan: false, closeOnClick: false, autoClose: false})
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
    var marker = L.marker([item['lat'], item['lon']], {icon: nestMarkerIcon, zIndexOffset: 1020, virtal: true}).bindPopup(nestLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function nestLabel(item) {
    var str = ''
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
        var nestName = ''
        if (item['name'] !== null && item['name'] !== 'Unknown Areaname') {
            nestName = '<b>' + item['name'] + '</b>'
        }
        var pokemonAvg = ''
        if (item['pokemon_avg'] > 0) {
            pokemonAvg = '<div>' + i8ln('Nest Pokemon per hour') + ': ' + item['pokemon_avg'].toFixed(2) + '</div>'
        }
        str += '<center>' +
            '<div>' +
            item.pokemon_name + ' - ' +
            typesDisplay +
            '</div>' +
            nestName + '<br />' +
            '<div>' +
            '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_00.png" style="width:65px;height:65px;"/>' +
            '</div>' +
            pokemonAvg +
            '</center>'
    } else {
        str += '<div align="center" class="marker-nests">' +
            '<img src="static/images/nest-empty.png" align"middle" style="width:36px;height: auto;"/>' +
            '</div>' +
            '<center><b>' + i8ln('No Pokemon - Assign One Below') + '</b></center>'
    }
    if (item.type === 1) {
        str += '<center><div style="margin-bottom:5px; margin-top:5px;">' + i8ln('As found on thesilphroad.com') + '</div></center>'
    }
    if (!noDeleteNests) {
        str += '<i class="fas fa-trash-alt delete-nest" onclick="deleteNest(event);" data-id="' + item['nest_id'] + '"></i>'
    }
    if (!noManualNests) {
        str += '<center><div>' + i8ln('Add Nest') + ' <i class="fas fa-binoculars submit-nest" onclick="openNestModal(event);" data-id="' + item['nest_id'] + '"></i></div></center>'
    }
    str += '<center><div>' +
        '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ')" title="' + i8ln('View in Maps') + '"><i class="fas fa-road"></i> ' + item.lat.toFixed(6) + ', ' + item.lon.toFixed(7) + '</a> - <a href="./?lat=' + item.lat + '&lon=' + item.lon + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</div></center>'
    if ((!noWhatsappLink) && (item.pokemon_id > 0)) {
        str += '<div>' +
            '<center>' +
            '<a href="whatsapp://send?text=%2A' + encodeURIComponent(item.pokemon_name) + '%2A%20nest has been found.%0A%0ALocation:%20https://www.google.com/maps/search/?api=1%26query=' + item.lat + ',' + item.lon + '" data-action="share/whatsapp/share">' + i8ln('Whatsapp Link') + '</a>' +
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

    var marker = L.marker([item['lat'], item['lon']], {icon: icon, zIndexOffset: 1030, virtual: true}).bindPopup(communityLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
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
        str += '<center><div>' + i8ln('Welcome to Teams') + ':<br>'
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
        '<center><div>' + item.size + ' ' + i8ln('Members') + '</div></center>'
    }
    if (item.has_invite_url === 1 && (item.invite_url !== '#' || item.invite_url !== undefined)) {
        str +=
        '<center><div class="button-container">' +
            '<a class="button" href="' + item.invite_url + '"><i class="fas fa-comments"></i> ' + i8ln('Join Now') +
            '</a>' +
        '</div></center>'
    }
    if (!noEditCommunity) {
        str +=
        '<center><div class="button-container">' +
        '<a class="button" onclick="openEditCommunityModal(event);" data-id="' + item.community_id + '" data-title="' + item.title + '" data-description="' + item.description + '" data-invite="' + item.invite_url + '"><i class="fas fa-edit"></i> ' + i8ln('Edit Community') + '</center>' +
            '</a>' +
        '</div></center>'
    }
    if (item.source === 2) {
        str += '<center><div style="margin-bottom:5px; margin-top:5px;">' + i8ln('Join on') + '<a href="https://thesilphroad.com/map#18/' + item.lat + '/' + item.lon + '">thesilphroad.com</a>' + '</div></center>'
    }
    if (!noDeleteCommunity) {
        str += '<i class="fas fa-trash-alt delete-community" onclick="deleteCommunity(event);" data-id="' + item.community_id + '"></i>'
    }
    return str
}

function setupPortalMarker(item) {
    var ts = Math.round(new Date().getTime() / 1000)
    var yesterday = ts - markPortalsAsNew
    if (item.checked === '1') {
        var circle = {
            color: 'red',
            radius: 20,
            fillOpacity: 0.4,
            fillColor: '#f00',
            weight: 1,
            pane: 'portals'
        }
    } else if (item.imported > yesterday) {
        circle = {
            color: 'green',
            radius: 20,
            fillOpacity: 0.4,
            fillColor: '#9f3',
            weight: 1,
            pane: 'portals'
        }
    } else {
        circle = {
            color: 'blue',
            radius: 20,
            fillOpacity: 0.4,
            fillColor: '#00f',
            weight: 1,
            pane: 'portals'
        }
    }
    var marker = L.circle([item['lat'], item['lon']], circle).bindPopup(portalLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)

    addListeners(marker)

    return marker
}

function setupInnMarker(item) {
    var html = ''
    if (item['type'] === '0' || item['type'] === '5') {
        html = '<div><img src="static/forts/hpwu/inn5.png" style="width:33px;height:49px;top:-35px;right:10px;"/><div>'
    } else {
        html = '<div><img src="static/forts/hpwu/inn' + item['type'] + '.png" style="width:33px;height:49px;top:-35px;right:10px;"/><div>'
    }
    var innMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [20, 45],
        popupAnchor: [0, -45],
        className: 'marker-inns',
        html: html
    })
    var marker = L.marker([item['lat'], item['lon']], {icon: innMarkerIcon, zIndexOffset: 1020}).bindPopup(innLabel(item), {autoPan: false, closeOnClick: false, autoClose: false, virtual: true})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function setupFortressMarker(item) {
    var html = '<div><img src="static/forts/hpwu/fortress.png" style="width:33px;height:75px;top:-35px;right:10px;"/><div>'
    var fortressMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [18, 68],
        popupAnchor: [0, -45],
        className: 'marker-fortresses',
        html: html
    })
    var marker = L.marker([item['lat'], item['lon']], {icon: fortressMarkerIcon, zIndexOffset: 1020}).bindPopup(fortressLabel(item), {autoPan: false, closeOnClick: false, autoClose: false, virtual: true})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function setupGreenhouseMarker(item) {
    var html = '<div><img src="static/forts/hpwu/greenhouse.png" style="width:40px;height:40px;top:-35px;right:10px;"/><div>'
    var greenhouseMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [20, 33],
        popupAnchor: [0, -45],
        className: 'marker-greenhouses',
        html: html
    })
    var marker = L.marker([item['lat'], item['lon']], {icon: greenhouseMarkerIcon, zIndexOffset: 1020}).bindPopup(greenhouseLabel(item), {autoPan: false, closeOnClick: false, autoClose: false, virtual: true})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function setupPoiMarker(item) {
    var dot = ''
    if (item.status === '1') {
        dot = 'dot possible-candidate'
    } else if (item.status === '2') {
        dot = 'dot candidate-submitted'
    } else if (item.status === '3') {
        dot = 'dot candidate-declined'
    } else if (item.status === '4') {
        dot = 'dot candidate-resubmit'
    } else if (item.status === '5') {
        dot = 'dot candidate-not-eligible'
    }
    var html = '<div><span class="' + dot + '" style="width:20px;height:20px;"></span></div>'
    var poiMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [10, 16],
        popupAnchor: [8, -10],
        className: 'marker-poi',
        html: html
    })
    var marker = L.marker([item['lat'], item['lon']], {icon: poiMarkerIcon, zIndexOffset: 1020}).bindPopup(poiLabel(item), {autoPan: false, closeOnClick: false, autoClose: false, virtual: true})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function portalLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var imported = formatDate(new Date(item.imported * 1000))
    var str = '<center><div style="font-weight:900;font-size:12px;margin-left:10px;">' + item.name + '</div></center>' +
        '<center><img id="' + item.external_id + '" src="' + item.url + '" align"middle" style="width:175px;height:auto;" onclick="openFullscreenModal(document.getElementById(\'' + item.external_id + '\').src)"/></center>'
    if (!noConvertPortal) {
        str += '<center><div><a class="button" style="margin-top:0px;margin-bottom:3px;" onclick="openConvertPortalModal(event);" data-id="' + item.external_id + '"><i class="fas fa-sync-alt convert-portal"></i>' + ' ' + i8ln('Convert portal') + '</a></div></center>'
    }
    str += '<center><div>' + i8ln('Last updated') + ': ' + updated + '</div></center>' +
        '<center><div>' + i8ln('Date imported') + ': ' + imported + '</div></center>' +
        '<center>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item['lat'] + ',' + item['lon'] + ');" title="' + i8ln('View in Maps') + '">' +
        '<i class="fas fa-road"></i> ' + item['lat'].toFixed(6) + ' , ' + item['lon'].toFixed(7) + '</a> - ' +
        '<a href="./?lat=' + item['lat'] + '&lon=' + item['lon'] + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center>'
    if (!noDeletePortal) {
        str += '<i class="fas fa-trash-alt delete-portal" onclick="deletePortal(event);" data-id="' + item.external_id + '"></i>'
    }
    return str
}

function innLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var str = '<center>' +
        '<div><b>' + item['name'] + '</b></div>' +
        '<div><img src="' + item.url + '" style="width:175px;height:auto;"/></div>' +
        '<div><b>' + i8ln('Submitted by') + ': ' + item['submitted_by'] + '</b></div>' +
        '<div><b>' + i8ln('Submitted at') + ': ' + updated + '</b></div>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item['lat'] + ',' + item['lon'] + ');" title="' + i8ln('View in Maps') + '">' +
        '<i class="fas fa-road"></i> ' + item['lat'].toFixed(6) + ' , ' + item['lon'].toFixed(7) + '</a> - ' +
        '<a href="./?lat=' + item['lat'] + '&lon=' + item['lon'] + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center>'
    if (!noDeleteInn) {
        str += '<i class="fas fa-trash-alt delete-portal" onclick="deleteInn(event);" data-id="' + item['id'] + '"></i>'
    }
    return str
}

function fortressLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var str = '<center>' +
        '<div><b>' + item['name'] + '</b></div>' +
        '<div><img src="' + item.url + '" style="width:175px;height:auto;"/></div>' +
        '<div><b>' + i8ln('Submitted by') + ': ' + item['submitted_by'] + '</b></div>' +
        '<div><b>' + i8ln('Submitted at') + ': ' + updated + '</b></div>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item['lat'] + ',' + item['lon'] + ');" title="' + i8ln('View in Maps') + '">' +
        '<i class="fas fa-road"></i> ' + item['lat'].toFixed(6) + ' , ' + item['lon'].toFixed(7) + '</a> - ' +
        '<a href="./?lat=' + item['lat'] + '&lon=' + item['lon'] + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center>'
    if (!noDeleteFortress) {
        str += '<i class="fas fa-trash-alt delete-portal" onclick="deleteFortress(event);" data-id="' + item['id'] + '"></i>'
    }
    return str
}

function greenhouseLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var str = '<center>' +
        '<div><b>' + item['name'] + '</b></div>' +
        '<div><img src="' + item.url + '" style="width:175px;height:auto;"/></div>' +
        '<div><b>' + i8ln('Submitted by') + ': ' + item['submitted_by'] + '</b></div>' +
        '<div><b>' + i8ln('Submitted at') + ': ' + updated + '</b></div>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item['lat'] + ',' + item['lon'] + ');" title="' + i8ln('View in Maps') + '">' +
        '<i class="fas fa-road"></i> ' + item['lat'].toFixed(6) + ' , ' + item['lon'].toFixed(7) + '</a> - ' +
        '<a href="./?lat=' + item['lat'] + '&lon=' + item['lon'] + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center>'
    if (!noDeleteGreenhouse) {
        str += '<i class="fas fa-trash-alt delete-portal" onclick="deleteGreenhouse(event);" data-id="' + item['id'] + '"></i>'
    }
    return str
}

function poiLabel(item) {
    var updated = formatDate(new Date(item.updated * 1000))
    var str = ''
    var dot = ''
    if (item.status === '1') {
        dot = 'dot possible-candidate'
        str += '<center><div style="font-weight:900;margin-bottom:5px;">' + i8ln('Possible Candidate') + '</div></center>'
    } else if (item.status === '2') {
        dot = 'dot candidate-submitted'
        str += '<center><div style="font-weight:900;margin-bottom:5px;">' + i8ln('Candidate submitted') + '</div></center>'
    } else if (item.status === '3') {
        dot = 'dot candidate-declined'
        str += '<center><div style="font-weight:900;margin-bottom:5px;">' + i8ln('Candidate declined') + '</div></center>'
    } else if (item.status === '4') {
        dot = 'dot candidate-resubmit'
        str += '<center><div style="font-weight:900;margin-bottom:5px;">' + i8ln('Candidate eligible for resubmit') + '</div></center>'
    } else if (item.status === '5') {
        dot = 'dot candidate-not-eligible'
        str += '<center><div style="font-weight:900;margin-bottom:5px;">' + i8ln('Not a eligible candidate') + '</div></center>'
    }
    str += '<center><div><b>' + item.name + '</b></div>' +
        '<div>' + item.description + '</div>'
    if (item.notes) {
        str += '<div><b>' + i8ln('Notes') + ':</b> ' + item.notes + '</div>'
    }
    if (item.poiimageurl && item.poisurroundingurl) {
        str += '<center><img id="poi-image"src="' + item.poiimageurl + '" style="float:left;width:45%;margin-right:1%;margin-bottom:0.5em;" onclick="openFullscreenModal(document.getElementById(\'poi-image\').src)"/></center>'
        str += '<center><img id="poi-surrounding" src="' + item.poisurroundingurl + '" style="float:right;width:45%;margin-right:1%;margin-bottom:0.5em;" onclick="openFullscreenModal(document.getElementById(\'poi-surrounding\').src)"/></center>'
    }
    if (item.poiimageurl && !item.poisurroundingurl) {
        str += '<center><img id="poi-image"src="' + item.poiimageurl + '" style="width:45%;margin-right:1%;margin-bottom:0.5em;" onclick="openFullscreenModal(document.getElementById(\'poi-image\').src)"/></center>'
    }
    if (item.poisurroundingurl && !item.poiimageurl) {
        str += '<center><img id="poi-surrounding" src="' + item.poisurroundingurl + '" style="width:45%;margin-right:1%;margin-bottom:0.5em;" onclick="openFullscreenModal(document.getElementById(\'poi-surrounding\').src)"/></center>'
    }
    if (item.poiimageurl || item.poisurroundingurl) {
        str += '<p style="clear:both;">'
    }
    str += '<span class="' + dot + '"></span>' +
        '<div><b>' + i8ln('Submitted by') + ':</b> ' + item.submitted_by + '</div>'
    if (item.edited_by) {
        str += '<div><b>' + i8ln('Last Edited by') + ':</b> ' + item.edited_by + '</div>'
    }
    str += '<div><b>' + i8ln('Updated at') + ':</b> ' + updated + '</div></center>'
    if (!noDeletePoi) {
        str += '<i class="fas fa-trash-alt delete-poi" onclick="deletePoi(event);" data-id="' + item.poi_id + '"></i>'
    }
    if (!noEditPoi) {
        str += '<center><div><button onclick="openEditPoiModal(event);" data-id="' + item.poi_id + '" data-name="' + item.name + '" data-description="' + item.description + '" data-notes="' + item.notes + '" data-poiimage="' + item.poiimageurl + '" data-poisurrounding="' + item.poisurroundingurl + '" class="convertpoi"><i class="fas fa-edit edit-poi"></i> ' + i8ln('Edit POI') + '</button></div></center>'
    }
    if (!noMarkPoi) {
        str += '<center><div><button onclick="openMarkPoiModal(event);" data-id="' + item.poi_id + '" class="convertpoi"><i class="fas fa-sync-alt convert-poi"></i> ' + i8ln('Mark POI') + '</button></div></center>'
    }
    str += '<center><a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ');" title="' + i8ln('View in Maps') + '"><i class="fas fa-road"></i> ' + item.lat.toFixed(5) + ' , ' + item.lon.toFixed(5) + '</a> - <a href="./?lat=' + item.lat + '&lon=' + item.lon + '&zoom=16"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a></center>'
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

function deleteInn(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var innid = button.data('id')
    if (innid && innid !== '') {
        if (confirm(i8ln('I confirm that this inn does not longer exist. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-inn',
                    'innId': innid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting inn'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="inns-switch"]').click()
                    jQuery('label[for="inns-switch"]').click()
                }
            })
        }
    }
}

function deleteFortress(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var fortressid = button.data('id')
    if (fortressid && fortressid !== '') {
        if (confirm(i8ln('I confirm that this fortress does not longer exist. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-fortress',
                    'fortressId': fortressid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting fortress'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="fortresses-switch"]').click()
                    jQuery('label[for="fortresses-switch"]').click()
                }
            })
        }
    }
}

function deleteGreenhouse(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var greenhouseid = button.data('id')
    if (greenhouseid && greenhouseid !== '') {
        if (confirm(i8ln('I confirm that this greenhouse does not longer exist. This is a permanent deleture'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'delete-greenhouse',
                    'greenhouseId': greenhouseid
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Oops something went wrong.'), i8ln('Error Deleting greenhouse'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="greenhouses-switch"]').click()
                    jQuery('label[for="greenhouses-switch"]').click()
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

function setupSpawnpointMarker(item) {
    var color = ''
    if (item['time'] > 1800) {
        color = 'green'
    } else if (item['time'] > 0) {
        color = 'lightgreen'
    } else {
        color = 'red'
    }

    var rangeCircleOpts = {
        radius: 4,
        weight: 1,
        color: color,
        opacity: 1,
        center: [item['latitude'], item['longitude']],
        fillColor: color,
        fillOpacity: 0.4
    }
    var circle = L.circle([item['latitude'], item['longitude']], rangeCircleOpts).bindPopup(spawnpointLabel(item), {autoPan: false, closeOnclick: false, autoClose: false})
    markersnotify.addLayer(circle)
    addListeners(circle)

    return circle
}

function setupScanLocationMarker(item) {
    var html = ''
    if (item['last_seen'] < Math.round((new Date()).getTime() / 1000) - deviceOfflineAfterSeconds) {
        html = '<img src="static/images/device-offline.png" style="width:36px;height: auto;"/>'
    } else {
        html = '<img src="static/images/device-online.png" style="width:36px;height: auto;"/>'
    }
    var icon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [18, 24],
        popupAnchor: [0, -35],
        className: 'marker-livescan',
        html: html
    })

    var marker = L.marker([item['latitude'], item['longitude']], {icon: icon, zIndexOffset: 1030, virtual: true}).bindPopup(liveScanLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    liveScanGroup.addLayer(marker)

    addListeners(marker)

    return marker
}

function liveScanLabel(item) {
    var lastSeen = formatDate(new Date(item.last_seen * 1000))
    var str = '<center><div style="font-weight:900;font-size:12px;margin-left:10px;">' + item.uuid + '</div></center>' +
        '<center><div>' + i8ln('Current instance') + ': ' + item.instance_name + '</div></center>' +
        '<center><div>' + i8ln('Last seen') + ': ' + lastSeen + '</div></center>'
    return str
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
    var loadRocket = Store.get('showRocket')
    var loadQuests = Store.get('showQuests')
    var loadDustamount = Store.get('showDustAmount')
    var loadNests = Store.get('showNests')
    var loadCommunities = Store.get('showCommunities')
    var loadPortals = Store.get('showPortals')
    var loadInns = Store.get('showInns')
    var loadFortresses = Store.get('showFortresses')
    var loadGreenhouses = Store.get('showGreenhouses')
    var loadPois = Store.get('showPoi')
    var loadNewPortalsOnly = Store.get('showNewPortalsOnly')
    var loadSpawnpoints = Store.get('showSpawnpoints')
    var loadScanLocation = Store.get('showScanLocation')
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
            'pokemon': loadPokemon,
            'lastpokemon': lastpokemon,
            'pokestops': loadPokestops,
            'lures': loadLures,
            'rocket': loadRocket,
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
            'inns': loadInns,
            'lastinns': lastinns,
            'fortresses': loadFortresses,
            'lastfortresses': lastfortresses,
            'greenhouses': loadGreenhouses,
            'lastgreenhouses': lastgreenhouses,
            'lastpokestops': lastpokestops,
            'gyms': loadGyms,
            'lastgyms': lastgyms,
            'exEligible': exEligible,
            'lastslocs': lastslocs,
            'spawnpoints': loadSpawnpoints,
            'lastspawns': lastspawns,
            'scanlocations': loadScanLocation,
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
                    var formStr = ''
                    if (element.quest_pokemon_formid === 0) {
                        formStr = '00'
                    } else {
                        formStr = element.quest_pokemon_formid
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
                            html += '<span style="background:url(' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png) no-repeat;" class="i-icon" ></span>'
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
                        html += '<div class="right-column"><i class="fas fa-binoculars submit-quests"  onClick="openQuestModal(event);" data-id="' + element.external_id + '"></i></div>'
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
                        html += '<div class="right-column"><i class="fas fa-binoculars submit-raid"  onClick="openRaidModal(event);" data-id="' + element.external_id + '"></i></div>'
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
                        html += '<div class="right-column"><i class="fas fa-binoculars submit-quests"  onClick="openQuestModal(event);" data-id="' + element.external_id + '"></i></div>'
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
function renameGymData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var gymId = form.find('.renamegymid').val()
    var gymName = form.find('[name="gym-name"]').val()
    if (gymName && gymName !== '') {
        if (confirm(i8ln('I confirm this is an accurate new name for this gym'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'renamegym',
                    'gymId': gymId,
                    'gymName': gymName
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Please check connectivity or reduce marker settings.'), i8ln('Error changing gym name'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('label[for="gyms-switch"]').click()
                    lastgyms = false
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
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
                    toastr['error'](i8ln('Pokéstop ID got lost somewhere.'), i8ln('Error converting Pokéstop'))
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
function convertPortalToInnData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is a Inn'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportalinn',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error converting to Inn'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastinns = false
                    jQuery('label[for="inns-switch"]').click()
                    jQuery('label[for="inns-switch"]').click()
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function convertPortalToFortressData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is a Fortress'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportalfortress',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error converting to Fortress'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastfortresses = false
                    jQuery('label[for="fortresses-switch"]').click()
                    jQuery('label[for="fortresses-switch"]').click()
                    updateMap()
                    $('.ui-dialog-content').dialog('close')
                }
            })
        }
    }
}
function convertPortalToGreenhouseData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('.convertportalid').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is a Greenhouse'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportalgreenhouse',
                    'portalId': portalId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Portal ID got lost somewhere.'), i8ln('Error converting to Greenhouse'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastgreenhouses = false
                    jQuery('label[for="greenhouses-switch"]').click()
                    jQuery('label[for="greenhouses-switch"]').click()
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
function editPoiData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('.editpoiid').val()
    var poiName = form.find('[name="poi-name"]').val()
    var poiDescription = form.find('[name="poi-description"]').val()
    var poiNotes = form.find('[name="poi-notes"]').val()
    var poiImage = form.find('[name="preview-poi-image"]').attr('src')
    var poiSurrounding = form.find('[name="preview-poi-surrounding"]').attr('src')
    if (typeof poiImage !== 'undefined') {
        poiImage = poiImage.split(',')[1]
    } else {
        poiImage = null
    }
    if (typeof poiSurrounding !== 'undefined') {
        poiSurrounding = poiSurrounding.split(',')[1]
    } else {
        poiSurrounding = null
    }
    if (poiName && poiName !== '' && poiDescription && poiDescription !== '') {
        if (confirm(i8ln('I confirm this is an eligible POI location'))) {
            $('.loader').show()
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 600000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'edit-poi',
                    'poiId': poiId,
                    'poiName': poiName,
                    'poiDescription': poiDescription,
                    'poiNotes': poiNotes,
                    'poiImage': poiImage,
                    'poiSurrounding': poiSurrounding
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Unable to update poi'), i8ln('Error Updating poi'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpois = false
                    updateMap()
                    $('.loader').hide()
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
    var poiNotes = form.find('[name="poi-notes"]').val()
    var poiImage = form.find('[name="preview-poi-image"]').attr('src')
    var poiSurrounding = form.find('[name="preview-poi-surrounding"]').attr('src')
    if (typeof poiImage !== 'undefined') {
        poiImage = poiImage.split(',')[1]
    } else {
        poiImage = null
    }
    if (typeof poiSurrounding !== 'undefined') {
        poiSurrounding = poiSurrounding.split(',')[1]
    } else {
        poiSurrounding = null
    }
    if (poiName && poiName !== '' && poiDescription && poiDescription !== '') {
        if (confirm(i8ln('I confirm this is an eligible POI location'))) {
            $('.loader').show()
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 600000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'poi-add',
                    'lat': lat,
                    'lon': lon,
                    'poiName': poiName,
                    'poiDescription': poiDescription,
                    'poiNotes': poiNotes,
                    'poiImage': poiImage,
                    'poiSurrounding': poiSurrounding
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Make sure all fields are filled.'), i8ln('Error Submitting poi'))
                    toastr.options = toastrOptions
                },
                complete: function complete() {
                    lastpois = false
                    updateMap()
                    $('.loader').hide()
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
        if (confirm(i8ln('I confirm this candidate is submitted to OPR'))) {
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
                    toastr['error'](i8ln('Candidate id got lost somewhere.'), i8ln('Error marking candidate'))
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
        if (confirm(i8ln('I confirm this candidate is declined by OPR'))) {
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
                    toastr['error'](i8ln('Candidate id got lost somewhere.'), i8ln('Error marking candidate'))
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

function markPoiResubmit(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('.markpoiid').val()
    if (poiId && poiId !== '') {
        if (confirm(i8ln('I confirm this candidate is declined by OPR but can be resubmitted as candidate'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'markpoiresubmit',
                    'poiId': poiId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Candidate id got lost somewhere.'), i8ln('Error marking candidate'))
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

function markNotCandidate(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('.markpoiid').val()
    if (poiId && poiId !== '') {
        if (confirm(i8ln('I confirm this is not a eligible candidate to submit to OPR'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'marknotcandidate',
                    'poiId': poiId
                },
                error: function error() {
                    // Display error toast
                    toastr['error'](i8ln('Candidate id got lost somewhere.'), i8ln('Error marking candidate'))
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
            placeholder: i8ln('Pokémon'),
            data: pokeList,
            multiple: true,
            maximumSelectionSize: 2
        })

        var $pokemonTypes = $('.quest-modal #typeCatchList')
        $pokemonTypes.select2({
            placeholder: i8ln('Pokémon type'),
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
            placeholder: i8ln('Pokémon encounter'),
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
    $('.renamepokestop-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Rename Pokéstop'),
        classes: {
            'ui-dialog': 'ui-dialog raid-widget-popup'
        }
    })
}

function openRenameGymModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.renamegymid').val(val)
    $('.renamegym-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Rename Gym'),
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

function openEditPoiModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    var name = $(event.target).data('name')
    var description = $(event.target).data('description')
    var notes = $(event.target).data('notes')
    var poiimageurl = $(event.target).data('poiimage')
    var poisurroundingurl = $(event.target).data('poisurrounding')
    $('.editpoiid').val(val)
    $('#poi-name').val(name)
    $('#poi-description').val(description)
    $('#poi-notes').val(notes)
    $('#preview-poi-image').attr('src', poiimageurl)
    $('#preview-poi-surrounding').attr('src', poisurroundingurl)
    $('.editpoi-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title: i8ln('Edit POI'),
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
        '<button type="button" onclick="manualRaidData(event);" class="submitting-raid"><i class="fas fa-binoculars" style="margin-right:10px;"></i>' + i8ln('Submit Raid') + '</button>' +
        '<button type="button" onclick="$(\'.ui-dialog-content\').dialog(\'close\');" class="close-modal"><i class="fas fa-times" aria-hidden="true"></i></button>' +
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

function openFullscreenModal(image) { // eslint-disable-line no-unused-vars
    var modal = document.getElementById('fullscreenModal')
    var modalImg = document.getElementById('fullscreenimg')
    modal.style.display = 'block'
    modalImg.src = image
    var span = document.getElementsByClassName('close')[0]
    span.onclick = function () {
        modal.style.display = 'none'
    }
}
function closeFullscreenModal() { // eslint-disable-line no-unused-vars
    var modal = document.getElementById('fullscreenModal')
    modal.style.display = 'none'
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
function processInns(i, item) {
    if (!Store.get('showInns')) {
        return false
    }

    if (!mapData.inns[item['id']]) {
        if (item.marker && item.marker.rangeCircle) {
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupInnMarker(item)
        mapData.inns[item['id']] = item
    }
}
function processFortresses(i, item) {
    if (!Store.get('showFortresses')) {
        return false
    }

    if (!mapData.fortresses[item['id']]) {
        if (item.marker && item.marker.rangeCircle) {
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupFortressMarker(item)
        mapData.fortresses[item['id']] = item
    }
}
function processGreenhouses(i, item) {
    if (!Store.get('showGreenhouses')) {
        return false
    }

    if (!mapData.greenhouses[item['id']]) {
        if (item.marker && item.marker.rangeCircle) {
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupGreenhouseMarker(item)
        mapData.greenhouses[item['id']] = item
    }
}
function processPois(i, item) {
    if (!Store.get('showPoi')) {
        return false
    }
    if (!mapData.pois[item['poi_id']]) {
        if (item.marker && item.marker.rangeCircle) {
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
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

    if (Store.get('showRocket') && !item['incident_expiration']) {
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
        if (!!item['incident_expiration'] !== !!item2['incident_expiration']) {
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
    var currentTime = Math.round(new Date().getTime())
    var d = new Date()
    var lastMidnight = ''
    if (mapFork === 'mad') {
        lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    } else {
        lastMidnight = 0
    }

    $.each(mapData.pokestops, function (key, value) {
        // change lured pokestop marker to unlured when expired.
        if (value['lure_expiration'] > 0 && value['lure_expiration'] < currentTime && value['lure_expiration'] > (currentTime - 300000)) {
            if (value.marker && value.marker.rangeCircle) {
                markers.removeLayer(value.marker.rangeCircle)
                markersnotify.removeLayer(value.marker.rangeCircle)
            }
            markers.removeLayer(value.marker)
            markersnotify.removeLayer(value.marker)
            value.marker = setupPokestopMarker(value)
        }
        // change Team Rocket pokestop marker to normal when expired.
        if (value['incident_expiration'] > 0 && value['incident_expiration'] < currentTime && value['incident_expiration'] > (currentTime - 300000)) {
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
    if (Store.get('showRocket')) {
        $.each(mapData.pokestops, function (key, value) {
            if (value['incident_expiration'] < currentTime) {
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

    if (Store.get('exEligible') && (item.park === null || item.park === 0)) {
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

function processScanlocation(i, item) {
    if (!Store.get('showScanLocation')) {
        return false
    }
    setupScanLocationMarker(item)
}

function updateSpawnPoints() {
    if (!Store.get('showSpawnpoints')) {
        return false
    }

    $.each(mapData.spawnpoints, function (key, value) {
        if (map.getBounds().contains(value.marker.getLatLng())) {
            var color = ''
            if (value['time'] > 1800) {
                color = 'green'
            } else if (value['time'] > 0) {
                color = 'lightgreen'
            } else {
                color = 'red'
            }
            value.marker.setStyle({color: color, fillColor: color})
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
    liveScanGroup.clearLayers()
    loadRawData().done(function (result) {
        $.each(result.pokemons, processPokemons)
        $.each(result.pokestops, processPokestops)
        $.each(result.gyms, processGyms)
        $.each(result.spawnpoints, processSpawnpoints)
        $.each(result.scanlocations, processScanlocation)
        $.each(result.nests, processNests)
        $.each(result.communities, processCommunities)
        $.each(result.portals, processPortals)
        $.each(result.inns, processInns)
        $.each(result.fortresses, processFortresses)
        $.each(result.greenhouses, processGreenhouses)
        $.each(result.pois, processPois)
        showInBoundsMarkers(mapData.pokemons, 'pokemon')
        showInBoundsMarkers(mapData.gyms, 'gym')
        showInBoundsMarkers(mapData.pokestops, 'pokestop')
        showInBoundsMarkers(mapData.spawnpoints, 'inbound')

        clearStaleMarkers()

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
        lastinns = result.lastinns
        lastfortresses = result.lastfortresses
        lastgreenhouses = result.lastgreenhouses
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
        if (Store.get('showExCells') && (map.getZoom() > 10)) {
            exLayerGroup.clearLayers()
            showS2Cells(13, {color: 'black', weight: 5, dashOffset: '8', dashArray: '2 6'})
        } else if (Store.get('showExCells') && (map.getZoom() <= 10)) {
            exLayerGroup.clearLayers()
        }
        if (Store.get('showStopCells') && (map.getZoom() > 14)) {
            stopLayerGroup.clearLayers()
            showS2Cells(17, {color: 'black'})
        } else if (Store.get('showStopCells') && (map.getZoom() <= 14)) {
            stopLayerGroup.clearLayers()
        }
        if (Store.get('showGymCells') && (map.getZoom() > 11)) {
            gymLayerGroup.clearLayers()
            showS2Cells(14, {color: 'black', weight: 3, dashOffset: '4', dashArray: '2 6'})
        } else if (Store.get('showGymCells') && (map.getZoom() <= 11)) {
            gymLayerGroup.clearLayers()
        }
    }
}

function drawWeatherOverlay(weather) {
    if (weather) {
        $.each(weather, function (idx, item) {
            if (map.getZoom() <= 13) {
                $.each(weatherMarkers, function (index, marker) {
                    markers.addLayer(marker)
                })
            }
            weatherArray.push(S2.idToCornerLatLngs(item.s2_cell_id))
            var poly = L.polygon(weatherArray, {
                color: weatherColors[item.condition],
                opacity: 1,
                weight: 1,
                fillOpacity: 0.1,
                fillColor: weatherColors[item.condition]
            })
            var bounds = new L.LatLngBounds()
            var i, center

            for (i = 0; i < weatherArray[0].length; i++) {
                bounds.extend(weatherArray[0][i])
            }
            center = bounds.getCenter()
            var icon = L.icon({
                iconSize: [25, 25],
                iconAnchor: [13, 13],
                iconUrl: 'static/weather/a-' + item.condition + '.png'
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
    $('.raid-countdown').each(function (index, element) {
        var disappearsAt = getTimeUntil(parseInt(element.getAttribute('disappears-at')))
        var hours = disappearsAt.hour
        var minutes = disappearsAt.min
        var seconds = disappearsAt.sec
        var timestring = ''
        if (disappearsAt.time < disappearsAt.now) {
            if (element.hasAttribute('start')) {
                timestring = i8ln('started')
            } else if (element.hasAttribute('end')) {
                timestring = i8ln('ended')
            } else {
                timestring = i8ln('expired')
            }
        } else {
            if (hours > 0) {
                timestring += hours + 'h'
            }
            timestring += lpad(minutes, 2, 0) + 'm'
            timestring += lpad(seconds, 2, 0) + 's'
        }
        $(element).text(timestring)
    })
}

function generateRemainingTimer(timestamp, type) {
    var disappearsAt = getTimeUntil(parseInt(timestamp))
    var hours = disappearsAt.hour
    var minutes = disappearsAt.min
    var seconds = disappearsAt.sec
    var timestring = ''
    if (disappearsAt.time < disappearsAt.now) {
        if (type === 'start') {
            timestring = i8ln('started')
        } else if (type === 'end') {
            timestring = i8ln('ended')
        } else {
            timestring = i8ln('expired')
        }
    } else {
        if (hours > 0) {
            timestring += hours + 'h'
        }
        timestring += lpad(minutes, 2, 0) + 'm'
        timestring += lpad(seconds, 2, 0) + 's'
    }
    return timestring
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
            var mapBox
            if (mBoxKey === '') {
                mapBox = false
            } else {
                mapBox = true
            }
            var mapBoxStyle = value.includes('Mapbox')
            if (!googleStyle && !mapBoxStyle) {
                styleList.push({
                    id: key,
                    text: i8ln(value)
                })
            } else if (googleMaps && googleStyle) {
                styleList.push({
                    id: key,
                    text: i8ln(value)
                })
            } else if (mapBox && mapBoxStyle) {
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

    $switchOpenGymsOnly = $('#open-gyms-only-switch')

    $switchOpenGymsOnly.on('change', function () {
        Store.set('showOpenGymsOnly', this.checked)
        lastgyms = false
        updateMap()
    })

    $switchActiveRaids = $('#active-raids-switch')

    $switchActiveRaids.on('change', function () {
        Store.set('activeRaids', this.checked)
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

    $selectIconStyle = $('#icon-style')

    $selectIconStyle.select2({
        placeholder: 'Select Style',
        minimumResultsForSearch: Infinity
    })
    $selectIconStyle.on('change', function (e) {
        Store.set('icons', this.value)
        var port = ''
        if (window.location.port.length > 0) {
            port = ':' + window.location.port
        }
        var path = window.location.protocol + '//' + window.location.hostname + port + window.location.pathname
        var r = new RegExp('^(?:[a-z]+:)?//', 'i')
        iconpath = r.test(Store.get('icons')) ? Store.get('icons') : path + Store.get('icons')

        redrawPokemon(mapData.pokemons)
        jQuery('label[for="pokestops-switch"]').click()
        jQuery('label[for="pokestops-switch"]').click()
        jQuery('label[for="raids-switch"]').click()
        jQuery('label[for="raids-switch"]').click()
    })
    $selectIconStyle.val(Store.get('icons')).trigger('change')
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
    window.setInterval(updateMap, queryInterval)
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
                } else if (storageKey === 'showRocket') {
                    lastpokestops = false
                } else if (storageKey === 'showQuests') {
                    lastpokestops = false
                } else if (storageKey === 'showPortals') {
                    lastportals = false
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
        var gymRaidsFilterWrapper = $('#gyms-raid-filter-wrapper')
        if (this.checked) {
            lastgyms = false
            wrapper.show(options)
            gymRaidsFilterWrapper.show(options)
        } else {
            lastgyms = false
            wrapper.hide(options)
            if (!Store.get('showGyms')) {
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
        var gymRaidsFilterWrapper = $('#gyms-raid-filter-wrapper')
        if (this.checked) {
            lastgyms = false
            wrapper.show(options)
            gymRaidsFilterWrapper.show(options)
        } else {
            lastgyms = false
            wrapper.hide(options)
            if (!Store.get('showRaids')) {
                gymRaidsFilterWrapper.hide(options)
            }
        }
        buildSwitchChangeListener(mapData, ['gyms'], 'showGyms').bind(this)()
    })
    $('#nests-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#nest-filter-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
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
    $('#inns-switch').change(function () {
        lastinns = false
        buildSwitchChangeListener(mapData, ['inns'], 'showInns').bind(this)()
    })
    $('#fortresses-switch').change(function () {
        lastfortresses = false
        buildSwitchChangeListener(mapData, ['fortresses'], 'showFortresses').bind(this)()
    })
    $('#greenhouses-switch').change(function () {
        lastgreenhouses = false
        buildSwitchChangeListener(mapData, ['greenhouses'], 'showGreenhouses').bind(this)()
    })

    $('#s2-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#s2-switch-wrapper')
        if (this.checked) {
            wrapper.show(options)
            if (Store.get('showExCells')) {
                showS2Cells(13, {color: 'black', weight: 5, dashOffset: '8', dashArray: '2 6'})
            }
            if (Store.get('showGymCells')) {
                showS2Cells(14, {color: 'black', weight: 3, dashOffset: '4', dashArray: '2 6'})
            }
            if (Store.get('showStopCells')) {
                showS2Cells(17, {color: 'black'})
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
            showS2Cells(13, {color: 'black', weight: 5, dashOffset: '8', dashArray: '2 6'})
        } else {
            exLayerGroup.clearLayers()
        }
    })

    $('#s2-level14-switch').change(function () {
        Store.set('showGymCells', this.checked)
        if (this.checked) {
            showS2Cells(14, {color: 'black', weight: 3, dashOffset: '4', dashArray: '2 6'})
        } else {
            gymLayerGroup.clearLayers()
        }
    })

    $('#s2-level17-switch').change(function () {
        Store.set('showStopCells', this.checked)
        if (this.checked) {
            showS2Cells(17, {color: 'black'})
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
    $('#scan-location-switch').change(function () {
        Store.set('showScanLocation', this.checked)
        if (this.checked) {
        } else {
            liveScanGroup.clearLayers()
        }
    })
    $('#nest-polygon-switch').change(function () {
        Store.set('showNestPolygon', this.checked)
        if (this.checked) {
            buildNestPolygons()
        } else {
            nestLayerGroup.clearLayers()
        }
    })

    $('#raid-timer-switch').change(function () {
        Store.set('showRaidTimer', this.checked)
        lastgyms = false
        buildSwitchChangeListener(mapData, ['gyms'], 'showRaidTimer').bind(this)()
    })

    $('#rocket-timer-switch').change(function () {
        Store.set('showRocketTimer', this.checked)
        lastpokestops = false
        jQuery('label[for="rocket-switch"]').click()
        jQuery('label[for="rocket-switch"]').click()
        buildSwitchChangeListener(mapData, ['pokestops'], 'showRocketTimer').bind(this)()
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
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('label[for="rocket-switch"]').click()
        }
        var options = {
            'duration': 500
        }
        var wrapper = $('#quests-filter-wrapper')
        if (this.checked) {
            lastpokestops = false
            wrapper.hide(options)
            updateMap()
        } else {
            lastpokestops = false
            updateMap()
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showLures').bind(this)()
    })

    $('#rocket-switch').change(function () {
        Store.set('showRocket', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            $('#quests-switch').prop('checked', false)
        }
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        var options = {
            'duration': 500
        }
        var wrapper = $('#quests-filter-wrapper')
        var rocketWrapper = $('#rocket-wrapper')
        if (this.checked) {
            lastpokestops = false
            wrapper.hide(options)
            rocketWrapper.show(options)
            updateMap()
        } else {
            lastpokestops = false
            rocketWrapper.hide(options)
            updateMap()
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showRocket').bind(this)()
    })

    $('#quests-switch').change(function () {
        Store.set('showQuests', this.checked)
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('label[for="rocket-switch"]').click()
        }
        var options = {
            'duration': 1000
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
