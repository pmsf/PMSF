//
// Global map.js variables
//

var $selectExclude
var $selectExcludeMinIV
var $selectPokemonNotify
var $selectRarityNotify
var $textPerfectionNotify
var $textLevelNotify
var $textMinLLRank
var $textMinGLRank
var $textMinULRank
var $textMinIV
var $textMinLevel
var $raidNotify
var $selectStyle
var $selectGymMarkerStyle
var $selectLocationIconMarker
var $selectDirectionProvider
var $questsExcludePokemon
var $questsExcludeItem
var $questsExcludeEnergy
var $questsExcludeCandy
var $excludeGrunts
var $excludeRaidboss
var $excludeRaidegg
var $selectIconStyle
var $selectRewardIconStyle

var pokemonTable
var rewardTable
var shinyTable

var language = document.documentElement.lang === '' ? 'en' : document.documentElement.lang
var languageSite = 'en'
var idToPokemon = {}
var idToItem = {}
var idToGrunt = {}
var idToRaidegg = {}
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
var questsExcludedEnergy = []
var questsExcludedCandy = []
var excludedGrunts = []
var excludedRaidboss = []
var excludedRaidegg = []
var notifiedMinPerfection = null
var notifiedMinLevel = null
var minIV = null
var minLLRank = null
var minGLRank = null
var minULRank = null
var prevMinIV = null
var prevMinLevel = null
var prevMinLLRank = null
var prevMinGLRank = null
var prevMinULRank = null
var onlyPokemon = 0
var directionProvider

var buffer = []
var reincludedPokemon = []
var reincludedQuestsPokemon = []
var reincludedQuestsItem = []
var reincludedQuestsEnergy = []
var reincludedQuestsCandy = []
var reincludedGrunts = []
var reincludedRaidboss = []
var reincludedRaidegg = []
var reids = []
var qpreids = []
var qireids = []
var qereids = []
var qcreids = []
var greids = []
var rbreids = []
var rereids = []
var dustamount
var reloaddustamount
var xpamount
var reloadxpamount
var nestavg
var toastdelayslider

var L
var map
var markers
var markersnotify
var _oldlayer = 'openstreetmap'
var rawDataIsLoading = false
var searchDelay
var locationMarker
var rangeMarkers = ['pokemon', 'pokestop', 'gym']
var placementRangeMarkers = ['pokestop', 'gym']
var storeZoom = true
var moves
var pokedex

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
var itemList = []
var gruntList = []
var questtypeList = []
var rewardtypeList = []
var conditiontypeList = []
var raideggList = []
var gymId

var deviceLocation = []

var assetsPath = 'static/sounds/'
var iconpath = null

var gymTypes = ['Uncontested', 'Mystic', 'Valor', 'Instinct']

var triggerGyms = Store.get('triggerGyms')
var onlyTriggerGyms
var noExGyms

createjs.Sound.registerSound('static/sounds/ding.mp3', 'ding')

var pokemonTypes = ['unset', 'Normal', 'Fighting', 'Flying', 'Poison', 'Ground', 'Rock', 'Bug', 'Ghost', 'Steel', 'Fire', 'Water', 'Grass', 'Electric', 'Psychic', 'Ice', 'Dragon', 'Dark', 'Fairy']
var genderType = ['♂', '♀', '⚲']
var throwType = JSON.parse('{"10": "Nice", "11": "Great", "12": "Excellent"}')
var gruntCharacterTypes = ['unset', 'Team Leader(s)', 'Team GO Rocket Grunt(s)', 'Arlo', 'Cliff', 'Sierra', 'Giovanni']
var weatherTexts = ['None', 'Clear', 'Rain', 'Partly Cloudy', 'Cloudy', 'Windy', 'Snow', 'Fog']
var weatherBoostedTypes = ['None', 'Grass, Ground, Fire', 'Water, Electric, Bug', 'Normal, Rock', 'Fairy, Fighting, Poison', 'Dragon, Flying, Psychic', 'Ice, Steel', 'Dark, Ghost']

var weatherLayerGroup = new L.LayerGroup()
var weatherArray = []
var weatherPolys = []
var weatherMarkers = []
var weatherColors
var s2Colors
var S2
var exLayerGroup = new L.LayerGroup()
var gymLayerGroup = new L.LayerGroup()
var pokemonLayerGroup = new L.LayerGroup()
var stopLayerGroup = new L.LayerGroup()
var scanAreaGroup = new L.LayerGroup()
var liveScanGroup = new L.LayerGroup()
var nestLayerGroup = new L.LayerGroup()
var scanAreas = []
/*
 text place holders:
 <pkm> - pokemon name
 <lv>  - pokemon level
 <prc> - iv in percent without percent symbol
 <atk> - attack as number
 <def> - defense as number
 <sta> - stamnia as number
 */
var notifyIvTitle = '<pkm>, Level: <lv>, IV: <prc>% (<atk>/<def>/<sta>)'
var notifyNoIvTitle = '<pkm>'

/*
 text place holders:
 <dist>  - disappear time
 <udist> - time until disappear
 */
var notifyText = i8ln('Disappears at') + ' <dist> (<udist>)'

var OpenStreetMapProvider = window.GeoSearch.OpenStreetMapProvider
var searchProvider = new OpenStreetMapProvider()
//
// Extras
//

var _mapLoaded = false

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
if (copyrightSafe) {
    var setPokemon = Store.get('iconsArray')
    setPokemon.pokemon = 'static/sprites/'
    Store.set('iconsArray', setPokemon)
} else if (localStorage.hasOwnProperty('iconsArray')) {
    var oldIconsArray = Store.get('iconsArray')
    for (const [key, value] of Object.entries(iconFolderArray)) {
        if (Object.prototype.toString.call(value) === '[object String]') {
            oldIconsArray[key] = iconFolderArray[key]
        } else if ((key in oldIconsArray === false) || (Object.values(iconFolderArray[key]).includes(oldIconsArray[key]) === false)) {
            oldIconsArray[key] = iconFolderArray[key][Object.keys(iconFolderArray[key])[0]]
        }
    }
    Store.set('iconsArray', oldIconsArray)
} else {
    for (const [key, value] of Object.entries(iconFolderArray)) {
        if (Object.prototype.toString.call(value) === '[object Object]') {
            iconFolderArray[key] = iconFolderArray[key][Object.keys(iconFolderArray[key])[0]]
        }
    }
    Store.set('iconsArray', iconFolderArray)
}
if (forcedTileServer) {
    Store.set('map_style', 'tileserver')
}
if (noRaids && Store.get('showRaids')) {
    Store.set('showRaids', false)
}
if (!noDarkMode && Store.get('darkMode')) {
    enableDarkMode()
}
if (noQuestsARTaskToggle) {
    Store.set('showQuestsWithTaskAR', true)
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
    $('#exclude-pokemon .pokemon-list .pokemon-icon-sprite[data-value="' + id + '"]').addClass('active')
    clearStaleMarkers()
}

function notifyAboutPokemon(id) { // eslint-disable-line no-unused-vars
    $selectPokemonNotify.val(
        $selectPokemonNotify.val().split(',').concat(id).join(',')
    ).trigger('change')
    $('#notify-pokemon .pokemon-list .pokemon-icon-sprite[data-value="' + id + '"]').addClass('active')
}

function removePokemonMarker(encounterId) { // eslint-disable-line no-unused-vars
    if (mapData.pokemons[encounterId].marker.rangeCircle) {
        markers.removeLayer(mapData.pokemons[encounterId].marker.rangeCircle)
        delete mapData.pokemons[encounterId].marker.rangeCircle
    }
    markers.removeLayer(mapData.pokemons[encounterId].marker)
    mapData.pokemons[encounterId].hidden = true
}

function removePokestopMarker(pokestopId) { // eslint-disable-line no-unused-vars
    if (mapData.pokestops[pokestopId].marker.placementRangeCircle) {
        markers.removeLayer(mapData.pokestops[pokestopId].marker.placementRangeCircle)
        delete mapData.pokestops[pokestopId].marker.placementRangeCircle
    }
    if (mapData.pokestops[pokestopId].marker.rangeCircle) {
        markers.removeLayer(mapData.pokestops[pokestopId].marker.rangeCircle)
        delete mapData.pokestops[pokestopId].marker.rangeCircle
    }
    markers.removeLayer(mapData.pokestops[pokestopId].marker)
    mapData.pokestops[pokestopId].hidden = true
}

