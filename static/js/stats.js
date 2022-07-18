function countMarkers(map) { // eslint-disable-line no-unused-vars
    var i = 0
    var arenaCount = []
    var arenaTotal = 0
    var raidCount = []
    var raidTotal = 0
    var spawnpointCount = []
    var spawnpointTotal = 0
    var pkmnCount = []
    var pkmnTotal = 0
    var pokestopCount = []
    var pokestopTotal = 0
    var pokeStatTable = $('#pokemonList_table').DataTable()

    var currentVisibleMap = map.getBounds()
    var thisPokeIsVisible = false
    var thisGymIsVisible = false
    var thisPokestopIsVisible = false
    var thisRaidIsVisible = false
    var thisSpawnpointIsVisible = false

    if (Store.get('showPokemon')) {
        $.each(mapData.pokemons, function (key, value) {
            var thisPokeLocation = {lat: mapData.pokemons[key]['latitude'], lng: mapData.pokemons[key]['longitude']}
            thisPokeIsVisible = currentVisibleMap.contains(thisPokeLocation)
            if (thisPokeIsVisible) {
                pkmnTotal++
                if ((mapData.pokemons[key]['form'] === 0) && (pkmnCount[mapData.pokemons[key]['pokemon_id']] === 0 || !pkmnCount[mapData.pokemons[key]['pokemon_id']])) {
                    pkmnCount[mapData.pokemons[key]['pokemon_id']] = {
                        'ID': mapData.pokemons[key]['pokemon_id'],
                        'Form': mapData.pokemons[key]['form'],
                        'Count': 1,
                        'Name': i8ln(mapData.pokemons[key]['pokemon_name'])
                    }
                } else if (mapData.pokemons[key]['form'] === 0) {
                    pkmnCount[mapData.pokemons[key]['pokemon_id']].Count += 1
                }
                if ((mapData.pokemons[key]['form'] > 0) && (pkmnCount[mapData.pokemons[key]['form']] === 0 || !pkmnCount[mapData.pokemons[key]['form']])) {
                    pkmnCount[mapData.pokemons[key]['form']] = {
                        'ID': mapData.pokemons[key]['pokemon_id'],
                        'Form': mapData.pokemons[key]['form'],
                        'Count': 1,
                        'Name': i8ln(mapData.pokemons[key]['pokemon_name'])
                    }
                } else if (mapData.pokemons[key]['form'] > 0) {
                    pkmnCount[mapData.pokemons[key]['form']].Count += 1
                }
            }
        })

        var pokeCounts = []

        for (i = 0; i < pkmnCount.length; i++) {
            if (pkmnCount[i] && pkmnCount[i].Count > 0) {
                var pkmnPercentage = (pkmnCount[i].Count * 100 / pkmnTotal * 10) / 10
                pokeCounts.push([
                    '<img style="height:30px;" src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', pkmnCount[i].ID, 0, pkmnCount[i].Form) + '"/>',
                    '<a href=\'https://pokemon.gameinfo.io/' + languageSite + '/pokemon/' + pkmnCount[i].ID + '\' target=\'_blank\' title=\'' + i8ln('View in Pokédex') + '\' style=\'color: black;\'>' + pkmnCount[i].Name + '</a>',
                    pkmnCount[i].Count,
                    pkmnPercentage.toFixed(2) + '%'
                ])
            }
        }
        $('#pokemonList_table').dataTable().show()
        pokeStatTable.clear().rows.add(pokeCounts).draw()
    } else {
        pokeStatTable.clear().draw()
        $('#pokeStatStatus').html('<center>' + i8ln('Pokémon markers are disabled') + '<center>')
        $('#pokemonList_table').dataTable().hide()
    }


    if (Store.get('showGyms') || Store.get('showRaids')) {
        $.each(mapData.gyms, function (key, value) {
            var thisGymLocation = {lat: mapData.gyms[key]['latitude'], lng: mapData.gyms[key]['longitude']}
            thisGymIsVisible = currentVisibleMap.contains(thisGymLocation)
            if (thisGymIsVisible) {
                arenaTotal++
                if (arenaCount[mapData.gyms[key]['team_id']] === 0 || !arenaCount[mapData.gyms[key]['team_id']]) {
                    arenaCount[mapData.gyms[key]['team_id']] = 1
                } else {
                    arenaCount[mapData.gyms[key]['team_id']] += 1
                }
            }
        })

        var arenaListString = '<table><th>' + i8ln('Icon') + '</th><th>' + i8ln('Team') + '</th><th>' + i8ln('Count') + '</th><th>%</th><tr><td></td><td>' + i8ln('Total') + '</td><td>' + arenaTotal + '</td></tr>'
        for (i = 0; i < arenaCount.length; i++) {
            if (arenaCount[i] > 0) {
                if (i === 1) {
                    arenaListString += '<tr><td><img src="' + getIcon(iconpath.gym, 'gym', '.png', '1') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Mystic') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    arenaListString += '<tr><td><img src="' + getIcon(iconpath.gym, 'gym', '.png', '2') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Valor') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    arenaListString += '<tr><td><img src="' + getIcon(iconpath.gym, 'gym', '.png', '3') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Instinct') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else {
                    arenaListString += '<tr><td><img src="' + getIcon(iconpath.gym, 'gym', '.png', '0') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Uncontested') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        arenaListString += '</table>'
        $('#arenaList').html(arenaListString)
    } else {
        $('#arenaList').html('<center>' + i8ln('Gym markers are disabled') + '</center>')
    }


    if (Store.get('showRaids')) {
        $.each(mapData.gyms, function (key, value) {
            var thisRaidLocation = {lat: mapData.gyms[key]['latitude'], lng: mapData.gyms[key]['longitude']}
            thisRaidIsVisible = currentVisibleMap.contains(thisRaidLocation)
            if (thisRaidIsVisible) {
                if (mapData.gyms[key]['raid_end'] && mapData.gyms[key]['raid_end'] > Date.now()) {
                    if (mapData.gyms[key]['raid_level'] === '8') {
                        if (raidCount[8] === 0 || !raidCount[8]) {
                            raidCount[8] = 1
                        } else {
                            raidCount[8] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '7') {
                        if (raidCount[7] === 0 || !raidCount[7]) {
                            raidCount[7] = 1
                        } else {
                            raidCount[7] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '6') {
                        if (raidCount[6] === 0 || !raidCount[6]) {
                            raidCount[6] = 1
                        } else {
                            raidCount[6] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '5') {
                        if (raidCount[5] === 0 || !raidCount[5]) {
                            raidCount[5] = 1
                        } else {
                            raidCount[5] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '4') {
                        if (raidCount[4] === 0 || !raidCount[4]) {
                            raidCount[4] = 1
                        } else {
                            raidCount[4] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '3') {
                        if (raidCount[3] === 0 || !raidCount[3]) {
                            raidCount[3] = 1
                        } else {
                            raidCount[3] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '2') {
                        if (raidCount[2] === 0 || !raidCount[2]) {
                            raidCount[2] = 1
                        } else {
                            raidCount[2] += 1
                        }
                    }
                    if (mapData.gyms[key]['raid_level'] === '1') {
                        if (raidCount[1] === 0 || !raidCount[1]) {
                            raidCount[1] = 1
                        } else {
                            raidCount[1] += 1
                        }
                    }
                    raidTotal++
                }
            }
        })

        var raidListString = '<table><th>' + i8ln('Icon') + '</th><th>' + i8ln('Level') + '</th><th>' + i8ln('Count') + '</th><th>%</th><tr><td></td></tr>'
        for (i = 0; i < raidCount.length; i++) {
            if (raidCount[i] > 0) {
                if (i === 1) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '1') + '" style="height:48px;"/></td><td style="vertical-align:middle;">1</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '2') + '" style="height:48px;"/></td><td style="vertical-align:middle;">2</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '3') + '" style="height:48px;"/></td><td style="vertical-align:middle;">3</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 4) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '4') + '" style="height:48px;"/></td><td style="vertical-align:middle;">4</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 5) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '5') + '" style="height:48px;"/></td><td style="vertical-align:middle;">5</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 6) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '6') + '" style="height:48px;"/></td><td style="vertical-align:middle;">6</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 7) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '7') + '" style="height:48px;"/></td><td style="vertical-align:middle;">7</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 8) {
                    raidListString += '<tr><td><img src="' + getIcon(iconpath.raid, 'raid/egg', '.png', '8') + '" style="height:48px;"/></td><td style="vertical-align:middle;">8</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        raidListString += '</table>'
        $('#raidList').html(raidListString)
    } else {
        $('#raidList').html('<center>' + i8ln('Raid markers are disabled') + '</center>')
    }


    if (Store.get('showPokestops')) {
        $.each(mapData.pokestops, function (key, value) {
            var thisPokestopLocation = {lat: mapData.pokestops[key]['latitude'], lng: mapData.pokestops[key]['longitude']}
            thisPokestopIsVisible = currentVisibleMap.contains(thisPokestopLocation)
            var d = new Date()
            var lastMidnight = ''
            if (mapFork === 'mad') {
                lastMidnight = d.setHours(0, 0, 0, 0) / 1000
            } else {
                lastMidnight = 0
            }
            if (thisPokestopIsVisible) {
                if (mapData.pokestops[key]['incident_expiration'] && mapData.pokestops[key]['incident_expiration'] > Date.now()) {
                    if (pokestopCount[7] === 0 || !pokestopCount[7]) {
                        pokestopCount[7] = 1
                    } else {
                        pokestopCount[7] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now() && mapData.pokestops[key]['lure_id'] === 505) {
                    if (pokestopCount[6] === 0 || !pokestopCount[6]) {
                        pokestopCount[6] = 1
                    } else {
                        pokestopCount[6] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now() && mapData.pokestops[key]['lure_id'] === 504) {
                    if (pokestopCount[5] === 0 || !pokestopCount[5]) {
                        pokestopCount[5] = 1
                    } else {
                        pokestopCount[5] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now() && mapData.pokestops[key]['lure_id'] === 503) {
                    if (pokestopCount[4] === 0 || !pokestopCount[4]) {
                        pokestopCount[4] = 1
                    } else {
                        pokestopCount[4] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now() && mapData.pokestops[key]['lure_id'] === 502) {
                    if (pokestopCount[3] === 0 || !pokestopCount[3]) {
                        pokestopCount[3] = 1
                    } else {
                        pokestopCount[3] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now() && mapData.pokestops[key]['lure_id'] === 501) {
                    if (pokestopCount[2] === 0 || !pokestopCount[2]) {
                        pokestopCount[2] = 1
                    } else {
                        pokestopCount[2] += 1
                    }
                }
                if (lastMidnight < Number(mapData.pokestops[key]['quest_timestamp']) && mapData.pokestops[key]['quest_type'] && mapData.pokestops[key]['quest_type'] > 0) {
                    if (pokestopCount[1] === 0 || !pokestopCount[1]) {
                        pokestopCount[1] = 1
                    } else {
                        pokestopCount[1] += 1
                    }
                }
                pokestopTotal++
            }
        })

        var pokestopListString = '<table><th>' + i8ln('Icon') + '</th><th>' + i8ln('Status') + '</th><th>' + i8ln('Count') + '</th><th>%</th><tr><td></td><td>' + i8ln('Total') + '</td><td>' + pokestopTotal + '</td></tr>'
        for (i = 0; i < pokestopCount.length; i++) {
            if (pokestopCount[i] > 0) {
                if (i === 1) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '0', 0, 1) + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Quest') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '501') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Normal Lure') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '502') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Glacial Lure') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 4) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '503') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Mossy Lure') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 5) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '504') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Magnetic Lure') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 6) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '505') + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Rainy Lure') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 7) {
                    pokestopListString += '<tr><td><img src="' + getIcon(iconpath.pokestop, 'pokestop', '.png', '0', 1) + '" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Team Rocket') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        pokestopListString += '</table>'
        $('#pokestopList').html(pokestopListString)
    } else {
        $('#pokestopList').html('<center>' + i8ln('Pokéstop markers are disabled') + '<center>')
    }


    if (Store.get('showSpawnpoints')) {
        $.each(mapData.spawnpoints, function (key, value) {
            var thisSpawnpointLocation = {lat: mapData.spawnpoints[key]['latitude'], lng: mapData.spawnpoints[key]['longitude']}
            thisSpawnpointIsVisible = currentVisibleMap.contains(thisSpawnpointLocation)
            if (thisSpawnpointIsVisible) {
                if (mapData.spawnpoints[key]['time'] === 0) {
                    if (spawnpointCount[2] === 0 || !spawnpointCount[2]) {
                        spawnpointCount[2] = 1
                    } else {
                        spawnpointCount[2] += 1
                    }
                } else {
                    if (spawnpointCount[1] === 0 || !spawnpointCount[1]) {
                        spawnpointCount[1] = 1
                    } else {
                        spawnpointCount[1] += 1
                    }
                }
                spawnpointTotal++
            }
        })

        var spawnpointListString = '<table><th>' + i8ln('Icon') + '</th><th>' + i8ln('TTH') + '</th><th>' + i8ln('Count') + '</th><th>%</th><tr><td></td><td>' + i8ln('Total') + '</td><td>' + spawnpointTotal + '</td></tr>'
        for (i = 0; i < spawnpointCount.length; i++) {
            if (spawnpointCount[i] > 0) {
                if (i === 1) {
                    spawnpointListString += '<tr><td><img src="static/images/green.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Known') + '</td><td style="vertical-align:middle;">' + spawnpointCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(spawnpointCount[i] * 100 / spawnpointTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    spawnpointListString += '<tr><td><img src="static/images/red.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Unknown') + '</td><td style="vertical-align:middle;">' + spawnpointCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(spawnpointCount[i] * 100 / spawnpointTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        spawnpointListString += '</table>'
        $('#spawnpointList').html(spawnpointListString)
    } else {
        $('#spawnpointList').html('<center>' + i8ln('Spawnpoint markers are disabled') + '</center>')
    }
    $('#loadingSpinner').hide()
}

function processOverviewStats(i, item) { // eslint-disable-line no-unused-vars
    $('h4.pokemon-count').html(item['pokemon_count'])
    $('h4.gym-count').html(item['gym_count'])
    $('h4.raid-count').html(item['raid_count'])
    $('h4.pokestop-count').html(item['pokestop_count'])
}

function processTeamStats(i, item) { // eslint-disable-line no-unused-vars
    $('h4.neutral-count').html(item['neutral_count'])
    $('h4.mystic-count').html(item['mystic_count'])
    $('h4.valor-count').html(item['valor_count'])
    $('h4.instinct-count').html(item['instinct_count'])
}

function processPokestopStats(i, item) { // eslint-disable-line no-unused-vars
    $('h4.quest-count').html(item['quest'])
    $('h4.rocket-count').html(item['rocket'])
    $('h4.normal-lure-count').html(item['normal_lure'])
    $('h4.glacial-lure-count').html(item['glacial_lure'])
    $('h4.mossy-lure-count').html(item['mossy_lure'])
    $('h4.magnetic-lure-count').html(item['magnetic_lure'])
    $('h4.rainy-lure-count').html(item['rainy_lure'])
}

function processSpawnpointStats(i, item) { // eslint-disable-line no-unused-vars
    $('h4.spawnpoint-total').html(item['total'])
    $('h4.spawnpoint-found').html(item['found'])
    $('h4.spawnpoint-missing').html(item['missing'])
}

function processPokemonStats(i, item) { // eslint-disable-line no-unused-vars
    var id = item['pokemon_id']
    var pokemon = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', id, 0, item['form'], item['costume']) + '" style="width:40px;"><span style="display:none">' + item['name'] + '</span>'

    var types = item['pokemon_types']
    var typeDisplay = ''

    $.each(types, function (index, type) {
        typeDisplay += '<nobr>' + i8ln(type['type']) + ' <img src="' + getIcon(iconpath.type, 'type', '.png', getKeyByValue(pokemonTypes, type.type)) + '" style="width:18px;"></nobr>'
        if (index === 0) {
            typeDisplay += '<br>'
        }
    })

    pokemonTable.row.add([
        item['pokemon_id'],
        pokemon,
        typeDisplay,
        item['count'],
        item['percentage']
    ])
}

function processRewardStats(i, item) { // eslint-disable-line no-unused-vars
    var reward = ''
    var type = ''
    var hiddenName = '<span style="display: none;">' + item['name'] + '</span>'

    switch (item['quest_reward_type']) {
        case 2:
            reward = '<img src="' + getIcon(iconpath.reward, 'reward/item', '.png', item['quest_item_id'], item['quest_reward_amount']) + '" style="width:40px;">' +
            hiddenName
            type = i8ln('Item')
            break
        case 3:
            reward = '<img src="' + getIcon(iconpath.reward, 'reward/stardust', '.png', item['quest_reward_amount']) + '" style="width:40px;">' +
            hiddenName
            type = i8ln('Stardust')
            break
        case 4:
            reward = '<img src="' + getIcon(iconpath.reward, 'reward/candy', '.png', item['quest_candy_pokemon_id']) + '" style="width:40px;">' +
            hiddenName
            type = i8ln('Candy')
            break
        case 7:
            reward = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', item['quest_pokemon_id'], 0, item['quest_pokemon_form'], item['quest_pokemon_costume']) + '" style="width:40px;">' +
            hiddenName
            type = i8ln('Pokémon')
            break
        case 12:
            reward = '<img src="' + getIcon(iconpath.reward, 'reward/mega_resource', '.png', item['quest_energy_pokemon_id'], item['quest_reward_amount']) + '" style="width:40px;">' +
            hiddenName
            type = i8ln('Mega Energy')
            break
    }

    rewardTable.row.add([
        type,
        reward,
        item['count'],
        item['percentage']
    ])
}

function processShinyStats(i, item) { // eslint-disable-line no-unused-vars
    var hiddenName = '<span style="display: none;">' + item['name'] + '</span>'
    var pokemon = '<img src="' + getIcon(iconpath.pokemon, 'pokemon', '.png', item['pokemon_id'], 0, item['form'], item['costume'], 0, 1) + '" style="width:40px;">' +
    hiddenName
    var rate = item['rate'] + '<br>(' + item['percentage'] + ')'

    shinyTable.row.add([
        pokemon,
        item['shiny_count'],
        rate,
        item['sample_size']
    ])
}
