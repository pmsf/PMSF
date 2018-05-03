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
var $selectLuredPokestopsOnly
var $selectGymMarkerStyle
var $selectLocationIconMarker
var $switchGymSidebar
var $switchTinyRat
var $switchBigKarp
var $selectDirectionProvider
var $switchExEligible

var language = document.documentElement.lang === '' ? 'en' : document.documentElement.lang
var languageSite = 'en'
var idToPokemon = {}
var i8lnDictionary = {}
var languageLookups = 0
var languageLookupThreshold = 3

var searchMarkerStyles

var timestamp
var excludedPokemon = []
var excludedMinIV = []
var notifiedPokemon = []
var notifiedRarity = []
var notifiedMinPerfection = null
var notifiedMinLevel = null
var minIV = null
var prevMinIV = null
var prevMinLevel = null
var onlyPokemon = 0
var directionProvider

var buffer = []
var reincludedPokemon = []
var reids = []

var map
var rawDataIsLoading = false
var locationMarker
var rangeMarkers = ['pokemon', 'pokestop', 'gym']
var storeZoom = true
var scanPath
var moves
var weather
var boostedMons // eslint-disable-line no-unused-vars
var osmTileServer

var oSwLat
var oSwLng
var oNeLat
var oNeLng

var lastpokestops
var lastgyms
var lastnests
var lastpokemon
var lastslocs
var lastspawns

var selectedStyle = 'light'

var updateWorker
var lastUpdateTime
var lastWeatherUpdateTime

var token

var cries

var raidBoss = {}
var questList = []
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


var genderType = ['♂', '♀', '⚲']
var forms = ['unset', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '!', '?', i8ln('Normal'), i8ln('Sunny'), i8ln('Rainy'), i8ln('Snowy'), i8ln('Normal'), i8ln('Attack'), i8ln('Defense'), i8ln('Speed')]
var cpMultiplier = [0.094, 0.16639787, 0.21573247, 0.25572005, 0.29024988, 0.3210876, 0.34921268, 0.37523559, 0.39956728, 0.42250001, 0.44310755, 0.46279839, 0.48168495, 0.49985844, 0.51739395, 0.53435433, 0.55079269, 0.56675452, 0.58227891, 0.59740001, 0.61215729, 0.62656713, 0.64065295, 0.65443563, 0.667934, 0.68116492, 0.69414365, 0.70688421, 0.71939909, 0.7317, 0.73776948, 0.74378943, 0.74976104, 0.75568551, 0.76156384, 0.76739717, 0.7731865, 0.77893275, 0.7846369, 0.79030001]

var weatherArray = []
var weatherPolys = []
var weatherMarkers = []
var weatherColors

var S2

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
        mapData.pokemons[encounterId].marker.rangeCircle.setMap(null)
        delete mapData.pokemons[encounterId].marker.rangeCircle
    }
    mapData.pokemons[encounterId].marker.setMap(null)
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
    map = new google.maps.Map(document.getElementById('map'), {
        center: {
            lat: centerLat,
            lng: centerLng
        },
        zoom: zoom == null ? Store.get('zoomLevel') : zoom,
        minZoom: minZoom,
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
                'style_pgo_dynamic',
                'osm'
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

    // OpenStreetMap support
    map.mapTypes.set('openstreetmap', new google.maps.ImageMapType({
        getTileUrl: function (coord, zoom) {
            return '//' + osmTileServer + '/' + zoom + '/' + coord.x + '/' + coord.y + '.png'
        },
        tileSize: new google.maps.Size(256, 256),
        name: 'OpenStreetMap',
        maxZoom: 18
    }))

    // dynamic map style chooses stylePgoDay or stylePgoNight depending on client time
    var currentDate = new Date()
    var currentHour = currentDate.getHours()
    var stylePgoDynamic = currentHour >= 6 && currentHour < 19 ? stylePgoDay : stylePgoNight
    map.mapTypes.set('style_pgo_dynamic', stylePgoDynamic)

    map.addListener('maptypeid_changed', function (s) {
        Store.set('map_style', this.mapTypeId)
    })

    excludedPokemon = Store.get('remember_select_exclude')

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
        if (this.getZoom() > 13) {
            // hide weather markers
            $.each(weatherMarkers, function (index, marker) {
                marker.setVisible(false)
            })
            // show header weather
            $('#currentWeather').fadeIn()
        } else {
            // show weather markers
            $.each(weatherMarkers, function (index, marker) {
                marker.setVisible(true)
            })
            // hide header weather
            $('#currentWeather').fadeOut()
        }
    })

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

    map.addListener('click', function (e) {
        if ($('.submit-on-off-button').hasClass('on')) {
            $('.submitLatitude').val(e.latLng.lat())
            $('.submitLongitude').val(e.latLng.lng())
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
                    $('.submit-widget-popup .pokemon-list-cont').each(function(index) {
                        $(this).attr('id','pokemon-list-cont-6' + index);
                        var options = {
                            valueNames: ['name', 'types', 'id']
                        };
                        var monList = new List('pokemon-list-cont-6' + index, options);
                    });
                }
            })
        }
    })
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
    var lat = 'lat' in position ? position.lat : centerLat
    var lng = 'lng' in position ? position.lng : centerLng

    var locationMarker = new google.maps.Marker({
        map: map,
        animation: google.maps.Animation.DROP,
        position: {
            lat: lat,
            lng: lng
        },
        draggable: false,
        icon: null,
        optimized: false,
        zIndex: google.maps.Marker.MAX_ZINDEX + 2
    })

    locationMarker.setIcon(searchMarkerStyles[Store.get('locationMarkerStyle')].icon)

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
    $('#gyms-switch').prop('checked', Store.get('showGyms'))
    $('#nests-switch').prop('checked', Store.get('showNests'))
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
    $('#lured-pokestops-only-switch').val(Store.get('showLuredPokestopsOnly'))
    $('#lured-pokestops-only-wrapper').toggle(Store.get('showPokestops'))
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
        var searchBox = new google.maps.places.Autocomplete(document.getElementById('next-location'))
        $('#next-location').css('background-color', $('#geoloc-switch').prop('checked') ? '#e0e0e0' : '#ffffff')

        searchBox.addListener('place_changed', function () {
            var place = searchBox.getPlace()

            if (!place.geometry) return

            var loc = place.geometry.location
            changeLocation(loc.lat(), loc.lng())
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
    var urlSpriteLarge = r.test(Store.get('spritefileLarge')) ? Store.get('spritefileLarge') : path + Store.get('spritefileLarge')
    document.body.style.setProperty('--sprite-large', 'url(' + urlSpriteLarge + ')')
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

    $.each(types, function (index, type) {
        typesDisplay += getTypeSpan(type)
    })

    var details = ''
    if (atk != null && def != null && sta != null) {
        var iv = getIv(atk, def, sta)
        details =
            '<div>' +
            'IV: ' + iv.toFixed(1) + '% (' + atk + '/' + def + '/' + sta + ')' +
            '</div>'

        if (cp != null && (cpMultiplier != null || level != null)) {
            var pokemonLevel
            if (level != null) {
                pokemonLevel = level
            } else {
                pokemonLevel = getPokemonLevel(cpMultiplier)
            }
            details +=
                '<div>' +
                i8ln('CP') + ' : ' + cp + ' | ' + i8ln('Level') + ' : ' + pokemonLevel +
                '</div>'
        }
        details +=
            '<div>' +
            i8ln('Moves') + ' : ' + pMove1 + ' / ' + pMove2 +
            '</div>'
    }
    if (weatherBoostedCondition !== 0) {
        details +=
            '<div>' +
            i8ln('Weather') + ': ' + i8ln(weather[weatherBoostedCondition]) +
            '</div>'
    }
    if (gender != null) {
        details +=
            '<div>' +
            i8ln('Gender') + ': ' + genderType[gender - 1]
        if (weight != null) {
            details += ' | ' + i8ln('Weight') + ': ' + weight.toFixed(2) + 'kg'
        }
        if (height != null) {
            details += ' | ' + i8ln('Height') + ': ' + height.toFixed(2) + 'm'
        }
        details +=
            '</div>'
    }
    var contentstring =
        '<div>' +
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
        '</small>' +
        '<span> ' + rarityDisplay + '</span>' +
        '<span> - </span>' +
        '<small>' + typesDisplay + '</small>' +
        '</div>'
    if (pokemonReportTime === true) {
        contentstring += '<div>' +
            i8ln('Reported at') + ' ' + getTimeStr(reportTime) +
            '</div>'
    } else {
        contentstring += '<div>' +
            i8ln('Disappears at') + ' ' + getTimeStr(disappearTime) +
            ' <span class="label-countdown" disappears-at="' + disappearTime + '">(00m00s)</span>' +
            '</div>'
    }

    contentstring += '<div>' +
        i8ln('Location') + ': <a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ', ' + longitude + ')" title="' + i8ln('View in Maps') + '">' + coordText + '</a>' +
        '</div>' +
        details +
        '<div>' +
        '<a href="javascript:excludePokemon(' + id + ')">' + i8ln('Exclude') + '</a>&nbsp&nbsp' +
        '<a href="javascript:notifyAboutPokemon(' + id + ')">' + i8ln('Notify') + '</a>&nbsp&nbsp' +
        '<a href="javascript:removePokemonMarker(\'' + encounterId + '\')">' + i8ln('Remove') + '</a>&nbsp&nbsp' +
        '<a href="javascript:void(0);" onclick="javascript:toggleOtherPokemon(' + id + ');" title="' + i8ln('Toggle display of other Pokemon') + '">' + i8ln('Toggle Others') + '</a>' +
        '</div>'
    return contentstring
}