function removeGymMarker(gymId) { // eslint-disable-line no-unused-vars
    if (mapData.gyms[gymId].marker.placementRangeCircle) {
        markers.removeLayer(mapData.gyms[gymId].marker.placementRangeCircle)
        delete mapData.gyms[gymId].marker.placementRangeCircle
    }
    if (mapData.gyms[gymId].marker.rangeCircle) {
        markers.removeLayer(mapData.gyms[gymId].marker.rangeCircle)
        delete mapData.gyms[gymId].marker.rangeCircle
    }
    markers.removeLayer(mapData.gyms[gymId].marker)
    mapData.gyms[gymId].hidden = true
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
        zoomControl: false,
        preferCanvas: true,
        worldCopyJump: true,
        updateWhenZooming: false,
        updateWhenIdle: true,
        attributionControl: false,
        layers: [weatherLayerGroup, exLayerGroup, gymLayerGroup, pokemonLayerGroup, stopLayerGroup, scanAreaGroup, liveScanGroup, nestLayerGroup]
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
        updateS2Overlay()
        if (storeZoom === true) {
            Store.set('zoomLevel', map.getZoom())
        } else {
            storeZoom = true
        }
        if (this.getZoom() > 13) {
            // hide weather markers
            $.each(weatherMarkers, function (index, marker) {
                markersnotify.removeLayer(marker)
            })
            // show header weather
            $('#currentWeather').fadeIn()
        } else {
            // show weather markers
            $.each(weatherMarkers, function (index, marker) {
                markersnotify.addLayer(marker)
            })
            // hide header weather
            $('#currentWeather').fadeOut()
            // reset header weather
            $('#currentWeather').data('current-cell', '')
            $('#currentWeather').data('updated', '')
            $('#currentWeather').html('')
        }
    })

    map.createPane('portals')
    map.getPane('portals').style.zIndex = 400
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
    createSnow()
    createFireworks()
    createHearts()
    updateUser()

    map.on('moveend', function () {
        updateS2Overlay()
    })

    map.on('click', function (e) {
        if ($('.submit-on-off-button').hasClass('on')) {
            var submitModal = new bootstrap.Modal(document.getElementById('submitModal'), {})
            updateS2Overlay()
            $('.submitLatitude').val(e.latlng.lat)
            $('.submitLongitude').val(e.latlng.lng)
            submitModal.show()
            $('#submitModal').on('shown.bs.modal', function (event) {
                $('#submitModal .pokemon-list-cont').each(function (index) {
                    $(this).attr('id', 'pokemon-list-cont-6' + index)
                    var options = {
                        valueNames: ['name', 'types', 'id', 'genid', 'genname', 'forms']
                    }
                    var monList = new List('pokemon-list-cont-6' + index, options) // eslint-disable-line no-unused-vars
                })
            })
        }
    })

    $('#pokemon-icon-size').on('change', function () {
        Store.set('pokemonIconSize', this.value)
        redrawPokemon(mapData.pokemons)
    })

    $('#pokemon-icon-notify-size').on('change', function () {
        Store.set('iconNotifySizeModifier', this.value)
        redrawPokemon(mapData.pokemons)
    })

    $('#team-gyms-only-switch').on('change', function () {
        Store.set('showTeamGymsOnly', this.value)
        lastgyms = false
        updateMap()
    })

    $('#last-update-gyms-switch').on('change', function () {
        Store.set('showLastUpdatedGymsOnly', this.value)
        lastgyms = false
        updateMap()
    })

    $('#min-level-gyms-filter-switch').on('change', function () {
        Store.set('minGymLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $('#max-level-gyms-filter-switch').on('change', function () {
        Store.set('maxGymLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $('#min-level-raids-filter-switch').on('change', function () {
        Store.set('minRaidLevel', this.value)
        lastgyms = false
        updateMap()
    })

    $('#max-level-raids-filter-switch').on('change', function () {
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
        var userzoom = 'zoom' in position ? position.zoom : zoom

        var latlng = new L.LatLng(lat, lng)
        locationMarker.setLatLng(latlng)
        map.setView(latlng, userzoom)
    }

    $.getJSON('static/dist/data/searchmarkerstyle.min.json').done(function (data) {
        searchMarkerStyles = data

        $selectLocationIconMarker.on('change', function (e) {
            Store.set('locationMarkerStyle', this.value)
            updateLocationMarker(this.value)
        })

        $selectLocationIconMarker.val(Store.get('locationMarkerStyle')).trigger('change')
    })

    _mapLoaded = true
    $('.loader').hide()
}

function toggleFullscreenMap() { // eslint-disable-line no-unused-vars
    map.toggleFullscreen()
}

// dynamic map style chooses mapboxPogo or mapboxPogoDark depending on client time
var currentDate = new Date()
var currentHour = currentDate.getHours()
var mapboxPogoDynamicConfig = currentHour >= 6 && currentHour < 19 ? getTileLayerConfig('mapboxPogo') : getTileLayerConfig('mapboxPogoDark') // eslint-disable-line no-unused-vars

function getTileLayerConfig(selectedStyle) {
    var tileLayerConfig
    switch (selectedStyle) {
        case 'googlemapssat':
            tileLayerConfig = L.gridLayer.googleMutant({type: 'satellite'})
            break
        case 'googlemapsroad':
            tileLayerConfig = L.gridLayer.googleMutant({type: 'roadmap'})
            break
        case 'mapboxPogoDynamic':
            tileLayerConfig = mapboxPogoDynamicConfig
            break
        default:
            if (selectedStyle.includes('mapbox')) {
                tileLayerConfig = L.tileLayer(mapStyleList[selectedStyle]['url'] + mapStyleList[selectedStyle]['key'], {
                    attribution: mapStyleList[selectedStyle]['attribution'],
                    maxZoom: maxZoom,
                    maxNativeZoom: mapStyleList[selectedStyle]['maxnativezoom']
                })
                break
            }
            tileLayerConfig = L.tileLayer(mapStyleList[selectedStyle]['url'], {
                attribution: mapStyleList[selectedStyle]['attribution'],
                maxZoom: maxZoom,
                maxNativeZoom: mapStyleList[selectedStyle]['maxnativezoom']
            })
    }
    return tileLayerConfig
}

function setTileLayer(layername) {
    if (map.hasLayer(getTileLayerConfig(_oldlayer)) && getTileLayerConfig(_oldlayer) !== getTileLayerConfig(layername)) {
        map.removeLayer(getTileLayerConfig(_oldlayer))
    }
    map.addLayer(getTileLayerConfig(layername))
    $('.gmnoprint, .gm-style-cc').hide()
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

function cellLabel(stopCount, sponsoredStopCount, sponsoredGymCount, gymCount, totalCount, possibleCandidatePoiCount, submittedPoiCount, declinedPoiCount, resubmittedPoiCount, notEligiblePoiCount, totalPoiCount) {
    var html = ''
    var count = ''
    if (totalCount >= 20 && gymCount >= 3) {
        html += '<div><center><b>' + i8ln('Max amount of Gyms reached') + '</b></center></div>'
    } else if (totalCount >= 6 && gymCount === 2) {
        count = 20 - totalCount
        html += '<div><center><b>' + count + ' ' + i8ln('more Pokéstop(s) until new gym') + '</b></center></div>'
    } else if (totalCount >= 2 && gymCount === 1) {
        count = 6 - totalCount
        html += '<div><center><b>' + count + ' ' + i8ln('more Pokéstop(s) until new gym') + '</b></center></div>'
    } else if (totalCount < 2 && gymCount === 0) {
        count = 2 - totalCount
        html += '<div><center><b>' + count + ' ' + i8ln('more Pokéstop(s) until new gym') + '</b></center></div>'
    }

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
    return html
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
        }
        if (cell.level === 14 && Store.get('showPoi')) {
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

        var filledStyle = {color: 'black', fillOpacity: 0.0}
        if (cell.level === 14 && Store.get('showPokestops') && Store.get('showGyms')) {
            if ((totalCount === 1 && gymCount === 0) || (totalCount === 5 && gymCount === 1) || (totalCount === 19 && gymCount === 2)) {
                filledStyle = {fillColor: s2Colors[1], fillOpacity: 0.3}
            } else if ((totalCount === 4 && gymCount === 1) || (totalCount === 18 && gymCount === 2)) {
                filledStyle = {fillColor: s2Colors[2], fillOpacity: 0.3}
            } else if (totalCount >= 20 && gymCount >= 3) {
                filledStyle = {fillColor: s2Colors[3], fillOpacity: 0.3}
            }
        } else if (cell.level === 17) {
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

        const poly = L.polygon(vertices, Object.assign({
            pane: 'portals',
            color: 'black',
            opacity: 0.5,
            weight: 0.5,
            fillOpacity: 0.0
        }, style, filledStyle))

        if (cell.level === 14 && Store.get('showPokestops') && Store.get('showGyms') && !$('.submit-on-off-button').hasClass('on')) {
            poly.bindPopup(cellLabel(stopCount, sponsoredStopCount, sponsoredGymCount, gymCount, totalCount, possibleCandidatePoiCount, submittedPoiCount, declinedPoiCount, resubmittedPoiCount, notEligiblePoiCount, totalPoiCount), {
                autoPan: false,
                closeOnClick: false,
                autoClose: false
            })
        }

        if (cell.level === 13) {
            exLayerGroup.addLayer(poly)
        } else if (cell.level === 14) {
            gymLayerGroup.addLayer(poly)
        } else if (cell.level === 15) {
            pokemonLayerGroup.addLayer(poly)
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
    if (!Store.get(['showScanPolygon']) || geoJSONfile.trim() === '') {
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
    if (!Store.get(['showNestPolygon']) || !Store.get(['showNests']) || nestGeoJSONfile.trim() === '') {
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

function createSnow() {
    if (!letItSnow) {
        return false
    }
    var d = new Date()
    if (d.getMonth() === 11 && d.getDate() >= 24 && d.getDate() <= 26) {
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
}

function createFireworks() {
    if (!makeItBang) {
        return false
    }
    var d = new Date()
    if ((d.getMonth() === 11 && d.getDate() === 31) || (d.getMonth() === 0 && d.getDate() === 1)) {
        const fireworks = '<div class="pyro">' +
            '<div class="before"></div>' +
            '<div class="after"></div>' +
            '</div>'
        $('#map').append(fireworks)
    }
}

function createHearts() {
    if (!showYourLove) {
        return false
    }
    var d = new Date()
    if (d.getMonth() === 1 && d.getDate() === 14) {
        const valentine = '<canvas id="valentine-canvas"></canvas>'
        $('#map').append(valentine)
        var hearts = {
            heartHeight: 25,
            heartWidth: 25,
            hearts: [],
            heartImage: 'static/images/misc/heart-0.png',
            heartImageAlt: 'static/images/misc/heart-1.png',
            maxHearts: 50,
            minScale: 0.4,
            draw: function () {
                this.setCanvasSize()
                this.ctx.clearRect(0, 0, this.w, this.h)
                for (var i = 0; i < this.hearts.length; i++) {
                    var heart = this.hearts[i]
                    heart.image = new Image()
                    heart.image.style.height = heart.height
                    if (i % 2 === 1) {
                        heart.image.src = this.heartImageAlt
                    } else {
                        heart.image.src = this.heartImage
                    }
                    this.ctx.globalAlpha = heart.opacity
                    this.ctx.drawImage(heart.image, heart.x, heart.y, heart.width, heart.height)
                }
                this.move()
            },
            move: function () {
                for (var b = 0; b < this.hearts.length; b++) {
                    var heart = this.hearts[b]
                    heart.y += heart.ys
                    if (heart.y > this.h) {
                        heart.x = Math.random() * this.w
                        heart.y = -1 * this.heartHeight
                    }
                }
            },
            setCanvasSize: function () {
                this.canvas.width = window.innerWidth
                this.canvas.height = window.innerHeight
                this.w = this.canvas.width
                this.h = this.canvas.height
            },
            initialize: function () {
                this.canvas = $('#valentine-canvas')[0]
                if (!this.canvas.getContext) {
                    return
                }
                this.setCanvasSize()
                this.ctx = this.canvas.getContext('2d')
                for (var a = 0; a < this.maxHearts; a++) {
                    var scale = (Math.random() * (1 - this.minScale)) + this.minScale
                    this.hearts.push({
                        x: Math.random() * this.w,
                        y: Math.random() * this.h,
                        ys: Math.random() + 1,
                        height: scale * this.heartHeight,
                        width: scale * this.heartWidth,
                        opacity: scale
                    })
                }
                setInterval($.proxy(this.draw, this), 30)
            }
        }
        hearts.initialize()
    }
}

function enableDarkMode() {
    $('body').addClass('dark')
}

function disableDarkMode() {
    $('body').removeClass('dark')
}

function initSidebar() {
    $('#gyms-switch').prop('checked', Store.get('showGyms'))
    $('#nests-switch').prop('checked', Store.get('showNests'))
    $('#communities-switch').prop('checked', Store.get('showCommunities'))
    $('#portals-switch').prop('checked', Store.get('showPortals'))
    $('#poi-switch').prop('checked', Store.get('showPoi'))
    $('#s2-switch').prop('checked', Store.get('showCells'))
    $('#s2-switch-wrapper').toggle(Store.get('showCells'))
    $('#placement-ranges-switch').prop('checked', Store.get('showPlacementRanges'))
    $('#s2-level13-switch').prop('checked', Store.get('showExCells'))
    $('#s2-level14-switch').prop('checked', Store.get('showGymCells'))
    $('#s2-level15-switch').prop('checked', Store.get('showPokemonCells'))
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
    $('#eventstops-wrapper').toggle(Store.get('showEventStops'))
    $('#active-raids-switch').prop('checked', Store.get('activeRaids'))
    $('#min-level-gyms-filter-switch').val(Store.get('minGymLevel'))
    $('#max-level-gyms-filter-switch').val(Store.get('maxGymLevel'))
    $('#min-level-raids-filter-switch').val(Store.get('minRaidLevel'))
    $('#max-level-raids-filter-switch').val(Store.get('maxRaidLevel'))
    $('#last-update-gyms-switch').val(Store.get('showLastUpdatedGymsOnly'))
    $('#pokemon-switch').prop('checked', Store.get('showPokemon'))
    $('#pokemon-filter-wrapper').toggle(Store.get('showPokemon'))
    $('#nest-filter-wrapper').toggle(Store.get('showNests'))
    $('#missing-iv-only-switch').prop('checked', Store.get('showMissingIVOnly'))
    $('#big-karp-switch').prop('checked', Store.get('showBigKarp'))
    $('#tiny-rat-switch').prop('checked', Store.get('showTinyRat'))
    $('#no-zero-iv-switch').prop('checked', Store.get('showZeroIv'))
    $('#no-hundo-iv-switch').prop('checked', Store.get('showHundoIv'))
    $('#no-xxs-switch').prop('checked', Store.get('showXXS'))
    $('#no-xxl-switch').prop('checked', Store.get('showXXL'))
    $('#no-independant-pvp-switch').prop('checked', Store.get('showIndependantPvpAndStats'))
    $('#despawn-time-type-select').val(Store.get('showDespawnTimeType'))
    $('#pokemon-gender-select').val(Store.get('showPokemonGender'))
    $('#pokestops-switch').prop('checked', Store.get('showPokestops'))
    $('#allPokestops-switch').prop('checked', Store.get('showAllPokestops'))
    $('#pokestops-filter-wrapper').toggle(Store.get('showPokestops'))
    $('#lures-switch').prop('checked', Store.get('showLures'))
    $('#rocket-switch').prop('checked', Store.get('showRocket'))
    $('#eventstops-switch').prop('checked', Store.get('showEventStops'))
    $('#quests-switch').prop('checked', Store.get('showQuests'))
    $('#quests-with_ar').prop('checked', Store.get('showQuestsWithTaskAR'))
    $('#quests-filter-wrapper').toggle(Store.get('showQuests'))
    $('#dustvalue').text(Store.get('showDustAmount'))
    $('#dustrange').val(Store.get('showDustAmount'))
    $('#xpvalue').text(Store.get('showXpAmount'))
    $('#xprange').val(Store.get('showXpAmount'))
    $('#nestrange').val(Store.get('showNestAvg'))
    $('#nestavg').text(Store.get('showNestAvg'))
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
    $('#eventstops-timer-switch').prop('checked', Store.get('showEventStopsTimer'))
    $('#toast-switch').prop('checked', Store.get('showToast'))
    $('#toast-delay-slider').val(Store.get('toastPokemonDelay'))
    $('#toast-switch-wrapper').toggle(Store.get('showToast'))
    $('#sound-switch').prop('checked', Store.get('playSound'))
    $('#cries-switch').prop('checked', Store.get('playCries'))
    $('#cries-switch-wrapper').toggle(Store.get('playSound'))
    $('#cries-type-filter-wrapper').toggle(Store.get('playCries'))
    $('#bounce-switch').prop('checked', Store.get('remember_bounce_notify'))
    $('#notification-switch').prop('checked', Store.get('remember_notification_notify'))
    $('#dark-mode-switch').prop('checked', Store.get('darkMode'))

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

    $('#pokemon-icon-size').val(Store.get('pokemonIconSize'))
    $('#pokemon-icon-notify-size').val(Store.get('iconNotifySizeModifier'))
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
        case 'geouri':
            url = 'geo:' + lat + ',' + lng
            break
    }
    window.open(url, '_blank')
}

function copyCoordsToClipboard(coordsElementNode) { // eslint-disable-line no-unused-vars
    var range
    var sel
    try {
        range = document.createRange()
        range.selectNodeContents(coordsElementNode)
        sel = window.getSelection()
        sel.removeAllRanges()
        sel.addRange(range)
        document.execCommand('Copy')
    } catch (ex) {
        alert(ex)
    }
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
    var firstSeen = item['first_seen_timestamp']
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
    var costume = item['costume']
    var cp = item['cp']
    var weatherBoostedCondition = item['weather_boosted_condition']

    $.each(types, function (index, type) {
        typesDisplay += '<img src="' + getIcon(iconpath.type, 'type', '.png', getKeyByValue(pokemonTypes, type.type)) + '" style="height:20px;">'
    })

    var details = ''
    if (atk != null && def != null && sta != null) {
        var iv = (item['iv'] != null) ? item['iv'] : false
        var pokemonLevel = (item['level'] != null) ? item['level'] : 1
        if (pMove1 !== 'unknown') {
            pMoveType1 = '<img style="position:relative;top:3px;left:2px;height:15px;" src="' + getIcon(iconpath.type, 'type', '.png', getKeyByValue(pokemonTypes, moves[item['move_1']]['type'])) + '">'
        }
        if (pMove2 !== 'unknown') {
            pMoveType2 = '<img style="position:relative;top:3px;left:2px;height:15px;" src="' + getIcon(iconpath.type, 'type', '.png', getKeyByValue(pokemonTypes, moves[item['move_2']]['type'])) + '">'
        }

        var catchRates = ''
        if (!noCatchRates && item['catch_rate_1'] != null && item['catch_rate_2'] != null && item['catch_rate_3'] != null) {
            catchRates = '<div>' +
            '<img src="static/images/pokeball-1.png" style="height:14px;position:relative;top:2px;"> ' + (item['catch_rate_1'] * 100).toFixed(1) + '% ' +
            '<img src="static/images/greatball.png" style="height:14px;position:relative;top:2px;"> ' + (item['catch_rate_2'] * 100).toFixed(1) + '% ' +
            '<img src="static/images/ultraball.png" style="height:14px;position:relative;top:2px;"> ' + (item['catch_rate_3'] * 100).toFixed(1) + '%' +
            '</div>'
        }

        var size = ''
        if (item['size'] != null) {
            size = ' | <span style="color: white; border-radius: 5px; background: #5A5A5A; padding: 1px 4px 1px 4px;">' + i8ln(item['size']) + '</span>'
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
            '<div>' + i8ln('Weight') + ': <b>' + weight + '</b>' + ' | ' + i8ln('Height') + ': <b>' + height + '</b>' + size + '</div>' +
            catchRates +
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
        '<div><img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', id, 0, form, costume, gender) + '" style="width:50px;margin-top:10px;"/>' +
        '<b style="position:absolute;top:55px;left:72px;">'
    if (firstSeen > 0) {
        contentstring += '<i class="fas fa-history"></i>' + ' ' + getTimeStr(firstSeen) +
            '<br>'
    }
    if (item['expire_timestamp_verified'] > 0) {
        contentstring += '<i class="far fa-clock"></i>' + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            ' <i class="fas fa-check-square" style="color:#28b728;" title="' + i8ln('Despawntime verified') + '"></i>' +
            '</b></div>'
    } else if (pokemonReportTime === true) {
        contentstring += ' <i class="far fa-clock"></i>' + ' ' + getTimeStr(reportTime) +
            '</b></div>'
    } else {
        contentstring += ' <i class="far fa-clock"></i>' + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            ' <i class="fas fa-question" style="color:red;" title="' + i8ln('Despawntime not verified') + '"></i>' +
            '</b></div>'
    }

    contentstring += '<small>' + typesDisplay + '</small>' + '<br>' + details
    if (atk != null && def != null && sta != null && noCatchRates) {
        contentstring += '<center><div style="position:relative;top:55px;">'
    } else if (atk != null && def != null && sta != null && !noCatchRates) {
        contentstring += '<center><div style="position:relative;top:70px;">'
    } else {
        contentstring += '<center><div style="position:relative;">'
    }
    contentstring += '<a href="javascript:excludePokemon(' + id + ')"  title="' + i8ln('Exclude this Pokémon') + '"><i class="fas fa-minus-circle" style="font-size:15px;width:20px;"></i></a>' +
    ' | <a href="javascript:notifyAboutPokemon(' + id + ')" title="' + i8ln('Notify about this Pokémon') + '"><i class="fas fa-bell" style="font-size:15px;width:20px;"></i></a>'
    if (!noHideSingleMarker) {
        contentstring += ' | <a href="javascript:removePokemonMarker(\'' + encounterId + '\')" title="' + i8ln('Remove this Pokémon from the map') + '"><i class="fas fa-eye-slash" style="font-size:15px;width:20px;"></i></a>'
    }
    contentstring += ' | <a href="javascript:void(0);" onclick="javascript:toggleOtherPokemon(' + id + ');" title="' + i8ln('Toggle display of other Pokémon') + '"><i class="fas fa-search-plus" style="font-size:15px;width:20px;"></i></a>' +
    '</div></center>'
    if (atk != null && def != null && sta != null && noCatchRates) {
        contentstring += '<div style="position:relative;top:55px;"><center>'
    } else if (atk != null && def != null && sta != null && !noCatchRates) {
        contentstring += '<div style="position:relative;top:70px;"><center>'
    } else {
        contentstring += '<div style="position:relative;"><center>'
    }

    contentstring +=
    '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ', ' + longitude + ')" title="' + i8ln('View in Maps') + '">' +
    '<i class="fas fa-road" style="padding-right:0.25em"></i>' + coordText + '</a>'
    if (hidePokemonCoords === true) {
        contentstring += '-'
    } else {
        contentstring += ' ' +
                '<button onclick="copyCoordsToClipboard(this.previousElementSibling);" class="small-tight">' + 'Copy' + '</button> '
    }
    contentstring += '<a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=18&encId=' + encounterId + '">' +
    '<i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;margin-bottom:10px;font-size:18px;"></i>' +
    '</a>'

    if (!noPvp) {
        if (item['pvp_rankings_little_league'] !== undefined && item['pvp_rankings_little_league'] !== null) {
            contentstring += '<br>'
            contentstring += '<b>' + i8ln('Little League') + ':</b>' + '<br>'
            var littleLeague = JSON.parse(item['pvp_rankings_little_league'])
            $.each(littleLeague, function (index, ranking) {
                let pokemonName = ''
                $.each(pokedex[ranking.pokemon]['forms'], function (index, form) {
                    if (ranking.form === form['protoform'] && form['nameform'] !== 'Normal') {
                        pokemonName = i8ln(form['nameform']) + ' ' + i8ln(pokedex[ranking.pokemon]['name'])
                    }
                })
                if (pokemonName === '') {
                    pokemonName = i8ln(pokedex[ranking.pokemon]['name'])
                }
                if (ranking.evolution !== undefined && ranking.evolution > 0) {
                    switch (ranking.evolution) {
                        case 1:
                            pokemonName = i8ln('Mega') + ' ' + pokemonName
                            break
                        case 2:
                            pokemonName = i8ln('Mega X') + ' ' + pokemonName
                            break
                        case 3:
                            pokemonName = i8ln('Mega Y') + ' ' + pokemonName
                            break
                        case 4:
                            pokemonName = i8ln('Primal') + ' ' + pokemonName
                            break
                    }
                }

                let infoString
                if (ranking.rank === null) {
                    infoString = i8ln('CP too high')
                } else {
                    infoString = '#' + ranking.rank
                }
                if (ranking.cp !== null) {
                    infoString += ' @' + ranking.cp + i8ln('CP') + ' (' + i8ln('Lvl') + ' ' + (ranking.level) + ')'
                    if (!noPvpCapText) {
                        infoString += ' [' + i8ln('Cap') + ' ' + (ranking.cap) + ']'
                    }
                }
                if (ranking.cp !== null) {
                }

                let color = ''
                if (ranking.rank === 1) {
                    color = 'color:green'
                }
                contentstring += '<small style="font-size: 11px;' + color + '"><b>' + pokemonName + ':</b> ' + infoString + '</small><br>'
            })
        }

        if (item['pvp_rankings_great_league'] !== undefined && item['pvp_rankings_great_league'] !== null) {
            contentstring += '<br>'
            contentstring += '<b>' + i8ln('Great League') + ':</b>' + '<br>'
            var greatLeague = JSON.parse(item['pvp_rankings_great_league'])
            $.each(greatLeague, function (index, ranking) {
                let pokemonName = ''
                $.each(pokedex[ranking.pokemon]['forms'], function (index, form) {
                    if (ranking.form === form['protoform'] && form['nameform'] !== 'Normal') {
                        pokemonName = i8ln(form['nameform']) + ' ' + i8ln(pokedex[ranking.pokemon]['name'])
                    }
                })
                if (pokemonName === '') {
                    pokemonName = i8ln(pokedex[ranking.pokemon]['name'])
                }
                if (ranking.evolution !== undefined && ranking.evolution > 0) {
                    switch (ranking.evolution) {
                        case 1:
                            pokemonName = i8ln('Mega') + ' ' + pokemonName
                            break
                        case 2:
                            pokemonName = i8ln('Mega X') + ' ' + pokemonName
                            break
                        case 3:
                            pokemonName = i8ln('Mega Y') + ' ' + pokemonName
                            break
                        case 4:
                            pokemonName = i8ln('Primal') + ' ' + pokemonName
                            break
                    }
                }

                let infoString
                if (ranking.rank === null) {
                    infoString = i8ln('CP too high')
                } else {
                    infoString = '#' + ranking.rank
                }
                if (ranking.cp !== null) {
                    infoString += ' @' + ranking.cp + i8ln('CP') + ' (' + i8ln('Lvl') + ' ' + (ranking.level) + ')'
                    if (!noPvpCapText) {
                        infoString += ' [' + i8ln('Cap') + ' ' + (ranking.cap) + ']'
                    }
                }

                let color = ''
                if (ranking.rank === 1) {
                    color = 'color:green'
                }
                contentstring += '<small style="font-size: 11px;' + color + '"><b>' + pokemonName + ':</b> ' + infoString + '</small><br>'
            })
        }

        if (item['pvp_rankings_ultra_league'] !== undefined && item['pvp_rankings_ultra_league'] !== null) {
            contentstring += '<br>'
            contentstring += '<b>' + i8ln('Ultra League') + ':</b>' + '<br>'
            var ultraLeague = JSON.parse(item['pvp_rankings_ultra_league'])
            $.each(ultraLeague, function (index, ranking) {
                let pokemonName = ''
                $.each(pokedex[ranking.pokemon]['forms'], function (index, form) {
                    if (ranking.form === form['protoform'] && form['nameform'] !== 'Normal') {
                        pokemonName = i8ln(form['nameform']) + ' ' + i8ln(pokedex[ranking.pokemon]['name'])
                    }
                })
                if (pokemonName === '') {
                    pokemonName = i8ln(pokedex[ranking.pokemon]['name'])
                }
                if (ranking.evolution !== undefined && ranking.evolution > 0) {
                    switch (ranking.evolution) {
                        case 1:
                            pokemonName = i8ln('Mega') + ' ' + pokemonName
                            break
                        case 2:
                            pokemonName = i8ln('Mega X') + ' ' + pokemonName
                            break
                        case 3:
                            pokemonName = i8ln('Mega Y') + ' ' + pokemonName
                            break
                        case 4:
                            pokemonName = i8ln('Primal') + ' ' + pokemonName
                            break
                    }
                }

                let infoString
                if (ranking.rank === null) {
                    infoString = i8ln('CP too high')
                } else {
                    infoString = '#' + ranking.rank
                }
                if (ranking.cp !== null) {
                    infoString += ' @' + ranking.cp + i8ln('CP') + ' (' + i8ln('Lvl') + ' ' + (ranking.level) + ')'
                    if (!noPvpCapText) {
                        infoString += ' [' + i8ln('Cap') + ' ' + (ranking.cap) + ']'
                    }
                }

                let color = ''
                if (ranking.rank === 1) {
                    color = 'color:green'
                }
                contentstring += '<small style="font-size: 11px;' + color + '"><b>' + pokemonName + ':</b> ' + infoString + '</small><br>'
            })
        }
    }

    contentstring += '</center></div>'
    if (atk != null && def != null && sta != null) {
        contentstring += '<br><br><br>'
        if (!noCatchRates) {
            contentstring += '<br>'
        }
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
    var freeSlots = item['slots_available']
    var pokemonid = item['raid_pokemon_id']
    var form = item['raid_pokemon_form']
    var gender = item['raid_pokemon_gender']
    var evolution = item['raid_pokemon_evolution']
    var costume = item['raid_pokemon_costume']
    var alignment = item['raid_pokemon_alignment']

    var raidSpawned = item['raid_level'] != null
    var raidStarted = item['raid_pokemon_id'] != null

    var numStars = (item['raid_level'] >= 11 && item['raid_level'] <= 15) ? (item['raid_level'] - 10) : item['raid_level']
    var shadowStr = ((item['raid_level'] >= 11 && item['raid_level'] <= 15) || parseInt(item['raid_pokemon_alignment']) === 1) ? i8ln('Shadow') + ' ' : ''

    var raidStr = ''
    var raidIcon = ''
    var i = 0
    if (raidSpawned && item.raid_end > Date.now()) {
        var levelStr = ''
        for (i = 0; i < numStars; i++) {
            levelStr += '★'
        }
        raidStr = '<h3 style="margin-bottom: 0">Raid ' + shadowStr + levelStr
        if (raidStarted) {
            var cpStr = ''
            if (item.raid_pokemon_cp > 0) {
                cpStr = ' CP ' + item.raid_pokemon_cp
            }
            raidStr += '<br>' + item.raid_pokemon_name
            if (form !== null && form > 0 && item['raid_pokemon_form_name'] !== 'Normal') {
                raidStr += ' (' + i8ln(item['raid_pokemon_form_name']) + ')'
            }
            if (evolution !== null && evolution > 0) {
                switch (evolution) {
                    case 1:
                        raidStr += ' Mega'
                        break
                    case 2:
                        raidStr += ' Mega X'
                        break
                    case 3:
                        raidStr += ' Mega Y'
                        break
                    case 4:
                        raidStr += ' Primal'
                        break
                }
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
        raidStr += '<div>' + i8ln('Start') + ': <b>' + raidStartStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_start'] + '" start>(' + generateRemainingTimer(item['raid_start'], 'start') + ')</span></div>'
        raidStr += '<div>' + i8ln('End') + ': <b>' + raidEndStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_end'] + '" end>(' + generateRemainingTimer(item['raid_end'], 'end') + ')</span></div>'
        if (!noHideSingleMarker) {
            raidStr += '<a href="javascript:removeGymMarker(\'' + item['gym_id'] + '\')" title="' + i8ln('Hide this Gym') + '"><i class="fas fa-eye-slash" style="font-size:15px;"></i></a>'
        }
        if (raidStarted) {
            raidIcon = '<img style="width: 70px;" src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, evolution, form, costume, gender, 0, alignment) + '"/>'
        } else if (item.raid_start <= Date.now()) {
            raidIcon = '<img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'], 1) + '" style="height:70px;">'
        } else {
            raidIcon = '<img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level']) + '" style="height:70px;">'
        }
    }
    if (!noRaids && manualRaids && item['scanArea'] === false) {
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

    var lastScannedStr = (lastScanned != null) ? '<div>' + i8ln('Last Scanned') + ': ' + getDateStr(lastScanned) + ' ' + getTimeStr(lastScanned) + '</div>' : ''

    var lastModifiedStr = getDateStr(lastModified) + ' ' + getTimeStr(lastModified)

    var nameStr = (name ? '<div style="font-weight:900">' + name + '</div>' : '')

    var gymColor = ['0, 0, 0, .4', '6, 119, 239', '255, 45, 33', '251, 210, 8']
    var str
    var gymImage = ''
    if (url !== null) {
        gymImage = '<img id="' + item['gym_id'] + '"class="gym-image" style="border:3px solid rgba(' + gymColor[teamId] + ')" src="' + url + '" onclick="openFullscreenModal(document.getElementById(\'' + item['gym_id'] + '\').src)">'
    }
    var inBattle = (item['in_battle'] === 1 && lastScanned > (Date.now() - 5 * 60 * 1000)) ? '<img src="static/images/in_battle_small.png" style="position:absolute;right:170px;bottom:140px;"/>' : ''
    var teamStr = (teamId === 0) ? i8ln('Uncontested Gym') : '<b style="color:rgba(' + gymColor[teamId] + ')">' + i8ln('Team') + ' ' + i8ln(teamName) + '</b><br>'
    var whatsappLink = ''
    var exGym = (item.park && item.park !== '0') ? i8ln('%20(EX Gym)') : ''
    if (((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) && (item.raid_pokemon_id > 1 && item.raid_pokemon_id < numberOfPokemon)) {
        whatsappLink = '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + exGym + '%0ALevel%20' + item.raid_level + '%20' + item.raid_pokemon_name + '%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0AStats:%0Ahttps://pokemongo.gamepress.gg/pokemon/' + item.raid_pokemon_id + '%0AMoves:%0A' + pMove1 + ' / ' + pMove2 + '%0A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share"><i class="fab fa-whatsapp" style="position:relative;top:3px;left:5px;color:#26c300;font-size:20px;"></i></a>'
    } else if ((!noWhatsappLink) && (raidSpawned && item.raid_end > Date.now())) {
        whatsappLink = '<a href="whatsapp://send?text=' + encodeURIComponent(item.name) + exGym + '%0ALevel%20' + item.raid_level + '%20egg%0A%2AStart:%20' + raidStartStr + '%2A%0A%2AEnd:%20' + raidEndStr + '%2A%0ADirections:%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item.latitude + ',' + item.longitude + '" data-action="share/whatsapp/share"><i class="fab fa-whatsapp" style="position:relative;top:3px;left:5px;color:#26c300;font-size:20px;"></i></a>'
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
        inBattle +
        raidIcon +
        '</div>' +
        '<div><b>' + freeSlots + ' ' + i8ln('Free Slots') + '</b></div>' +
        raidStr +
        '<div>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '"><i class="fas fa-road" style="padding-right:0.25em"></i>' + coordText + '</a>'
    if (hideGymCoords === true) {
        str += '-'
    } else {
        str += ' ' +
                '<button onclick="copyCoordsToClipboard(this.previousElementSibling);" class="small-tight">' + 'Copy' + '</button> '
    }
    str += '<a href="./?lat=' + latitude + '&lon=' + longitude + '&zoom=18&gymId=' + item['gym_id'] + '"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
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
    var pokemonid = item['reward_pokemon_id']
    var costumeid = item['reward_pokemon_costumeid']
    var genderid = item['reward_pokemon_genderid']
    var formid = item['reward_pokemon_formid']
    var shiny = item['reward_pokemon_shiny']
    var styleStr = ''
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        styleStr = 'position:absolute;height:50px;top:60px;'
    } else if (item['quest_reward_type'] === 2) {
        styleStr = 'position:absolute;height:35px;right:55%;top:85px;filter:drop-shadow(1px 0 0 black)drop-shadow(-1px 0 0 black);'
    } else {
        styleStr = 'position:absolute;height:35px;right:55%;top:85px;'
    }
    switch (item['quest_reward_type']) {
        case 1:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.reward, 'reward/experience', '.png', item['reward_amount']) + '"/>'
            break
        case 2:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.reward, 'reward/item', '.png', item['reward_item_id'], item['reward_amount']) + '"/>'
            break
        case 3:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.reward, 'reward/stardust', '.png', item['reward_amount']) + '"/>'
            break
        case 4:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.reward, 'reward/candy', '.png', item['reward_pokemon_id'], item['reward_amount']) + '"/>'
            break
        case 7:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, 0, formid, costumeid, genderid, shiny) + '"/>'
            break
        case 12:
            rewardImage = '<img style="' + styleStr + '" src="' + getIcon(iconpath.reward, 'reward/mega_resource', '.png', item['reward_pokemon_id'], item['reward_amount']) + '"/>'
            break
        default:
            rewardImage = ''
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
                                tstr += i8ln(pokemonTypes[typeId]) + ' or '
                            } else if (index === questinfo['pokemon_type_ids'].length - 1) {
                                tstr += i8ln(pokemonTypes[typeId])
                            } else {
                                tstr += i8ln(pokemonTypes[typeId]) + ', '
                            }
                        })
                    } else {
                        tstr = i8ln(pokemonTypes[questinfo['pokemon_type_ids']])
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
                                pstr += idToPokemon[id] + ' or '
                            } else if (index === questinfo['pokemon_ids'].length - 1) {
                                pstr += idToPokemon[id]
                            } else {
                                pstr += idToPokemon[id] + ', '
                            }
                        })
                    } else {
                        pstr = idToPokemon[questinfo['pokemon_ids']]
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
                    if (item['quest_type'] === 53) {
                        str = str.replace('Charged', 'supereffective Charged')
                    } else {
                        str = str.replace('Complete', 'Use a super effective charged attack in')
                    }
                    break
                case 11:
                    if (item['quest_type'] === 13) {
                        str = str.replace('Catch', 'Use').replace('pokémon with berrie(s)', 'berrie(s) to help catch Pokémon')
                    }
                    if (questinfo !== null) {
                        str = str.replace('berrie(s)', idToItem[questinfo['item_id']])
                        str = str.replace('Evolve {0} pokémon', 'Evolve {0} pokémon with a ' + idToItem[questinfo['item_id']])
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
                case 21:
                    str = str.replace('Catch {0}', 'Catch {0} different species of')
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
                case 26:
                    str = str.replace('pokémon', 'shadow Pokémon')
                    break
                case 27:
                    var gstr = ''
                    $.each(questinfo['character_category_ids'], function (index, charId) {
                        if (index === (questinfo['character_category_ids'].length - 2)) {
                            gstr += gruntCharacterTypes[charId] + ' or '
                        } else if (index === (questinfo['character_category_ids'].length - 1)) {
                            gstr += gruntCharacterTypes[charId]
                        } else {
                            gstr += gruntCharacterTypes[charId] + ', '
                        }
                    })
                    str = str.replace('Team GO Rocket Grunt(s)', gstr)
                    if (item['quest_condition_type_1'] === 18) {
                        str = str.replace('Battle against', 'Defeat')
                    }
                    break
                case 28:
                    if (item['quest_type'] === 28) {
                        str = str.replace('Snapshot(s)', 'Snapshot(s) of your Buddy')
                    }
                    break
                case 41:
                    if (item['quest_type'] === 27 && questinfo !== null && parseInt(questinfo['combat_type']) === 6) {
                        str = 'Battle in GO Battle League {0} times'
                    }
                    break
                case 46:
                    str = str.replace('{0} gift(s)', '{0} gift(s) with a sticker')
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
    var eventEndStr = ''
    var incidentEndStr = ''
    var stopName = ''
    var gruntReward = ''
    if (item['pokestop_name'] === null) {
        item['pokestop_name'] = 'Pokéstop'
    }
    var d = new Date()
    var lastMidnight = ''
    if (mapFork === 'mad') {
        lastMidnight = d.setHours(0, 0, 0, 0) / 1000
    } else {
        lastMidnight = 0
    }
    if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
        stopName = '<b class="pokestop-rocket-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-rocket-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>' +
            '<img src="static/sprites/misc/teamRocket.png" style="position:absolute;height:30px;left:55%;">' +
            '<img src="' + getIcon(iconpath.invasion, 'invasion', '.png', item['grunt_type']) + '" style="position:absolute;height:35px;right:55%;top:85px;">'
        }
    } else if (!noEventStops && item['eventstops_expiration'] > Date.now()) {
        stopName = '<b class="pokestop-eventstops-name">' + item['pokestop_name'] + '</b>'
        if (item['url'] !== null) {
            stopImage = '<img class="pokestop-eventstops-image" id="' + item['pokestop_id'] + '" src="' + item['url'] + '" onclick="openFullscreenModal(document.getElementById(\'' + item['pokestop_id'] + '\').src)"/>'
        }
    } else if (!noQuests && item['quest_type'] > 0 && lastMidnight < Number(item['quest_timestamp'])) {
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

    if (!noQuests && item['quest_type'] > 0 && typeof questtypeList[item['quest_type']] !== 'undefined' && lastMidnight < Number(item['quest_timestamp'])) {
        var questStr = getQuest(item)
        var questArStr = ''
        if (item['quest_with_artask'] === true) {
            questArStr = '<div><span class="pokestop-quest-artext">' + i8ln('With AR-Scan Task') + '</span></div>'
        } else if (item['quest_with_artask'] === false) {
            questArStr = '<div><span class="pokestop-quest-artext">' + i8ln('Without AR-Scan Task') + '</span></div>'
        }
        str += getReward(item) + '</div>' +
            questArStr +
            '<div>' +
            i8ln('Quest') + ': <b>' +
            i8ln(questStr) +
            '</b></div>'
        if (item['quest_reward_type'] === 1) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_amount'] + ' ' +
            i8ln('XP') +
            '</b></div>'
        } else if (item['quest_reward_type'] === 2) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_amount'] + ' ' +
            item['reward_item_name'] +
            '</b></div>'
        } else if (item['quest_reward_type'] === 3) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_amount'] + ' ' +
            i8ln('Stardust') +
            '</b></div>'
        } else if (item['quest_reward_type'] === 4) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_amount'] + 'x ' + item['reward_pokemon_name'] + ' ' +
            i8ln('Candy') +
            '</b></div>'
        } else if (item['quest_reward_type'] === 7) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_pokemon_name'] +
            '</b></div>'
        } else if (item['quest_reward_type'] === 12) {
            str += '<div>' +
            i8ln('Reward') + ': <b>' +
            item['reward_amount'] + ' ' + item['reward_pokemon_name'] + ' ' +
            i8ln('Mega energy') +
            '</b></div>'
        }
        if (!noHideSingleMarker) {
            str += '<a href="javascript:removePokestopMarker(\'' + item['pokestop_id'] + '\')" title="' + i8ln('Hide this Pokéstop') + '"><i class="fas fa-eye-slash" style="font-size:15px;"></i></a>'
        }
    } else {
        str += '</div>'
    }
    if (!noQuests && item['quest_type'] > 0 && typeof questtypeList[item['quest_type']] === 'undefined' && lastMidnight < Number(item['quest_timestamp'])) {
        console.log('Undefined Quest Type: ' + item['quest_type'])
        str += '<div>' + i8ln('Error: Undefined Quest Type') + ': ' + item['quest_type'] + '</div>'
    }
    if (!noEventStops && item['eventstops_expiration'] > Date.now()) {
        var eventType = ''
        if (item['eventstops_id'] === 7) {
            eventType = '<img src="static/sprites/misc/EventStopsCoin.png" style="padding:5px;position:relative;left:0px;top:12px;height:40px;"/>'
        } else if (item['eventstops_id'] === 8) {
            eventType = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', 352) + '" style="padding:5px;position:relative;left:0px;top:12px;height:40px;"/>'
        } else if (item['eventstops_id'] === 9) {
            eventType = i8ln('Showcase')
        } else {
            console.log('Unknown Event Type: ' + item['eventstops_id'])
            eventType = i8ln('Unknown Event Type') + ': ' + item['eventstops_id']
        }
        eventEndStr = getTimeStr(item['eventstops_expiration'])
        str +=
        '<div>' + i8ln('Event Type') + ': <b>' + eventType + '</b></div>' +
        '<div>' + i8ln('Event Expiration') + ': <b>' + eventEndStr +
        ' <span class="label-countdown" disappears-at="' + item['eventstops_expiration'] + '">(00m00s)</span>' +
        '</b></div>'
    }
    if (!noLures && item['lure_expiration'] > Date.now()) {
        var lureType = '<img style="padding:5px;position:relative;left:0px;top:12px;height:40px;" src="static/sprites/misc/LureModule_' + item['lure_id'] + '.png"/>'
        if (item['lure_id'] === 501) {
            lureType += i8ln('Normal')
        } else if (item['lure_id'] === 502) {
            lureType += i8ln('Glacial')
        } else if (item['lure_id'] === 503) {
            lureType += i8ln('Mossy')
        } else if (item['lure_id'] === 504) {
            lureType += i8ln('Magnetic')
        } else if (item['lure_id'] === 505) {
            lureType += i8ln('Rainy')
        } else if (item['lure_id'] === 506) {
            lureType += i8ln('Golden')
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
                item['encounters']['first'].forEach(function (pokemonid) {
                    gruntReward += '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid) + '" style="width:30px;height:auto;position:absolute;"/>' +
                    '<img src="static/images/shadow_icon.png" style="width:30px;height:30px;"/>'
                })
                gruntReward += '</div></div></center>'
            } else if (item['second_reward'] === 'true') {
                gruntReward += '<center>' +
                '<div>85% ' + i8ln('encounter chance for') + ':<br>'
                item['encounters']['first'].forEach(function (pokemonid) {
                    gruntReward += '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid) + '" style="width:30px;height:auto;position:absolute;"/>' +
                    '<img src="static/images/shadow_icon.png" style="width:30px;height:30px;"/>'
                })
                gruntReward += '</div>' +
                '<div>15% ' + i8ln('encounter chance for') + ':<br>'
                item['encounters']['second'].forEach(function (pokemonid) {
                    gruntReward += '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid) + '" style="width:30px;height:auto;position:absolute;"/>' +
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
        '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item['latitude'] + ',' + item['longitude'] + ')" title="' + i8ln('View in Maps') + '"><i class="fas fa-road" style="padding-right:0.25em"></i>' + coordText + '</a>'
    if (hidePokestopCoords === true) {
        str += '-'
    } else {
        str += ' ' +
                '<button onclick="copyCoordsToClipboard(this.previousElementSibling);" class="small-tight">' + 'Copy' + '</button> '
    }
    str += '<a href="./?lat=' + item['latitude'] + '&lon=' + item['longitude'] + '&zoom=18&stopId=' + item['pokestop_id'] + '"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>'
    if (!noQuests && !noWhatsappLink && item['quest_type'] > 0 && lastMidnight < Number(item['quest_timestamp'])) {
        var quest = getQuest(item)
        var reward = ''
        if (item['reward_pokemon_id'] > 0) {
            reward = item['reward_pokemon_name']
        } else if (item['reward_item_id'] > 0) {
            reward = item['reward_amount'] + ' ' + item['reward_item_name']
        } else if (item['quest_reward_type'] === 1) {
            reward = item['reward_amount'] + ' ' + i8ln('XP')
        } else if (item['quest_reward_type'] === 3) {
            reward = item['reward_amount'] + ' ' + i8ln('Stardust')
        }
        str += '<a href="whatsapp://send?text=' + encodeURIComponent(item['pokestop_name']) + '%0A%2AQuest:%20' + quest + '%2A%0A%2AReward:%20' + reward + '%2A%0Ahttps://www.google.com/maps/search/?api=1%26query=' + item['latitude'] + ',' + item['longitude'] + '" data-action="share/whatsapp/share">' +
            '<i class="fab fa-whatsapp" style="position:relative;top:3px;left:5px;color:#26c300;font-size:20px;"></i></a>'
    }
    str += '</center></div>'
    if (!noQuests && item['quest_type'] > 0 && lastMidnight < Number(item['quest_timestamp'])) {
        str += '<center><div>' +
            i8ln('Quest found') + ': ' + getDateStr(item['quest_timestamp'] * 1000) + ' ' + getTimeStr(item['quest_timestamp'] * 1000) +
            '</div></center>'
    }
    str += '<center><div>' + i8ln('Last seen') + ': ' + getDateStr(item['last_seen']) + ' ' + getTimeStr(item['last_seen']) + '</div></center>'
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
    if (item.time !== null) {
        str += '<div><b>' + i8ln('Spawn Point') + '</b></div>' +
        '<div>' + i8ln('Despawn time') + ': xx:' + formatSpawnTime(item.time) + '</div>'
    } else {
        str += '<div>' + i8ln('Unknown spawnpoint info') + '</div>'
    }
    return str
}

function addPlacementRangeCircle(marker, map) {
    var markerPos = marker.getLatLng()
    var lat = markerPos.lat
    var lng = markerPos.lng
    var circleCenter = L.latLng(lat, lng)

    var rangeCircleOpts = {
        color: '#999999',
        radius: 20, // meters
        strokeWeight: 1,
        strokeColor: '#999999',
        strokeOpacity: 0.9,
        center: circleCenter,
        fillColor: '#999999',
        fillOpacity: 0.2
    }

    var rangeCircle = L.circle(circleCenter, rangeCircleOpts)

    markers.addLayer(rangeCircle)

    return rangeCircle
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
            range = 80
            break
        case 'gym':
            circleColor = teamColor
            range = 80
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
        fillOpacity: 0.2
    }
    var rangeCircle = L.circle(circleCenter, rangeCircleOpts)
    markers.addLayer(rangeCircle)
    return rangeCircle
}

function isPlacementRangeActive(map) {
    if (map.getZoom() < 17) return false
    return Store.get('showCells') && Store.get('showPlacementRanges')
}

function isRangeActive(map) {
    if (map.getZoom() < 15) return false
    return Store.get('showRanges')
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
    var iv = (item['iv'] != null) ? item['iv'] : false
    var level = (item['level'] != null) ? item['level'] : 1
    var find = ['<prc>', '<pkm>', '<lv>', '<atk>', '<def>', '<sta>']
    var replace = [iv ? iv.toFixed(1) : '', item['pokemon_name'], level, item['individual_attack'], item['individual_defense'], item['individual_stamina']]
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

    var pokemonId = item['pokemon_id']
    var pokemonForm = item['form']
    var pokemonCostume = item['costume']

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokemon')
    }

    marker.bindPopup(pokemonLabel(item), {autoPan: false, closeOnClick: false, autoClose: false, minWidth: 200})

    if (notifiedPokemon.indexOf(item['pokemon_id']) > -1 || notifiedRarity.indexOf(item['pokemon_rarity']) > -1) {
        if (!skipNotification) {
            checkAndCreateSound(item['pokemon_id'])
            sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, pokemonForm, pokemonCostume), item['latitude'], item['longitude'])
        }
        if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
            marker.bounce()
        }
    }

    if (item['individual_attack'] != null) {
        var perfection = (item['iv'] != null) ? item['iv'] : false
        if (notifiedMinPerfection > 0 && perfection >= notifiedMinPerfection) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, pokemonForm, pokemonCostume), item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
                marker.bounce()
            }
        }
    }

    if (item['level'] != null) {
        if (notifiedMinLevel > 0 && item['level'] >= notifiedMinLevel) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, pokemonForm, pokemonCostume), item['latitude'], item['longitude'])
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
    var pokemonid = item['raid_pokemon_id']
    var evolutionId = item['raid_pokemon_evolution']
    var formId = item['raid_pokemon_form']
    var costumeId = item['raid_pokemon_costume']
    var genderId = item['raid_pokemon_gender']
    var alignmentId = item['raid_pokemon_alignment']
    var team = item.team_id
    var fortMarker = ''
    var exIcon = (((park !== '0' && onlyTriggerGyms === false && park) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false)) ? '<img src="static/images/ex.png" style="position:absolute;right:25px;bottom:2px;"/>' : ''
    var inBattle = (item['in_battle'] === 1 && item.last_scanned > (Date.now() - 5 * 60 * 1000)) ? '<img src="static/images/in_battle_small.png" style="width:26px;position:absolute;right:31px;bottom:28px;"/>' : ''
    var smallExIcon = (((park !== '0' && onlyTriggerGyms === false && park) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false)) ? '<img src="static/images/ex.png" style="width:26px;position:absolute;right:35px;bottom:13px;"/>' : ''
    var html = ''
    if (item['raid_pokemon_id'] != null && item.raid_end > Date.now()) {
        html = '<div style="position:relative;">' +
            '<img src="' + getIcon(iconpath.gym, 'gym', '.png', team, level, item['in_battle'], park) + '" style="width:50px;height:auto;"/>' +
            exIcon +
            inBattle +
            '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, evolutionId, formId, costumeId, genderId, 0, alignmentId) + '" style="width:50px;height:auto;position:absolute;top:-15px;right:0px;"/>' +
            '</div>'
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div class="gym-icon-raid-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['raid_end'] + '" end>' + generateRemainingTimer(item['raid_end'], 'end') + '</span></div>'
        }
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -70],
            className: 'raid-marker',
            html: html
        })
    } else if (item['raid_level'] !== null && item.raid_start <= Date.now() && item.raid_end > Date.now()) {
        html = '<div style="position:relative;">' +
            '<img src="' + getIcon(iconpath.gym, 'gym', '.png', team, level, item['in_battle'], park) + '" style="width:50px;height:auto;"/>' +
            exIcon +
            inBattle +
            '<img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'], 1) + '" style="width:35px;height:auto;position:absolute;top:-11px;right:18px;"/>' +
            '</div>'
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div class="gym-icon-raid-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['raid_end'] + '" end>' + generateRemainingTimer(item['raid_end'], 'end') + '</span></div>'
        }
        fortMarker = L.divIcon({
            iconSize: [50, 50],
            iconAnchor: [25, 45],
            popupAnchor: [0, -40],
            className: 'active-egg-marker',
            html: html
        })
    } else if (item['raid_level'] !== null && item.raid_end > Date.now()) {
        html = '<div style="position:relative;">' +
            '<img src="' + getIcon(iconpath.gym, 'gym', '.png', team, level, item['in_battle'], park) + '" style="width:50px;height:auto;"/>' +
            exIcon +
            inBattle +
            '<img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level']) + '" style="width:30px;position:absolute;top:4px;right:15px;"/>' +
            '</div>'
        if (noRaidTimer === false && Store.get(['showRaidTimer'])) {
            html += '<div class="gym-icon-egg-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['raid_start'] + '" end>' + generateRemainingTimer(item['raid_start'], 'end') + '</span></div>'
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
            '<img src="' + getIcon(iconpath.gym, 'gym', '.png', team, level, item['in_battle'], park) + '" style="width:35px;height:auto;"/>' +
            smallExIcon +
            inBattle +
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
    if (!marker.placementRangeCircle && isPlacementRangeActive(map)) {
        marker.placementRangeCircle = addPlacementRangeCircle(marker, map)
    }

    var raidLevel = item.raid_level
    if (raidLevel >= Store.get('remember_raid_notify') && item.raid_end > Date.now() && Store.get('remember_raid_notify') !== 0) {
        var title = i8ln('Raid level') + ': ' + raidLevel

        var raidStartStr = getTimeStr(item['raid_start'])
        var raidEndStr = getTimeStr(item['raid_end'])
        var text = raidStartStr + ' - ' + raidEndStr

        var raidStarted = item['raid_pokemon_id'] != null
        var icon
        if (raidStarted) {
            var pokemonid = item.raid_pokemon_id
            var evolutionid = item['raid_pokemon_evolution']
            var formid = item['raid_pokemon_form']
            var costumeid = item['raid_pokemon_costume']
            var genderid = item['raid_pokemon_gender']
            var alignmentid = item['raid_pokemon_alignment']
            icon = getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, evolutionid, formid, costumeid, genderid, 0, alignmentid)
            checkAndCreateSound(item.raid_pokemon_id)
        } else if (item.raid_start <= Date.now()) {
            icon = getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'], 1)
        } else {
            icon = getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'])
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
            var title = i8ln('Raid level') + ': ' + raidLevel

            var raidStartStr = getTimeStr(item['raid_start'])
            var raidEndStr = getTimeStr(item['raid_end'])
            var text = raidStartStr + ' - ' + raidEndStr

            var raidStarted = item['raid_pokemon_id'] != null
            var icon
            if (raidStarted) {
                var pokemonid = item.raid_pokemon_id
                var evolutionid = item['raid_pokemon_evolution']
                var formid = item['raid_pokemon_form']
                var costumeid = item['raid_pokemon_costume']
                var genderid = item['raid_pokemon_gender']
                var alignmentid = item['raid_pokemon_alignment']

                icon = getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, evolutionid, formid, costumeid, genderid, 0, alignmentid)
                checkAndCreateSound(item.raid_pokemon_id)
            } else if (item.raid_start <= Date.now()) {
                icon = getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'], 1)
            } else {
                checkAndCreateSound()
                icon = getIcon(iconpath.raid, 'raid/egg', '.png', item['raid_level'])
            }
            sendNotification(title, text, icon, item['latitude'], item['longitude'])
        }
    }

    return marker
}

