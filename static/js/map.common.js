'use strict'

/* eslint no-unused-vars: "off" */
var L
var markers
var noLabelsStyle = [{
    featureType: 'poi',
    elementType: 'labels',
    stylers: [{
        visibility: 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}]
var light2Style = [{
    'elementType': 'geometry',
    'stylers': [{
        'hue': '#ff4400'
    }, {
        'saturation': -68
    }, {
        'lightness': -4
    }, {
        'gamma': 0.72
    }]
}, {
    'featureType': 'road',
    'elementType': 'labels.icon'
}, {
    'featureType': 'landscape.man_made',
    'elementType': 'geometry',
    'stylers': [{
        'hue': '#0077ff'
    }, {
        'gamma': 3.1
    }]
}, {
    'featureType': 'water',
    'stylers': [{
        'hue': '#00ccff'
    }, {
        'gamma': 0.44
    }, {
        'saturation': -33
    }]
}, {
    'featureType': 'poi.park',
    'stylers': [{
        'hue': '#44ff00'
    }, {
        'saturation': -23
    }]
}, {
    'featureType': 'water',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'hue': '#007fff'
    }, {
        'gamma': 0.77
    }, {
        'saturation': 65
    }, {
        'lightness': 99
    }]
}, {
    'featureType': 'water',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'gamma': 0.11
    }, {
        'weight': 5.6
    }, {
        'saturation': 99
    }, {
        'hue': '#0091ff'
    }, {
        'lightness': -86
    }]
}, {
    'featureType': 'transit.line',
    'elementType': 'geometry',
    'stylers': [{
        'lightness': -48
    }, {
        'hue': '#ff5e00'
    }, {
        'gamma': 1.2
    }, {
        'saturation': -23
    }]
}, {
    'featureType': 'transit',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'saturation': -64
    }, {
        'hue': '#ff9100'
    }, {
        'lightness': 16
    }, {
        'gamma': 0.47
    }, {
        'weight': 2.7
    }]
}]
var darkStyle = [{
    'featureType': 'all',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'saturation': 36
    }, {
        'color': '#b39964'
    }, {
        'lightness': 40
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'visibility': 'on'
    }, {
        'color': '#000000'
    }, {
        'lightness': 16
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'administrative',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 20
    }]
}, {
    'featureType': 'administrative',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 17
    }, {
        'weight': 1.2
    }]
}, {
    'featureType': 'landscape',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 20
    }]
}, {
    'featureType': 'poi',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 21
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 17
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 29
    }, {
        'weight': 0.2
    }]
}, {
    'featureType': 'road.arterial',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 18
    }]
}, {
    'featureType': 'road.local',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#181818'
    }, {
        'lightness': 16
    }]
}, {
    'featureType': 'transit',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 19
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry',
    'stylers': [{
        'lightness': 17
    }, {
        'color': '#525252'
    }]
}]
var pGoStyle = [{
    'featureType': 'landscape.man_made',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#a1f199'
    }]
}, {
    'featureType': 'landscape.natural.landcover',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'landscape.natural.terrain',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'visibility': 'on'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#e4dfd9'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'poi.park',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#84b09e'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#fafeb8'
    }, {
        'weight': '1.25'
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#5ddad6'
    }]
}]
var light2StyleNoLabels = [{
    'elementType': 'geometry',
    'stylers': [{
        'hue': '#ff4400'
    }, {
        'saturation': -68
    }, {
        'lightness': -4
    }, {
        'gamma': 0.72
    }]
}, {
    'featureType': 'road',
    'elementType': 'labels.icon'
}, {
    'featureType': 'landscape.man_made',
    'elementType': 'geometry',
    'stylers': [{
        'hue': '#0077ff'
    }, {
        'gamma': 3.1
    }]
}, {
    'featureType': 'water',
    'stylers': [{
        'hue': '#00ccff'
    }, {
        'gamma': 0.44
    }, {
        'saturation': -33
    }]
}, {
    'featureType': 'poi.park',
    'stylers': [{
        'hue': '#44ff00'
    }, {
        'saturation': -23
    }]
}, {
    'featureType': 'water',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'hue': '#007fff'
    }, {
        'gamma': 0.77
    }, {
        'saturation': 65
    }, {
        'lightness': 99
    }]
}, {
    'featureType': 'water',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'gamma': 0.11
    }, {
        'weight': 5.6
    }, {
        'saturation': 99
    }, {
        'hue': '#0091ff'
    }, {
        'lightness': -86
    }]
}, {
    'featureType': 'transit.line',
    'elementType': 'geometry',
    'stylers': [{
        'lightness': -48
    }, {
        'hue': '#ff5e00'
    }, {
        'gamma': 1.2
    }, {
        'saturation': -23
    }]
}, {
    'featureType': 'transit',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'saturation': -64
    }, {
        'hue': '#ff9100'
    }, {
        'lightness': 16
    }, {
        'gamma': 0.47
    }, {
        'weight': 2.7
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}]
var darkStyleNoLabels = [{
    'featureType': 'all',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'administrative',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 20
    }]
}, {
    'featureType': 'administrative',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 17
    }, {
        'weight': 1.2
    }]
}, {
    'featureType': 'landscape',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 20
    }]
}, {
    'featureType': 'poi',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 21
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 17
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 29
    }, {
        'weight': 0.2
    }]
}, {
    'featureType': 'road.arterial',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 18
    }]
}, {
    'featureType': 'road.local',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#181818'
    }, {
        'lightness': 16
    }]
}, {
    'featureType': 'transit',
    'elementType': 'geometry',
    'stylers': [{
        'color': '#000000'
    }, {
        'lightness': 19
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry',
    'stylers': [{
        'lightness': 17
    }, {
        'color': '#525252'
    }]
}]
var pGoStyleNoLabels = [{
    'featureType': 'landscape.man_made',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#a1f199'
    }]
}, {
    'featureType': 'landscape.natural.landcover',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'landscape.natural.terrain',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'visibility': 'on'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#e4dfd9'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'poi.park',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#37bda2'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#84b09e'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#fafeb8'
    }, {
        'weight': '1.25'
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#5ddad6'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.stroke',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.text.fill',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'all',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}]
var pGoStyleDay = [{
    'featureType': 'landscape.man_made',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#99f291'
    }]
}, {
    'featureType': 'landscape.natural.landcover',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#00af8f'
    }]
}, {
    'featureType': 'landscape.natural.terrain',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#00af8f'
    }]
}, {
    'featureType': 'landscape.natural',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#00af8f'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'visibility': 'on'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#e4dfd9'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'poi.park',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#00af8f'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#7eb2a4'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#ffff92'
    }, {
        'weight': '2'
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#1688da'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#e4fdee'
    }]
}, {
    'featureType': 'poi.sports_complex',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#d4ffbc'
    }]
}]
var pGoStyleNight = [{
    'featureType': 'landscape.man_made',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#12a085'
    }]
}, {
    'featureType': 'landscape.natural.landcover',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#02706a'
    }]
}, {
    'featureType': 'landscape.natural.terrain',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#02706a'
    }]
}, {
    'featureType': 'landscape.natural',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#02706a'
    }]
}, {
    'featureType': 'poi',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#6da298'
    }]
}, {
    'featureType': 'poi.medical',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#6da298'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'visibility': 'on'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#1fba9c'
    }]
}, {
    'featureType': 'poi.business',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'poi.park',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#02706a'
    }]
}, {
    'featureType': 'transit',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#428290'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#316589'
    }]
}, {
    'featureType': 'road',
    'elementType': 'geometry.stroke',
    'stylers': [{
        'color': '#7f8b60'
    }, {
        'weight': '2'
    }]
}, {
    'featureType': 'road.highway',
    'elementType': 'labels.icon',
    'stylers': [{
        'visibility': 'off'
    }]
}, {
    'featureType': 'water',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#1e4fbc'
    }]
}, {
    'featureType': 'poi.attraction',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#1fba9c'
    }]
}, {
    'featureType': 'poi.sports_complex',
    'elementType': 'geometry.fill',
    'stylers': [{
        'color': '#1fba9c'
    }]
}]