function gymLabel(item) {
    var teamName = gymTypes[item['team_id']]
    var teamId = item['team_id']
    var latitude = item['latitude']
    var longitude = item['longitude']
    var name = item['name']
    var members = item['pokemon']

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
            if (item.raid_pokemon_cp != null) {
                cpStr = ' CP ' + item.raid_pokemon_cp
            }
            raidStr += '<br>' + item.raid_pokemon_name + cpStr
        }
        raidStr += '</h3>'
        if (raidStarted && item.raid_pokemon_move_1 != null && item.raid_pokemon_move_2 != null) {
            var pMove1 = (moves[item['raid_pokemon_move_1']] !== undefined) ? i8ln(moves[item['raid_pokemon_move_1']]['name']) : 'gen/unknown'
            var pMove2 = (moves[item['raid_pokemon_move_2']] !== undefined) ? i8ln(moves[item['raid_pokemon_move_2']]['name']) : 'gen/unknown'
            raidStr += '<div><b>' + pMove1 + ' / ' + pMove2 + '</b></div>'
        }

        var raidStartStr = getTimeStr(item['raid_start'])
        var raidEndStr = getTimeStr(item['raid_end'])
        raidStr += '<div>' + i8ln('Start') + ': <b>' + raidStartStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_start'] + '" start>(00m00s)</span></div>'
        raidStr += '<div>' + i8ln('End') + ': <b>' + raidEndStr + '</b> <span class="label-countdown" disappears-at="' + item['raid_end'] + '" end>(00m00s)</span></div>'

        if (raidStarted) {
            raidIcon = '<i class="pokemon-sprite-large n' + item.raid_pokemon_id + '"></i>'
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
    if (manualRaids) {
        raidStr += '<div class="raid-container"><i class="fa fa-binoculars submit-raid" onclick="openRaidModal(event);" data-id="' + item['gym_id'] + '"></i>' +
            '</div>'
    }
    if (!noDeleteGyms) {
        raidStr += '<i class="fa fa-trash-o delete-gym" onclick="deleteGym(event);" data-id="' + item['gym_id'] + '"></i>'
    }


    var park = ''
    if ((item['park'] !== 'None' && item['park'] !== undefined && item['park']) && (noParkInfo === false)) {
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

    var nameStr = (name ? '<div>' + name + '</div>' : '')

    var gymColor = ['0, 0, 0, .4', '74, 138, 202, .6', '240, 68, 58, .6', '254, 217, 40, .6']
    var str
    if (teamId === 0) {
        str =
            '<div>' +
            '<center>' +
            '<div>' +
            '<b style="color:rgba(' + gymColor[teamId] + ')">' + i8ln(teamName) + '</b><br>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/' + teamName + '_large.png">' +
            raidIcon +
            '</div>' +
            nameStr +
            raidStr +
            '<div>' +
            park +
            '</div>' +
            '<div>' +
            i8ln('Location') + ': <a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ' , ' + longitude.toFixed(7) + '</a>' +
            '</div>' +
            '</center>' +
            '</div>'
    } else {
        var freeSlots = item['slots_available']
        var gymCp = ''
        if (item['total_gym_cp'] != null) {
            gymCp = '<div>' + i8ln('Total Gym CP') + ' : <b>' + item.total_gym_cp + '</b></div>'
        }
        str =
            '<div>' +
            '<center>' +
            '<div style="padding-bottom: 2px">' +

            i8ln('Gym owned by') + ' : ' +
            '</div>' +
            '<div>' +
            '<b style="color:rgba(' + gymColor[teamId] + ')">' + i8ln('Team') + ' ' + i8ln(teamName) + '</b><br>' +
            '<img height="70px" style="padding: 5px;" src="static/forts/' + teamName + '_large.png">' +
            raidIcon +
            '</div>' +
            nameStr +
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
            i8ln('Location') + ': <a href="javascript:void(0);" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ');" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ' , ' + longitude.toFixed(7) + '</a>' +
            '</div>' +
            '</center>' +
            '</div>'
    }

    return str
}