function updateGymIcons() {
    $.each(mapData.gyms, function (key, value) {
        mapData.gyms[key]['marker'].setIcon(getGymMarkerIcon(mapData.gyms[key]))
        mapData.gyms[key]['marker'].setPopupContent(gymLabel(mapData.gyms[key]))
    })
}

function updatePokestopIcons() {
    $.each(mapData.pokestops, function (key, value) {
        mapData.pokestops[key]['marker'].setIcon(getPokestopMarkerIcon(mapData.pokestops[key]))
        mapData.pokestops[key]['marker'].setPopupContent(pokestopLabel(mapData.pokestops[key]))
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
    var markerStr = '0'
    var pokemonid
    var formid
    var costumeid
    var genderid
    var shiny
    if (Store.get(['showPokestops']) && !Store.get(['showQuests']) && !Store.get(['showLures']) && !Store.get(['showRocket']) && !Store.get(['showEventStops']) && !Store.get(['showAllPokestops'])) {
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-marker',
            html: '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr) + '" style="width:50px;height:72;top:-35px;right:10px;"/></div>'
        })
    } else if (Store.get(['showAllPokestops']) && !noAllPokestops) {
        if (!noTeamRocket && item['incident_expiration'] > Date.now()) {
            if (!noLures && item['lure_expiration'] > Date.now()) {
                markerStr = item['lure_id']
            }
            html = '<div style="position:relative;"><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>'
            if (item['grunt_type'] > 0) {
                html += '<img src="' + getIcon(iconpath.invasion, 'invasion', '.png', item['grunt_type']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
            } else {
                html += '</div>'
            }
            if (noRocketTimer === false && Store.get(['showRocketTimer'])) {
                html += '<div class="pokestop-icon-rocket-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['incident_expiration'] + '"> </span></div>'
            }
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-rocket-marker',
                html: html
            })
        } else if (!noEventStops && item['eventstops_expiration'] > Date.now()) {
            if (!noLures && item['lure_expiration'] > Date.now()) {
                markerStr = item['lure_id']
            }
            html = '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, item['eventstops_id']) + '" style="width:50px;height:72;top:-35px;right:10px;"/><div>'
            if (item['eventstops_id'] === 7) {
                html += '<img src="static/sprites/misc/EventStopsCoin.png" style="width:25px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
            } else if (item['eventstops_id'] === 8) {
                html += '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', 352) + '" style="width:25px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
            } else {
                html += '</div>'
            }
            if (noEventStopsTimer === false && Store.get(['showEventStopsTimer'])) {
                html += '<div class="pokestop-icon-eventstops-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['eventstops_expiration'] + '"> </span></div>'
            }
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-eventstops-marker',
                html: html
            })
        } else if (!noQuests && item['quest_reward_type'] > 0 && lastMidnight < Number(item['quest_timestamp'])) {
            if (!noLures && item['lure_expiration'] > Date.now()) {
                markerStr = item['lure_id']
            }
            if (item['quest_reward_type'] === 12) {
                html = '<div style="position:relative;">' +
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.reward, 'reward/mega_resource', '.png', item['reward_pokemon_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                    '</div>'
                stopMarker = L.divIcon({
                    iconSize: [31, 31],
                    iconAnchor: [25, 45],
                    popupAnchor: [0, -35],
                    className: 'stop-quest-marker',
                    html: html
                })
            } else if (item['quest_reward_type'] === 7) {
                pokemonid = item['reward_pokemon_id']
                formid = item['reward_pokemon_formid']
                costumeid = item['reward_pokemon_costumeid']
                genderid = item['reward_pokemon_genderid']
                shiny = item['reward_pokemon_shiny']
                html = '<div style="position:relative;">' +
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, 0, formid, costumeid, genderid, shiny) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                    '</div>'
                stopMarker = L.divIcon({
                    iconSize: [31, 31],
                    iconAnchor: [25, 45],
                    popupAnchor: [0, -35],
                    className: 'stop-quest-marker',
                    html: html
                })
            } else if (item['quest_reward_type'] === 4) {
                html = '<div style="position:relative;">' +
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.reward, 'reward/candy', '.png', item['reward_pokemon_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                    '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', item['reward_pokemon_id']) + '" style="width:25px;height:auto;position:absolute;top:6px;left:10px;"/>' +
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
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.reward, 'reward/stardust', '.png', item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
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
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.reward, 'reward/item', '.png', item['reward_item_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                    '</div>'
                stopMarker = L.divIcon({
                    iconSize: [31, 31],
                    iconAnchor: [25, 45],
                    popupAnchor: [0, -35],
                    className: 'stop-quest-marker',
                    html: html
                })
            } else if (item['quest_reward_type'] === 1) {
                html = '<div style="position:relative;">' +
                    '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                    '<img src="' + getIcon(iconpath.reward, 'reward/experience', '.png', item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
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
            html = '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', item['lure_id']) + '" style="width:50px;height:72;top:-35px;right:10px;"/><div>'
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
                html: '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr) + '" style="width:50px;height:72;top:-35px;right:10px;"/></div>'
            })
        }
    } else if (Store.get(['showRocket']) && !noTeamRocket && item['incident_expiration'] > Date.now()) {
        if (!noLures && item['lure_expiration'] > Date.now()) {
            markerStr = 'Lured_' + item['lure_id']
        }
        html = '<div style="position:relative;"><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>'
        if (item['grunt_type'] > 0) {
            html += '<img src="' + getIcon(iconpath.invasion, 'invasion', '.png', item['grunt_type']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
        } else {
            html += '</div>'
        }
        if (noRocketTimer === false && Store.get(['showRocketTimer'])) {
            html += '<div class="pokestop-icon-rocket-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['incident_expiration'] + '"> </span></div>'
        }
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-rocket-marker',
            html: html
        })
    } else if (Store.get(['showEventStops']) && !noEventStops && item['eventstops_expiration'] > Date.now()) {
        if (!noLures && item['lure_expiration'] > Date.now()) {
            markerStr = item['lure_id']
        }
        html = '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, item['eventstops_id']) + '" style="width:50px;height:72;top:-35px;right:10px;"/><div>'

        if (item['eventstops_id'] === 7) {
            html += '<img src="static/sprites/misc/EventStopsCoin.png" style="width:25px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
        } else if (item['eventstops_id'] === 8) {
            html += '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', 352) + '" style="width:25px;height:auto;position:absolute;top:4px;left:0px;"/></div>'
        } else {
            html += '</div>'
        }
        if (noEventStopsTimer === false && Store.get(['showEventStopsTimer'])) {
            html += '<div class="pokestop-icon-eventstops-timer"><span class="icon-countdown" style="padding: .25rem!important; white-space: nowrap;" disappears-at="' + item['eventstops_expiration'] + '"> </span></div>'
        }
        stopMarker = L.divIcon({
            iconSize: [31, 31],
            iconAnchor: [25, 45],
            popupAnchor: [0, -35],
            className: 'stop-eventstops-marker',
            html: html
        })
    } else if (Store.get(['showQuests']) && !noQuests && item['quest_reward_type'] > 0 && lastMidnight < Number(item['quest_timestamp'])) {
        if (!noLures && item['lure_expiration'] > Date.now()) {
            markerStr = item['lure_id']
        }
        if (item['quest_reward_type'] === 12) {
            html = '<div style="position:relative;">' +
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.reward, 'reward/mega_resource', '.png', item['reward_pokemon_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 7) {
            pokemonid = item['reward_pokemon_id']
            formid = item['reward_pokemon_formid']
            costumeid = item['reward_pokemon_costumeid']
            genderid = item['reward_pokemon_genderid']
            shiny = item['reward_pokemon_shiny']
            html = '<div style="position:relative;">' +
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonid, 0, formid, costumeid, genderid, shiny) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 4) {
            html = '<div style="position:relative;">' +
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.reward, 'reward/candy', '.png', item['reward_pokemon_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', item['reward_pokemon_id']) + '" style="width:25px;height:auto;position:absolute;top:6px;left:10px;"/>' +
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
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.reward, 'reward/stardust', '.png', item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
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
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.reward, 'reward/item', '.png', item['reward_item_id'], item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        } else if (item['quest_reward_type'] === 1) {
            html = '<div style="position:relative;">' +
                '<img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr, 0, 1) + '" style="width:50px;height:72;top:-35px;right:10px;"/>' +
                '<img src="' + getIcon(iconpath.reward, 'reward/experience', '.png', item['reward_amount']) + '" style="width:30px;height:auto;position:absolute;top:4px;left:0px;"/>' +
                '</div>'
            stopMarker = L.divIcon({
                iconSize: [31, 31],
                iconAnchor: [25, 45],
                popupAnchor: [0, -35],
                className: 'stop-quest-marker',
                html: html
            })
        }
    } else if (Store.get(['showLures']) && !noLures && item['lure_expiration'] > Date.now()) {
        html = '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', item['lure_id']) + '" style="width:50px;height:72;top:-35px;right:10px;"/><div>'
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
            html: '<div><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', markerStr) + '" style="width:50px;height:72;top:-35px;right:10px;"/></div>'
        })
    }
    return stopMarker
}