var pokemonSprites = {
    columns: 28,
    iconWidth: 80,
    iconHeight: 80,
    spriteWidth: 2240,
    spriteHeight: 1440
}

//
// LocalStorage helpers
//

var StoreTypes = {
    Boolean: {
        parse: function parse(str) {
            switch (str.toLowerCase()) {
                case '1':
                case 'true':
                case 'yes':
                    return true
                default:
                    return false
            }
        },
        stringify: function stringify(b) {
            return b ? 'true' : 'false'
        }
    },
    JSON: {
        parse: function parse(str) {
            return JSON.parse(str)
        },
        stringify: function stringify(json) {
            return JSON.stringify(json)
        }
    },
    String: {
        parse: function parse(str) {
            return str
        },
        stringify: function stringify(str) {
            return str
        }
    },
    Number: {
        parse: function parse(str) {
            return parseInt(str, 10)
        },
        stringify: function stringify(number) {
            return number.toString()
        }
    }

// set the default parameters for you map here
}
var StoreOptions = {
    'map_style': {
        default: mapStyle, // roadmap, satellite, hybrid, nolabels_style, dark_style, style_light2, style_pgo, dark_style_nl, style_pgo_day, style_pgo_night, style_pgo_dynamic
        type: StoreTypes.String
    },
    'remember_select_exclude': {
        default: hidePokemon,
        type: StoreTypes.JSON
    },
    'remember_select_exclude_min_iv':
        {
            default: excludeMinIV,
            type: StoreTypes.JSON
        },
    'remember_select_notify':
        {
            default: notifyPokemon,
            type: StoreTypes.JSON
        },
    'remember_select_rarity_notify':
        {
            default: notifyRarity, // Common, Uncommon, Rare, Very Rare, Ultra Rare
            type: StoreTypes.JSON
        },
    'remember_text_perfection_notify':
        {
            default: notifyIv,
            type: StoreTypes.Number
        },
    'remember_text_level_notify':
        {
            default: notifyLevel,
            type: StoreTypes.Number
        },
    'remember_text_min_iv':
        {
            default: minIV,
            type: StoreTypes.Number
        },
    'remember_text_min_level':
        {
            default: minLevel,
            type: StoreTypes.Number
        },
    'remember_raid_notify':
        {
            default: notifyRaid,
            type: StoreTypes.Number
        },
    'remember_bounce_notify':
        {
            default: notifyBounce,
            type: StoreTypes.Boolean
        },
    'remember_notification_notify':
        {
            default: notifyNotification,
            type: StoreTypes.Boolean
        },
    'remember_quests_exclude_pokemon':
        {
            default: hideQuestsPokemon,
            type: StoreTypes.JSON
        },
    'remember_quests_exclude_item':
        {
            default: hideQuestsItem,
            type: StoreTypes.JSON
        },
    'showRaids':
        {
            default: enableRaids,
            type: StoreTypes.Boolean
        },
    'activeRaids':
        {
            default: activeRaids,
            type: StoreTypes.Boolean
        },
    'minRaidLevel':
        {
            default: minRaidLevel,
            type: StoreTypes.Number
        },
    'maxRaidLevel':
        {
            default: maxRaidLevel,
            type: StoreTypes.Number
        },
    'showGyms':
        {
            default: enableGyms,
            type: StoreTypes.Boolean
        },
    'showNests':
        {
            default: enableNests,
            type: StoreTypes.Boolean
        },
    'showCommunities':
        {
            default: enableCommunities,
            type: StoreTypes.Boolean
        },
    'showPortals':
        {
            default: enablePortals,
            type: StoreTypes.Boolean
        },
    'showPoi':
        {
            default: enablePoi,
            type: StoreTypes.Boolean
        },
    'showNewPortalsOnly':
        {
            default: enableNewPortals,
            type: StoreTypes.Number
        },
    'showCells':
        {
            default: enableS2Cells,
            type: StoreTypes.Boolean
        },
    'showExCells':
        {
            default: enableLevel13Cells,
            type: StoreTypes.Boolean
        },
    'showGymCells':
        {
            default: enableLevel14Cells,
            type: StoreTypes.Boolean
        },
    'showStopCells':
        {
            default: enableLevel17Cells,
            type: StoreTypes.Boolean
        },
    'useGymSidebar':
        {
            default: gymSidebar,
            type: StoreTypes.Boolean
        },
    'showOpenGymsOnly':
        {
            default: false,
            type: StoreTypes.Boolean
        },
    'showTeamGymsOnly':
        {
            default: 0,
            type: StoreTypes.Number
        },
    'showLastUpdatedGymsOnly':
        {
            default: 0,
            type: StoreTypes.Number
        },
    'minGymLevel':
        {
            default: 0,
            type: StoreTypes.Number
        },
    'maxGymLevel':
        {
            default: 6,
            type: StoreTypes.Number
        },
    'showPokemon':
        {
            default: enablePokemon,
            type: StoreTypes.Boolean
        },
    'showBigKarp':
        {
            default: showBigKarp,
            type: StoreTypes.Boolean
        },
    'showTinyRat':
        {
            default: showTinyRat,
            type: StoreTypes.Boolean
        },
    'showPokestops':
        {
            default: enablePokestops,
            type: StoreTypes.Boolean
        },
    'showLures':
        {
            default: enableLured,
            type: StoreTypes.Boolean
        },
    'showQuests':
        {
            default: enableQuests,
            type: StoreTypes.Boolean
        },
    'showDustAmount':
        {
            default: 500,
            type: StoreTypes.Number
        },
    'showWeather':
        {
            default: enableWeatherOverlay,
            type: StoreTypes.Boolean
        },
    'showScanned':
        {
            default: enableScannedLocations,
            type: StoreTypes.Boolean
        },
    'showSpawnpoints':
        {
            default: enableSpawnpoints,
            type: StoreTypes.Boolean
        },
    'showRanges':
        {
            default: enableRanges,
            type: StoreTypes.Boolean
        },
    'showScanPolygon':
        {
            default: enableScanPolygon,
            type: StoreTypes.Boolean
        },
    'playSound':
        {
            default: notifySound,
            type: StoreTypes.Boolean
        },
    'playCries':
        {
            default: criesSound,
            type: StoreTypes.Boolean
        },
    'geoLocate':
        {
            default: false,
            type: StoreTypes.Boolean
        },
    'lockMarker':
        {
            default: isTouchDevice(), // default to true if touch device
            type: StoreTypes.Boolean
        },
    'startAtUserLocation':
        {
            default: enableStartMe,
            type: StoreTypes.Boolean
        },
    'startAtLastLocation':
        {
            default: enableStartLast,
            type: StoreTypes.Boolean
        },
    'startAtLastLocationPosition':
        {
            default: [],
            type: StoreTypes.JSON
        },
    'followMyLocation':
        {
            default: enableFollowMe,
            type: StoreTypes.Boolean
        },
    'followMyLocationPosition':
        {
            default: [],
            type: StoreTypes.JSON
        },
    'spawnArea':
        {
            default: enableSpawnArea,
            type: StoreTypes.Boolean
        },
    'scanHere':
        {
            default: false,
            type: StoreTypes.Boolean
        },
    'scanHereAlerted':
        {
            default: false,
            type: StoreTypes.Boolean
        },
    'iconSizeModifier':
        {
            default: iconSize,
            type: StoreTypes.Number
        },
    'iconNotifySizeModifier':
        {
            default: iconNotifySizeModifier,
            type: StoreTypes.Number
        },
    'searchMarkerStyle':
        {
            default: 'OSM',
            type: StoreTypes.String
        },
    'locationMarkerStyle':
        {
            default: locationStyle,
            type: StoreTypes.String
        },
    'directionProvider':
        {
            default: directionProvider,
            type: StoreTypes.String
        },
    'gymMarkerStyle':
        {
            default: gymStyle,
            type: StoreTypes.String
        },
    'zoomLevel':
        {
            default: 16,
            type: StoreTypes.Number
        },
    'icons':
        {
            default: icons,
            type: StoreTypes.String
        },
    'triggerGyms':
        {
            default: triggerGyms,
            type: StoreTypes.JSON
        },
    'exEligible':
        {
            default: exEligible,
            type: StoreTypes.Boolean
        }
}