function pokestopLabel(expireTime, latitude, longitude, stopName, lureUser, id, quest, reward) {
    var str
    if (stopName === undefined) {
        stopName = 'Pokéstop'
    }
    if (expireTime) {
        if (lureUser) {
            str =
                '<div>' +
                '<b>' + stopName + '<br>' + i8ln('Lured by') + ': ' + lureUser + '</b>' +
                '</div>'
        } else {
            str =
                '<div>' +
                '<b>' + stopName + ' (' + i8ln('Lured') + ')</b>' +
                '</div>'
        }
        str +=
            '<div>' +
            i8ln('Lure expires at') + ' ' + getTimeStr(expireTime) +
            ' <span class="label-countdown" disappears-at="' + expireTime + '">(00m00s)</span>' +
            '</div>' +

            '<div>' +
            'Location: <a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ')" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ', ' + longitude.toFixed(7) + '</a>' +
            '</div>'
    } else {
        str =
            '<div>' +
            '<b>' + stopName + '</b>' +
        '</div>';
        if (!noManualQuests && quest !== null) {
            str += '<div>'+
                i8ln('Quest:') + ' ' +
                i8ln(questList[quest]) +
                '</div>'
            if(reward !== null && reward !== "NULL"){
                str += '<div>'+
                    i8ln('Reward:') + ' ' +
                    i8ln(reward) +
                    '</div>'
            }
        }
        str +=  '<div>' +
            i8ln('Location:') + ' ' + '<a href="javascript:void(0)" onclick="javascript:openMapDirections(' + latitude + ',' + longitude + ')" title="' + i8ln('View in Maps') + '">' + latitude.toFixed(6) + ', ' + longitude.toFixed(7) + '</a>' +
            '</div>'
        if (!noDeletePokestops) {
            str += '<i class="fa fa-trash-o delete-pokestop" onclick="deletePokestop(event);" data-id="' + id + '"></i>'
        }
        if (!noManualQuests) {
            str += '<i class="fa fa-binoculars submit-quest" onclick="openQuestModal(event);" data-id="' + id + '"></i>'
        }

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
    marker.addListener('click', function () {
        this.setAnimation(null)
        this.animationDisabled = true
    })

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokemon')
    }

    marker.infoWindow = new google.maps.InfoWindow({
        content: pokemonLabel(item),
        disableAutoPan: true
    })

    if (notifiedPokemon.indexOf(item['pokemon_id']) > -1 || notifiedRarity.indexOf(item['pokemon_rarity']) > -1) {
        if (!skipNotification) {
            checkAndCreateSound(item['pokemon_id'])
            sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + item['pokemon_id'] + '.png', item['latitude'], item['longitude'])
        }
        if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
            marker.setAnimation(google.maps.Animation.BOUNCE)
        }
    }

    if (item['individual_attack'] != null) {
        var perfection = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
        if (notifiedMinPerfection > 0 && perfection >= notifiedMinPerfection) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + item['pokemon_id'] + '.png', item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
                marker.setAnimation(google.maps.Animation.BOUNCE)
            }
        }
    }

    if (item['level'] != null) {
        var level = item['level']
        if (notifiedMinLevel > 0 && level >= notifiedMinLevel) {
            if (!skipNotification) {
                checkAndCreateSound(item['pokemon_id'])
                sendNotification(getNotifyText(item).fav_title, getNotifyText(item).fav_text, iconpath + item['pokemon_id'] + '.png', item['latitude'], item['longitude'])
            }
            if (marker.animationDisabled !== true && Store.get('remember_bounce_notify')) {
                marker.setAnimation(google.maps.Animation.BOUNCE)
            }
        }
    }

    addListeners(marker)
}

function getGymMarkerIcon(item) {
    var park = item['park']
    var level = item.raid_level
    var team = item.team_id
    var teamStr = ''
    if (team === 0 || level === null) {
        teamStr = gymTypes[item['team_id']]
    } else {
        teamStr = gymTypes[item['team_id']] + '_' + level
    }
    var exIcon = ''
    if ((((park !== 'None' && park !== undefined && onlyTriggerGyms === false && park) || (item['sponsor'] !== undefined && item['sponsor'] > 0) || triggerGyms.includes(item['gym_id'])) && (noExGyms === false))) {
        exIcon = '<img src="static/images/ex.png" style="position:absolute;right:25px;bottom:2px;"/>'
    }
    if (item['raid_pokemon_id'] != null && item.raid_end > Date.now()) {
        return '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:55px;height:auto;"/>' +
            '<i class="pokemon-raid-sprite n' + item.raid_pokemon_id + '"></i>' +
            exIcon +
            '</div>'
    } else if (item['raid_level'] !== null && item.raid_end > Date.now()) {
        var raidEgg = ''
        if (item['raid_level'] <= 2) {
            raidEgg = 'normal'
        } else if (item['raid_level'] <= 4) {
            raidEgg = 'rare'
        } else {
            raidEgg = 'legendary'
        }
        return '<div style="position:relative;">' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + teamStr + '.png" style="width:55px;height:auto;"/>' +
            '<img src="static/raids/egg_' + raidEgg + '.png" style="width:30px;height:auto;position:absolute;top:8px;right:12px;"/>' +
            exIcon +
            '</div>'
    } else {
        return '<div>' +
            '<img src="static/forts/' + Store.get('gymMarkerStyle') + '/' + gymTypes[item['team_id']] + '.png" style="width:48px;height: auto;"/>' +
            exIcon +
            '</div>'
    }
}