function updatePokestopMarker(item, marker) {
    marker.setIcon(getPokestopMarkerIcon(item))
    marker.setPopupContent(pokestopLabel(item))

    return marker
}

function setupPokestopMarker(item) {
    var pokestopMarkerIcon = getPokestopMarkerIcon(item)
    var marker
    if (item['reward_pokemon_shiny'] === 'true') {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050, virtual: true}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper shiny', autoPan: false, closeOnClick: false, autoClose: false})
    } else {
        marker = L.marker([item['latitude'], item['longitude']], {icon: pokestopMarkerIcon, zIndexOffset: 1050, virtual: true}).bindPopup(pokestopLabel(item), {className: 'leaflet-popup-content-wrapper normal', autoPan: false, closeOnClick: false, autoClose: false})
    }
    markers.addLayer(marker)

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokestop')
    }
    if (!marker.placementRangeCircle && isPlacementRangeActive(map)) {
        marker.placementRangeCircle = addPlacementRangeCircle(marker, map)
    }

    addListeners(marker)

    return marker
}

function setupNestMarker(item) {
    var getNestMarkerIcon = ''
    if (item.pokemon_id > 0) {
        var pokemonId = item.pokemon_id
        var formId = item.pokemon_form
        getNestMarkerIcon = '<div class="marker-nests">' +
            '<img src="' + getIcon(iconpath.nest, 'nest', '.png', getKeyByValue(pokemonTypes, item.english_pokemon_types[0].type)) + '" style="width:45px;height: auto;"/>' +
            '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, formId) + '" style="position:absolute;width:40px;height:40px;top:6px;left:3px"/>' +
            '</div>'
    } else {
        getNestMarkerIcon = '<div class="marker-nests">' +
            '<img src="' + getIcon(iconpath.nest, 'nest', '.png', 0) + '" style="width:36px;height:auto;"/>' +
            '</div>'
    }
    var nestMarkerIcon = L.divIcon({
        iconSize: [36, 48],
        iconAnchor: [20, 45],
        popupAnchor: [0, -45],
        className: 'marker-nests',
        html: getNestMarkerIcon
    })
    if (noNestPolygon === false && Store.get('showNestPolygon') === true && item['polygon_path'] !== null) {
        var polygonColor = item['pokemon_types'][0]['color'] ? item['pokemon_types'][0]['color'] : 'grey'
        var polygon = L.polygon(JSON.parse(item['polygon_path']), {
            color: polygonColor
        })
        nestLayerGroup.addLayer(polygon)
    }
    var marker = L.marker([item['lat'], item['lon']], {icon: nestMarkerIcon, zIndexOffset: 1020, virtal: true}).bindPopup(nestLabel(item), {autoPan: false, closeOnClick: false, autoClose: false})
    markers.addLayer(marker)
    addListeners(marker)

    return marker
}

function nestLabel(item) {
    var str = ''
    if (item.pokemon_id > 0) {
        var types = item['english_pokemon_types']
        var typesDisplay = ''
        $.each(types, function (index, type) {
            typesDisplay += '<img src="' + getIcon(iconpath.type, 'type', '.png', getKeyByValue(pokemonTypes, type.type)) + '" style="height:20px;top:5px;position:relative;">'
        })
        var formId = item.pokemon_form
        var pokemonId = item.pokemon_id
        var nestName = (item['name'] !== null && item['name'] !== 'Unknown Areaname') ? '<b>' + item['name'] + '</b>' : ''
        var pokemonAvg = (item['pokemon_avg'] > 0) ? '<div>' + i8ln('Nest Pokemon per hour') + ': ' + item['pokemon_avg'].toFixed(2) + '</div>' : ''
        var pokemonCount = (item['pokemon_count'] > 0) ? '<div>' + i8ln('Total nest Pokemon count') + ': ' + item['pokemon_count'] + '</div>' : ''
        var nestSubmittedBy = (item['nest_submitted_by'] !== null) ? '<div>' + i8ln('Submitted by') + ': ' + item['nest_submitted_by'] + '</div>' : ''

        str += '<center>' +
            '<div>' +
            item.pokemon_name + ' - ' +
            typesDisplay +
            '</div>' +
            nestName + '<br />' +
            '<div>' +
            '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, formId) + '" style="width:65px;height:65px;"/>' +
            '</div>' +
            pokemonAvg +
            pokemonCount +
            nestSubmittedBy +
            '</center>'
    } else {
        str += '<div align="center" class="marker-nests">' +
            '<img src="' + getIcon(iconpath.nest, 'nest', '.png', 0) + '" align"middle" style="width:36px;height: auto;"/>' +
            '</div>' +
            '<center><b>' + i8ln('No Pokemon - Assign One Below') + '</b></center>'
    }
    if (item.type === 0) {
        str += '<center><div style="margin-bottom:5px; margin-top:5px;">' + i8ln('Found by ') + nestBotName + '</div></center>'
    }
    if (!noDeleteNests) {
        str += '<i class="fas fa-trash-alt delete-nest" onclick="deleteNest(event);" data-id="' + item['nest_id'] + '"></i>'
    }
    if (!noManualNests) {
        str += '<center><div>' + i8ln('Add Nest') + ' <i class="fas fa-binoculars submit-nest" onclick="openNestModal(event);" data-id="' + item['nest_id'] + '"></i></div></center>'
    }
    var coordText = item.lat.toFixed(6) + ', ' + item.lon.toFixed(7)
    if (hideNestCoords === true) {
        coordText = i8ln('Directions')
    }
    str += '<center><div>' +
        '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ')" title="' + i8ln('View in Maps') + '"><i class="fas fa-road" style="padding-right:0.25em"></i>' + coordText + '</a>'
    if (hideNestCoords === true) {
        str += '-'
    } else {
        str += ' ' +
                '<button onclick="copyCoordsToClipboard(this.previousElementSibling);" class="small-tight">' + 'Copy' + '</button> '
    }
    str += '<a href="./?lat=' + item.lat + '&lon=' + item.lon + '&zoom=18"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</div></center>'
    if (!noWhatsappLink && item.pokemon_id > 0) {
        str += '<div>' +
            '<center>' +
            '<a href="whatsapp://send?text=%2A' + encodeURIComponent(item.pokemon_name) + '%2A%20nest has been found.%0A%0ALocation:%20https://www.google.com/maps/search/?api=1%26query=' + item.lat + ',' + item.lon + '" data-action="share/whatsapp/share">' + i8ln('Whatsapp Link') + '</a>' +
            '</center>' +
            '</div>'
    }
    str += '<center><div>' + i8ln('Last Updated') + ': ' + getDateStr(item['updated']) + ' ' + getTimeStr(item['updated']) + '</div></center>'
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
    str += '<div class="d-grid">'
    if (item.has_invite_url === 1 && (item.invite_url !== '#' || item.invite_url !== undefined)) {
        str +=
        '<button class="btn btn-primary btn-sm" onclick="window.open(\'' + item.invite_url + '\',\'_blank\')" type="button"><i class="fas fa-comments"></i> ' + i8ln('Join Now') + '</button>'
    }
    if (!noEditCommunity) {
        str +=
        '<button class="btn btn-primary btn-sm" role="button" onclick="openEditCommunityModal(event);" data-id="' + item.community_id + '" data-title="' + item.title + '" data-description="' + item.description + '" data-invite="' + item.invite_url + '"><i class="fas fa-edit"></i> ' + i8ln('Edit Community') + '</button>'
    }
    str += '</div>'
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
        str += '<div class="d-grid">' +
            '<button class="btn btn-primary btn-sm" role="button" onclick="openConvertPortalModal(event);" data-id="' + item.external_id + '"><i class="fas fa-sync-alt convert-portal"></i>' + ' ' + i8ln('Convert portal') + '</button>' +
            '</div>'
    }
    str += '<center><div>' + i8ln('Last updated') + ': ' + updated + '</div></center>' +
        '<center><div>' + i8ln('Date imported') + ': ' + imported + '</div></center>' +
        '<center>' +
        '<a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item['lat'] + ',' + item['lon'] + ');" title="' + i8ln('View in Maps') + '">' +
        '<i class="fas fa-road"></i> ' + item['lat'].toFixed(6) + ' , ' + item['lon'].toFixed(7) + '</a> - ' +
        '<a href="./?lat=' + item['lat'] + '&lon=' + item['lon'] + '&zoom=18"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a>' +
        '</center>'
    if (!noDeletePortal) {
        str += '<i class="fas fa-trash-alt delete-portal" onclick="deletePortal(event);" data-id="' + item.external_id + '"></i>'
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
    str += '<div class="d-grid">'
    if (!noEditPoi) {
        str += '<button class="btn btn-primary btn-sm" role="button" onclick="openEditPoiModal(event);" data-id="' + item.poi_id + '" data-name="' + item.name + '" data-description="' + item.description + '" data-notes="' + item.notes + '" data-poiimage="' + item.poiimageurl + '" data-poisurrounding="' + item.poisurroundingurl + '"><i class="fas fa-edit edit-poi"></i> ' + i8ln('Edit POI') + '</button>'
    }
    if (!noMarkPoi) {
        str += '<button class="btn btn-primary btn-sm" role="button" onclick="openMarkPoiModal(event);" data-id="' + item.poi_id + '"><i class="fas fa-sync-alt convert-poi"></i> ' + i8ln('Mark POI') + '</button>'
    }
    str += '</div>'
    str += '<center><a href="javascript:void(0);" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ');" title="' + i8ln('View in Maps') + '"><i class="fas fa-road"></i> ' + item.lat.toFixed(5) + ' , ' + item.lon.toFixed(5) + '</a> - <a href="./?lat=' + item.lat + '&lon=' + item.lon + '&zoom=18"><i class="far fa-share-square" aria-hidden="true" style="position:relative;top:3px;left:0px;color:#26c300;font-size:20px;"></i></a></center>'
    return str
}

function deletePortal(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var portalid = button.data('id')
    if (portalid && portalid !== '') {
        if (confirm(i8ln('I confirm that this portal does not longer exist. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Error Deleting portal'), i8ln('Oops something went wrong.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Deleting portal'), i8ln('Deleting portal successful.'), 'true')
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
        if (confirm(i8ln('I confirm that this poi has been accepted through niantic or is not eligible as POI. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Error Deleting poi'), i8ln('Oops something went wrong.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Deleting poi'), i8ln('Deleting portal successful'), 'true')
                    jQuery('label[for="poi-switch"]').click()
                    jQuery('label[for="poi-switch"]').click()
                }
            })
        }
    }
}

function setupSpawnpointMarker(item) {
    var color = ''
    if (item['time'] !== null) {
        color = 'green'
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

    var rangeCircleOpts = {
        color: '#2ECC71',
        radius: 70, // meters
        center: [item['latitude'], item['longitude']],
        fillColor: '#2ECC71',
        fillOpacity: 0.2,
        weight: 1
    }

    marker.rangeCircle = L.circle([item['latitude'], item['longitude']], rangeCircleOpts)
    liveScanGroup.addLayer(marker.rangeCircle)

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
        var keepMons = false
        if (Store.get('showMissingIVOnly') === true) {
            keepMons = (mapData.pokemons[key]['individual_attack'] === null || mapData.pokemons[key]['individual_defense'] === null || mapData.pokemons[key]['individual_stamina'] === null)
        } else {
            keepMons = (minIV === 0 && minLevel === 0 && minLLRank === 0 && minGLRank === 0 && minULRank === 0)

            if (!keepMons) {
                if (mapData.pokemons[key]['individual_attack'] !== null && mapData.pokemons[key]['individual_defense'] !== null && mapData.pokemons[key]['individual_stamina'] !== null) {
                    if (Store.get('showZeroIv') === true && mapData.pokemons[key]['individual_attack'] === 0 && mapData.pokemons[key]['individual_defense'] === 0 && mapData.pokemons[key]['individual_stamina'] === 0) {
                        keepMons = true
                    } else if (Store.get('showHundoIv') === true && mapData.pokemons[key]['individual_attack'] === 15 && mapData.pokemons[key]['individual_defense'] === 15 && mapData.pokemons[key]['individual_stamina'] === 15) {
                        keepMons = true
                    }
                }

                if (!keepMons) {
                    if (mapData.pokemons[key]['size'] !== null) {
                        if (Store.get('showXXS') === true && mapData.pokemons[key]['size'] === 'XXS') {
                            keepMons = true
                        } else if (Store.get('showXXL') === true && mapData.pokemons[key]['size'] === 'XXL') {
                            keepMons = true
                        }
                    }

                    if (!keepMons) {
                        var keepPvp = true
                        if (minLLRank > 0 || minGLRank > 0 || minULRank > 0) {
                            keepPvp = false
                            if (minLLRank > 0 && mapData.pokemons[key]['pvp_rankings_little_league_best'] !== null && mapData.pokemons[key]['pvp_rankings_little_league_best'] <= minLLRank) {
                                keepPvp = true
                            } else if (minGLRank > 0 && mapData.pokemons[key]['pvp_rankings_great_league_best'] !== null && mapData.pokemons[key]['pvp_rankings_great_league_best'] <= minGLRank) {
                                keepPvp = true
                            } else if (minULRank > 0 && mapData.pokemons[key]['pvp_rankings_ultra_league_best'] !== null && mapData.pokemons[key]['pvp_rankings_ultra_league_best'] <= minULRank) {
                                keepPvp = true
                            }
                            keepMons = (Store.get('showIndependantPvpAndStats') === true && keepPvp)
                        }

                        if (!keepMons) {
                            var keepMinIvLvl = (excludedMinIV.includes(mapData.pokemons[key]['pokemon_id']) === true || ((minIV === 0 || (mapData.pokemons[key]['iv'] !== null && mapData.pokemons[key]['iv'] >= minIV)) && (minLevel === 0 || (mapData.pokemons[key]['level'] !== null && mapData.pokemons[key]['level'] >= minLevel))))
                            keepMons = ((Store.get('showIndependantPvpAndStats') === true && keepMinIvLvl) || (Store.get('showIndependantPvpAndStats') === false && keepMinIvLvl && keepPvp))
                        }
                    }
                }
            }
        }

        if (
            mapData.pokemons[key]['disappear_time'] < new Date().getTime() ||
            (
                (excludedPokemon.indexOf(mapData.pokemons[key]['pokemon_id']) >= 0 ||
                    isTemporaryHidden(mapData.pokemons[key]['pokemon_id']) ||
                    (keepMons === false) ||
                    (Store.get('showBigKarp') === true && mapData.pokemons[key]['pokemon_id'] === 129 && (mapData.pokemons[key]['weight'] < 13.14 || mapData.pokemons[key]['weight'] === null)) ||
                    (Store.get('showTinyRat') === true && mapData.pokemons[key]['pokemon_id'] === 19 && (mapData.pokemons[key]['weight'] > 2.40 || mapData.pokemons[key]['weight'] === null)) ||
                    (Store.get('showDespawnTimeType') === 1 && mapData.pokemons[key]['expire_timestamp_verified'] === 0) ||
                    (Store.get('showDespawnTimeType') === 2 && (mapData.pokemons[key]['expire_timestamp_verified'] > 0 || mapData.pokemons[key]['spawn_id'] === null)) ||
                    (Store.get('showDespawnTimeType') === 3 && mapData.pokemons[key]['expire_timestamp_verified'] > 0) ||
                    (Store.get('showPokemonGender') === 1 && mapData.pokemons[key]['gender'] !== 1) ||
                    (Store.get('showPokemonGender') === 2 && mapData.pokemons[key]['gender'] !== 2)
                ) &&
                encounterId !== mapData.pokemons[key]['encounter_id']
            )
        ) {
            if (mapData.pokemons[key].marker.rangeCircle) {
                markers.removeLayer(mapData.pokemons[key].marker.rangeCircle)
                delete mapData.pokemons[key].marker.rangeCircle
            }
            markers.removeLayer(mapData.pokemons[key].marker)
            delete mapData.pokemons[key]
        }
    })
    if (Store.get('showQuests')) {
        var d = new Date()
        var lastMidnight = d.setHours(0, 0, 0, 0) / 1000

        $.each(mapData.pokestops, function (key, value) {
            if (Number(mapData.pokestops[key]['quest_timestamp']) < lastMidnight) {
                if (mapData.pokestops[key].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[key].marker.rangeCircle)
                    delete mapData.pokestops[key].marker.rangeCircle
                }
                if (mapData.pokestops[key].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.pokestops[key].marker.placementRangeCircle)
                    delete mapData.pokestops[key].marker.placementRangeCircle
                }
                markers.removeLayer(mapData.pokestops[key].marker)
                delete mapData.pokestops[key]
            }
        })
    }
    if (!Store.get('showGyms') && Store.get('showRaids')) {
        $.each(mapData.gyms, function (key, value) {
            if ((((excludedRaidboss.indexOf(Number(mapData.gyms[key]['raid_pokemon_id'])) > -1) && mapData.gyms[key]['raid_pokemon_id'] > 0) && (mapData.gyms[key]['raid_start'] < new Date().getTime() && mapData.gyms[key]['raid_end'] > new Date().getTime())) || ((excludedRaidegg.indexOf(Number(mapData.gyms[key]['raid_level'])) > -1) && mapData.gyms[key]['raid_start'] > new Date().getTime()) || ((excludedRaidegg.indexOf(Number(mapData.gyms[key]['raid_level']) + 15) > -1) && (mapData.gyms[key]['raid_start'] < new Date().getTime() && (mapData.gyms[key]['raid_pokemon_id'] <= 0)))) {
                if (mapData.gyms[key].marker.rangeCircle) {
                    markers.removeLayer(mapData.gyms[key].marker.rangeCircle)
                    delete mapData.gyms[key].marker.rangeCircle
                }
                if (mapData.gyms[key].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.gyms[key].marker.placementRangeCircle)
                    delete mapData.gyms[key].marker.placementRangeCircle
                }
                markers.removeLayer(mapData.gyms[key].marker)
                delete mapData.gyms[key]
            }
        })
    }
    if (Store.get('showNests')) {
        $.each(mapData.nests, function (key, value) {
            if (Number(mapData.nests[key]['pokemon_avg']) < Store.get('showNestAvg')) {
                if (mapData.nests[key].marker.rangeCircle) {
                    markers.removeLayer(mapData.nests[key].marker.rangeCircle)
                    delete mapData.nests[key].marker.rangeCircle
                }
                markers.removeLayer(mapData.nests[key].marker)
                delete mapData.nests[key]
            }
        })
    }
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
            if (!marker.rangeCircle) {
                // no range circle yet...let's create one
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
                    delete marker.rangeCircle
                }
            }
        }
        if (show && placementRangeMarkers.indexOf(type) !== -1) {
            if (!marker.placementRangeCircle) {
                // there's no placement range circle yet... let's create one
                // but only if range is active
                if (isPlacementRangeActive(map)) {
                    marker.placementRangeCircle = addPlacementRangeCircle(marker, map)
                }
            } else {
                // there's already a placement range circle... let's do something with it
                if (isPlacementRangeActive(map)) {
                    // display it... placement range is active
                    markers.addLayer(marker.placementRangeCircle)
                } else {
                    // delete it... placement range isn't active
                    markers.removeLayer(marker.placementRangeCircle)
                    delete marker.placementRangeCircle
                }
            }
        }
    })
}

