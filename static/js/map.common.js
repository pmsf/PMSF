'use strict'

/* eslint no-unused-vars: "off" */
var L
var markers
var pokemonSprites = {
   iconWidth: 80,
   iconHeight: 80
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
   'remember_select_exclude_min_iv': {
      default: excludeMinIV,
      type: StoreTypes.JSON
   },
   'remember_select_notify': {
      default: notifyPokemon,
      type: StoreTypes.JSON
   },
   'remember_select_rarity_notify': {
      default: notifyRarity, // Common, Uncommon, Rare, Very Rare, Ultra Rare
      type: StoreTypes.JSON
   },
   'remember_text_perfection_notify': {
      default: notifyIv,
      type: StoreTypes.Number
   },
   'remember_text_level_notify': {
      default: notifyLevel,
      type: StoreTypes.Number
   },
   'remember_text_min_gl_rank': {
      default: minGLRank,
      type: StoreTypes.Number
   },
   'remember_text_min_ul_rank': {
      default: minULRank,
      type: StoreTypes.Number
   },
   'remember_text_min_iv': {
      default: minIV,
      type: StoreTypes.Number
   },
   'remember_text_min_level': {
      default: minLevel,
      type: StoreTypes.Number
   },
   'remember_raid_notify': {
      default: notifyRaid,
      type: StoreTypes.Number
   },
   'remember_bounce_notify': {
      default: notifyBounce,
      type: StoreTypes.Boolean
   },
   'remember_notification_notify': {
      default: notifyNotification,
      type: StoreTypes.Boolean
   },
   'remember_quests_exclude_pokemon': {
      default: hideQuestsPokemon,
      type: StoreTypes.JSON
   },
   'remember_quests_exclude_energy': {
      default: hideQuestsEnergy,
      type: StoreTypes.JSON
   },
   'remember_quests_exclude_candy': {
      default: hideQuestsCandy,
      type: StoreTypes.JSON
   },
   'remember_quests_exclude_item': {
      default: hideQuestsItem,
      type: StoreTypes.JSON
   },
   'remember_exclude_grunts': {
      default: hideGrunts,
      type: StoreTypes.JSON
   },
   'showRaids': {
      default: enableRaids,
      type: StoreTypes.Boolean
   },
   'activeRaids': {
      default: activeRaids,
      type: StoreTypes.Boolean
   },
   'minRaidLevel': {
      default: minRaidLevel,
      type: StoreTypes.Number
   },
   'maxRaidLevel': {
      default: maxRaidLevel,
      type: StoreTypes.Number
   },
   'remember_exclude_raidboss': {
      default: hideRaidboss,
      type: StoreTypes.JSON
   },
   'remember_exclude_raidegg': {
      default: hideRaidegg,
      type: StoreTypes.JSON
   },
   'showGyms': {
      default: enableGyms,
      type: StoreTypes.Boolean
   },
   'showNests': {
      default: enableNests,
      type: StoreTypes.Boolean
   },
   'showCommunities': {
      default: enableCommunities,
      type: StoreTypes.Boolean
   },
   'showPortals': {
      default: enablePortals,
      type: StoreTypes.Boolean
   },
   'showPoi': {
      default: enablePoi,
      type: StoreTypes.Boolean
   },
   'showNewPortalsOnly': {
      default: enableNewPortals,
      type: StoreTypes.Number
   },
   'showCells': {
      default: enableS2Cells,
      type: StoreTypes.Boolean
   },
   'showPlacementRanges': {
      default: enablePlacementRanges,
      type: StoreTypes.Boolean
   },
   'showExCells': {
      default: enableLevel13Cells,
      type: StoreTypes.Boolean
   },
   'showGymCells': {
      default: enableLevel14Cells,
      type: StoreTypes.Boolean
   },
   'showPokemonCells': {
      default: enableLevel15Cells,
      type: StoreTypes.Boolean
   },
   'showStopCells': {
      default: enableLevel17Cells,
      type: StoreTypes.Boolean
   },
   'showOpenGymsOnly': {
      default: false,
      type: StoreTypes.Boolean
   },
   'showTeamGymsOnly': {
      default: 0,
      type: StoreTypes.Number
   },
   'showLastUpdatedGymsOnly': {
      default: 0,
      type: StoreTypes.Number
   },
   'minGymLevel': {
      default: 0,
      type: StoreTypes.Number
   },
   'maxGymLevel': {
      default: 6,
      type: StoreTypes.Number
   },
   'showPokemon': {
      default: enablePokemon,
      type: StoreTypes.Boolean
   },
   'showMissingIVOnly': {
      default: false,
      type: StoreTypes.Boolean
   },
   'showBigKarp': {
      default: showBigKarp,
      type: StoreTypes.Boolean
   },
   'showTinyRat': {
      default: showTinyRat,
      type: StoreTypes.Boolean
   },
   'showDespawnTimeType': {
      default: showDespawnTimeType,
      type: StoreTypes.Number
   },
   'showPokemonGender': {
      default: showPokemonGender,
      type: StoreTypes.Number
   },
   'showPokestops': {
      default: enablePokestops,
      type: StoreTypes.Boolean
   },
   'showAllPokestops': {
      default: enableAllPokestops,
      type: StoreTypes.Boolean
   },
   'showLures': {
      default: enableLured,
      type: StoreTypes.Boolean
   },
   'showRocket': {
      default: enableRocket,
      type: StoreTypes.Boolean
   },
   'showQuests': {
      default: enableQuests,
      type: StoreTypes.Boolean
   },
   'showQuestsWithTaskAR': {
      default: true,
      type: StoreTypes.Boolean
   },
   'showDustAmount': {
      default: defaultDustAmount,
      type: StoreTypes.Number
   },
   'showNestAvg': {
      default: nestAvgDefault,
      type: StoreTypes.Number
   },
   'showWeather': {
      default: enableWeatherOverlay,
      type: StoreTypes.Boolean
   },
   'showSpawnpoints': {
      default: enableSpawnpoints,
      type: StoreTypes.Boolean
   },
   'showRanges': {
      default: enableRanges,
      type: StoreTypes.Boolean
   },
   'showScanPolygon': {
      default: enableScanPolygon,
      type: StoreTypes.Boolean
   },
   'showScanLocation': {
      default: enableLiveScan,
      type: StoreTypes.Boolean
   },
   'showNestPolygon': {
      default: enableNestPolygon,
      type: StoreTypes.Boolean
   },
   'showToast': {
      default: true,
      type: StoreTypes.Boolean
   },
   'toastPokemonDelay': {
      default: 2000,
      type: StoreTypes.Number
   },
   'playSound': {
      default: notifySound,
      type: StoreTypes.Boolean
   },
   'playCries': {
      default: criesSound,
      type: StoreTypes.Boolean
   },
   'geoLocate': {
      default: false,
      type: StoreTypes.Boolean
   },
   'lockMarker': {
      default: isTouchDevice(), // default to true if touch device
      type: StoreTypes.Boolean
   },
   'startAtUserLocation': {
      default: enableStartMe,
      type: StoreTypes.Boolean
   },
   'startAtLastLocation': {
      default: enableStartLast,
      type: StoreTypes.Boolean
   },
   'startAtLastLocationPosition': {
      default: [],
      type: StoreTypes.JSON
   },
   'followMyLocation': {
      default: enableFollowMe,
      type: StoreTypes.Boolean
   },
   'followMyLocationPosition': {
      default: [],
      type: StoreTypes.JSON
   },
   'spawnArea': {
      default: enableSpawnArea,
      type: StoreTypes.Boolean
   },
   'scanHere': {
      default: false,
      type: StoreTypes.Boolean
   },
   'scanHereAlerted': {
      default: false,
      type: StoreTypes.Boolean
   },
   'pokemonIconSize': {
      default: pokemonIconSize,
      type: StoreTypes.Number
   },
   'iconNotifySizeModifier': {
      default: iconNotifySizeModifier,
      type: StoreTypes.Number
   },
   'searchMarkerStyle': {
      default: 'OSM',
      type: StoreTypes.String
   },
   'locationMarkerStyle': {
      default: locationStyle,
      type: StoreTypes.String
   },
   'directionProvider': {
      default: directionProvider,
      type: StoreTypes.String
   },
   'zoomLevel': {
      default: defaultZoom,
      type: StoreTypes.Number
   },
   'iconsArray': {
      default: iconFolderArray,
      type: StoreTypes.JSON
   },
   'triggerGyms': {
      default: triggerGyms,
      type: StoreTypes.JSON
   },
   'exEligible': {
      default: exEligible,
      type: StoreTypes.Boolean
   },
   'showRaidTimer': {
      default: enableRaidTimer,
      type: StoreTypes.Boolean
   },
   'showRocketTimer': {
      default: enableRocketTimer,
      type: StoreTypes.Boolean
   },
   'oldMotd': {
      default: 'default',
      type: StoreTypes.String
   },
   'darkMode': {
      default: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches,
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
   spawnpoints: {},
   nests: {},
   communities: {},
   portals: {},
   pois: {}
}

function getPokemonSprite(index, sprite, displayHeight, weather = 0, encounterForm = 0, pokemonCostume = 0, attack = 0, defense = 0, stamina = 0, gender = 0) {
   displayHeight = Math.max(displayHeight, 3)
   var scale = displayHeight / sprite.iconHeight
   // Crop icon just a tiny bit to avoid bleedover from neighbor
   var scaledIconSizeWidth = scale * sprite.iconWidth
   var scaledWeatherIconSizeWidth = scaledIconSizeWidth * 0.6
   var scaledWeatherIconOffset = scaledIconSizeWidth * 0.2
   var scaledIconCenterOffset = [scale * sprite.iconWidth / 2, scale * sprite.iconHeight / 2]
   var pokemonId = index + 1
   var iv = 100 * (attack + defense + stamina) / 45
   var html = ''
   if (weather === 0 || noWeatherIcons) {
      html = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, encounterForm, pokemonCostume, gender) + '" style="width:' + scaledIconSizeWidth + 'px;height:auto;'
      if (!noIvShadow) {
         if (iv === 100) {
            html += 'filter:drop-shadow(0 0 10px red)drop-shadow(0 0 10px red);-webkit-filter:drop-shadow(0 0 10px red)drop-shadow(0 0 10px red);'
         } else if (iv === 0 && level > 0) {
            html += 'filter:drop-shadow(0 0 10px black)drop-shadow(0 0 10px black);-webkit-filter:drop-shadow(0 0 10px black)drop-shadow(0 0 10px black);'
         }
      }
      html += '"/>'
   } else if (noWeatherIcons === false) {
      html = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pokemonId, 0, encounterForm, pokemonCostume, gender) + '" style="width:' + scaledIconSizeWidth + 'px;height:auto;'
      if (!noIvShadow) {
         if (iv === 100) {
            html += 'filter:drop-shadow(0 0 10px red)drop-shadow(0 0 10px red);-webkit-filter:drop-shadow(0 0 10px red)drop-shadow(0 0 10px red);'
         } else if (iv === 0 && level > 0) {
            html += 'filter:drop-shadow(0 0 10px black)drop-shadow(0 0 10px black);-webkit-filter:drop-shadow(0 0 10px black)drop-shadow(0 0 10px black);'
         }
      }
      html += '"/>' +
         '<img src="static/weather/a-' + weather + '.png" style="width:' + scaledWeatherIconSizeWidth + 'px;height:auto;position:absolute;top:-' + scaledWeatherIconOffset + 'px;left:' + scaledWeatherIconSizeWidth + 'px;"/>'
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
   var iconSize = Store.get('pokemonIconSize')
   if (isNotifiedPokemon(item) === true) {
      iconSize += Store.get('iconNotifySizeModifier')
   }
   var pokemonIndex = item['pokemon_id'] - 1
   var pokemonCostume = item['costume']
   var attack = item['individual_attack']
   var defense = item['individual_defense']
   var stamina = item['individual_stamina']
   var icon = getPokemonSprite(pokemonIndex, pokemonSprites, iconSize, item['weather_boosted_condition'], item['form'], item['costume'], item['individual_attack'], item['individual_defense'], item['individual_stamina'], item['gender'])

   var animationDisabled = false
   if (isBounceDisabled === true) {
      animationDisabled = true
   }
   var marker = L.marker([item['latitude'], item['longitude']], {
      icon: icon,
      zIndexOffset: 9999,
      virtual: true
   }).addTo(markers)
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