function setupGymMarker(item) {
    var marker = new RichMarker({
        position: new google.maps.LatLng(item['latitude'], item['longitude']),
        map: map,
        content: getGymMarkerIcon(item),
        flat: true,
        anchor: RichMarkerPosition.MIDDLE
    })

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'gym', item['team_id'])
    }

    marker.infoWindow = new google.maps.InfoWindow({
        content: gymLabel(item),
        disableAutoPan: true,
        pixelOffset: new google.maps.Size(0, -20)
    })

    var raidLevel = item.raid_level
    if (raidLevel >= Store.get('remember_raid_notify') && item.raid_end > Date.now() && Store.get('remember_raid_notify') !== 0) {
        var title = 'Raid level: ' + raidLevel

        var raidStartStr = getTimeStr(item['raid_start'])
        var raidEndStr = getTimeStr(item['raid_end'])
        var text = raidStartStr + ' - ' + raidEndStr

        var raidStarted = item['raid_pokemon_id'] != null
        var icon
        if (raidStarted) {
            icon = iconpath + item.raid_pokemon_id + '.png'
            checkAndCreateSound(item.raid_pokemon_id)
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
        marker.addListener('click', function () {
            var gymSidebar = document.querySelector('#gym-details')
            if (gymSidebar.getAttribute('data-id') === item['gym_id'] && gymSidebar.classList.contains('visible')) {
                gymSidebar.classList.remove('visible')
            } else {
                gymSidebar.setAttribute('data-id', item['gym_id'])
                showGymDetails(item['gym_id'])
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
    } else {
        addListeners(marker)
    }
    return marker
}

function updateGymMarker(item, marker) {
    marker.setContent(getGymMarkerIcon(item))
    marker.infoWindow.setContent(gymLabel(item))

    var raidLevel = item.raid_level
    if (raidLevel >= Store.get('remember_raid_notify') && item.raid_end > Date.now() && Store.get('remember_raid_notify') !== 0) {
        var raidPokemon = mapData.gyms[item['gym_id']].raid_pokemon_id
        if (item.raid_pokemon_id !== raidPokemon) {
            var title = 'Raid level: ' + raidLevel

            var raidStartStr = getTimeStr(item['raid_start'])
            var raidEndStr = getTimeStr(item['raid_end'])
            var text = raidStartStr + ' - ' + raidEndStr

            var raidStarted = item['raid_pokemon_id'] != null
            var icon
            if (raidStarted) {
                icon = iconpath + item.raid_pokemon_id + '.png'
                checkAndCreateSound(item.raid_pokemon_id)
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
        mapData.gyms[key]['marker'].setContent(getGymMarkerIcon(mapData.gyms[key]))
    })
}

function setupPokestopMarker(item) {
    var imagename = item['lure_expiration'] ? 'PstopLured' : 'Pstop'
    imagename = item['quest_id'] > 0 ? 'Pstop-quest' : imagename
    var marker = new google.maps.Marker({
        position: {
            lat: item['latitude'],
            lng: item['longitude']
        },
        map: map,
        zIndex: 2,
        icon: 'static/forts/' + imagename + '.png'
    })

    if (!marker.rangeCircle && isRangeActive(map)) {
        marker.rangeCircle = addRangeCircle(marker, map, 'pokestop')
    }

    marker.infoWindow = new google.maps.InfoWindow({
        content: pokestopLabel(item['lure_expiration'], item['latitude'], item['longitude'], item['pokestop_name'], item['lure_user'], item['pokestop_id'], item['quest_id'], item['reward']),
        disableAutoPan: true
    })

    addListeners(marker)

    return marker
}
function setupNestMarker(item){
    if(item.pokemon_id > 0){
        var str = '<div class="marker-nests">' +
            '<img src="static/images/nest-' + item.pokemon_types[0].type.toLowerCase() + '.png" style="width:36px;height: auto;"/>' +
            '<i class="nest-pokemon-sprite n' + item.pokemon_id + '"></i>' +
            '</div>'
    }
    else{
        var str = '<div class="marker-nests">' +
            '<img src="static/images/nest-empty.png" style="width:36px;height: auto;"/>' +
            '</div>'
    }

    var marker = new RichMarker({
        position: new google.maps.LatLng(item['lat'], item['lon']),
        map: map,
        content: str,
        flat: true,
        anchor: RichMarkerPosition.MIDDLE
    })

    marker.infoWindow = new google.maps.InfoWindow({
        content: nestLabel(item),
        disableAutoPan: true,
        pixelOffset: new google.maps.Size(0, -20)
    })
    addListeners(marker)

    return marker
}


function nestLabel(item) {

    var str = '<div>';
    if (item.pokemon_id > 0) {
        var types = item['pokemon_types']
        var typesDisplay = ''
        $.each(types, function (index, type) {
            typesDisplay += getTypeSpan(type)
        })
        str += '<b>' + item.pokemon_name  + '</b>' +
            '</div>' +
            '<div>' +
            typesDisplay +
            '</div>'
    } else {
        str += '<b>' + i8ln('No Pokemon - Assign One Below') + '</b>'
    }
    str += '<div>' +
    'Location: <a href="javascript:void(0)" onclick="javascript:openMapDirections(' + item.lat + ',' + item.lon + ')" title="' + i8ln('View in Maps') + '">' + item.lat.toFixed(6) + ', ' + item.lon.toFixed(7) + '</a>' +
    '</div>'
    if(item.type === 1){
        str += '<div style="margin-bottom:5px;">' + i8ln('As found on thesilphroad.com') + '</div>'
    }
    if (!noDeleteNests) {
        str += '<i class="fa fa-trash-o delete-nest" onclick="deleteNest(event);" data-id="' + item['nest_id'] + '"></i>'
    }
    if (!noManualNests) {
        str += '<i class="fa fa-binoculars submit-nest" onclick="openNestModal(event);" data-id="' + item['nest_id'] + '"></i>'
    }

    return str
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

    return hue
}

function changeSpawnIcon(color, zoom) {
    var urlColor = ''
    if (color === 275) {
        urlColor = './static/icons/hsl-275-light.png'
    } else {
        urlColor = './static/icons/hsl-' + color + '.png'
    }
    var zoomScale = 1.6 // adjust this value to change the size of the spawnpoint icons
    var minimumSize = 1
    var newSize = Math.round(zoomScale * (zoom - 10) // this scales the icon based on zoom
    )
    if (newSize < minimumSize) {
        newSize = minimumSize
    }

    var newIcon = {
        url: urlColor,
        scaledSize: new google.maps.Size(newSize, newSize),
        anchor: new google.maps.Point(newSize / 2, newSize / 2)
    }

    return newIcon
}

function spawnPointIndex(color) {
    var newIndex = 1
    var scale = 0
    if (color >= 0 && color <= 120) {
        // high to low over 15 minutes of active spawn
        scale = color / 120
        newIndex = 100 + scale * 100
    } else if (color >= 200 && color <= 250) {
        // low to high over 5 minutes til spawn
        scale = (color - 200) / 50
        newIndex = scale * 100
    }

    return newIndex
}

function setupSpawnpointMarker(item) {
    var circleCenter = new google.maps.LatLng(item['latitude'], item['longitude'])
    var hue = getColorBySpawnTime(item.time)
    var zoom = map.getZoom()

    var marker = new google.maps.Marker({
        map: map,
        position: circleCenter,
        icon: changeSpawnIcon(hue, zoom),
        zIndex: spawnPointIndex(hue)
    })

    marker.infoWindow = new google.maps.InfoWindow({
        content: spawnpointLabel(item),
        disableAutoPan: true,
        position: circleCenter
    })

    addListeners(marker)

    return marker
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
        if (((mapData.pokemons[key]['disappear_time'] < new Date().getTime() || ((excludedPokemon.indexOf(mapData.pokemons[key]['pokemon_id']) >= 0 || isTemporaryHidden(mapData.pokemons[key]['pokemon_id']) || ((((mapData.pokemons[key]['individual_attack'] + mapData.pokemons[key]['individual_defense'] + mapData.pokemons[key]['individual_stamina']) / 45 * 100 < minIV) || ((mapType === 'monocle' && mapData.pokemons[key]['level'] < minLevel) || (mapType === 'rm' && !isNaN(minLevel) && (mapData.pokemons[key]['cp_multiplier'] < cpMultiplier[minLevel - 1])))) && !excludedMinIV.includes(mapData.pokemons[key]['pokemon_id'])) || (Store.get('showBigKarp') === true && mapData.pokemons[key]['pokemon_id'] === 129 && (mapData.pokemons[key]['weight'] < 13.14 || mapData.pokemons[key]['weight'] === null)) || (Store.get('showTinyRat') === true && mapData.pokemons[key]['pokemon_id'] === 19 && (mapData.pokemons[key]['weight'] > 2.40 || mapData.pokemons[key]['weight'] === null))) && encounterId !== mapData.pokemons[key]['encounter_id'])) || (encounterId && encounterId === mapData.pokemons[key]['encounter_id'] && mapData.pokemons[key]['disappear_time'] < new Date().getTime()))) {
            if (mapData.pokemons[key].marker.rangeCircle) {
                mapData.pokemons[key].marker.rangeCircle.setMap(null)
                delete mapData.pokemons[key].marker.rangeCircle
            }
            mapData.pokemons[key].marker.setMap(null)
            delete mapData.pokemons[key]
        }
    })

    $.each(mapData.lurePokemons, function (key, value) {
        if (mapData.lurePokemons[key]['lure_expiration'] < new Date().getTime() || (excludedPokemon.indexOf(mapData.lurePokemons[key]['pokemon_id']) >= 0 && ((encounterId && encounterId !== mapData.pokemons[key]['encounter_id']) || !encounterId))) {
            mapData.lurePokemons[key].marker.setMap(null)
            delete mapData.lurePokemons[key]
        }
    })

    $.each(mapData.scanned, function (key, value) {
        // If older than 15mins remove
        if (mapData.scanned[key]['last_modified'] < new Date().getTime() - 15 * 60 * 1000) {
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
            } else {
                // there's already a range circle
                if (isRangeActive(map)) {
                    marker.rangeCircle.setMap(map)
                } else {
                    marker.rangeCircle.setMap(null)
                }
            }
        }

        if (show && !marker.getMap()) {
            marker.setMap(map
                // Not all markers can be animated (ex: scan locations)
            )
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
    var loadGyms = (Store.get('showGyms') || Store.get('showRaids')) ? 'true' : 'false'
    var loadPokestops = Store.get('showPokestops')
    var loadNests = Store.get('showNests')
    var loadScanned = Store.get('showScanned')
    var loadSpawnpoints = Store.get('showSpawnpoints')
    var loadLuredOnly = Boolean(Store.get('showLuredPokestopsOnly'))
    var loadMinIV = Store.get('remember_text_min_iv')
    var loadMinLevel = Store.get('remember_text_min_level')
    var bigKarp = Boolean(Store.get('showBigKarp'))
    var tinyRat = Boolean(Store.get('showTinyRat'))
    var exEligible = Boolean(Store.get('exEligible'))

    var bounds = map.getBounds()
    var swPoint = bounds.getSouthWest()
    var nePoint = bounds.getNorthEast()
    var swLat = swPoint.lat()
    var swLng = swPoint.lng()
    var neLat = nePoint.lat()
    var neLng = nePoint.lng()

    return $.ajax({
        url: 'raw_data',
        type: 'POST',
        timeout: 300000,
        data: {
            'timestamp': timestamp,
            'pokemon': loadPokemon,
            'lastpokemon': lastpokemon,
            'pokestops': loadPokestops,
            'nests': loadNests,
            'lastnests': lastnests,
            'lastpokestops': lastpokestops,
            'luredonly': loadLuredOnly,
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

function loadWeatherCellData(cell) {
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

function searchAjax(field) { // eslint-disable-line no-unused-vars
    var term = field.val()
    var type = field.data('type')
    if (term !== '') {
        $.ajax({
            url: 'search',
            type: 'POST',
            timeout: 300000,
            dataType: 'json',
            cache: false,
            data: {
                'action': type,
                'term': term
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
                data.forEach(function (element) {
                    var html = '<li class="search-result ' + type + '" data-lat="' + element.lat + '" data-lon="' + element.lon + '"><div class="left-column" onClick="centerMapOnCoords(event);">'
                    if (element.url !== '') {
                        html += '<span style="background:url(' + element.url + ') no-repeat;" class="i-icon" ></span>'
                    }
                    html += '<div class="cont"><span class="name" >' + element.name + '</span>'
                    if(sr.hasClass('reward-results')){
                        html += '<span>&nbsp;-&nbsp;</span> <span class="reward" style="font-weight:bold">' + element.reward + '</span>'
                    }
                    html += '</div></div>'
                    if (sr.hasClass('gym-results') && manualRaids) {
                        html += '<div class="right-column"><i class="fa fa-binoculars submit-raid"  onClick="openRaidModal(event);" data-id="' + element.external_id + '"></i></div>'
                    } if (sr.hasClass('pokestop-results') && !noManualQuests) {
                        html += '<div class="right-column"><i class="fa fa-binoculars submit-quests"  onClick="openQuestModal(event);" data-id="' + element.external_id + '"></i></div>'
                    }
                    html += '</li>'
                    sr.append(html)
                })
            }
        })
    }
}

function centerMapOnCoords(event) { // eslint-disable-line no-unused-vars
    var point = $(event.target)
    if (point.hasClass('left-column')) {
        point = point.parent()
    } else if (point.hasClass('cont')) {
        point = point.parent().parent().parent()
    } else if (point.hasClass('name') || point.hasClass('reward')) {
        point = point.parent().parent().parent()
    } else if (!point.hasClass('search-result')) {
        point = point.parent().parent()
    }  else{
        point = point.parent().parent().parent()
    }
    var lat = point.data('lat')
    var lon = point.data('lon')
    map.setCenter(new google.maps.LatLng(lat, lon))
    map.setZoom(20)
    $('.ui-dialog-content').dialog('close')
}

function manualPokestopData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var pokestopName = form.find('[name="pokestop-name"]').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lng = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    if (pokestopName && pokestopName !== '') {
        if (confirm(i8ln('I confirm this is an accurate reporting of a new pokestop'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'pokestop',
                    'pokestop': pokestopName,
                    'lat': lat,
                    'lng': lng
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
    }
}

function manualGymData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent()
    var gymName = form.find('[name="gym-name"]').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lng = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    if (gymName && gymName !== '') {
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
                    'lng': lng
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
    }
}
function manualPokemonData(event) { // eslint-disable-line no-unused-vars
    var form = $(event.target).parent().parent().parent()
    var id = form.find('.pokemonID').val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lng = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    if (id && id !== '') {
        if (confirm(i8ln('I confirm this is an accurate reporting of a new pokemon'))) {
            return $.ajax({
                url: 'submit',
                type: 'POST',
                timeout: 300000,
                dataType: 'json',
                cache: false,
                data: {
                    'action': 'pokemon',
                    'id': id,
                    'lat': lat,
                    'lng': lng
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
                    'id': gymId
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
                    'id': pokestopId
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
    var id = cont.find( '.pokemonID' ).val()
    var lat = $('.submit-modal.ui-dialog-content .submitLatitude').val()
    var lng = $('.submit-modal.ui-dialog-content .submitLongitude').val()
    if (lat && lat !== '' && lng && lng !== '') {
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
                    'lng': lng,
                    'id' : id
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
    var questId = cont.find('.questList').val()
    var reward = cont.find('.rewardList').val()
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
                    'questId': questId,
                    'reward': reward,
                    'pokestopId': pokestopId,
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
function openNestModal(event) { // eslint-disable-line no-unused-vars
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.submitting-nests').attr('data-nest',val)
    $('.global-nest-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        title: i8ln('Submit a Nest'),
        buttons: {},
        classes: {
            'ui-dialog': 'ui-dialog nest-widget-popup'
        },
        open: function (event, ui) {
            $('.nest-widget-popup .pokemon-list-cont').each(function(index) {
                $(this).attr('id','pokemon-list-cont-7' + index);
                var options = {
                    valueNames: ['name', 'types', 'id']
                };
                var monList = new List('pokemon-list-cont-7' + index, options);
            });
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
    $('.ui-dialog').remove()
    var val = $(event.target).data('id')
    $('.questPokestop').val(val)
    $('.quest-modal').clone().dialog({
        modal: true,
        maxHeight: 600,
        buttons: {},
        title:i8ln('Submit a Quest'),
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
            jQuery('input[name="gym-search"], input[name="pokestop-search"], input[name="reward-search"]').bind('input', function () {
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
            item.marker.setMap(null)
        }
        if (!item.hidden) {
            item.marker = setupPokemonMarker(item, map)
            customizePokemonMarker(item.marker, item)
            mapData.pokemons[item['encounter_id']] = item
        }

        if (encounterId && encounterId === item['encounter_id']) {
            if (!item.marker.infoWindowIsOpen) {
                item.marker.infoWindow.open(map, item.marker)
                clearSelection()
                updateLabelDiffTime()
                item.marker.persist = true
                item.marker.infoWindowIsOpen = true
            } else {
                item.marker.persist = null
                item.marker.infoWindow.close()
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
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupNestMarker(item)
        mapData.nests[item['nest_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.nests[item['nest_id']]
        item2.marker.setMap(null)
        item.marker = setupNestMarker(item)
        mapData.nests[item['nest_id']] = item

    }
}

function processPokestops(i, item) {
    if (!Store.get('showPokestops')) {
        return false
    }

    if (Store.get('showLuredPokestopsOnly') && !item['lure_expiration']) {
        return true
    }

    if (!mapData.pokestops[item['pokestop_id']]) {
        // new pokestop, add marker to map and item to dict
        if (item.marker && item.marker.rangeCircle) {
            item.marker.rangeCircle.setMap(null)
        }
        if (item.marker) {
            item.marker.setMap(null)
        }
        item.marker = setupPokestopMarker(item)
        mapData.pokestops[item['pokestop_id']] = item
    } else {
        // change existing pokestop marker to unlured/lured
        var item2 = mapData.pokestops[item['pokestop_id']]
        if (!!item['lure_expiration'] !== !!item2['lure_expiration']) {
            if (item2.marker && item2.marker.rangeCircle) {
                item2.marker.rangeCircle.setMap(null)
            }
            item2.marker.setMap(null)
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
    var currentTime = new Date().getTime()

    // change lured pokestop marker to unlured when expired
    $.each(mapData.pokestops, function (key, value) {
        if (value['lure_expiration'] && value['lure_expiration'] < currentTime) {
            value['lure_expiration'] = null
            if (value.marker && value.marker.rangeCircle) {
                value.marker.rangeCircle.setMap(null)
            }
            value.marker.setMap(null)
            value.marker = setupPokestopMarker(value)
        }
    })

    // remove unlured stops if show lured only is selected
    if (Store.get('showLuredPokestopsOnly')) {
        $.each(mapData.pokestops, function (key, value) {
            if (!value['lure_expiration']) {
                removeStops.push(key)
            }
        })
        $.each(removeStops, function (key, value) {
            if (mapData.pokestops[value] && mapData.pokestops[value].marker) {
                if (mapData.pokestops[value].marker.rangeCircle) {
                    mapData.pokestops[value].marker.rangeCircle.setMap(null)
                }
                mapData.pokestops[value].marker.setMap(null)
                delete mapData.pokestops[value]
            }
        })
    }
}

function processGyms(i, item) {
    if (!Store.get('showGyms') && !Store.get('showRaids')) {
        return false // in case the checkbox was unchecked in the meantime.
    }

    var gymLevel = item.slots_available
    var raidLevel = item.raid_level
    var removeGymFromMap = function removeGymFromMap(gymid) {
        if (mapData.gyms[gymid] && mapData.gyms[gymid].marker) {
            if (mapData.gyms[gymid].marker.rangeCircle) {
                mapData.gyms[gymid].marker.rangeCircle.setMap(null)
            }
            mapData.gyms[gymid].marker.setMap(null)
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
            item.marker.setMap(null)
        }
        item.marker = setupSpawnpointMarker(item)
        mapData.spawnpoints[id] = item
    }
}

function updateSpawnPoints() {
    if (!Store.get('showSpawnpoints')) {
        return false
    }

    var zoom = map.getZoom()

    $.each(mapData.spawnpoints, function (key, value) {
        if (map.getBounds().contains(value.marker.getPosition())) {
            var hue = getColorBySpawnTime(value['time'])
            value.marker.setIcon(changeSpawnIcon(hue, zoom))
            value.marker.setZIndex(spawnPointIndex(hue))
        }
    })
}

function updateMap() {
    var position = map.getCenter()
    Store.set('startAtLastLocationPosition', {
        lat: position.lat(),
        lng: position.lng()
    })

    // lets try and get the s2 cell id in the middle
    var s2CellCenter = S2.keyToId(S2.latLngToKey(position.lat(), position.lng(), 10))
    if ((s2CellCenter) && (String(s2CellCenter) !== $('#currentWeather').data('current-cell')) && (map.getZoom() > 13)) {
        loadWeatherCellData(s2CellCenter).done(function (cellWeather) {
            var currentWeather = cellWeather.weather
            var currentCell = $('#currentWeather').data('current-cell')
            if ((currentWeather) && (currentCell !== currentWeather.s2_cell_id)) {
                $('#currentWeather').data('current-cell', currentWeather.s2_cell_id)
                $('#currentWeather').html('<img src="static/weather/' + currentWeather.condition + '.png" alt="">')
            } else if (!currentWeather) {
                $('#currentWeather').data('current-cell', '')
                $('#currentWeather').html('')
            }
        })
    }

    loadRawData().done(function (result) {
        $.each(result.pokemons, processPokemons)
        $.each(result.pokestops, processPokestops)
        $.each(result.gyms, processGyms)
        $.each(result.scanned, processScanned)
        $.each(result.spawnpoints, processSpawnpoints)
        $.each(result.nests, processNests)
        showInBoundsMarkers(mapData.pokemons, 'pokemon')
        showInBoundsMarkers(mapData.lurePokemons, 'pokemon')
        showInBoundsMarkers(mapData.gyms, 'gym')
        showInBoundsMarkers(mapData.pokestops, 'pokestop')
        showInBoundsMarkers(mapData.scanned, 'scanned')
        showInBoundsMarkers(mapData.spawnpoints, 'inbound'
            //      drawScanPath(result.scanned)
        )
        clearStaleMarkers()

        updateScanned()
        updateSpawnPoints()
        updatePokestops()

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

        prevMinIV = result.preMinIV
        prevMinLevel = result.preMinLevel
        reids = result.reids
        if (reids instanceof Array) {
            reincludedPokemon = reids.filter(function (e) {
                return this.indexOf(e) < 0
            }, reincludedPokemon)
        }
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

function drawWeatherOverlay(weather) {
    if (weather) {
        $.each(weather, function (idx, item) {
            weatherArray.push(S2.idToCornerLatLngs(item.s2_cell_id))
            var poly = new google.maps.Polygon({
                id: item.id,
                paths: weatherArray,
                strokeColor: weatherColors[item.condition],
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: weatherColors[item.condition],
                fillOpacity: 0.35
            })
            var bounds = new google.maps.LatLngBounds()
            var i, center

            for (i = 0; i < weatherArray[0].length; i++) {
                bounds.extend(weatherArray[0][i])
            }
            center = bounds.getCenter()

            var overlayIconSize = new google.maps.Size(30, 30)
            var scaledIconCenterOffset = new google.maps.Point(15, 15)
            var image = 'static/weather/i-' + item.condition + '.png'
            var marker = new google.maps.Marker({
                position: {
                    lat: center.lat(),
                    lng: center.lng()
                },
                map: map,
                icon: {
                    url: image,
                    size: overlayIconSize,
                    scaledSize: overlayIconSize,
                    origin: new google.maps.Point(0, 0),
                    anchor: scaledIconCenterOffset
                }
            })
            weatherPolys.push(poly)
            weatherMarkers.push(marker)
            poly.setMap(map)
            weatherArray = []
        })
    }
}

function destroyWeatherOverlay() {
    $.each(weatherPolys, function (idx, poly) {
        poly.setMap(null)
    })
    $.each(weatherMarkers, function (idx, marker) {
        marker.setMap(null)
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
            if (item.marker.rangeCircle) item.marker.rangeCircle.setMap(null)
            var newMarker = setupPokemonMarker(item, map, this.marker.animationDisabled)
            customizePokemonMarker(newMarker, item, skipNotification)
            item.marker.setMap(null)
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

function getPointDistance(pointA, pointB) {
    return google.maps.geometry.spherical.computeDistanceBetween(pointA, pointB)
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
                    centerMap(lat, lon, 20)
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
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude)
            locationMarker.setPosition(latlng)
            map.setCenter(latlng)
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

function changeLocation(lat, lng) {
    var loc = new google.maps.LatLng(lat, lng)
    map.setCenter(loc)
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
            var center = new google.maps.LatLng(lat, lng)

            if (Store.get('followMyLocation')) {
                if (typeof locationMarker !== 'undefined' && getPointDistance(locationMarker.getPosition(), center) >= 5) {
                    map.panTo(center)
                    locationMarker.setPosition(center)
                    if (Store.get('spawnArea')) {
                        if (locationMarker.rangeCircle) {
                            locationMarker.rangeCircle.setMap(null)
                            delete locationMarker.rangeCircle
                        }
                        var rangeCircleOpts = {
                            map: map,
                            radius: 35, // meters
                            strokeWeight: 1,
                            strokeColor: '#FF9200',
                            strokeOpacity: 0.9,
                            center: center,
                            fillColor: '#FF9200',
                            fillOpacity: 0.3
                        }
                        locationMarker.rangeCircle = new google.maps.Circle(rangeCircleOpts)
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
        if (((result['park'] !== 'None' && result['park'] !== undefined && result['park']) && (noParkInfo === false))) {
            if (result['park'] === 1) {
                // RM only stores boolean, so just call it "Park Gym"
                park = i8ln('Park Gym')
            } else {
                park = i8ln('Park') + ': ' + result['park']
            }
        }

        var raidSpawned = result['raid_level'] != null
        var raidStarted = result['raid_pokemon_id'] != null

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
                if (result.raid_pokemon_cp != null) {
                    cpStr = ' CP ' + result.raid_pokemon_cp
                }
                raidStr += '<br>' + result.raid_pokemon_name + cpStr
            }
            raidStr += '</h3>'
            if (raidStarted && result.raid_pokemon_move_1 != null && result.raid_pokemon_move_2 != null) {
                var pMove1 = (moves[result['raid_pokemon_move_1']] !== undefined) ? i8ln(moves[result['raid_pokemon_move_1']]['name']) : 'gen/unknown'
                var pMove2 = (moves[result['raid_pokemon_move_2']] !== undefined) ? i8ln(moves[result['raid_pokemon_move_2']]['name']) : 'gen/unknown'
                raidStr += '<div><b>' + pMove1 + ' / ' + pMove2 + '</b></div>'
            }

            var raidStartStr = getTimeStr(result['raid_start'])
            var raidEndStr = getTimeStr(result['raid_end'])
            raidStr += '<div>' + i8ln('Start') + ': <b>' + raidStartStr + '</b> <span class="label-countdown" disappears-at="' + result['raid_start'] + '" start>(00m00s)</span></div>'
            raidStr += '<div>' + i8ln('End') + ': <b>' + raidEndStr + '</b> <span class="label-countdown" disappears-at="' + result['raid_end'] + '" end>(00m00s)</span></div>'

            if (raidStarted) {
                raidIcon = '<i class="pokemon-sprite-large n' + result.raid_pokemon_id + '"></i>'
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

        var headerHtml =
            '<center class="team-' + result.team_id + '-text">' +
            '<div>' +
            '<b class="team-' + result.team_id + '-text">' + (result.name || '') + '</b>' +
            '</div>' +
            '<div>' +
            '<img height="60px" style="padding: 5px;" src="static/forts/' + gymTypes[result.team_id] + '_large.png">' +
            raidIcon +
            '</div>' +
            raidStr +
            gymLevelStr +
            '<div>' +
            park +
            '</div>' +
            '<div>' +
            '<a href=\'javascript:void(0)\' onclick=\'javascript:openMapDirections(' + result.latitude + ',' + result.longitude + ')\' title=\'' + i8ln('View in Maps') + '\'>' + i8ln('Get directions') + '</a>' +
            '</div>' +
            '</center>'

        if (pokemon.length) {
            $.each(pokemon, function (i, pokemon) {
                var perfectPercent = getIv(pokemon.iv_attack, pokemon.iv_defense, pokemon.iv_stamina)
                var moveEnergy = Math.round(100 / pokemon.move_2_energy)

                pokemonHtml +=
                    '<tr onclick=toggleGymPokemonDetails(this)>' +
                    '<td width="30px">' +
                    '<i class="pokemon-sprite n' + pokemon.pokemon_id + '"></i>' +
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
            pokemonHtml =
                '<center class="team-' + result.team_id + '-text">' +
                'Gym Leader:<br>' +
                '<i class="pokemon-sprite-large n' + result.guard_pokemon_id + '"></i><br>' +
                '<b class="team-' + result.team_id + '-text">' + result.guard_pokemon_name + '</b>' +
                '<p style="font-size: .75em margin: 5px">' +
                'No additional gym information is available for this gym. Make sure you are collecting detailed gym info. If you have detailed gym info collection running, this gym\'s Pokemon information may be out of date.' +
                '</p>' +
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
        var j = Math.floor(element / 28)
        var b = element % 28
        if (b === 0) {
            b = 28
            j = j - 1
        }
        var k = b - 1
        var p = j * 48.25
        var a = k * 48.25
        data += '<span class="pokemon-icon-sprite" data-value="' + element + '" data-label="' + raidBoss[element].name + '" onclick="pokemonRaidFilter(event);"><span class="' + element + ' inner-bg" style="background-position:-' + a + 'px -' + p + 'px"></span></span>'
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

function loadDefaultImages() {
    var ep = Store.get('remember_select_exclude')
    var eminiv = Store.get('remember_select_exclude_min_iv')
    var en = Store.get('remember_select_notify')
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

    $selectLuredPokestopsOnly = $('#lured-pokestops-only-switch')

    $selectLuredPokestopsOnly.select2({
        placeholder: 'Only Show Lured Pokestops',
        minimumResultsForSearch: Infinity
    })

    $selectLuredPokestopsOnly.on('change', function () {
        Store.set('showLuredPokestopsOnly', this.value)
        lastpokestops = false
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
                    mapData[dType][key].marker.rangeCircle.setMap(null)
                    delete mapData[dType][key].marker.rangeCircle
                }
                mapData[dType][key].marker.setMap(null)
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
                    mapData[dType][key].marker.rangeCircle.setMap(null)
                    delete mapData[dType][key].marker.rangeCircle
                }
                mapData[dType][key].marker.setMap(null)
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

            var latlng = new google.maps.LatLng(lat, lng)
            locationMarker.setPosition(latlng)
            map.setCenter(latlng)
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

    $.getJSON('static/dist/data/quests.min.json').done(function (data) {
        $.each(data, function (key, value) {
            questList[key] = value['name'];
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
    var numberOfPokemon = 386

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
    $('.area-go-to').on('click', function (e) {
        e.preventDefault()
        var lat = $(this).data('lat')
        var lng = $(this).data('lng')
        var zoom = $(this).data('zoom')
        map.setCenter(new google.maps.LatLng(lat, lng))
        map.setZoom(zoom)
    })

    $raidNotify.select2({
        placeholder: 'Minimum raid level',
        minimumResultsForSearch: Infinity
    })

    $raidNotify.on('change', function () {
        Store.set('remember_raid_notify', this.value)
    })
    if (manualRaids) {
        $.getJSON('static/dist/data/raid-boss.min.json').done(function (data) {
            $.each(data, function (key, value) {
                if (key > numberOfPokemon) {
                    return false
                }
                raidBoss[key] = {
                    name: i8ln(value['name']),
                    level: value['level'],
                    cp: value['cp']
                }
            })
            $('.global-raid-modal').html(generateRaidModal())
        })
    }
    $('#dialog_edit').on('click', '#closeButtonId', function () {
        $(this).closest('#dialog_edit').dialog('close')
    })


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

        if (isTouchDevice() && isMobileDevice()) {
            $('.select2-search input').prop('readonly', true)
        }
        $('#tabs').tabs()
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

    $('#pokestops-switch').change(function () {
        var options = {
            'duration': 500
        }
        var wrapper = $('#lured-pokestops-only-wrapper')
        if (this.checked) {
            lastpokestops = false
            wrapper.show(options)
        } else {
            lastpokestops = false
            wrapper.hide(options)
        }
        return buildSwitchChangeListener(mapData, ['pokestops'], 'showPokestops').bind(this)()
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
        locationMarker.setDraggable(!this.checked)
    })

    $('#spawn-area-switch').change(function () {
        Store.set('spawnArea', this.checked)
        if (locationMarker.rangeCircle) {
            locationMarker.rangeCircle.setMap(null)
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