function loadRawData() {
    var loadPokemon = Store.get('showPokemon')
    var loadGyms = Store.get('showGyms')
    var loadRaids = Store.get('showRaids')
    var loadPokestops = Store.get('showPokestops')
    var loadLures = Store.get('showLures')
    var loadRocket = Store.get('showRocket')
    var loadEventStops = Store.get('showEventStops')
    var loadQuests = Store.get('showQuests')
    var showQuestsWithTaskAR = Store.get('showQuestsWithTaskAR')
    var loadDustamount = Store.get('showDustAmount')
    var loadXpamount = Store.get('showXpAmount')
    var loadNestAvg = Store.get('showNestAvg')
    var loadNests = Store.get('showNests')
    var loadCommunities = Store.get('showCommunities')
    var loadPortals = Store.get('showPortals')
    var loadPois = Store.get('showPoi')
    var loadNewPortalsOnly = Store.get('showNewPortalsOnly')
    var loadSpawnpoints = Store.get('showSpawnpoints')
    var loadScanLocation = Store.get('showScanLocation')
    var loadMinIV = Store.get('remember_text_min_iv')
    var loadMinLevel = Store.get('remember_text_min_level')
    var bigKarp = Boolean(Store.get('showBigKarp'))
    var tinyRat = Boolean(Store.get('showTinyRat'))
    var zeroIv = Boolean(Store.get('showZeroIv'))
    var hundoIv = Boolean(Store.get('showHundoIv'))
    var xxs = Boolean(Store.get('showXXS'))
    var xxl = Boolean(Store.get('showXXL'))
    var independantPvpAndStats = Boolean(Store.get('showIndependantPvpAndStats'))
    var minLLRank = Store.get('remember_text_min_ll_rank')
    var minGLRank = Store.get('remember_text_min_gl_rank')
    var minULRank = Store.get('remember_text_min_ul_rank')
    var despawnTimeType = Store.get('showDespawnTimeType')
    var pokemonGender = Store.get('showPokemonGender')
    var missingIvOnly = Store.get('showMissingIVOnly')
    var exEligible = Boolean(Store.get('exEligible'))
    var bounds = map.getBounds()
    var swPoint = bounds.getSouthWest()
    var nePoint = bounds.getNorthEast()
    var swLat = swPoint.lat
    var swLng = swPoint.lng
    var neLat = nePoint.lat
    var neLng = nePoint.lng

    var statsOpen = $('#statsModal').hasClass('show')
    var loadOverviewStats = $('#nav-overview-stats-tab').hasClass('active') && statsOpen
    var loadPokemonStats = $('#nav-pokemon-stats-tab').hasClass('active') && statsOpen
    var loadRewardStats = $('#nav-reward-stats-tab').hasClass('active') && statsOpen
    var loadShinyStats = $('#nav-shiny-stats-tab').hasClass('active') && statsOpen

    return $.ajax({
        url: 'raw_data',
        type: 'POST',
        timeout: 300000,
        data: {
            'timestamp': timestamp,
            'pokemon': loadPokemon,
            'loadOverviewStats': loadOverviewStats,
            'loadPokemonStats': loadPokemonStats,
            'loadRewardStats': loadRewardStats,
            'loadShinyStats': loadShinyStats,
            'lastpokemon': lastpokemon,
            'pokestops': loadPokestops,
            'lures': loadLures,
            'rocket': loadRocket,
            'eventstops': loadEventStops,
            'quests': loadQuests,
            'quests_with_ar': showQuestsWithTaskAR,
            'dustamount': loadDustamount,
            'reloaddustamount': reloaddustamount,
            'xpamount': loadXpamount,
            'reloadxpamount': reloadxpamount,
            'nestavg': loadNestAvg,
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
            'raids': loadRaids,
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
            'zeroIv': zeroIv,
            'hundoIv': hundoIv,
            'xxs': xxs,
            'xxl': xxl,
            'independantPvpAndStats': independantPvpAndStats,
            'minLLRank': minLLRank,
            'prevMinLLRank': prevMinLLRank,
            'minGLRank': minGLRank,
            'prevMinGLRank': prevMinGLRank,
            'minULRank': minULRank,
            'prevMinULRank': prevMinULRank,
            'despawnTimeType': despawnTimeType,
            'pokemonGender': pokemonGender,
            'missingIvOnly': missingIvOnly,
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
            'qereids': String(reincludedQuestsEnergy),
            'qeeids': String(questsExcludedEnergy),
            'qcreids': String(reincludedQuestsCandy),
            'qceids': String(questsExcludedCandy),
            'geids': String(excludedGrunts),
            'greids': String(reincludedGrunts),
            'rbeids': String(excludedRaidboss),
            'rbreids': String(reincludedRaidboss),
            'reeids': String(excludedRaidegg),
            'rereids': String(reincludedRaidegg),
            'token': token,
            'encId': encounterId
        },
        dataType: 'json',
        cache: false,
        beforeSend: function beforeSend() {
            if (maxLatLng > 0 && (((neLat - swLat) > maxLatLng) || ((neLng - swLng) > maxLatLng))) {
                sendToast('warning', i8ln('Max zoom'), i8ln('Please zoom in to get data'), 'true')
                return false
            }
            if (rawDataIsLoading) {
                return false
            } else {
                rawDataIsLoading = true
            }
        },
        error: function (xhr) {
            // Display error toast
            switch (xhr.status) {
                case 400:
                    sendToast('danger', i8ln('Not Acceptable'), i8ln('Please check connectivity or reduce marker settings.'), 'true')
                    setTimeout(function () { window.location.href = './logout' }, 5000)
                    break
                case 401:
                    sendToast('danger', i8ln('Unauthorized'), i8ln('Another device just logged in with the same account.'), 'true')
                    setTimeout(function () { window.location.href = './login?action=login&error=invalid-token' }, 5000)
                    break
                case 403:
                    sendToast('danger', i8ln('Forbidden'), i8ln('This action is not allowed.'), 'true')
                    setTimeout(function () { window.location.href = './logout' }, 5000)
                    break
                case 404:
                    sendToast('danger', i8ln('Not found'), i8ln('Session tokens haven\'t been found.'), 'true')
                    setTimeout(function () { window.location.href = './login?action=login&error=no-id' }, 5000)
                    break
                case 413:
                    sendToast('danger', i8ln('You got me overwhelmed'), i8ln('This is too much data for me please zoom in.'), 'true')
                    break
                default:
                    sendToast('danger', i8ln('Webserver error'), i8ln('Server went away...'), 'true')
            }
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
            sendToast('danger', i8ln('Error getting weather'), i8ln('Please check connectivity or reduce marker settings.'), 'true')
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
            sendToast('danger', i8ln('Error getting weather'), i8ln('Please check connectivity or reduce marker settings.'), 'true')
        },
        complete: function complete() {

        }
    })
}
function searchForItem(lat, lon, term, type, field) {
    clearTimeout(searchDelay)
    searchDelay = setTimeout(function () {
        if (term !== '') {
            var showQuestsWithTaskAR = Store.get('showQuestsWithTaskAR')
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
                    'lon': lon,
                    'quests_with_ar': showQuestsWithTaskAR
                },
                error: function error(xhr) {
                    // Display error toast
                    switch (xhr.status) {
                        case 404:
                            sendToast('warning', i8ln('Error searching'), i8ln('Could not find any results please try again.'), 'true')
                            break
                    }
                }
            }).done(function (data) {
                if (data) {
                    var par = field.parent()
                    var sr = par.find('.search-results')
                    sr.html('')
                    $.each(data.reward, function (i, element) {
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
                            if (element.quest_reward_type === 7) {
                                html += '<span style="background:url(' + getIcon(iconpath.pokemon, 'pokemon', '.png', element.reward_pokemon_id, 0, element.reward_pokemon_formid, element.reward_pokemon_costumeid, element.reward_pokemon_genderid, element.reward_pokemon_shiny) + ') no-repeat;" class="i-icon" ></span>'
                            }
                            if (element.quest_reward_type === 2) {
                                html += '<span style="background:url(' + getIcon(iconpath.reward, 'reward/item', '.png', element.reward_item_id, element.reward_amount) + ') no-repeat;" class="i-icon" ></span>'
                            }
                            if (element.quest_reward_type === 4) {
                                html += '<span style="background:url(' + getIcon(iconpath.reward, 'reward/candy', '.png', element.reward_pokemon_id, element.reward_amount) + ') no-repeat;" class="i-icon" ></span>'
                            }
                            if (element.quest_reward_type === 3) {
                                html += '<span style="background:url(' + getIcon(iconpath.reward, 'reward/stardust', '.png', element.reward_amount) + ') no-repeat;" class="i-icon" ></span>'
                            }
                            if (element.quest_reward_type === 1) {
                                html += '<span style="background:url(' + getIcon(iconpath.reward, 'reward/experience', '.png', element.reward_amount) + ') no-repeat;" class="i-icon" ></span>'
                            }
                            if (element.quest_reward_type === 12) {
                                html += '<span style="background:url(' + getIcon(iconpath.reward, 'reward/mega_resource', '.png', element.reward_pokemon_id, element.reward_amount) + ') no-repeat;" class="i-icon" ></span>'
                            }
                        }
                        html += '<div class="cont">'
                        if (sr.hasClass('reward-results')) {
                            if (element.reward_pokemon_name !== null) {
                                html += '<span class="reward" style="font-weight:bold">' + element.reward_pokemon_name + '</span><span>&nbsp;-&#32;</span>'
                            }
                            if (element.reward_item_name !== null) {
                                html += '<span class="reward" style="font-weight:bold">' + element.reward_item_name + '</span><span>&nbsp;-&#32;</span>'
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
                        var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                        if (sr.hasClass('nest-results')) {
                            html += '<span style="background:url(' + getIcon(iconpath.pokemon, 'pokemon', '.png', element.pokemon_id) + ') no-repeat;" class="i-icon" ></span>'
                        }
                        html += '<div class="cont">' +
                        '<span class="name" style="font-weight:bold">' + element.name + '</span>' + '<span class="distance" style="font-weight:bold">&nbsp;-&#32;' + element.distance + defaultUnit + '</span>' +
                        '</div></div>' +
                        '</li>'
                        sr.append(html)
                    })
                    $.each(data.pokemon, function (i, element) {
                        var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                        if (sr.hasClass('pokemon-results')) {
                            html += '<span style="background:url(' + getIcon(iconpath.pokemon, 'pokemon', '.png', element.pokemon_id) + ') no-repeat;" class="i-icon" ></span>'
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
    }, 300)
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
        point = point.parent().parent()
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
    $('.modal').modal('hide')
}

function manualPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopName = form.find('[name="pokestop-name"]').val()
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Error Submitting Pokéstop'), i8ln('Could not submit Pokéstop.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting Pokéstop'), pokestopName + ' ' + i8ln('successful submitted.'), 'true')
                    lastpokestops = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a Pokéstop inside the scan area is not allowed'))) {
            $('.modal').modal('hide')
        }
    }
}

function manualGymData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var gymName = form.find('[name="gym-name"]').val()
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Error Submitting Gym'), i8ln('Could not submit Gym.'), 'true')
                },
                complete: function complete() {
                    sendToast('danger', i8ln('Submitting Gym'), gymName + ' ' + i8ln('successful submitted.'), 'true')
                    lastgyms = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a Gym inside the scan area is not allowed'))) {
            $('.modal').modal('hide')
        }
    }
}
function manualPokemonData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent().parent()
    var pokemonId = form.find('.pokemonID').val()
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Error Submitting Pokémon'), i8ln('Could not submit Pokémon.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting Pokémon'), pokeList[pokemonId - 1].name + ' ' + i8ln('successful submitted.'), 'true')
                    lastpokemon = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    } else if (scanArea) {
        if (confirm(i8ln('Adding a wild spawn inside the scan area is not allowed'))) {
            $('.modal').modal('hide')
        }
    }
}
function deleteGym(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var gymId = button.data('id')
    if (gymId && gymId !== '') {
        if (confirm(i8ln('I confirm that I want to delete this gym. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Error Deleting Gym'), i8ln('Could not delete Gym.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Deleting Gym'), i8ln('Gym successful deleted.'), 'true')
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
    var gymId = form.find('[name="renamegymid"]').val()
    var gymName = form.find('[name="gym-rename"]').val()
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
                    sendToast('danger', i8ln('Error Gym'), i8ln('Could not rename Gym.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Gym rename'), gymName + ' ' + i8ln('renamed'), 'true')
                    jQuery('label[for="gyms-switch"]').click()
                    jQuery('label[for="gyms-switch"]').click()
                    lastgyms = false
                    updateMap()
                    $('.modal').modal('hide')
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
                    sendToast('danger', i8ln('Error EX Gym'), i8ln('Could not change Gym to EX Gym.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Change EX Gym'), i8ln('Successful changed Gym to EX Gym.'), 'true')
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
        if (confirm(i8ln('I confirm that I want to delete this pokestop. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Deleting Pokéstop'), i8ln('Could not delete Pokéstop.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Deleting Pokéstop'), i8ln('Successful deleted Pokéstop.'), 'true')
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                }
            })
        }
    }
}
function renamePokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopId = form.find('[name="renamepokestopid"]').val()
    var pokestopName = form.find('[name="pokestop-rename"]').val()
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
                    sendToast('danger', i8ln('Rename Pokéstop'), i8ln('Could not rename Pokéstop.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Rename Pokéstop'), pokestopName + ' ' + i8ln('renamed'), 'true')
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}
function convertPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopId = form.find('[name="convertpokestopid"]').val()
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
                    sendToast('danger', i8ln('Error converting Pokéstop'), i8ln('Pokéstop ID got lost somewhere.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Converting Pokéstop'), i8ln('Pokéstop converted to Gym'), 'true')
                    lastgyms = false
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function convertPortalData(event, targetType) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('[name="convertportalid"]').val()
    var confirmText = ''
    var errorText = ''
    if (portalId && portalId !== '') {
        switch (targetType) {
            case '1':
                confirmText = i8ln('I confirm this portal is a pokestop')
                errorText = i8ln('Error converting to Pokestop')
                break
            case '2':
                confirmText = i8ln('I confirm this portal is a gym')
                errorText = i8ln('Error converting to Gym')
                break
        }
        if (confirm(confirmText)) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'convertportal',
                    'portalId': portalId,
                    'targetType': targetType
                },
                error: function error() {
                    // Display error toast
                    sendToast('danger', errorText, i8ln('Portal ID got lost somewhere.'), 'true')
                },
                complete: function complete() {
                    switch (targetType) {
                        case '1':
                            sendToast('success', i8ln('Convert Portal'), i8ln('Portal successful converted to Pokéstop'), 'true')
                            jQuery('label[for="pokestops-switch"]').click()
                            jQuery('label[for="pokestops-switch"]').click()
                            lastpokestops = false
                            break
                        case '2':
                            sendToast('success', i8ln('Convert Portal'), i8ln('Portal successful converted to Gym'), 'true')
                            lastgyms = false
                            break
                    }
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function markPortalData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var portalId = form.find('[name="convertportalid"]').val()
    if (portalId && portalId !== '') {
        if (confirm(i8ln('I confirm this portal is not a Pokéstop or Gym'))) {
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
                    sendToast('danger', i8ln('Error marking portal'), i8ln('Portal ID got lost somewhere.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Marking portal'), i8ln('Portal marked as not a Pokéstop or Gym'), 'true')
                    jQuery('label[for="portals-switch"]').click()
                    jQuery('label[for="portals-switch"]').click()
                    lastportals = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function deleteNest(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var nestid = button.data('id')
    if (nestid && nestid !== '') {
        if (confirm(i8ln('I confirm that I want to delete this nest. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Error Deleting nest'), i8ln('Could not delete Nest'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Deleting nest'), i8ln('Successful deleted Nest'), 'true')
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
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Submitting nest'), i8ln('Could not submit Nest'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting nest'), pokeList[pokemonId - 1].name + ' ' + i8ln('nest successful submitted'), 'true')
                    lastnests = false
                    updateMap()
                    jQuery('label[for="nests-switch"]').click()
                    jQuery('label[for="nests-switch"]').click()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function manualNestData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent().parent()
    var nestId = form.find('[name="editnestid"]').val()
    var pokemonId = form.find('.pokemonID').val()
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
                    sendToast('danger', i8ln('Submitting nest'), i8ln('Could not change Nest'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting nest'), i8ln('Nest changed to ') + pokeList[pokemonId - 1].name, 'true')
                    lastnests = false
                    updateMap()
                    jQuery('label[for="nests-switch"]').click()
                    jQuery('label[for="nests-switch"]').click()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function manualQuestData(event) { // eslint-disable-line no-unused-vars
    var questType = $('#questTypeList').val()
    var questTarget = $('#questAmountSelect').val()
    var conditionType = $('#conditionTypeList').val()
    var catchPokemon = $('#pokeCatch').val()
    var catchPokemonCategory = $('#typeCatch').val()
    var raidLevel = $('#raidLevel').val()
    var throwType = $('#throwType').val()
    var curveThrow = $('#curveThrow').val()
    var rewardType = $('#rewardTypeList').val()
    var encounter = $('#pokeReward').val()
    var item = $('#itemReward').val()
    var itemamount = $('#itemAmount').val()
    var dust = $('#dustAmount').val()
    var xp = $('#xpAmount').val()
    var pokestopId = $('#questpokestopid').val()
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
                    'xp': xp,
                    'pokestopId': pokestopId
                },
                error: function error() {
                    // Display error toast
                    sendToast('danger', i8ln('Submitting quest'), i8ln('Could not submit quest'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting quest'), i8ln('Successful submitted quest'), 'true')
                    lastpokestops = false
                    updateMap()
                    jQuery('label[for="pokestops-switch"]').click()
                    jQuery('label[for="pokestops-switch"]').click()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function manualRaidData(event) { // eslint-disable-line no-unused-vars
    var pokemonId = $('#manualraidpokemonid').val()
    gymId = $('#manualraidgymid').val()
    var monTime = $('#mon_time').val()
    var eggTime = $('#egg_time').val()
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
                    sendToast('danger', i8ln('Submitting raid'), i8ln('Could not submit raid'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting raid'), pokeList[pokemonId - 1].name + ' ' + i8ln('raid submitted'), 'true')
                    lastgyms = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}
function submitNewCommunity(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Submitting community'), i8ln('Make sure all fields are filled and the invite link is a valid Discord, Telegram or Whatsapp link.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submitting community'), communityName + ' ' + i8ln('community submitted'), 'true')
                    jQuery('label[for="communities-switch"]').click()
                    jQuery('label[for="communities-switch"]').click()
                    lastcommunities = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function editCommunityData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var communityId = form.find('[name="editcommunityid"]').val()
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
                    sendToast('danger', i8ln('Edit community'), i8ln('Form fields are empty or an invalid url is found, please check the form.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Edit community'), communityName + ' ' + i8ln('edit successful'), 'true')
                    jQuery('label[for="communities-switch"]').click()
                    jQuery('label[for="communities-switch"]').click()
                    lastpokestops = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function deleteCommunity(event) { // eslint-disable-line no-unused-vars
    var button = $(event.target)
    var communityid = button.data('id')
    if (communityid && communityid !== '') {
        if (confirm(i8ln('I confirm that I want to delete this community. This is a permanent removal'))) {
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
                    sendToast('danger', i8ln('Delete community'), i8ln('Oops something went wrong.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Delete community'), i8ln('Community successful deleted'), 'true')
                    jQuery('label[for="communities-switch"]').click()
                    jQuery('label[for="communities-switch"]').click()
                }
            })
        }
    }
}

function submitPoi(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var lat = $('#submitModal .submitLatitude').val()
    var lon = $('#submitModal .submitLongitude').val()
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
                    sendToast('danger', i8ln('Submit POI'), i8ln('Oops something went wrong.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Submit POI'), poiName + ' ' + i8ln('successful submitted'), 'true')
                    jQuery('label[for="poi-switch"]').click()
                    jQuery('label[for="poi-switch"]').click()
                    lastpois = false
                    updateMap()
                    $('.loader').hide()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function editPoiData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var poiId = form.find('[name="editpoiid"]').val()
    var poiName = form.find('[name="poi-name"]').val()
    var poiDescription = form.find('[name="poi-description"]').val()
    var poiNotes = form.find('[name="poi-notes"]').val()
    var poiImage = form.find('[id="edit-preview-poi-image"]').attr('src')
    var poiSurrounding = form.find('[id="edit-preview-poi-surrounding"]').attr('src')
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
                    sendToast('danger', i8ln('Edit POI'), i8ln('Oops something went wrong.'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Edit POI'), poiName + ' ' + i8ln('successful editted'), 'true')
                    jQuery('label[for="poi-switch"]').click()
                    jQuery('label[for="poi-switch"]').click()
                    lastpois = false
                    updateMap()
                    $('.loader').hide()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function markPoi(event, poiMarkType) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent()
    var poiId = form.find('[name="markpoiid"]').val()
    var confirmText = ''
    if (poiId && poiId !== '') {
        switch (poiMarkType) {
            case '2':
                confirmText = i8ln('I confirm this candidate is submitted to OPR')
                break
            case '3':
                confirmText = i8ln('I confirm this candidate is declined by OPR')
                break
            case '4':
                confirmText = i8ln('I confirm this candidate is declined by OPR but can be resubmitted as candidate')
                break
            case '5':
                confirmText = i8ln('I confirm this is not a eligible candidate to submit to OPR')
                break
        }
        if (confirm(confirmText)) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'markpoi',
                    'poiMarkType': poiMarkType,
                    'poiId': poiId
                },
                error: function error() {
                    // Display error toast
                    sendToast('danger', i8ln('Marking POI'), i8ln('Error marking POI'), 'true')
                },
                complete: function complete() {
                    sendToast('success', i8ln('Marking POI'), i8ln('POI marked successful'), 'true')
                    jQuery('label[for="poi-switch"]').click()
                    jQuery('label[for="poi-switch"]').click()
                    lastpois = false
                    updateMap()
                    $('.modal').modal('hide')
                }
            })
        }
    }
}

function openNestModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#editnestid').val(val)
    $('#editNestModal').modal('show')
}
function openRaidModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#manualraidgymid').val(val)
    $('#manualRaidModal').modal('show')
}

function openQuestModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#questpokestopid').val(val)
    $(function () {
        $('#questTypeList').change(function () {
            if (Number($(this).val()) > 0) {
                $('#questAmountList').collapse('show')
            } else {
                $('#questAmountList').collapse('hide')
            }
        })
        $('#conditionTypeList').change(function () {
            if ($(this).val() === '1') {
                $('#pokeCatchList').collapse('hide')
                $('#typeCatchList').collapse('show')
                $('#raidLevelList').collapse('hide')
                $('#throwTypeList').collapse('hide')
                $('#curveThrow').collapse('hide')
            } else if ($(this).val() === '2') {
                $('#pokeCatchList').collapse('show')
                $('#typeCatchList').collapse('hide')
                $('#raidLevelList').collapse('hide')
                $('#throwTypeList').collapse('hide')
                $('#curveThrow').collapse('hide')
            } else if ($(this).val() === '7') {
                $('#pokeCatchList').collapse('hide')
                $('#typeCatchList').collapse('hide')
                $('#raidLevelList').collapse('show')
                $('#throwTypeList').collapse('hide')
                $('#curveThrow').collapse('hide')
            } else if ($(this).val() === '8' || $(this).val() === '14') {
                $('#pokeCatchList').collapse('hide')
                $('#typeCatchList').collapse('hide')
                $('#raidLevelList').collapse('hide')
                $('#throwTypeList').collapse('show')
                $('#curveThrow').collapse('show')
            } else {
                $('#pokeCatchList').collapse('hide')
                $('#typeCatchList').collapse('hide')
                $('#raidLevelList').collapse('hide')
                $('#throwTypeList').collapse('hide')
                $('#curveThrow').collapse('hide')
            }
        })
        $('#rewardTypeList').change(function () {
            if ($(this).val() === '1') {
                $('#itemRewardList').collapse('hide')
                $('#itemAmountList').collapse('hide')
                $('#dustAmountList').collapse('hide')
                $('#xpAmountList').collapse('show')
                $('#pokeRewardList').collapse('hide')
            } else if ($(this).val() === '2') {
                $('#itemRewardList').collapse('show')
                $('#itemAmountList').collapse('show')
                $('#dustAmountList').collapse('hide')
                $('#xpAmountList').collapse('hide')
                $('#pokeRewardList').collapse('hide')
            } else if ($(this).val() === '3') {
                $('#itemRewardList').collapse('hide')
                $('#itemAmountList').collapse('hide')
                $('#dustAmountList').collapse('show')
                $('#xpAmountList').collapse('hide')
                $('#pokeRewardList').collapse('hide')
            } else if ($(this).val() === '7') {
                $('#itemRewardList').collapse('hide')
                $('#itemAmountList').collapse('hide')
                $('#dustAmountList').collapse('hide')
                $('#xpAmountList').collapse('hide')
                $('#pokeRewardList').collapse('show')
            } else {
                $('#itemRewardList').collapse('hide')
                $('#itemAmountList').collapse('hide')
                $('#dustAmountList').collapse('hide')
                $('#xpAmountList').collapse('hide')
                $('#pokeRewardList').collapse('hide')
            }
        })
    })

    $('#manualQuestModal').modal('show')
}

function openRenamePokestopModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#renamepokestopid').val(val)
    $('#renamePokestopModal').modal('show')
}

function openRenameGymModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#renamegymid').val(val)
    $('#renameGymModal').modal('show')
}

function openConvertPokestopModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#convertpokestopid').val(val)
    $('#convertPokestopModal').modal('show')
}

function openConvertPortalModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#convertportalid').val(val)
    $('#convertPortalModal').modal('show')
}

function openEditCommunityModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    var title = $(event.target).data('title')
    var description = $(event.target).data('description')
    var invite = $(event.target).data('invite')
    $('#editcommunityid').val(val)
    $('#community-name').val(title)
    $('#community-description').val(description)
    $('#community-invite').val(invite)
    $('#editCommunityModal').modal('show')
}

function openEditPoiModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    var name = $(event.target).data('name')
    var description = $(event.target).data('description')
    var notes = $(event.target).data('notes')
    var poiimageurl = $(event.target).data('poiimage')
    var poisurroundingurl = $(event.target).data('poisurrounding')
    $('#editpoiid').val(val)
    $('#poi-name').val(name)
    $('#poi-description').val(description)
    $('#poi-notes').val(notes)
    $('#edit-preview-poi-image').attr('src', poiimageurl)
    $('#edit-preview-poi-surrounding').attr('src', poisurroundingurl)
    $('#editPoiModal').modal('show')
}