var Store = {
    getOption: function getOption(key) {
        var option = StoreOptions[key]
        if (!option) {
            throw new Error('Store key was not defined ' + key)
        }
        return option
    },
    get: function getKey(key) {
        var option = this.getOption(key)
        var optionType = option.type
        var rawValue = localStorage[key]
        if (rawValue === null || rawValue === undefined) {
            return option.default
        }
        return optionType.parse(rawValue)
    },
    set: function setKey(key, value) {
        var option = this.getOption(key)
        var optionType = option.type || StoreTypes.String
        localStorage[key] = optionType.stringify(value)
    },
    reset: function reset(key) {
        localStorage.removeItem(key)
    }
}

var mapData = {
    pokemons: {},
    gyms: {},
    pokestops: {},
    lurePokemons: {},
    scanned: {},
    spawnpoints: {},
    nests: {},
    communities: {},
    portals: {},
    pois: {}
}

function getPokemonSprite(index, sprite, displayHeight, weather = 0, encounterForm = 0) {
    displayHeight = Math.max(displayHeight, 3)
    var scale = displayHeight / sprite.iconHeight
    // Crop icon just a tiny bit to avoid bleedover from neighbor
    var scaledIconSizeWidth = scale * sprite.iconWidth
    var scaledWeatherIconSizeWidth = scale * sprite.iconWidth - 10
    var scaledIconCenterOffset = [scale * sprite.iconWidth / 2, scale * sprite.iconHeight / 2]
    var formStr = ''
    if (encounterForm === '0' || encounterForm === null || encounterForm === 0) {
        formStr = '00'
    } else {
        formStr = encounterForm
    }

    var pokemonId = index + 1
    var pokemonIdStr = ''
    if (pokemonId <= 9) {
        pokemonIdStr = '00' + pokemonId
    } else if (pokemonId <= 99) {
        pokemonIdStr = '0' + pokemonId
    } else {
        pokemonIdStr = pokemonId
    }
    var html = ''
    if (weather === 0) {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;"/>'
    } else if (boostedMons[weather].indexOf(pokemonId) === -1) {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;"/>'
    } else if (noWeatherIcons && noWeatherShadow) {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;"/>'
    } else if (noWeatherIcons && noWeatherShadow === false) {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;-webkit-filter: drop-shadow(10px 10px 10px #4444dd); filter: drop-shadow(10px 10px 10px #4444dd);"/>'
    } else if (noWeatherIcons === false && noWeatherShadow) {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;"/>' +
        '<img src="static/weather/i-' + weather + '.png" style="width:' + scaledWeatherIconSizeWidth + 'px;height:auto;position:absolute;top:-' + scaledWeatherIconSizeWidth + 'px;right:0px;"/>'
    } else {
        html = '<img src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png" style="width:' + scaledIconSizeWidth + 'px;height:auto;-webkit-filter: drop-shadow(10px 10px 10px #4444dd); filter: drop-shadow(10px 10px 10px #4444dd);"/>'
    }
    var pokemonIcon = L.divIcon({
        iconAnchor: scaledIconCenterOffset,
        popupAnchor: [0, -40],
        className: 'pokemon-marker',
        html: html
    })
    return pokemonIcon
}

function setupPokemonMarker(item, map, isBounceDisabled) {
    var iconSize = (12) * (12) * 0.2 + Store.get('iconSizeModifier')
    if (isNotifiedPokemon(item) === true) {
        iconSize += Store.get('iconNotifySizeModifier')
    }
    var pokemonIndex = item['pokemon_id'] - 1
    var icon = getPokemonSprite(pokemonIndex, pokemonSprites, iconSize, item['weather_boosted_condition'], item['form'])

    var animationDisabled = false
    if (isBounceDisabled === true) {
        animationDisabled = true
    }
    var marker = L.marker([item['latitude'], item['longitude']], {icon: icon, zIndexOffset: 9999}).addTo(markers)
    return marker
}

function isNotifiedPokemon(item) {
    var level = item['level']
    var iv = getIv(item['individual_attack'], item['individual_defense'], item['individual_stamina'])
    var notifiedMinPerfection = Store.get('remember_text_perfection_notify')
    var notifiedMinLevel = Store.get('remember_text_level_notify')
    var notifiedPokemon = Store.get('remember_select_notify')
    var notifiedRarity = Store.get('remember_select_rarity_notify')

    if ((iv >= notifiedMinPerfection && notifiedMinPerfection > 0) || notifiedPokemon.indexOf(item['pokemon_id']) > -1 ||
        notifiedRarity.indexOf(item['pokemon_rarity']) > -1 || (notifiedMinLevel > 0 && level >= notifiedMinLevel)) {
        return true
    }

    return false
}

function isTouchDevice() {
// Should cover most browsers
    return 'ontouchstart' in window || navigator.maxTouchPoints
}

function isMobileDevice() {
//  Basic mobile OS (not browser) detection
    return (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))
}
