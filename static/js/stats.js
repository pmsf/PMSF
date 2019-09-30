function countMarkers(map) { // eslint-disable-line no-unused-vars
    document.getElementById('stats-ldg-label').innerHTML = ''
    document.getElementById('stats-pkmn-label').innerHTML = i8ln('Pokémon')
    document.getElementById('stats-gym-label').innerHTML = i8ln('Gyms')
    document.getElementById('stats-pkstop-label').innerHTML = i8ln('Pokéstops')
    document.getElementById('stats-raid-label').innerHTML = i8ln('Raids')

    var i = 0
    var arenaCount = []
    var arenaTotal = 0
    var raidCount = []
    var raidTotal = 0
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

    if (Store.get('showPokemon')) {
        $.each(mapData.pokemons, function (key, value) {
            var thisPokeLocation = {lat: mapData.pokemons[key]['latitude'], lng: mapData.pokemons[key]['longitude']}
            thisPokeIsVisible = currentVisibleMap.contains(thisPokeLocation)
            if (thisPokeIsVisible) {
                pkmnTotal++
                if ((mapData.pokemons[key]['form'] === '0') && (pkmnCount[mapData.pokemons[key]['pokemon_id']] === 0 || !pkmnCount[mapData.pokemons[key]['pokemon_id']])) {
                    pkmnCount[mapData.pokemons[key]['pokemon_id']] = {
                        'ID': mapData.pokemons[key]['pokemon_id'],
                        'Form': mapData.pokemons[key]['form'],
                        'Count': 1,
                        'Name': i8ln(mapData.pokemons[key]['pokemon_name'])
                    }
                } else if (mapData.pokemons[key]['form'] === '0') {
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
                var pokemonIdStr = ''
                if (pkmnCount[i].ID <= 9) {
                    pokemonIdStr = '00' + pkmnCount[i].ID
                } else if (pkmnCount[i].ID <= 99) {
                    pokemonIdStr = '0' + pkmnCount[i].ID
                } else {
                    pokemonIdStr = pkmnCount[i].ID
                }
                var formStr = ''
                if (pkmnCount[i].Form === '0') {
                    formStr = '00'
                } else {
                    formStr = pkmnCount[i].Form
                }
                var pkmnPercentage = (pkmnCount[i].Count * 100 / pkmnTotal * 10) / 10
                pokeCounts.push([
                    '<img style="height:30px;" src="' + iconpath + 'pokemon_icon_' + pokemonIdStr + '_' + formStr + '.png"/>',
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
        document.getElementById('pokeStatStatus').innerHTML = '<center>' + i8ln('Pokémon markers are disabled') + '<center>'
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
                    arenaListString += '<tr><td><img src="static/forts/Mystic.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Mystic') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    arenaListString += '<tr><td><img src="static/forts/Valor.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Valor') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    arenaListString += '<tr><td><img src="static/forts/Instinct.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Instinct') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                } else {
                    arenaListString += '<tr><td><img src="static/forts/Uncontested.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Uncontested') + '</td><td style="vertical-align:middle;">' + arenaCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(arenaCount[i] * 100 / arenaTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        arenaListString += '</table>'
        document.getElementById('arenaList').innerHTML = arenaListString
    } else {
        document.getElementById('arenaList').innerHTML = '<center>' + i8ln('Gym markers are disabled') + '</center>'
    }


    if (Store.get('showRaids')) {
        $.each(mapData.gyms, function (key, value) {
            var thisRaidLocation = {lat: mapData.gyms[key]['latitude'], lng: mapData.gyms[key]['longitude']}
            thisRaidIsVisible = currentVisibleMap.contains(thisRaidLocation)
            if (thisRaidIsVisible) {
                if (mapData.gyms[key]['raid_end'] && mapData.gyms[key]['raid_end'] > Date.now()) {
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
                    raidListString += '<tr><td><img src="static/raids/egg_normal.png" style="height:48px;"/></td><td style="vertical-align:middle;">1</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    raidListString += '<tr><td><img src="static/raids/egg_normal.png" style="height:48px;"/></td><td style="vertical-align:middle;">2</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    raidListString += '<tr><td><img src="static/raids/egg_rare.png" style="height:48px;"/></td><td style="vertical-align:middle;">3</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 4) {
                    raidListString += '<tr><td><img src="static/raids/egg_rare.png" style="height:48px;"/></td><td style="vertical-align:middle;">4</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 5) {
                    raidListString += '<tr><td><img src="static/raids/egg_legendary.png" style="height:48px;"/></td><td style="vertical-align:middle;">5</td><td style="vertical-align:middle;">' + raidCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(raidCount[i] * 100 / raidTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        raidListString += '</table>'
        document.getElementById('raidList').innerHTML = raidListString
    } else {
        document.getElementById('raidList').innerHTML = '<center>' + i8ln('Raid markers are disabled') + '</center>'
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
                    if (pokestopCount[3] === 0 || !pokestopCount[3]) {
                        pokestopCount[3] = 1
                    } else {
                        pokestopCount[3] += 1
                    }
                }
                if (mapData.pokestops[key]['lure_expiration'] && mapData.pokestops[key]['lure_expiration'] > Date.now()) {
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
                if (pokestopCount[0] === 0 || !pokestopCount[0]) {
                    pokestopCount[0] = 1
                } else {
                    pokestopCount[0] += 1
                }
                pokestopTotal++
            }
        })

        var pokestopListString = '<table><th>' + i8ln('Icon') + '</th><th>' + i8ln('Status') + '</th><th>' + i8ln('Count') + '</th><th>%</th>'
        for (i = 0; i < pokestopCount.length; i++) {
            if (pokestopCount[i] > 0) {
                if (i === 0) {
                    pokestopListString += '<tr><td><img src="static/forts/Pstop.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Total') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td></tr>'
                } else if (i === 1) {
                    pokestopListString += '<tr><td><img src="static/forts/PstopQuest.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Quest') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 2) {
                    pokestopListString += '<tr><td><img src="static/forts/PstopLured_1.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Lured') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                } else if (i === 3) {
                    pokestopListString += '<tr><td><img src="static/forts/Pstop_rocket.png" style="height:48px;"/></td><td style="vertical-align:middle;">' + i8ln('Team Rocket') + '</td><td style="vertical-align:middle;">' + pokestopCount[i] + '</td><td style="vertical-align:middle;">' + Math.round(pokestopCount[i] * 100 / pokestopTotal * 10) / 10 + '%</td></tr>'
                }
            }
        }
        pokestopListString += '</table>'
        document.getElementById('pokestopList').innerHTML = pokestopListString
    } else {
        document.getElementById('pokestopList').innerHTML = '<center>' + i8ln('Pokéstop markers are disabled') + '<center>'
    }
}