function openMarkPoiModal(event) { // eslint-disable-line no-unused-vars
    $('.modal').modal('hide')
    var val = $(event.target).data('id')
    $('#markpoiid').val(val)
    $('#markPoiModal').modal('show')
}

function openFullscreenModal(image) { // eslint-disable-line no-unused-vars
    var modalImg = document.getElementById('fullscreenimg')
    $('#fullscreenModal').modal('show')
    modalImg.src = image
}
function processPokemons(i, item) {
    if (!Store.get('showPokemon')) {
        return false // in case the checkbox was unchecked in the meantime.
    }
    if (item['disappear_time'] > Date.now() && ((encounterId && encounterId === item['encounter_id']) || (excludedPokemon.indexOf(item['pokemon_id']) < 0 && !isTemporaryHidden(item['pokemon_id'])))) {
        if (item['encounter_id'] in mapData.pokemons) {
            if ((mapData.pokemons[item['encounter_id']]['spawn_id'] !== item['spawn_id']) || (mapData.pokemons[item['encounter_id']]['pokemon_id'] !== item['pokemon_id']) || (mapData.pokemons[item['encounter_id']]['individual_attack'] !== item['individual_attack']) || (mapData.pokemons[item['encounter_id']]['individual_defense'] !== item['individual_defense']) || (mapData.pokemons[item['encounter_id']]['individual_stamina'] !== item['individual_stamina'])) {
                // updated information received. delete marker and item from dict
                if (mapData.pokemons[item['encounter_id']].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokemons[item['encounter_id']].marker.rangeCircle)
                    delete mapData.pokemons[item['encounter_id']].marker.rangeCircle
                }
                markers.removeLayer(mapData.pokemons[item['encounter_id']].marker)
                delete mapData.pokemons[item['encounter_id']]
            } else {
                // in mapData and appears up to date, skip
                return true
            }
        }

        // add marker to map and item to dict
        if (item.marker) {
            markers.removeLayer(item.marker)
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
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupPoiMarker(item)
        mapData.pois[item['poi_id']] = item
    }
}

function processPokestops(i, item, lastMidnight) {
    if (!Store.get('showPokestops')) {
        return false
    }

    var removePokestopFromMap = function removePokestopFromMap(pokestopid) {
        if (mapData.pokestops[pokestopid] && mapData.pokestops[pokestopid].marker) {
            if (mapData.pokestops[pokestopid].marker.rangeCircle) {
                markers.removeLayer(mapData.pokestops[pokestopid].marker.rangeCircle)
            }
            if (mapData.pokestops[pokestopid].marker.placementRangeCircle) {
                markers.removeLayer(mapData.pokestops[pokestopid].marker.placementRangeCircle)
            }
            markers.removeLayer(mapData.pokestops[pokestopid].marker)
            delete mapData.pokestops[pokestopid]
        }
    }

    if (Store.get('showLures') && !item['lure_expiration']) {
        removePokestopFromMap(item['pokestop_id'])
        return true
    }

    if (Store.get('showRocket') && !item['incident_expiration']) {
        removePokestopFromMap(item['pokestop_id'])
        return true
    }

    if (Store.get('showEventStops') && !item['eventstops_expiration']) {
        removePokestopFromMap(item['pokestop_id'])
        return true
    }

    if (Store.get('showQuests') && !pokestopMeetsQuestFilter(item, lastMidnight)) {
        removePokestopFromMap(item['pokestop_id'])
        return true
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

    if (item['pokestop_id'] in mapData.pokestops) {
        // existing pokestop, update marker on map
        item.marker = updatePokestopMarker(item, mapData.pokestops[item['pokestop_id']].marker)
    } else {
        // new pokestop, add marker to map
        item.marker = setupPokestopMarker(item)
    }

    mapData.pokestops[item['pokestop_id']] = item

    if (stopId && stopId === item['pokestop_id']) {
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

function pokestopMeetsQuestFilter(pokestop, lastMidnight) {
    if (pokestop['quest_type'] === 0 || lastMidnight > Number(pokestop['quest_timestamp'])) {
        return false
    } else if (pokestop['quest_reward_type'] === 12 && pokestop['reward_pokemon_id'] > 0 && questsExcludedEnergy.indexOf(pokestop['reward_pokemon_id']) > -1) {
        return false
    } else if (pokestop['quest_reward_type'] === 4 && pokestop['reward_pokemon_id'] > 0 && questsExcludedCandy.indexOf(pokestop['reward_pokemon_id']) > -1) {
        return false
    } else if (pokestop['quest_reward_type'] === 7 && pokestop['reward_pokemon_id'] > 0 && questsExcludedPokemon.indexOf(pokestop['reward_pokemon_id']) > -1) {
        return false
    } else if (pokestop['quest_reward_type'] === 2 && pokestop['reward_item_id'] > 0 && questsExcludedItem.indexOf(pokestop['reward_item_id']) > -1) {
        return false
    } else if (pokestop['quest_reward_type'] === 3 && (Store.get('showDustAmount') === 0 || Number(pokestop['reward_amount']) < Number(Store.get('showDustAmount')))) {
        return false
    } else if (pokestop['quest_reward_type'] === 1 && (Store.get('showXpAmount') === 0 || Number(pokestop['reward_amount']) < Number(Store.get('showXpAmount')))) {
        return false
    } else {
        return true
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
            value.marker = updatePokestopMarker(value, value.marker)
        }
        // change Team Rocket pokestop marker to normal when expired.
        if (value['incident_expiration'] > 0 && value['incident_expiration'] < currentTime && value['incident_expiration'] > (currentTime - 300000)) {
            value.marker = updatePokestopMarker(value, value.marker)
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
                }
                if (mapData.pokestops[value].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.placementRangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
                delete mapData.pokestops[value]
            }
        })
    }

    // remove non-rocket stops if show rocket only is selected
    if (Store.get('showRocket')) {
        $.each(mapData.pokestops, function (key, value) {
            if (value['incident_expiration'] < currentTime || excludedGrunts.indexOf(Number(value['grunt_type'])) > -1) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                }
                if (mapData.pokestops[value].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.placementRangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
                delete mapData.pokestops[value]
            }
        })
    }

    // remove event stops if show event stops only is selected
    if (Store.get('showEventStops')) {
        $.each(mapData.pokestops, function (key, value) {
            if (value['eventstops_expiration'] < currentTime) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                }
                if (mapData.pokestops[value].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.placementRangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
                delete mapData.pokestops[value]
            }
        })
    }

    // remove invalid quest stops if show quest only is selected
    if (Store.get('showQuests')) {
        $.each(mapData.pokestops, function (key, value) {
            if (!pokestopMeetsQuestFilter(value, lastMidnight)) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.placementRangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.placementRangeCircle)
                }
                if (mapData.pokestops[value].marker.rangeCircle) {
                    markers.removeLayer(mapData.pokestops[value].marker.rangeCircle)
                }
                markers.removeLayer(mapData.pokestops[value].marker)
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
            }
            if (mapData.gyms[gymid].marker.placementRangeCircle) {
                markers.removeLayer(mapData.gyms[gymid].marker.placementRangeCircle)
            }
            markers.removeLayer(mapData.gyms[gymid].marker)
            delete mapData.gyms[gymid]
        }
    }
    if (!Store.get('showGyms') && Store.get('showRaids')) {
        if (item.raid_end === undefined || item.raid_end < Date.now()) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }
    if (Store.get('showGyms') && !Store.get('showRaids')) {
        item.raid_end = 0
        item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
    }

    if (Store.get('showGyms') && Store.get('showRaids')) {
        var time = new Date().getTime()
        // Remove raidbosses from gym
        if (excludedRaidboss.indexOf(Number(item['raid_pokemon_id'])) > -1) {
            if (item['raid_pokemon_id'] > 0) {
                if (item['raid_end'] > time) {
                    item.raid_end = 0
                    item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
                }
            }
        }
        // Remove Raid eggs from gym
        if (excludedRaidegg.indexOf(Number(item['raid_level'])) > -1) {
            if (item['raid_pokemon_id'] <= 0) {
                if (item['raid_start'] > time) {
                    if (item['raid_end'] > time) {
                        item.raid_end = 0
                        item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
                    }
                }
            }
        }
        // Remove Broken Raid eggs from gym
        if (excludedRaidegg.indexOf(Number(item['raid_level']) + 15) > -1) {
            if (item['raid_pokemon_id'] <= 0) {
                if (item['raid_start'] < time) {
                    if (item['raid_end'] > time) {
                        item.raid_end = 0
                        item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
                    }
                }
            }
        }
        // Remove Raid eggs if only active
        if (!noActiveRaids && Store.get('activeRaids') && item.raid_end > Date.now()) {
            if (((item.raid_pokemon_id === undefined) || (item.raid_pokemon_id === null)) && item.raid_start > Date.now()) {
                item.raid_end = 0
                item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
            }
        }
        // Remove Raids if min max level
        if (!noMinMaxRaidLevel) {
            if ((raidLevel < Store.get('minRaidLevel') && item.raid_end > Date.now()) || (raidLevel > Store.get('maxRaidLevel') && item.raid_end > Date.now())) {
                item.raid_end = 0
                item.raid_level = item.raid_pokemon_cp = item.raid_pokemon_id = item.raid_pokemon_move_1 = item.raid_pokemon_move_1 = item.raid_pokemon_name = null
            }
        }
    }
    if (!Store.get('showGyms') && !noActiveRaids && Store.get('activeRaids') && item.raid_end > Date.now()) {
        if (((item.raid_pokemon_id === undefined) || (item.raid_pokemon_id === null)) && item.raid_start > Date.now()) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (!Store.get('showGyms') && !noMinMaxRaidLevel) {
        if (raidLevel < Store.get('minRaidLevel') && item.raid_end > Date.now()) {
            removeGymFromMap(item['gym_id'])
            return true
        }
        if (raidLevel > Store.get('maxRaidLevel') && item.raid_end > Date.now()) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (Store.get('exEligible') && (item.park === null || item.park === 0)) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (!noOpenSpot && Store.get('showOpenGymsOnly')) {
        if (item.slots_available === 0 && (item.raid_end === undefined || item.raid_end < Date.now())) {
            removeGymFromMap(item['gym_id'])
            return true
        }
    }

    if (!noTeams && Store.get('showTeamGymsOnly') && Store.get('showTeamGymsOnly') !== item.team_id && (item.raid_end === undefined || item.raid_end < Date.now())) {
        removeGymFromMap(item['gym_id'])
        return true
    }

    if (!noLastScan && Store.get('showLastUpdatedGymsOnly')) {
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

    if (!noMinMaxFreeSlots) {
        if (gymLevel < Store.get('minGymLevel') && (item.raid_end === undefined || item.raid_end < Date.now())) {
            removeGymFromMap(item['gym_id'])
            return true
        }
        if (gymLevel > Store.get('maxGymLevel') && (item.raid_end === undefined || item.raid_end < Date.now())) {
            removeGymFromMap(item['gym_id'])
            return true
        }
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
    if (gymId && gymId === item['gym_id']) {
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
    var name = item['uuid']
    var newLoc = [item['latitude'], item['longitude']]
    var oldLoc = null
    if (typeof deviceLocation[name] !== 'undefined') {
        oldLoc = deviceLocation[name]
    }
    if (oldLoc === null) {
        setupScanLocationMarker(item)
    }
    if ((oldLoc !== null) && (oldLoc[0] !== newLoc[0] || oldLoc[1] !== newLoc[1])) {
        var deviceMarkers = liveScanGroup.getLayers()
        for (i = 0; i < deviceMarkers.length; i++) {
            var lat = deviceMarkers[i].getLatLng().lat
            var lon = deviceMarkers[i].getLatLng().lng
            if (lat === oldLoc[0] && lon === oldLoc[1]) {
                liveScanGroup.removeLayer(deviceMarkers[i])
            }
        }
        setupScanLocationMarker(item)
    }
    deviceLocation[name] = [item['latitude'], item['longitude']]
}

function updateSpawnPoints() {
    if (!Store.get('showSpawnpoints')) {
        return false
    }

    $.each(mapData.spawnpoints, function (key, value) {
        if (map.getBounds().contains(value.marker.getLatLng())) {
            var color = ''
            if (value['time'] !== null) {
                color = 'green'
            } else {
                color = 'red'
            }
            value.marker.setStyle({color: color, fillColor: color})
        }
    })
}

function updateMap() {
    if (_mapLoaded) {
        var position = map.getCenter()
        var d = new Date()
        var lastMidnight = ''
        if (mapFork === 'mad') {
            lastMidnight = d.setHours(0, 0, 0, 0) / 1000
        } else {
            lastMidnight = 0
        }
        var currentHourTime = d.setMinutes(0, 0, 0) / 1000
        Store.set('startAtLastLocationPosition', {
            lat: position.lat,
            lng: position.lng,
            zoom: map.getZoom()
        })
        // lets try and get the s2 cell id in the middle
        var s2CellCenter = S2.keyToId(S2.latLngToKey(position.lat, position.lng, 10))
        if (s2CellCenter && (String(s2CellCenter) !== $('#currentWeather').data('current-cell') || $('#currentWeather').data('updated') < currentHourTime) && map.getZoom() > 13) {
            loadWeatherCellData(s2CellCenter).done(function (cellWeather) {
                var currentWeather = cellWeather.weather
                if (currentWeather) {
                    var weatherText = weatherTexts[currentWeather.condition]
                    var boostedTypesText = weatherBoostedTypes[currentWeather.condition]
                    var markerTitle = 'Weather: ' + weatherText + (currentWeather.updated >= currentHourTime ? '' : ' (Out of date)') + '\nBoosted Types: ' + boostedTypesText + '\nLast Updated: ' + moment(currentWeather.updated * 1000).format('dddd, Do MMMM Y, HH:mm')

                    $('#currentWeather').data('current-cell', currentWeather.s2_cell_id)
                    $('#currentWeather').data('updated', currentWeather.updated)
                    $('#currentWeather').html('<img src="static/weather/' + currentWeather.condition + (currentWeather.updated >= currentHourTime ? '.png' : '-ood.png') + '" title="' + markerTitle + '">')
                } else {
                    $('#currentWeather').data('current-cell', '')
                    $('#currentWeather').data('updated', '')
                    $('#currentWeather').html('')
                }
            })
        }
        loadRawData().done(function (result) {
            $.each(result.pokemons, processPokemons)
            $.each(result.pokestops, processPokestops, lastMidnight)
            $.each(result.gyms, processGyms)
            $.each(result.spawnpoints, processSpawnpoints)
            $.each(result.scanlocations, processScanlocation)
            $.each(result.nests, processNests)
            $.each(result.communities, processCommunities)
            $.each(result.portals, processPortals)
            $.each(result.pois, processPois)
            showInBoundsMarkers(mapData.pokemons, 'pokemon')
            showInBoundsMarkers(mapData.gyms, 'gym')
            showInBoundsMarkers(mapData.pokestops, 'pokestop')
            showInBoundsMarkers(mapData.spawnpoints, 'inbound')

            clearStaleMarkers()
            cleanOldToasts()

            updateSpawnPoints()
            updatePokestops()
            updatePortals()

            if ($('#rightNav.offcanvas').hasClass('show')) {
                countMarkers(map)
            }

            if ($('#statsModal').hasClass('show')) {
                if ($('#nav-overview-stats-tab').hasClass('active')) {
                    $.each(result.overviewStats, processOverviewStats)
                    $.each(result.teamStats, processTeamStats)
                    $.each(result.pokestopStats, processPokestopStats)
                    $.each(result.spawnpointStats, processSpawnpointStats)
                }
                if ($('#nav-pokemon-stats-tab').hasClass('active')) {
                    pokemonTable.clear()
                    $.each(result.pokemonStats, processPokemonStats)
                    pokemonTable.draw(false)
                }
                if ($('#nav-reward-stats-tab').hasClass('active')) {
                    rewardTable.clear()
                    $.each(result.rewardStats, processRewardStats)
                    rewardTable.draw(false)
                }
                if ($('#nav-shiny-stats-tab').hasClass('active')) {
                    shinyTable.clear()
                    $.each(result.shinyStats, processShinyStats)
                    shinyTable.draw(false)
                }
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
            prevMinLLRank = result.preMinLLRank
            prevMinGLRank = result.preMinGLRank
            prevMinULRank = result.preMinULRank

            reids = result.reids
            qpreids = result.qpreids
            qireids = result.qireids
            qereids = result.qereids
            qcreids = result.qcreids
            greids = result.greids
            rbreids = result.rbreids
            rereids = result.rereids
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
            if (qereids instanceof Array) {
                reincludedQuestsEnergy = qereids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedQuestsEnergy)
            }
            if (qcreids instanceof Array) {
                reincludedQuestsCandy = qcreids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedQuestsCandy)
            }
            if (qireids instanceof Array) {
                reincludedQuestsItem = qireids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedQuestsItem)
            }
            if (greids instanceof Array) {
                reincludedGrunts = greids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedGrunts)
            }
            if (rbreids instanceof Array) {
                reincludedRaidboss = rbreids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedRaidboss)
            }
            if (rereids instanceof Array) {
                reincludedRaidegg = rereids.filter(function (e) {
                    return this.indexOf(e) < 0
                }, reincludedRaidegg)
            }
            reloaddustamount = false
            reloadxpamount = false

            timestamp = result.timestamp
            lastUpdateTime = Date.now()
            token = result.token
        })
    }
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
        if (Store.get('showPokemonCells') && (map.getZoom() > 11)) {
            pokemonLayerGroup.clearLayers()
            showS2Cells(15, {color: 'black', weight: 3, dashOffset: '2', dashArray: '2 6'})
        } else if (Store.get('showPokemonCells') && (map.getZoom() <= 11)) {
            pokemonLayerGroup.clearLayers()
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
            var weatherText = weatherTexts[item.condition]
            var boostedTypesText = weatherBoostedTypes[item.condition]
            var currentTime = new Date()
            var currentHourTime = currentTime.setMinutes(0, 0, 0)
            var weatherTime = item.updated * 1000
            var markerTitle = 'Weather: ' + weatherText + (weatherTime >= currentHourTime ? '' : ' (Out of date)') + '\nBoosted Types: ' + boostedTypesText + '\nLast Updated: ' + moment(weatherTime).format('dddd, Do MMMM Y, HH:mm')
            if (map.getZoom() <= 13) {
                $.each(weatherMarkers, function (index, marker) {
                    markersnotify.addLayer(marker)
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
                iconUrl: 'static/weather/a-' + item.condition + (weatherTime >= currentHourTime ? '.png' : '-ood.png')
            })
            var marker = L.marker([center.lat, center.lng], {icon: icon, title: markerTitle})
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
        markersnotify.removeLayer(marker)
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
                timestring += hours + 'h '
            }
            timestring += lpad(minutes, 2, 0) + 'm '
            timestring += lpad(seconds, 2, 0) + 's'
            timestring += ')'
        }
        $(element).text(timestring)
    })
    $('.icon-countdown').each(function (index, element) {
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
                timestring += hours + 'h '
            }
            timestring += lpad(minutes, 2, 0) + 'm '
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
            timestring += hours + 'h '
        }
        timestring += lpad(minutes, 2, 0) + 'm '
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
            sendToastPokemon(title, text, icon, lat, lon)
        })
    }
}
function sendToast(alertClass, headerText, bodyText, autoHide) {
    var identifier = Math.floor(Math.random() * 100) + 1
    var toastStr = '<div id="toast-message-' + identifier + '" class="toast fade" role="alert" data-bs-autohide="' + autoHide + '" data-toast-timestamp="' + timestamp + '" aria-live="assertive" aria-atomic="true">' +
        '<div class="toast-header ' + identifier + '">' +
        '<strong class="me-auto">' + headerText + '</strong>' +
        '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>' +
        '</div>' +
        '<div class="toast-body">' +
        bodyText +
        '</div>' +
        '</div>'
    $('.toast-container.right-top').append(toastStr)
    $('#toast-message-' + identifier).toast({
        animation: true,
        autohide: true,
        delay: toastDelay
    })
    $('.toast-header.' + identifier).css('background-color', 'var(--bs-' + alertClass + ')')
    $('#toast-message-' + identifier).toast('show')
}
function sendToastPokemon(title, text, icon, lat, lon) {
    if (!Store.get('showToast')) {
        return false
    }
    var identifier = Math.floor(Math.random() * 1000) + 1
    var toastStr = '<div id="toast-pokemon-' + identifier + '" class="toast fade" role="alert" data-toast-timestamp="' + timestamp + '" aria-live="assertive" aria-atomic="true">' +
        '<div class="toast-header ' + identifier + '">' +
        '<strong class="me-auto">' + title + '</strong>' +
        '<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>' +
        '</div>' +
        '<div class="toast-body">' +
        '<div class="row">' +
        '<div class="col-4">' +
        '<img src="' + icon + '">' +
        '</div>' +
        '<div class="col-6">' +
        text +
        '</div>' +
        '<div class="col-2" onclick="centerMap(' + lat + ',' + lon + ', 20)">' +
        '<i class="fas fa-map-marked-alt"</i>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>'
    $('.toast-container.right-bottom').append(toastStr)
    $('#toast-pokemon-' + identifier).toast({
        animation: true,
        autohide: (Store.get('toastPokemonDelay') !== 0),
        delay: Store.get('toastPokemonDelay')
    })
    $('.toast-header.' + identifier).css('background-color', 'var(--bs-yellow)')
    $('#toast-pokemon-' + identifier).toast('show')
}
function cleanOldToasts() {
    $('[id^=toast-message-]').each(function (index, element) {
        if ($(element).data('toast-timestamp') < (timestamp - (toastDelay / 1000))) {
            $(element).remove()
        }
    })
    $('[id^=toast-pokemon-]').each(function (index, element) {
        if ($(element).data('toast-timestamp') < (timestamp - (Store.get('toastPokemonDelay') / 1000))) {
            $(element).remove()
        }
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
            sendToastPokemon(
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
                            locationMarker.rangeCircle.setLatLng(center)
                        } else {
                            var rangeCircleOpts = {
                                color: '#FF9200',
                                radius: 35, // meters
                                center: center,
                                fillColor: '#FF9200',
                                fillOpacity: 0.4,
                                weight: 1
                            }
                            locationMarker.rangeCircle = L.circle(center, rangeCircleOpts)
                            markersnotify.addLayer(locationMarker.rangeCircle)
                        }
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

function pokemonSpritesFilter() {
    jQuery('.offcanvas-body.left .pokemon-list .pokemon-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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

function energySpritesFilter() {
    jQuery('.offcanvas-body.left .energy-list .energy-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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

function candySpritesFilter() {
    jQuery('.offcanvas-body.left .candy-list .candy-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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
    jQuery('.offcanvas-body.left .item-list .item-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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

function gruntSpritesFilter() {
    jQuery('.offcanvas-body.left .grunt-list .grunt-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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

function raideggSpritesFilter() {
    jQuery('.offcanvas-body.left .raidegg-list .raidegg-icon-sprite').on('click', function () {
        var img = jQuery(this)
        var select = jQuery(this).parent().parent().parent().find('.search-number')
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
    var eqe = Store.get('remember_quests_exclude_energy')
    var eqc = Store.get('remember_quests_exclude_candy')
    var eqi = Store.get('remember_quests_exclude_item')
    var eg = Store.get('remember_exclude_grunts')
    var erb = Store.get('remember_exclude_raidboss')
    var ere = Store.get('remember_exclude_raidegg')

    $('#exclude-pokemon .pokemon-icon-sprite').each(function () {
        if (ep.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-min-iv .pokemon-icon-sprite').each(function () {
        if (eminiv.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#notify-pokemon .pokemon-icon-sprite').each(function () {
        if (en.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-quest-pokemon .pokemon-icon-sprite').each(function () {
        if (eqp.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-quest-energy .energy-icon-sprite').each(function () {
        if (eqe.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-quest-candy .candy-icon-sprite').each(function () {
        if (eqc.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-quest-item .item-icon-sprite').each(function () {
        if (eqi.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-rocket .grunt-icon-sprite').each(function () {
        if (eg.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-raidboss .pokemon-icon-sprite').each(function () {
        if (erb.indexOf($(this).data('value')) !== -1) {
            $(this).addClass('active')
        }
    })
    $('#exclude-raidegg .raidegg-icon-sprite').each(function () {
        if (ere.indexOf($(this).data('value')) !== -1) {
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
        $('#pushNotifyIcon').addClass('far fa-bell')
        $('#pushNotifyIcon').css('color', 'green')
        $('#pushNotifyIcon').attr('title', 'Push notification granted')
        return
    } else {
        $('#pushNotifyIcon').addClass('far fa-bell-slash')
        $('#pushNotifyIcon').css('color', 'red')
        $('#pushNotifyIcon').attr('title', 'Push notification denied')
    }

    Push.Permission.request()
})

$(function () {
    if (Store.get('playCries')) {
        fetchCriesJson()
    }
    // load MOTD, if set
    if ((motd && !showMotdOnlyOnce) || (motd && showMotdOnlyOnce && Store.get('oldMotd') !== motdContent)) {
        Store.set('oldMotd', motdContent)
        $('#motdModal').modal('show')
    }
})

$(function () {
    $selectStyle = $('#map-style')
    $selectStyle.on('change', function (e) {
        selectedStyle = $selectStyle.val()
        if (_mapLoaded) {
            setTileLayer(selectedStyle)
        }
        if (selectedStyle === null) {
            selectedStyle = 'openstreetmap'
        }
        Store.set('map_style', selectedStyle)
    })
    $selectStyle.val(Store.get('map_style')).trigger('change')

    $selectDirectionProvider = $('#direction-provider')
    $selectDirectionProvider.on('change', function () {
        directionProvider = $selectDirectionProvider.val()
        Store.set('directionProvider', directionProvider)
    })
    $selectDirectionProvider.val(Store.get('directionProvider')).trigger('change')

    $('#open-gyms-only-switch').on('change', function () {
        Store.set('showOpenGymsOnly', this.checked)
        lastgyms = false
        updateMap()
    })

    $('#active-raids-switch').on('change', function () {
        Store.set('activeRaids', this.checked)
        lastgyms = false
        updateMap()
    })

    $('#new-portals-only-switch').on('change', function () {
        Store.set('showNewPortalsOnly', this.value)
        lastportals = false
        updateMap()
    })

    $('#ex-eligible-switch').on('change', function () {
        Store.set('exEligible', this.checked)
        lastgyms = false
        $.each(['gyms'], function (d, dType) {
            $.each(mapData[dType], function (key, value) {
                // for any marker you're turning off, you'll want to wipe off the range
                if (mapData[dType][key].marker.rangeCircle) {
                    markers.removeLayer(mapData[dType][key].marker.rangeCircle)
                    delete mapData[dType][key].marker.rangeCircle
                }
                if (mapData[dType][key].marker.placementRangeCircle) {
                    markers.removeLayer(mapData[dType][key].marker.placementRangeCircle)
                    delete mapData[dType][key].marker.placementRangeCircle
                }
                markers.removeLayer(mapData[dType][key].marker)
            })
            mapData[dType] = {}
        })
        updateMap()
    })

    if (Store.get('iconsArray').gym === undefined || Object.prototype.toString.call(Store.get('iconsArray').gym) === '[object Object]' || Store.get('iconsArray').gym === '') {
        iconFolderArray['gym'] = iconFolderArray['gym'][Object.keys(iconFolderArray['gym'])[0]]
        Store.set('iconsArray', iconFolderArray)
    }
    if (Store.get('iconsArray').pokemon === undefined || Object.prototype.toString.call(Store.get('iconsArray').pokemon) === '[object Object]' || Store.get('iconsArray').pokemon === '') {
        iconFolderArray['pokemon'] = iconFolderArray['pokemon'][Object.keys(iconFolderArray['pokemon'])[0]]
        Store.set('iconsArray', iconFolderArray)
    }
    if (Store.get('iconsArray').reward === undefined || Object.prototype.toString.call(Store.get('iconsArray').reward) === '[object Object]' || Store.get('iconsArray').reward === '') {
        iconFolderArray['reward'] = iconFolderArray['reward'][Object.keys(iconFolderArray['reward'])[0]]
        Store.set('iconsArray', iconFolderArray)
    }

    iconpath = Store.get('iconsArray')
    $.each(iconpath, function (key, val) {
        var prefix = key
        if (!key.includes('Index')) {
            if (enableJSDebug) {
                console.log('Attempting to load initial ' + key + ' Index: ' + iconpath[key] + prefix + '/index.json')
            }
            $.ajax({
                cache: false,
                url: iconpath[key] + prefix + '/index.json',
                dataType: 'json'
            })
                .done(function (data) {
                    if (enableJSDebug) {
                        console.log('Successfully loaded initial ' + key + ' Index: ' + iconpath[key] + prefix + '/index.json')
                    }
                    iconpath[key + 'Index'] = data
                    Store.set('iconsArray', iconpath)
                    if (key === 'pokemon') {
                        updateIcons('pkmn')
                    } else if (key === 'reward') {
                        updateIcons('reward')
                    }
                })
                .fail(function () {
                    if (enableJSDebug) {
                        console.log('Failed to load initial ' + key + ' Index: ' + iconpath[key] + prefix + '/index.json')
                    }
                })
        }
    })

    $selectGymMarkerStyle = $('#gym-marker-style')
    $selectGymMarkerStyle.on('change', function (e) {
        var newIconSet = this.value
        if (enableJSDebug) {
            console.log('Attempting to load Gym Index: ' + newIconSet + 'gym/index.json')
        }
        $.ajax({
            cache: false,
            url: this.value + 'gym/index.json',
            dataType: 'json'
        })
            .done(function (data) {
                if (enableJSDebug) {
                    console.log('Successfully loaded Gym Index: ' + newIconSet + 'gym/index.json')
                }
                iconpath.gym = newIconSet
                iconpath['gymIndex'] = data
                Store.set('iconsArray', iconpath)
                if (enableJSDebug) {
                    console.log('Now using Gym Index: ' + iconpath.gym + 'gym/index.json')
                }
                updateGymIcons()
            })
            .fail(function () {
                if (enableJSDebug) {
                    console.log('Failed to load Gym Index: ' + newIconSet + 'gym/index.json')
                }
            })
    })
    $selectGymMarkerStyle.val(Store.get('iconsArray').gym)

    $selectIconStyle = $('#pokemon-icon-style')
    $selectIconStyle.on('change', function (e) {
        var newIconSet = this.value
        if (enableJSDebug) {
            console.log('Attempting to load Pokemon Index: ' + newIconSet + 'pokemon/index.json')
        }
        $.ajax({
            cache: false,
            url: this.value + 'pokemon/index.json',
            dataType: 'json'
        })
            .done(function (data) {
                if (enableJSDebug) {
                    console.log('Successfully loaded Pokemon Index: ' + newIconSet + 'pokemon/index.json')
                }
                iconpath.pokemon = newIconSet
                iconpath['pokemonIndex'] = data
                Store.set('iconsArray', iconpath)
                if (enableJSDebug) {
                    console.log('Now using Pokemon Index: ' + iconpath.pokemon + 'pokemon/index.json')
                }
                updateIcons('pkmn')
                redrawPokemon(mapData.pokemons)
                updateGymIcons()
                updatePokestopIcons()
                if (Store.get('showNests')) {
                    lastnests = false
                }
            })
            .fail(function () {
                if (enableJSDebug) {
                    console.log('Failed to load Pokemon Index: ' + newIconSet + 'pokemon/index.json')
                }
            })
    })
    $selectIconStyle.val(Store.get('iconsArray').pokemon)

    $selectRewardIconStyle = $('#reward-icon-style')
    $selectRewardIconStyle.on('change', function (e) {
        var newIconSet = this.value
        if (enableJSDebug) {
            console.log('Attempting to load Reward Index: ' + newIconSet + 'reward/index.json')
        }
        $.ajax({
            cache: false,
            url: this.value + 'reward/index.json',
            dataType: 'json'
        })
            .done(function (data) {
                if (enableJSDebug) {
                    console.log('Successfully loaded Reward Index: ' + newIconSet + 'reward/index.json')
                }
                iconpath.reward = newIconSet
                iconpath['rewardIndex'] = data
                Store.set('iconsArray', iconpath)
                if (enableJSDebug) {
                    console.log('Now using Reward Index: ' + iconpath.reward + 'reward/index.json')
                }
                updateIcons('reward')
                updatePokestopIcons()
            })
            .fail(function () {
                if (enableJSDebug) {
                    console.log('Failed to load Reward Index: ' + newIconSet + 'reward/index.json')
                }
            })
    })
    $selectRewardIconStyle.val(Store.get('iconsArray').reward)

    loadDefaultImages()
    pokemonSpritesFilter()
    itemSpritesFilter()
    energySpritesFilter()
    candySpritesFilter()
    gruntSpritesFilter()
    raideggSpritesFilter()
})

$(function () {
    minLLRank = Store.get('remember_text_min_ll_rank')
    minGLRank = Store.get('remember_text_min_gl_rank')
    minULRank = Store.get('remember_text_min_ul_rank')
    minIV = Store.get('remember_text_min_iv')
    minLevel = Store.get('remember_text_min_level')
    excludedPokemon = String(Store.get('remember_select_exclude')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    excludedMinIV = String(Store.get('remember_select_exclude_min_iv')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    excludedRaidboss = String(Store.get('remember_exclude_raidboss')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    excludedRaidegg = String(Store.get('remember_exclude_raidegg')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    excludedGrunts = String(Store.get('remember_exclude_grunts')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    questsExcludedCandy = String(Store.get('remember_quests_exclude_candy')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    questsExcludedEnergy = String(Store.get('remember_quests_exclude_energy')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    questsExcludedItem = String(Store.get('remember_quests_exclude_item')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    questsExcludedPokemon = String(Store.get('remember_quests_exclude_pokemon')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    notifiedPokemon = String(Store.get('remember_select_notify')).split(',').map(Number).sort(function (a, b) {
        return parseInt(a) - parseInt(b)
    })
    notifiedRarity = Store.get('remember_select_rarity_notify')
    notifiedMinPerfection = Store.get('remember_text_perfection_notify')
    notifiedMinLevel = Store.get('remember_text_level_notify')

    $.getJSON('static/dist/data/moves.min.json').done(function (data) {
        moves = data
    })

    $.getJSON('static/dist/data/questtype.min.json', {_: new Date().getTime()}).done(function (data) {
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

    if (noScanPolygon !== true && geoJSONfile.trim() !== '') {
        $.getJSON(geoJSONfile).done(function (data) {
            $.each(data.features, function (key, value) {
                scanAreas.push(value)
            })
        })
    }

    $selectExclude = $('#exclude-pokemon .search-number')
    $selectExcludeMinIV = $('#exclude-min-iv .search-number')
    $selectPokemonNotify = $('#notify-pokemon .search-number')
    $selectRarityNotify = $('#notify-rarity')
    $textPerfectionNotify = $('#notify-perfection')
    $textMinLLRank = $('#min-ll-rank')
    $textMinGLRank = $('#min-gl-rank')
    $textMinULRank = $('#min-ul-rank')
    $textMinIV = $('#min-iv')
    $textMinLevel = $('#min-level')
    $textLevelNotify = $('#notify-level')
    $raidNotify = $('#notify-raid')
    $questsExcludePokemon = $('#exclude-quest-pokemon .search-number')
    $questsExcludeItem = $('#exclude-quest-item .search-number')
    $questsExcludeEnergy = $('#exclude-quest-energy .search-number')
    $questsExcludeCandy = $('#exclude-quest-candy .search-number')
    $excludeGrunts = $('#exclude-rocket .search-number')
    $excludeRaidboss = $('#exclude-raidboss .search-number')
    $excludeRaidegg = $('#exclude-raidegg .search-number')

    $.getJSON('static/dist/data/raidegg.min.json').done(function (data) {
        $.each(data, function (key, value) {
            raideggList.push({
                type: value['type'],
                level: value['level']
            })
            idToRaidegg[key] = value
        })
        $excludeRaidegg.on('change', function (e) {
            buffer = excludedRaidegg
            excludedRaidegg = $excludeRaidegg.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, excludedRaidegg)
            reincludedRaidegg = reincludedRaidegg.concat(buffer).map(String)
            lastgyms = false
            updateMap()
            Store.set('remember_exclude_raidegg', excludedRaidegg)
        })
        // recall saved lists
        $excludeRaidegg.val(Store.get('remember_exclude_raidegg'))
    })

    $.getJSON('static/dist/data/grunttype.min.json').done(function (data) {
        $.each(data, function (key, value) {
            gruntList.push({
                id: key,
                name: i8ln(value['type']),
                gender: i8ln(value['grunt'])
            })
            value['type'] = i8ln(value['type'])
            value['grunt'] = i8ln(value['grunt'])
            idToGrunt[key] = value
        })
        $excludeGrunts.on('change', function (e) {
            buffer = excludedGrunts
            excludedGrunts = $excludeGrunts.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, excludedGrunts)
            reincludedGrunts = reincludedGrunts.concat(buffer).map(String)
            updateMap()
            Store.set('remember_exclude_grunts', excludedGrunts)
        })
        // recall saved lists
        $excludeGrunts.val(Store.get('remember_exclude_grunts'))
    })

    $.getJSON('static/dist/data/items.min.json').done(function (data) {
        $.each(data, function (key, value) {
            itemList.push({
                id: key,
                name: i8ln(value['name'])
            })
            value['name'] = i8ln(value['name'])
            idToItem[key] = value['name']
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
        $questsExcludeItem.val(Store.get('remember_quests_exclude_item'))
    })

    $.getJSON('static/dist/data/pokemon.min.json').done(function (data) {
        pokedex = data

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
            idToPokemon[key] = value['name']
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
            lastpokemon = false
            updateMap()
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
            lastpokemon = false
            updateMap()
            Store.set('remember_select_exclude_min_iv', excludedMinIV)
        })
        $textMinLLRank.on('change', function (e) {
            minLLRank = Math.max(0, Math.min(parseInt($textMinLLRank.val(), 0) || 0, 100))
            $textMinLLRank.val(minLLRank)
            Store.set('remember_text_min_ll_rank', minLLRank)
            lastpokemon = false
            updateMap()
        })
        $textMinGLRank.on('change', function (e) {
            minGLRank = Math.max(0, Math.min(parseInt($textMinGLRank.val(), 0) || 0, 100))
            $textMinGLRank.val(minGLRank)
            Store.set('remember_text_min_gl_rank', minGLRank)
            lastpokemon = false
            updateMap()
        })
        $textMinULRank.on('change', function (e) {
            minULRank = Math.max(0, Math.min(parseInt($textMinULRank.val(), 0) || 0, 100))
            $textMinULRank.val(minULRank)
            Store.set('remember_text_min_ul_rank', minULRank)
            lastpokemon = false
            updateMap()
        })
        $textMinIV.on('change', function (e) {
            minIV = Math.max(0, Math.min(parseInt($textMinIV.val(), 10) || 0, 100))
            $textMinIV.val(minIV)
            Store.set('remember_text_min_iv', minIV)
            lastpokemon = false
            updateMap()
        })
        $textMinLevel.on('change', function (e) {
            minLevel = Math.max(0, Math.min(parseInt($textMinLevel.val(), 10) || 0, 35))
            $textMinLevel.val(minLevel)
            Store.set('remember_text_min_level', minLevel)
            lastpokemon = false
            updateMap()
        })
        $('#missing-iv-only-switch').on('change', function (e) {
            Store.set('showMissingIVOnly', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#tiny-rat-switch').on('change', function (e) {
            Store.set('showTinyRat', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#big-karp-switch').on('change', function (e) {
            Store.set('showBigKarp', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#no-zero-iv-switch').on('change', function (e) {
            Store.set('showZeroIv', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#no-hundo-iv-switch').on('change', function (e) {
            Store.set('showHundoIv', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#no-xxs-switch').on('change', function (e) {
            Store.set('showXXS', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#no-xxl-switch').on('change', function (e) {
            Store.set('showXXL', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#no-independant-pvp-switch').on('change', function (e) {
            Store.set('showIndependantPvpAndStats', this.checked)
            lastpokemon = false
            updateMap()
        })
        $('#despawn-time-type-select').on('change', function (e) {
            Store.set('showDespawnTimeType', this.value)
            lastpokemon = false
            updateMap()
        })
        $('#pokemon-gender-select').on('change', function (e) {
            Store.set('showPokemonGender', this.value)
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
            notifiedMinPerfection = Math.max(0, Math.min(parseInt($textPerfectionNotify.val(), 10) || 0, 100))
            $textPerfectionNotify.val(notifiedMinPerfection)
            Store.set('remember_text_perfection_notify', notifiedMinPerfection)
        })
        $textLevelNotify.on('change', function (e) {
            notifiedMinLevel = Math.max(0, Math.min(parseInt($textLevelNotify.val(), 10) || 0, 35))
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
        $questsExcludeEnergy.on('change', function (e) {
            buffer = questsExcludedEnergy
            questsExcludedEnergy = $questsExcludeEnergy.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, questsExcludedEnergy)
            reincludedQuestsEnergy = reincludedQuestsEnergy.concat(buffer).map(String)
            updateMap()
            Store.set('remember_quests_exclude_energy', questsExcludedEnergy)
        })
        $questsExcludeCandy.on('change', function (e) {
            buffer = questsExcludedCandy
            questsExcludedCandy = $questsExcludeCandy.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, questsExcludedCandy)
            reincludedQuestsCandy = reincludedQuestsCandy.concat(buffer).map(String)
            updateMap()
            Store.set('remember_quests_exclude_candy', questsExcludedCandy)
        })
        $excludeRaidboss.on('change', function (e) {
            buffer = excludedRaidboss
            excludedRaidboss = $excludeRaidboss.val().split(',').map(Number).sort(function (a, b) {
                return parseInt(a) - parseInt(b)
            })
            buffer = buffer.filter(function (e) {
                return this.indexOf(e) < 0
            }, excludedRaidboss)
            reincludedRaidboss = reincludedRaidboss.concat(buffer).map(String)
            lastgyms = false
            updateMap()
            Store.set('remember_exclude_raidboss', excludedRaidboss)
        })
        // recall saved lists
        $selectExclude.val(Store.get('remember_select_exclude'))
        $selectExcludeMinIV.val(Store.get('remember_select_exclude_min_iv'))
        $selectPokemonNotify.val(Store.get('remember_select_notify'))
        $selectRarityNotify.val(Store.get('remember_select_rarity_notify'))
        $textPerfectionNotify.val(Store.get('remember_text_perfection_notify'))
        $textLevelNotify.val(Store.get('remember_text_level_notify'))
        $textMinLLRank.val(Store.get('remember_text_min_ll_rank'))
        $textMinGLRank.val(Store.get('remember_text_min_gl_rank'))
        $textMinULRank.val(Store.get('remember_text_min_ul_rank'))
        $textMinIV.val(Store.get('remember_text_min_iv'))
        $textMinLevel.val(Store.get('remember_text_min_level'))
        $raidNotify.val(Store.get('remember_raid_notify'))
        $questsExcludePokemon.val(Store.get('remember_quests_exclude_pokemon'))
        $questsExcludeEnergy.val(Store.get('remember_quests_exclude_energy'))
        $questsExcludeCandy.val(Store.get('remember_quests_exclude_candy'))
        $excludeRaidboss.val(Store.get('remember_exclude_raidboss'))
    })

    $('.select-all').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.pokemon-list .pokemon-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfPokemon + 1).keys()).slice(1).join(',')).trigger('change')
    })
    $('.hide-all').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.pokemon-list .pokemon-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.select-all-energy').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.energy-list .energy-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfPokemon + 1).keys()).slice(1).join(',')).trigger('change')
    })
    $('.hide-all-energy').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.energy-list .energy-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.select-all-candy').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.candy-list .candy-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfPokemon + 1).keys()).slice(1).join(',')).trigger('change')
    })
    $('.hide-all-candy').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.candy-list .candy-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.select-all-item').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.item-list .item-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfItem + 1).keys()).slice(1).join(',')).trigger('change')
    })
    $('.hide-all-item').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.item-list .item-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.select-all-grunt').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.grunt-list .grunt-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfGrunt + 1).keys()).slice(1).join(',')).trigger('change')
    })

    $('.hide-all-grunt').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.grunt-list .grunt-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.select-all-egg').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.raidegg-list .raidegg-icon-sprite').addClass('active')
        parent.find('.search-number').val(Array.from(Array(numberOfEgg + 1).keys()).slice(1).join(',')).trigger('change')
    })

    $('.hide-all-egg').on('click', function (e) {
        e.preventDefault()
        var parent = $(this).parent()
        parent.find('.raidegg-list .raidegg-icon-sprite').removeClass('active')
        parent.find('.search-number').val('').trigger('change')
    })

    $('.area-go-to').on('click', function (e) {
        e.preventDefault()
        var lat = $(this).data('lat')
        var lng = $(this).data('lng')
        var zoom = $(this).data('zoom')
        map.setView(new L.LatLng(lat, lng), zoom)
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
    window.setInterval(updateUser, 300000)

    createUpdateWorker()

    // Wipe off/restore map icons when switches are toggled
    function buildSwitchChangeListener(data, dataType, storageKey) {
        return function () {
            Store.set(storageKey, this.checked)
            if (this.checked && storageKey !== 'showQuestsWithTaskAR') {
                // When switch is turned on we assume it has been off, makes sure we dont end up in limbo
                // Without this there could've been a situation where no markers are on map and only newly modified ones are loaded
                if (storageKey === 'showPokemon') {
                    lastpokemon = false
                } else if (storageKey === 'showRaids') {
                    lastgyms = false
                } else if (storageKey === 'showGyms') {
                    lastgyms = false
                } else if (storageKey === 'showPokestops') {
                    lastpokestops = false
                } else if (storageKey === 'showAllPokestops') {
                    lastpokestops = false
                } else if (storageKey === 'showLures') {
                    lastpokestops = false
                } else if (storageKey === 'showRocket') {
                    lastpokestops = false
                } else if (storageKey === 'showEventStops') {
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
                            delete data[dType][key].marker.rangeCircle
                        }
                        if (data[dType][key].marker.placementRangeCircle) {
                            markers.removeLayer(data[dType][key].marker.placementRangeCircle)
                            delete data[dType][key].marker.placementRangeCircle
                        }
                        if (storageKey === 'showSpawnpoints') {
                            markersnotify.removeLayer(data[dType][key].marker)
                            delete data[dType][key].marker
                        } else if (storageKey !== 'showRanges' && storageKey !== 'showPlacementRanges') {
                            markers.removeLayer(data[dType][key].marker)
                            delete data[dType][key].marker
                        }
                    })
                    if (storageKey !== 'showRanges' && storageKey !== 'showPlacementRanges') data[dType] = {}
                })
                if (storageKey === 'showAllPokestops' || storageKey === 'showLures' || storageKey === 'showRocket' || storageKey === 'showEventStops' || storageKey === 'showQuests' || storageKey === 'showQuestsWithTaskAR') {
                    lastpokestops = false
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
        if (!this.checked && Store.get('showNestPolygon') === true) {
            nestLayerGroup.clearLayers()
        }
        Store.set('showNests', this.checked)
        if (Store.get('showNestPolygon') === true) {
            buildNestPolygons()
        }
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
            if (Store.get('showPokemonCells')) {
                showS2Cells(15, {color: 'black', weight: 3, dashOffset: '2', dashArray: '2 6'})
            }
            if (Store.get('showStopCells')) {
                showS2Cells(17, {color: 'black'})
            }
        } else {
            wrapper.hide(options)
            exLayerGroup.clearLayers()
            gymLayerGroup.clearLayers()
            pokemonLayerGroup.clearLayers()
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

    $('#s2-level15-switch').change(function () {
        Store.set('showPokemonCells', this.checked)
        if (this.checked) {
            showS2Cells(15, {color: 'black', weight: 3, dashOffset: '2', dashArray: '2 6'})
        } else {
            pokemonLayerGroup.clearLayers()
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
    $('#placement-ranges-switch').change(buildSwitchChangeListener(mapData, ['gyms', 'pokestops'], 'showPlacementRanges'))

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
            lastnests = false
            updateMap()
        } else {
            nestLayerGroup.clearLayers()
        }
    })

    $('#raid-timer-switch').change(function () {
        Store.set('showRaidTimer', this.checked)
        $.each(mapData.gyms, function (key, value) {
            markers.removeLayer(value.marker)
            value.marker = setupGymMarker(value)
        })
    })

    $('#rocket-timer-switch').change(function () {
        Store.set('showRocketTimer', this.checked)
        $.each(mapData.pokestops, function (key, value) {
            markers.removeLayer(value.marker)
            value.marker = setupPokestopMarker(value)
        })
    })

    $('#eventstops-timer-switch').change(function () {
        Store.set('showEventStopsTimer', this.checked)
        $.each(mapData.pokestops, function (key, value) {
            markers.removeLayer(value.marker)
            value.marker = setupPokestopMarker(value)
        })
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

    $('#allPokestops-switch').change(function () {
        Store.set('showAllPokestops', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            jQuery('#quests-switch').click()
        }
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('#rocket-switch').click()
        }
        if (this.checked === true && Store.get('showEventStops') === true) {
            Store.set('showEventStops', false)
            jQuery('#eventstops-switch').click()
        }
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showAllPokestops').bind(this)()
    })

    $('#lures-switch').change(function () {
        Store.set('showLures', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            jQuery('#quests-switch').click()
        }
        if (this.checked === true && Store.get('showEventStops') === true) {
            Store.set('showEventStops', false)
            jQuery('#eventstops-switch').click()
        }
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('#rocket-switch').click()
        }
        if (this.checked === true && Store.get('showAllPokestops') === true) {
            Store.set('showAllPokestops', false)
            $('#allPokestops-switch').prop('checked', false)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showLures').bind(this)()
    })

    $('#rocket-switch').change(function () {
        Store.set('showRocket', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            jQuery('#quests-switch').click()
        }
        if (this.checked === true && Store.get('showEventStops') === true) {
            Store.set('showEventStops', false)
            jQuery('#eventstops-switch').click()
        }
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        if (this.checked === true && Store.get('showAllPokestops') === true) {
            Store.set('showAllPokestops', false)
            $('#allPokestops-switch').prop('checked', false)
        }
        var options = {
            'duration': 500
        }
        var rocketWrapper = $('#rocket-wrapper')
        if (this.checked) {
            rocketWrapper.show(options)
        } else {
            rocketWrapper.hide(options)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showRocket').bind(this)()
    })

    $('#eventstops-switch').change(function () {
        Store.set('showEventStops', this.checked)
        if (this.checked === true && Store.get('showQuests') === true) {
            Store.set('showQuests', false)
            jQuery('#quests-switch').click()
        }
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('#rocket-switch').click()
        }
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        if (this.checked === true && Store.get('showAllPokestops') === true) {
            Store.set('showAllPokestops', false)
            $('#allPokestops-switch').prop('checked', false)
        }
        var options = {
            'duration': 500
        }
        var eventStopsWrapper = $('#eventstops-wrapper')
        if (this.checked) {
            eventStopsWrapper.show(options)
        } else {
            eventStopsWrapper.hide(options)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showEventStops').bind(this)()
    })

    $('#quests-switch').change(function () {
        Store.set('showQuests', this.checked)
        if (this.checked === true && Store.get('showEventStops') === true) {
            Store.set('showEventStops', false)
            jQuery('#eventstops-switch').click()
        }
        if (this.checked === true && Store.get('showLures') === true) {
            Store.set('showLures', false)
            $('#lures-switch').prop('checked', false)
        }
        if (this.checked === true && Store.get('showRocket') === true) {
            Store.set('showRocket', false)
            jQuery('#rocket-switch').click()
        }
        if (this.checked === true && Store.get('showAllPokestops') === true) {
            Store.set('showAllPokestops', false)
            $('#allPokestops-switch').prop('checked', false)
        }
        var options = {
            'duration': 1000
        }
        var wrapper = $('#quests-filter-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showQuests').bind(this)()
    })

    $('#quests-with_ar').change(function () {
        Store.set('showQuestsWithTaskAR', this.checked)
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showQuestsWithTaskAR').bind(this)()
    })

    $('#dustrange').on('input', function () {
        dustamount = $(this).val()
        Store.set('showDustAmount', dustamount)
        if (dustamount === '0') {
            $('#dustvalue').text(i8ln('Off'))
            setTimeout(function () { updateMap() }, 2000)
        } else {
            $('#dustvalue').text(i8ln('above') + ' ' + dustamount)
            reloaddustamount = true
            setTimeout(function () { updateMap() }, 2000)
        }
    })
    $('#xprange').on('input', function () {
        xpamount = $(this).val()
        Store.set('showXpAmount', xpamount)
        if (xpamount === '0') {
            $('#xpvalue').text(i8ln('Off'))
            setTimeout(function () { updateMap() }, 2000)
        } else {
            $('#xpvalue').text(i8ln('above') + ' ' + xpamount)
            reloadxpamount = true
            setTimeout(function () { updateMap() }, 2000)
        }
    })
    $('#nestrange').on('input', function () {
        nestavg = $(this).val()
        Store.set('showNestAvg', nestavg)
        if (nestavg === '0') {
            $('#nestavg').text(i8ln('All'))
            lastnests = false
            setTimeout(function () { updateMap() }, 2000)
        } else {
            $('#nestavg').text(i8ln('minimum') + ' ' + nestavg)
            lastnests = false
            setTimeout(function () { updateMap() }, 2000)
        }
    })
    $('#toast-switch').change(function () {
        Store.set('showToast', this.checked)
        var options = {
            'duration': 500
        }
        var wrapper = $('#toast-switch-wrapper')
        if (this.checked) {
            wrapper.show(options)
        } else {
            wrapper.hide(options)
        }
    })
    $('#toast-delay-slider').on('input', function () {
        toastdelayslider = $(this).val()
        Store.set('toastPokemonDelay', toastdelayslider)
        if (toastdelayslider === '0') {
            $('#toast-delay-set').text(i8ln('No auto popup close'))
            setTimeout(function () { updateMap() }, 2000)
        } else {
            $('#toast-delay-set').text(i8ln('Popup autoclose') + ' ' + (toastdelayslider / 1000) + ' ' + i8ln('seconds'))
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

                if (locationMarker.rangeCircle) {
                    markersnotify.removeLayer(locationMarker.rangeCircle)
                    delete locationMarker.rangeCircle
                }
            }
        }
    })

    $('#spawn-area-switch').change(function () {
        Store.set('spawnArea', this.checked)
        if (locationMarker.rangeCircle) {
            markersnotify.removeLayer(locationMarker.rangeCircle)
            delete locationMarker.rangeCircle
        }
        if (this.checked) {
            var markerPos = locationMarker.getLatLng()
            var lat = markerPos.lat
            var lng = markerPos.lng
            var center = L.latLng(lat, lng)

            var rangeCircleOpts = {
                color: '#FF9200',
                radius: 35, // meters
                center: center,
                fillColor: '#FF9200',
                fillOpacity: 0.4,
                weight: 1
            }

            locationMarker.rangeCircle = L.circle(center, rangeCircleOpts)

            markersnotify.addLayer(locationMarker.rangeCircle)
        }
    })

    $('#dark-mode-switch').change(function () {
        Store.set('darkMode', this.checked)
        if (this.checked) {
            enableDarkMode()
        } else {
            disableDarkMode()
        }
    })

    $('#fullStatsToggle').on('click', function () {
        $('#rightNav').offcanvas('hide')
    })

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

    pokemonTable = $('#pokemonTable').DataTable({
        paging: true,
        lengthMenu: [
            [3, 10, 25, 50, -1],
            [i8ln('Show 3 rows'), i8ln('Show 10 rows'), i8ln('Show 25 rows'), i8ln('Show 50 rows'), i8ln('Show all rows')]
        ],
        searching: true,
        info: false,
        responsive: true,
        scrollX: false,
        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data))
        },
        stateLoadCallback: function (settings) {
            return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance))
        },
        stateDuration: 0,
        language: {
            search: '',
            searchPlaceholder: i8ln('Search...'),
            emptyTable: i8ln('Loading...') + ' <i class="fas fa-spinner fa-spin"></i>',
            info: i8ln('Showing _START_ to _END_ of _TOTAL_ entries'),
            lengthMenu: '_MENU_',
            paginate: {
                next: i8ln('Next'),
                previous: i8ln('Previous')
            }
        }
    })

    rewardTable = $('#rewardTable').DataTable({
        paging: true,
        lengthMenu: [
            [3, 10, 25, 50, -1],
            [i8ln('Show 3 rows'), i8ln('Show 10 rows'), i8ln('Show 25 rows'), i8ln('Show 50 rows'), i8ln('Show all rows')]
        ],
        searching: true,
        info: false,
        responsive: true,
        scrollX: false,
        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data))
        },
        stateLoadCallback: function (settings) {
            return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance))
        },
        stateDuration: 0,
        language: {
            search: '',
            searchPlaceholder: i8ln('Search...'),
            emptyTable: i8ln('Loading...') + ' <i class="fas fa-spinner fa-spin"></i>',
            info: i8ln('Showing _START_ to _END_ of _TOTAL_ entries'),
            lengthMenu: '_MENU_',
            paginate: {
                next: i8ln('Next'),
                previous: i8ln('Previous')
            }
        }
    })

    shinyTable = $('#shinyTable').DataTable({
        paging: true,
        lengthMenu: [
            [3, 10, 25, 50, -1],
            [i8ln('Show 3 rows'), i8ln('Show 10 rows'), i8ln('Show 25 rows'), i8ln('Show 50 rows'), i8ln('Show all rows')]
        ],
        searching: true,
        info: false,
        responsive: true,
        scrollX: false,
        stateSave: true,
        stateSaveCallback: function (settings, data) {
            localStorage.setItem('DataTables_' + settings.sInstance, JSON.stringify(data))
        },
        stateLoadCallback: function (settings) {
            return JSON.parse(localStorage.getItem('DataTables_' + settings.sInstance))
        },
        stateDuration: 0,
        language: {
            search: '',
            searchPlaceholder: i8ln('Search...'),
            emptyTable: i8ln('Loading...') + ' <i class="fas fa-spinner fa-spin"></i>',
            info: i8ln('Showing _START_ to _END_ of _TOTAL_ entries'),
            lengthMenu: '_MENU_',
            paginate: {
                next: i8ln('Next'),
                previous: i8ln('Previous')
            }
        }
    })
})
function getIcon(iconRepo, folder, fileType, iconKeyId, ...varArgs) {
    var icon = '0.png'
    var requestedIcon = ''
    var firstTry = true
    switch (folder) {
        case 'gym':
            if (iconpath['gymIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No gymIndex? Houston, we have a problem.')
                }
            } else {
                const gymId = iconKeyId
                const trainerCount = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_t' + varArgs[0], '']
                const inBattle = typeof varArgs[1] === 'undefined' ? [''] : varArgs[1] === 0 ? [''] : ['_b', '']
                const isEx = typeof varArgs[2] === 'undefined' ? [''] : varArgs[2] === 0 ? [''] : ['_ex', '']
                search:
                for (const trainer of trainerCount) {
                    for (const battle of inBattle) {
                        for (const ex of isEx) {
                            requestedIcon = `${gymId}${trainer}${battle}${ex}${fileType}`
                            if (iconpath['gymIndex'].includes(requestedIcon)) {
                                if (!firstTry) {
                                    if (enableJSDebug) {
                                        console.log('Repo has fallback gym icon! Returning: ' + requestedIcon)
                                    }
                                }
                                icon = requestedIcon
                                break search
                            } else {
                                if (enableJSDebug) {
                                    console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' gym icon: ' + requestedIcon)
                                }
                            }
                            firstTry = false
                        }
                    }
                }
            }
            break
        case 'invasion':
            if (iconpath['invasionIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No invasionIndex? Houston, we have a problem.')
                }
            } else {
                const gruntId = iconKeyId
                requestedIcon = `${gruntId}${fileType}`
                if (iconpath['invasionIndex'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing invasion icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'nest':
            if (iconpath['nestIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No nestIndex? Houston, we have a problem.')
                }
            } else {
                const typeId = iconKeyId
                requestedIcon = `${typeId}${fileType}`
                if (iconpath['nestIndex'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing nest icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'misc':
            break
        case 'pokemon':
            if (iconpath['pokemonIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No pokemonIndex? Houston, we have a problem.')
                }
            } else {
                /* varArgs order = evolution, form, costume, gender, shiny, alignment */
                const pokemonId = iconKeyId
                const evolutionId = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_e' + varArgs[0], '']
                const formId = typeof varArgs[1] === 'undefined' ? [''] : varArgs[1] === 0 ? [''] : ['_f' + varArgs[1], '']
                const costumeId = typeof varArgs[2] === 'undefined' ? [''] : varArgs[2] === 0 ? [''] : ['_c' + varArgs[2], '']
                const genderId = typeof varArgs[3] === 'undefined' ? [''] : varArgs[3] === 0 ? [''] : ['_g' + varArgs[3], '']
                const shinyId = typeof varArgs[4] === 'undefined' ? [''] : varArgs[4] === 0 ? [''] : ['_s', '']
                const alignmentId = typeof varArgs[5] === 'undefined' ? [''] : varArgs[5] === 0 ? [''] : ['_a' + varArgs[5], '']
                search:
                for (const evolution of evolutionId) {
                    for (const form of formId) {
                        for (const costume of costumeId) {
                            for (const gender of genderId) {
                                for (const alignment of alignmentId) {
                                    for (const shiny of shinyId) {
                                        requestedIcon = `${pokemonId}${evolution}${form}${costume}${gender}${alignment}${shiny}${fileType}`
                                        if (iconpath['pokemonIndex'].includes(requestedIcon)) {
                                            if (!firstTry) {
                                                if (enableJSDebug) {
                                                    console.log('Repo has fallback pokemon icon! Returning: ' + requestedIcon)
                                                }
                                            }
                                            icon = requestedIcon
                                            break search
                                        } else {
                                            if (enableJSDebug) {
                                                console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' pokemon icon: ' + requestedIcon)
                                            }
                                        }
                                        firstTry = false
                                    }
                                }
                            }
                        }
                    }
                }
            }
            break
        case 'pokestop':
            if (iconpath['pokestopIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No pokestopIndex? Houston, we have a problem.')
                }
            } else {
                const lureId = iconKeyId
                const invasionId = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_i' + varArgs[0], '_i', '']
                const questId = typeof varArgs[1] === 'undefined' ? [''] : varArgs[1] === 0 ? [''] : ['_q', '']
                search:
                for (const invasion of invasionId) {
                    for (const quest of questId) {
                        requestedIcon = `${lureId}${invasion}${quest}${fileType}`
                        if (iconpath['pokestopIndex'].includes(requestedIcon)) {
                            if (!firstTry) {
                                if (enableJSDebug) {
                                    console.log('Repo has fallback pokestop icon! Returning: ' + requestedIcon)
                                }
                            }
                            icon = requestedIcon
                            break search
                        } else {
                            if (enableJSDebug) {
                                console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' pokestop icon: ' + requestedIcon)
                            }
                        }
                        firstTry = false
                    }
                }
            }
            break
        case 'raid/egg':
            if (iconpath['raidIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No raidIndex? Houston, we have a problem.')
                }
            } else if (iconpath['raidIndex']['egg'] === undefined) {
                if (enableJSDebug) {
                    console.log('No raidIndex->egg? Houston, we have a problem.')
                }
            } else {
                const eggLevel = iconKeyId
                const isHatched = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_h', '']
                const ex = typeof varArgs[1] === 'undefined' ? [''] : varArgs[1] === 0 ? [''] : ['_ex', '']
                search:
                for (const hatched of isHatched) {
                    for (const e of ex) {
                        requestedIcon = `${eggLevel}${hatched}${e}${fileType}`
                        if (iconpath['raidIndex']['egg'].includes(requestedIcon)) {
                            if (!firstTry) {
                                if (enableJSDebug) {
                                    console.log('Repo has fallback raid->egg icon! Returning: ' + requestedIcon)
                                }
                            }
                            icon = requestedIcon
                            break search
                        } else {
                            if (enableJSDebug) {
                                console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' raid->egg icon: ' + requestedIcon)
                            }
                        }
                        firstTry = false
                    }
                }
            }
            break
        case 'reward/item':
            if (iconpath['rewardIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex? Houston, we have a problem.')
                }
            } else if (iconpath['rewardIndex']['item'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex->item? Houston, we have a problem.')
                }
            } else {
                const itemId = iconKeyId
                const itemAmount = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_a' + varArgs[0], '']
                search:
                for (const a of itemAmount) {
                    requestedIcon = `${itemId}${a}${fileType}`
                    if (iconpath['rewardIndex']['item'].includes(requestedIcon)) {
                        if (!firstTry) {
                            if (enableJSDebug) {
                                console.log('Repo has fallback reward->item icon! Returning: ' + requestedIcon)
                            }
                        }
                        icon = requestedIcon
                        break search
                    } else {
                        if (enableJSDebug) {
                            console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' reward->item icon: ' + requestedIcon)
                        }
                    }
                    firstTry = false
                }
            }
            break
        case 'reward/mega_resource':
            if (iconpath['rewardIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex? Houston, we have a problem.')
                }
            } else if (iconpath['rewardIndex']['mega_resource'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex->mega_resource? Houston, we have a problem.')
                }
            } else {
                const megaPokemon = iconKeyId
                const megaAmount = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_a' + varArgs[0], '']
                search:
                for (const a of megaAmount) {
                    requestedIcon = `${megaPokemon}${a}${fileType}`

                    if (iconpath['rewardIndex']['mega_resource'].includes(requestedIcon)) {
                        if (!firstTry) {
                            if (enableJSDebug) {
                                console.log('Repo has fallback reward->mega_resource icon: ' + requestedIcon)
                            }
                        }
                        icon = requestedIcon
                        break search
                    } else {
                        if (enableJSDebug) {
                            console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' reward->mega_resource icon: ' + requestedIcon)
                        }
                    }
                    firstTry = false
                }
            }
            break
        case 'reward/stardust':
            if (iconpath['rewardIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex? Houston, we have a problem.')
                }
            } else if (iconpath['rewardIndex']['stardust'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex->stardust? Houston, we have a problem.')
                }
            } else {
                const dustAmount = iconKeyId
                requestedIcon = `${dustAmount}${fileType}`
                if (iconpath['rewardIndex']['stardust'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing reward->stardust icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'reward/experience':
            if (iconpath['rewardIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex? Houston, we have a problem.')
                }
            } else if (iconpath['rewardIndex']['experience'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex->experience? Houston, we have a problem.')
                }
            } else {
                const xpAmount = iconKeyId
                requestedIcon = `${xpAmount}${fileType}`
                if (iconpath['rewardIndex']['experience'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing reward->experience icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'reward/candy':
            if (iconpath['rewardIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex? Houston, we have a problem.')
                }
            } else if (iconpath['rewardIndex']['candy'] === undefined) {
                if (enableJSDebug) {
                    console.log('No rewardIndex->candy? Houston, we have a problem.')
                }
            } else {
                const pokemonIdCandy = iconKeyId
                const candyAmount = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_a' + varArgs[0], '']
                search:
                for (const a of candyAmount) {
                    requestedIcon = `${pokemonIdCandy}${a}${fileType}`
                    if (iconpath['rewardIndex']['candy'].includes(requestedIcon)) {
                        if (!firstTry) {
                            if (enableJSDebug) {
                                console.log('Repo has fallback reward->candy icon! Returning: ' + requestedIcon)
                            }
                        }
                        icon = requestedIcon
                        break search
                    } else {
                        if (enableJSDebug) {
                            console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' reward->candy icon: ' + requestedIcon)
                        }
                    }
                    firstTry = false
                }
            }
            break
        case 'team':
            if (iconpath['teamIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No teamIndex? Houston, we have a problem.')
                }
            } else {
                const teamId = iconKeyId
                requestedIcon = `${teamId}${fileType}`
                if (iconpath['teamIndex'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing team icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'type':
            if (iconpath['typeIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No typeIndex? Houston, we have a problem.')
                }
            } else {
                const typeId = iconKeyId
                requestedIcon = `${typeId}${fileType}`
                if (iconpath['typeIndex'].includes(requestedIcon)) {
                    icon = requestedIcon
                } else {
                    if (enableJSDebug) {
                        console.log('Repo is missing type icon: ' + requestedIcon)
                    }
                }
            }
            break
        case 'weather':
            if (iconpath['weatherIndex'] === undefined) {
                if (enableJSDebug) {
                    console.log('No weatherIndex? Houston, we have a problem.')
                }
            } else {
                const weatherId = iconKeyId
                const severityLevel = typeof varArgs[0] === 'undefined' ? [''] : varArgs[0] === 0 ? [''] : ['_l' + varArgs[0], '']
                search:
                for (const severity of severityLevel) {
                    requestedIcon = `${weatherId}${severity}${fileType}`
                    if (iconpath['weatherIndex'].includes(requestedIcon)) {
                        if (!firstTry) {
                            if (enableJSDebug) {
                                console.log('Repo has fallback weather icon! Returning: ' + requestedIcon)
                            }
                        }
                        icon = requestedIcon
                        break search
                    } else {
                        if (enableJSDebug) {
                            console.log('Repo is missing ' + (firstTry ? 'optimal' : 'fallback') + ' weather icon: ' + requestedIcon)
                        }
                    }
                    firstTry = false
                }
            }
            break
    }
    return iconRepo + folder + '/' + icon
}
function updateIcons(iconset) {
    switch (iconset) {
        case 'pkmn':
            $('.pkmnfilter').each(function () {
                var currentImg = $(this).attr('src')
                var newImg = getIcon(iconpath.pokemon, 'pokemon', '.png', $(this).data('pkmnid'))
                if (currentImg !== newImg) {
                    $(this).attr('src', newImg)
                }
            })
            break
        case 'reward':
            $('.rewardfilter').each(function () {
                switch ($(this).data('type')) {
                    case 'mega_resource':
                        let currentMegaImg = $(this).attr('src')
                        let newMegaImg = getIcon(iconpath.reward, 'reward/mega_resource', '.png', $(this).data('megaid'))
                        if (currentMegaImg !== newMegaImg) {
                            $(this).attr('src', newMegaImg)
                        }
                        break
                    case 'item':
                        let currentItemImg = $(this).attr('src')
                        let newItemImg = getIcon(iconpath.reward, 'reward/item', '.png', $(this).data('itemid'))
                        if (currentItemImg !== newItemImg) {
                            $(this).attr('src', newItemImg)
                        }
                        break
                    case 'candy':
                        let currentCandyImg = $(this).attr('src')
                        let newCandyImg = getIcon(iconpath.reward, 'reward/candy', '.png', $(this).data('candyid'))
                        if (currentCandyImg !== newCandyImg) {
                            $(this).attr('src', newCandyImg)
                        }
                        break
                }
            })
            break
    }
}
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
function updateUser() {
    var engine = getCookie('LoginEngine')
    if (engine === '') {
        return false
    }
    loadUser(engine).done(function (result) {
        if (result.action === 'reload') {
            window.location.href = './logout?action=' + engine + '-logout&reason=change'
        }
    })
}
function loadUser(engine) {
    return $.ajax({
        url: 'login',
        type: 'POST',
        timeout: 3600,
        data: {
            'refresh': engine
        },
        dataType: 'json',
        cache: false,
        error: function error() {
            // Display error toast
            sendToast('danger', i8ln('Failed to refresh session'), i8ln('Manually reload the page.'), 'true')
        },
        complete: function complete() {
        }
    })
}
function getCookie(cname) {
    var name = cname + '='
    var decodedCookie = decodeURIComponent(document.cookie)
    var ca = decodedCookie.split(';')
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i]
        while (c.charAt(0) === ' ') {
            c = c.substring(1)
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length)
        }
    }
    return ''
}
function getKeyByValue(object, value) {
    for (var prop in object) {
        if (object.hasOwnProperty(prop)) {
            if (object[prop] === value) return prop
        }
    }
}
//
