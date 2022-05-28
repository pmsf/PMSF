<!-- Modals -->
<!-- Motd Modal -->
<?php if (! $noMotd) { ?>
    <div class="modal fade" id="motdModal" tabindex="-1" aria-labelledby="motdModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="motdModalLabel"><?php echo $motdTitle; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $motdContent; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- FullscreenModal-->
<div class="modal fade" id="fullscreenModal" tabindex="-1" aria-labelledby="fullscreenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullscreenModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img class="modal-content" id="fullscreenimg">
            </div>
        </div>
    </div>
</div>
<!-- InfoModal -->
<?php if (! $noInfoModal) { ?>
    <div class="modal fade" id="infoModal" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel"><?php echo $infoModalTitle; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php echo $infoModalContent; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- AccountModal -->
<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accountModalLabel"><?php echo i8ln('Profile') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <?php
            if (!empty($_SESSION['user']->id)) { ?>
                <div style="display:flex;">
                    <img src="<?php echo $_SESSION['user']->avatar; ?>" style="height:80px;width:80px;border-radius:50%;border:2px solid;position:relative;top:13px;">
                    <div style="position:relative;left:20px;font-size:13px;top:35px;">
                        <b><?php echo $_SESSION['user']->user; ?></b>
                    </div>
                </div>
                <div>
                    <a class="settings" style="position: absolute;left: 113px;top: 80px;color: red;cursor: pointer;border: 1px solid red;border-radius: 8px;" onclick="document.location.href='<?php echo 'logout?action=' . $_SESSION['user']->login_system . '-logout';?>'">
                        <i class="fas fa-sign-out-alt" aria-hidden="true"></i> <?php echo i8ln('Logout'); ?>
                    </a>
                </div>
                <?php
                if ($_SESSION['user']->login_system == 'native') {
                    ?>
                    <div>
                        <a style="position:relative;top:18px;left:96px;color:red;cursor:pointer;border:1px solid red;border-radius:8px;" class="settings" onclick="document.location.href='<?php echo 'register?action=password-update&username=' . $_SESSION['user']->user;?>'">
                            <i class="fas fa-lock" aria-hidden="true"></i> <?php echo i8ln('Change password'); ?>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo "<div class='d-grid gap-1'>";
                if ($noNativeLogin === false) {
                    echo "<button class='btn btn-primary' type='button' onclick=\"location.href='./login?action=login';\" value='Login'><i class='fas fa-user'></i> " . i8ln('Login with Email') . "</button>";
                }
                if ($noDiscordLogin === false) {
                    echo "<button class='btn btn-primary' type='button' onclick=\"location.href='./login?action=discord-login';\" value='Login with discord'><i class='fab fa-discord'></i> " . i8ln('Login with Discord') . "</button>";
                }
                if ($noFacebookLogin === false) {
                    echo "<button class='btn btn-primary' type='button' onclick=\"location.href='./login?action=facebook-login';\" value='Login with facebook'><i class='fab fa-facebook'></i> " . i8ln('Login with Facebook') . "</button>";
                }
                if ($noGroupmeLogin === false) {
                    echo "<button class='btn btn-primary' type='button' onclick=\"location.href='./login?action=groupme-login';\" value='Login with groupme'><i class='fas fa-smile'></i> " . i8ln('Login with Groupme') . "</button>";
                }
                if ($noPatreonLogin === false) {
                    echo "<button class='btn btn-primary' type='button' onclick=\"location.href='./login?action=patreon-login';\" value='Login with patreon'><i class='fab fa-patreon'></i> " . i8ln('Login with Patreon') . "</button>";
                }
                echo "</div>";
            } ?>

            <hr style="border: 1px solid #5a5a5aab;">

            <div class="d-grid gap-1 col-6 mx-auto">
                    <button class='btn btn-primary' type='button' onclick="confirm('<?php echo i8ln('Are you sure you want to reset settings to default values?') ?>') ? (localStorage.clear(), window.location.reload()) : false">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i> <?php echo i8ln('Reset Settings') ?>
                    </button>
                    <button class='btn btn-primary' type='button' onclick="download('<?= addslashes($title) ?>', JSON.stringify(JSON.stringify(localStorage)))">
                        <i class="fas fa-upload" aria-hidden="true"></i> <?php echo i8ln('Export Settings') ?>
                    </button>
                    <input id="fileInput" type="file" style="display:none;" onchange="openFile(event)"/>
                    <button class='btn btn-primary' type='button' onclick="document.getElementById('fileInput').click()">
                        <i class="fas fa-download" aria-hidden="true"></i> <?php echo i8ln('Import Settings') ?>
                    </button>
                <?php
                if (!$noLocaleSelection) {
                    ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-language" aria-hidden="true"></i> <?php echo i8ln('Select Language'); ?></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?lang=en"><span class="flag-icon flag-icon-<?php echo $enLocaleFlag; ?>"></span> <?php echo i8ln('English'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=de"><span class="flag-icon flag-icon-de"></span> <?php echo i8ln('German'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=fr"><span class="flag-icon flag-icon-fr"></span> <?php echo i8ln('French'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=it"><span class="flag-icon flag-icon-it"></span> <?php echo i8ln('Italian'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=pl"><span class="flag-icon flag-icon-pl"></span> <?php echo i8ln('Polish'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=sp"><span class="flag-icon flag-icon-es"></span> <?php echo i8ln('Spanish'); ?></a></li>
                            <div class="dropdown-divider"></div>
                            <li><a class="dropdown-item" href="?lang=sv"><span class="flag-icon flag-icon-se"></span> <?php echo i8ln('Swedish'); ?></a></li>
                            <!-- <li><a class="dropdown-item" href="?lang=zh-cn"><span class="flag-icon flag-icon-cn"></span> <?php echo i8ln('Chinese PRC'); ?></a></li>
                            <li><a class="dropdown-item" href="?lang=zh-hk"><span class="flag-icon flag-icon-hk"></span> <?php echo i8ln('Chinese HK'); ?></a></li>
                            <li><a class="dropdown-item" href="?lang=zh-tw"><span class="flag-icon flag-icon-tw"></span> <?php echo i8ln('Chinese Taiwan'); ?></a></li> -->
                        </ul>
                    </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Search Modal -->
<?php if (!$noSearch || (!$noSearchPokemon && !$noSearchPokestops && !$noSearchGyms && !$noSearchManualQuests && !$noSearchNests && !$noSearchPortals)) { ?>
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel"><?php echo i8ln('Search...'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <?php $firstTab = 1; ?>
                        <?php if (!$noPokemon && !$noSearchPokemon) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-pokemons-tab" data-bs-toggle="tab" data-bs-target="#nav-pokemons" type="button" role="tab" aria-controls="nav-pokemons" aria-selected="true"><img src="static/images/pokeball.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (!$noQuests && !$noSearchManualQuests) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-rewards-tab" data-bs-toggle="tab" data-bs-target="#nav-rewards" type="button" role="tab" aria-controls="nav-rewards" aria-selected="true"><img src="static/images/reward.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (!$noNests && !$noSearchNests) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-nests-tab" data-bs-toggle="tab" data-bs-target="#nav-nests" type="button" role="tab" aria-controls="nav-nests" aria-selected="true"><img src="static/images/nest.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (!$noGyms && !$noSearchGyms) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-gyms-tab" data-bs-toggle="tab" data-bs-target="#nav-gyms" type="button" role="tab" aria-controls="nav-gyms" aria-selected="true"><img src="static/sprites/gym/ingame/gym/0.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (!$noPokestops && !$noSearchPokestops) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-pokestops-tab" data-bs-toggle="tab" data-bs-target="#nav-pokestops" type="button" role="tab" aria-controls="nav-pokestops" aria-selected="true"><img src="static/sprites/pokestop/0.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (!$noPortals && !$noSearchPortals) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-portals-tab" data-bs-toggle="tab" data-bs-target="#nav-portals" type="button" role="tab" aria-controls="nav-portals" aria-selected="true"><img src="static/images/portal.png" width="30" height="30"/></button>
                        <?php } ?>
                    </div>
                </nav>
                <div class="modal-body">
                    <div class="tab-content" id="nav-tabContent">
                        <?php $firstTabContent = 1; ?>
                        <?php if (!$noPokemon && !$noSearchPokemon) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-pokemons" role="tabpanel" aria-labelledby="nav-pokemons-tab">
                                <input type="search" id="pokemon-search" oninput="searchAjax($(this))" name="pokemon-search"
                                       placeholder="<?php echo i8ln('Enter Pokémon or Type'); ?>"
                                       data-type="pokemon" class="search-input"/>
                                <ul id="pokemon-search-results" class="search-results pokemon-results"></ul>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (!$noQuests && !$noSearchManualQuests) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-rewards" role="tabpanel" aria-labelledby="nav-rewards-tab">
                                <input type="search" id="reward-search" oninput="searchAjax($(this))" name="reward-search"
                                       placeholder="<?php echo i8ln('Enter Reward Name'); ?>"
                                       data-type="reward" class="search-input"/>
                                <ul id="reward-search-results" class="search-results reward-results"></ul>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (!$noNests && !$noSearchNests) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-nests" role="tabpanel" aria-labelledby="nav-nests-tab">
                                <input type="search" id="nest-search" oninput="searchAjax($(this))" name="nest-search"
                                       placeholder="<?php echo i8ln('Enter nest Pokémon or Type'); ?>"
                                       data-type="nests" class="search-input"/>
                                <ul id="nest-search-results" class="search-results nest-results"></ul>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (!$noGyms && !$noSearchGyms) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-gyms" role="tabpanel" aria-labelledby="nav-gyms-tab">
                                <input type="search" id="gym-search" oninput="searchAjax($(this))" name="gym-search"
                                       placeholder="<?php echo i8ln('Enter Gym Name'); ?>"
                                       data-type="forts" class="search-input"/>
                                <ul id="gym-search-results" class="search-results gym-results"></ul>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (!$noPokestops && !$noSearchPokestops) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-pokestops" role="tabpanel" aria-labelledby="nav-pokestops-tab">
                                <input type="search" id="pokestop-search" oninput="searchAjax($(this))" name="pokestop-search"
                                       placeholder="<?php echo i8ln('Enter Pokéstop Name'); ?>" data-type="pokestops"
                                       class="search-input"/>
                                <ul id="pokestop-search-results" class="search-results pokestop-results"></ul>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (!$noPortals && !$noSearchPortals) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-portals" role="tabpanel" aria-labelledby="nav-portals-tab">
                                <input type="search" id="portals-search" oninput="searchAjax($(this))" name="portals-search"
                                       placeholder="<?php echo i8ln('Enter Portal Name'); ?>" data-type="portals"
                                       class="search-input"/>
                                <ul id="portals-search-results" class="search-results portals-results"></ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- SubmitModal -->
<?php if (!$noSubmit || (!$noManualPokemon && !$noManualPokestops && !$noManualGyms && !$noManualQuests && !$noManualNests && !$noAddNewCommunity && !$noAddPoi)) { ?>
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitModalLabel"><?php echo i8ln('Submit Data to Map') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" value="" name="submitLatitude" class="submitLatitude"/>
                <input type="hidden" value="" name="submitLongitude" class="submitLongitude"/>
                <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <?php $firstTab = 1; ?>
                        <?php if (! $noManualPokemon && !$noPokemon) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-pokemon-tab" data-bs-toggle="tab" data-bs-target="#nav-pokemon" type="button" role="tab" aria-controls="nav-pokemon" aria-selected="true"><img src="static/images/pokeball.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (! $noManualGyms && !$noGyms) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-gym-tab" data-bs-toggle="tab" data-bs-target="#nav-gym" type="button" role="tab" aria-controls="nav-gym" aria-selected="true"><img src="static/sprites/gym/ingame/gym/0.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (! $noManualPokestops && !$noPokestops) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-pokestop-tab" data-bs-toggle="tab" data-bs-target="#nav-pokestop" type="button" role="tab" aria-controls="nav-pokestop" aria-selected="true"><img src="static/sprites/pokestop/0.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (! $noAddNewNests && !$noNests) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-nest-tab" data-bs-toggle="tab" data-bs-target="#nav-nest" type="button" role="tab" aria-controls="nav-nest" aria-selected="true"><img src="static/images/nest.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (! $noAddNewCommunity && !$noCommunity) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-community-tab" data-bs-toggle="tab" data-bs-target="#nav-community" type="button" role="tab" aria-controls="nav-community" aria-selected="true"><img src="static/images/community.png" width="30" height="30"/></button>
                        <?php
                        $firstTab++;
                        }
                        if (! $noAddPoi && !$noPoi) { ?>
                            <button class="nav-link<?php echo (($firstTab == 1) ? " active" : ""); ?>" id="nav-poi-tab" data-bs-toggle="tab" data-bs-target="#nav-poi" type="button" role="tab" aria-controls="nav-poi" aria-selected="true"><img src="static/images/playground.png" width="30" height="30"/></button>
                        <?php } ?>
                     </div>
                </nav>
                <div class="modal-body">
                    <div class="tab-content" id="nav-tabContent">
                        <?php $firstTabContent = 1; ?>
                        <?php if (! $noManualPokemon && !$noPokemon) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-pokemon" role="tabpanel" aria-labelledby="nav-pokemon-tab">
                                <input type="hidden" name="pokemonID" class="pokemonID"/>
                                <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, 'pokemonSubmitFilter(event)', $pokemonToExclude, 6); ?>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="manualPokemonData(event);">
                                        <i class="fas fa-binoculars"></i> <?php echo i8ln('Submit Pokémon'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (! $noManualGyms && !$noGyms) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-gym" role="tabpanel" aria-labelledby="nav-gym-tab">
                                <input type="text" id="gym-name" name="gym-name"
                                       placeholder="<?php echo i8ln('Enter Gym Name'); ?>" data-type="forts"
                                       class="search-input">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="manualGymData(event);"><i
                                            class="fas fa-binoculars"></i> <?php echo i8ln('Submit Gym'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (! $noManualPokestops && !$noPokestops) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-pokestop" role="tabpanel" aria-labelledby="nav-pokestop-tab">
                                <input type="text" id="pokestop-name" name="pokestop-name"
                                       placeholder="<?php echo i8ln('Enter Pokéstop Name'); ?>" data-type="pokestop"
                                       class="search-input">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="manualPokestopData(event);"><i
                                            class="fas fa-binoculars"></i> <?php echo i8ln('Submit Pokéstop'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (! $noAddNewNests && !$noNests) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-nest" role="tabpanel" aria-labelledby="nav-nest-tab">
                                <input type="hidden" name="pokemonID" class="pokemonID"/>
                                <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 7); ?>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="submitNewNest(event);"><i
                                            class="fas fa-binoculars"></i> <?php echo i8ln('Submit Nest'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (! $noAddNewCommunity && !$noCommunity) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-community" role="tabpanel" aria-labelledby="nav-community-tab">
                                <input type="text" name="community-name" class="community-name"
                                       placeholder="<?php echo i8ln('Enter Community Name'); ?>" data-type="name"
                                       class="search-input">
                                <input type="text" name="community-description" class="community-description"
                                       placeholder="<?php echo i8ln('Enter description'); ?>" data-type="description"
                                       class="search-input">
                                <input type="text" name="community-invite" class="community-invite"
                                       placeholder="<?php echo i8ln('Whatsapp, Telegram, Discord Link'); ?>" data-type="invite-link"
                                       class="search-input">
                                <h6><center><?php echo i8ln('Link must be valid and start with https://'); ?></center></h6>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" onclick="submitNewCommunity(event);">
                                        <i class="fas fa-comments"></i> <?php echo i8ln('Submit Community'); ?>
                                    </button>
                                </div>
                            </div>
                        <?php
                        $firstTabContent++;
                        }
                        if (! $noAddPoi && !$noPoi) { ?>
                            <div class="tab-pane fade<?php echo (($firstTabContent == 1) ? " show active" : ""); ?>" id="nav-poi" role="tabpanel" aria-labelledby="nav-poi-tab">
                                <input type="text" name="poi-name" class="poi-name"placeholder="<?php echo i8ln('Enter candidate Name'); ?>" data-type="name" class="search-input">
                                <input type="text" name="poi-description" class="poi-description" placeholder="<?php echo i8ln('Enter candidate description'); ?>" data-type="description" class="search-input">
                                <input type="text" name="poi-notes" class="poi-notes" placeholder="<?php echo i8ln('Enter field notes'); ?>" data-type="description" class="search-input">
                                <?php if (! empty($imgurCID)) { ?>
                                    <div class="d-grid gap-1">
                                        <label class="btn btn-primary" type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload POI Image') ?>
                                            <input type="file" id="poi-image" name="poi-image" accept="image/*" class="poi-image" data-type="poi-image" class="search-input" onchange='previewPoiImage(event)' hidden>
                                        </label>
                                        <img class="rounded mx-auto d-block" id='preview-poi-image' name='preview-poi-image' width="50px" height="auto">
                                        <label class="btn btn-primary" type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload Surrounding Image') ?>
                                            <input type="file" id="poi-surrounding" name="poi-surrounding" accept="image/*" class="poi-surrounding" data-type="poi-surrounding" class="search-input" onchange='previewPoiSurrounding(event)' hidden>
                                        </label>
                                        <img class="rounded mx-auto d-block" id='preview-poi-surrounding' name='preview-poi-surrounding' width="50px" height="auto" >
                                    </div>
                                <?php } ?>
                                <div class="modal-footer">
                                    <h6><center><?php echo i8ln('If you submit a POI candidate you agree that your discord username will be shown in the marker label'); ?></center></h6>
                                    <button type="button" class="btn btn-primary" onclick="submitPoi(event);"><i class="fas fa-comments"></i> <?php echo i8ln('Submit POI candidate'); ?></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Edit Community Modal -->
<?php if (!$noCommunity && !$noEditCommunity) { ?>
    <div class="modal fade" id="editCommunityModal" tabindex="-1" aria-labelledby="editCommunityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCommunityModalLabel"><?php echo i8ln('Edit Community') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editcommunityid" name="editcommunityid" value=""/>
                    <input type="text" id="community-name" name="community-name" placeholder="<?php echo i8ln('Enter New community Name'); ?>" data-type="community-name" class="search-input">
                    <input type="text" id="community-description" name="community-description" placeholder="<?php echo i8ln('Enter New community Description'); ?>" data-type="community-description" class="search-input">
                    <input type="text" id="community-invite" name="community-invite" placeholder="<?php echo i8ln('Enter New community Invite link'); ?>" data-type="community-invite" class="search-input">
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="editCommunityData(event);" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo i8ln('Save Changes'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Edit Poi Modal -->
<?php if (!$noPoi && !$noEditPoi) { ?>
    <div class="modal fade" id="editPoiModal" tabindex="-1" aria-labelledby="editPoiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPoiModalLabel"><?php echo i8ln('Edit POI') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editpoiid" name="editpoiid" value=""/>
                    <input type="text" id="poi-name" name="poi-name" placeholder="<?php echo i8ln('Enter New POI Name'); ?>" data-type="poi-name" class="search-input">
                    <input type="text" id="poi-description" name="poi-description" placeholder="<?php echo i8ln('Enter New POI Description'); ?>" data-type="poi-description" class="search-input">
                    <input type="text" id="poi-notes" name="poi-notes" placeholder="<?php echo i8ln('Enter New POI Notes'); ?>" data-type="poi-notes" class="search-input">
                        <?php if (! empty($imgurCID)) {
                        ?>
                            <div class="d-grid gap-1">
                                <label class="btn btn-primary" type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload POI Image') ?>
                                    <input type="file" id="poi-image" name="poi-image" accept="image/*" class="poi-image" data-type="poi-image" class="search-input" onchange='previewPoiImage(event)' hidden>
                                </label>
                                <img class="rounded mx-auto d-block" id='edit-preview-poi-image' name="preview-poi-image" width="50px" height="auto">
                                <label class="btn btn-primary" type="button"><i class="fas fa-upload"></i> <?php echo i8ln('Upload Surrounding Image') ?>
                                    <input type="file" id="poi-surrounding" name="poi-surrounding" accept="image/*" class="poi-surrounding" data-type="poi-surrounding" class="search-input" onchange='previewPoiSurrounding(event)' hidden>
                                </label>
                                <img class="rounded mx-auto d-block" id='edit-preview-poi-surrounding' name="preview-poi-surrounding" width="50px" height="auto" >
                            </div>
                        <?php
                    } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="editPoiData(event);" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo i8ln('Save Changes'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Mark POI Modal -->
<?php if (!$noPoi && !$noMarkPoi) { ?>
    <div class="modal fade" id="markPoiModal" tabindex="-1" aria-labelledby="markPoiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="markPoiModalLabel"><?php echo i8ln('Mark POI'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="markpoiid" name="markpoiid" value=""/>
                        <button type="button" class="btn btn-primary btn-sm" id="2" onclick="markPoi(event, this.id);"><i class="fas fa-sync-alt"></i> <?php echo i8ln('Submitted'); ?></button>
                        <button type="button" class="btn btn-primary btn-sm" id="3" onclick="markPoi(event, this.id);"><i class="fas fa-times"></i> <?php echo i8ln('Declined'); ?></button>
                        <button type="button" class="btn btn-primary btn-sm" id="4" onclick="markPoi(event, this.id);"><i class="fas fa-times"></i> <?php echo i8ln('Resubmitted'); ?></button>
                        <button type="button" class="btn btn-primary btn-sm" id="5" onclick="markPoi(event, this.id);"><i class="fas fa-times"></i> <?php echo i8ln('Not a candidate'); ?></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Edit Nest Modal -->
<?php if (!$noNests && !$noManualNests) { ?>
    <div class="modal fade" id="editNestModal" tabindex="-1" aria-labelledby="editNestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNestModalLabel"><?php echo i8ln('Edit Nest'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editnestid" name="editnestid" value=""/>
                    <input type="hidden" name="pokemonID" class="pokemonID"/>
                    <?php pokemonFilterImages($noPokemonNames, $noPokemonNumbers, 'pokemonSubmitFilter(event)', $excludeNestMons, 5); ?>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="manualNestData(event);" class="btn btn-primary">
                        <i class="fas fa-binoculars"></i> <?php echo i8ln('Edit Nest'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Convert Portal Modal -->
<?php if (! $noPortals && !$noConvertPortal) { ?>
    <div class="modal fade" id="convertPortalModal" tabindex="-1" aria-labelledby="convertPortalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="convertPortalModalLabel"><?php echo i8ln('Convert to Pokestop/Gym'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="convertportalid" name="convertportalid" value=""/>
                        <button type="button" class="btn btn-primary btn-sm" id="1" onclick="convertPortalData(event, this.id);"><i class="fas fa-sync-alt"></i> <?php echo i8ln('Convert to Pokéstop'); ?></button>
                        <button type="button" class="btn btn-primary btn-sm" id="2" onclick="convertPortalData(event, this.id);"><i class="fas fa-sync-alt"></i> <?php echo i8ln('Convert to Gym'); ?></button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="markPortalData(event);"><i class="fas fa-times"></i> <?php echo i8ln('No Pokéstop or Gym'); ?></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Convert Pokestop Modal -->
<?php if (! $noPokestops && ! $noConvertPokestops) { ?>
    <div class="modal fade" id="convertPokestopModal" tabindex="-1" aria-labelledby="convertPokestopModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="convertPokestopModalLabel"><?php echo i8ln('Convert pokestop to Gym'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="convertpokestopid" name="convertpokestopid" value=""/>
                        <button type="button" class="btn btn-primary btn-sm" onclick="convertPokestopData(event);"><i class="fas fa-edit"></i> <?php echo i8ln('Convert to gym'); ?></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Rename Pokestop Modal -->
<?php if (! $noPokestops && ! $noRenamePokestops) { ?>
    <div class="modal fade" id="renamePokestopModal" tabindex="-1" aria-labelledby="renamePokestopModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renamePokestopModalLabel"><?php echo i8ln('Rename Pokéstop'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="renamepokestopid" name="renamepokestopid" value=""/>
                        <input type="text" id="pokestop-rename" name="pokestop-rename"
                            placeholder="<?php echo i8ln('Enter New Pokéstop Name'); ?>" data-type="pokestop" class="search-input">

                        <button type="button" class="btn btn-primary btn-sm" onclick="renamePokestopData(event);"><i class="fas fa-edit"></i> <?php echo i8ln('Rename Pokéstop'); ?></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Rename Gym Modal -->
<?php if (! $noGyms && ! $noRenameGyms) { ?>
    <div class="modal fade" id="renameGymModal" tabindex="-1" aria-labelledby="renameGymModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameGymModalLabel"><?php echo i8ln('Rename Gym'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="renamegymid" name="renamegymid" value=""/>
                        <input type="text" id="gym-rename" name="gym-rename"
                            placeholder="<?php echo i8ln('Enter New Gym Name'); ?>" data-type="gym" class="search-input">
                        <button type="button" class="btn btn-primary btn-sm" onclick="renameGymData(event);"><i class="fas fa-edit"></i> <?php echo i8ln('Rename Gym'); ?></button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Manual Quest Modal -->
<?php if (! $noManualQuests) { ?>
    <div class="modal fade" id="manualQuestModal" tabindex="-1" aria-labelledby="manualQuestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualQuestModalLabel"><?php echo i8ln('Submit a Quest'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body quest-modal">
                    <div class="d-grid">
                        <input type="hidden" id="questpokestopid" name="questpokestopid" value=""/>
                        <?php
                            $json   = file_get_contents('static/dist/data/questtype.min.json');
                            $questtypes  = json_decode($json, true);

                            $json    = file_get_contents('static/dist/data/rewardtype.min.json');
                            $rewardtypes   = json_decode($json, true);

                            $json    = file_get_contents('static/dist/data/conditiontype.min.json');
                            $conditiontypes   = json_decode($json, true);

                            $json    = file_get_contents('static/dist/data/pokemon.min.json');
                            $encounters = json_decode($json, true);

                            $json    = file_get_contents('static/dist/data/items.min.json');
                            $items = json_decode($json, true);
                        ?>
                        <label for="questTypeList"><?php echo i8ln('Quest'); ?>
                            <select id="questTypeList" name="questTypeList" class="form-select" aria-label="Quest type select">
                                <option selected><?php echo i8ln('Select Quest type'); ?></option>
                                <?php
                                foreach ($questtypes as $key => $value) {
                                    if (! in_array($key, $hideQuestTypes)) {
                                        ?>
                                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="collapse" id="questAmountList">
                                <select id="questAmountSelect" name="questAmountSelect" class="form-select" aria-label="Quest amount list">
                                    <option selected><?php echo i8ln('Select target amount'); ?></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                        </label>
                        <label for="conditionTypeList"><?php echo i8ln('Conditions'); ?>
                            <select id="conditionTypeList" name="conditionTypeList" class="form-select" aria-label="Condition list">
                                <option selected><?php echo i8ln('Select Condition'); ?></option>
                                <?php
                                foreach ($conditiontypes as $key => $value) {
                                    if (! in_array($key, $hideConditionTypes)) {
                                        ?>
                                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="collapse" id="pokeCatchList">
                                <select id="pokeCatch" name="pokeCatch" class="form-select" multiple aria-label="Pokemon">
                                    <option selected><?php echo i8ln('Select Pokemon'); ?></option>
                                    <?php
                                    foreach ($encounters as $key => $value) {
                                        if (in_array($key, $showEncounters)) {
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo i8ln($value['name']); ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="collapse" id="typeCatchList">
                                <select id="typeCatch" name="typeCatch" class="form-select" multiple aria-label="Type">
                                    <option selected><?php echo i8ln('Select type'); ?></option>
                                    <option value="1"><?php echo i8ln('Normal'); ?></option>
                                    <option value="2"><?php echo i8ln('Fighting'); ?></option>
                                    <option value="3"><?php echo i8ln('Flying'); ?></option>
                                    <option value="4"><?php echo i8ln('Poison'); ?></option>
                                    <option value="5"><?php echo i8ln('Ground'); ?></option>
                                    <option value="6"><?php echo i8ln('Rock'); ?></option>
                                    <option value="7"><?php echo i8ln('Bug'); ?></option>
                                    <option value="8"><?php echo i8ln('Ghost'); ?></option>
                                    <option value="9"><?php echo i8ln('Steel'); ?></option>
                                    <option value="10"><?php echo i8ln('Fire'); ?></option>
                                    <option value="11"><?php echo i8ln('Water'); ?></option>
                                    <option value="12"><?php echo i8ln('Grass'); ?></option>
                                    <option value="13"><?php echo i8ln('Electric'); ?></option>
                                    <option value="14"><?php echo i8ln('Psychic'); ?></option>
                                    <option value="15"><?php echo i8ln('Ice'); ?></option>
                                    <option value="16"><?php echo i8ln('Dragon'); ?></option>
                                    <option value="17"><?php echo i8ln('Dark'); ?></option>
                                    <option value="18"><?php echo i8ln('Fairy'); ?></option>
                                </select>
                            </div>
                            <div class="collapse" id="raidLevelList">
                                <select id="raidLevel" name="raidLevel" class="form-select" aria-label="Level Select">
                                    <option selected><?php echo i8ln('Select Level'); ?></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                </select>
                            </div>
                            <div class="collapse" id="throwTypeList">
                                <select id="throwType" name="throwType" class="form-select" aria-label="Throw type list">
                                <option selected><?php echo i8ln('Select throw type'); ?></option>
                                    <option value="10"><?php echo i8ln('Nice'); ?></option>
                                    <option value="11"><?php echo i8ln('Great'); ?></option>
                                    <option value="12"><?php echo i8ln('Excellent'); ?></option>
                                </select>
                            </div>
                            <div class="collapse" id="curveThrowList">
                                <select id="curveThrow" name="curveThrow" class="form-select" aria-label="Curve list">
                                    <option selected><?php echo i8ln('Select curve'); ?></option>
                                    <option value="0"><?php echo i8ln('Without curve throw'); ?></option>
                                    <option value="1"><?php echo i8ln('With curve throw'); ?></option>
                                </select>
                            </div>
                        </label>
                        <label for="rewardTypeList"><?php echo i8ln('Reward'); ?>
                            <select id="rewardTypeList" name="rewardTypeList" class="form-select" aria-label="Reward type list">
                                <option selected><?php echo i8ln('Select reward type'); ?></option>
                                <?php
                                foreach ($rewardtypes as $key => $value) {
                                    if (! in_array($key, $hideRewardTypes)) {
                                        ?>
                                        <option value="<?php echo $key; ?>"><?php echo i8ln($value['text']); ?></option>
                                    <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="collapse" id="pokeRewardList">
                                <select id="pokeReward" name="pokeReward" class="form-select" aria-label="Pokemon reward list">
                                    <option selected><?php echo i8ln('Select reward pokemon'); ?></option>
                                    <?php
                                    foreach ($encounters as $key => $value) {
                                        if (in_array($key, $showEncounters)) {
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo i8ln($value['name']); ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="collapse" id="itemRewardList">
                                <select id="itemReward" name="itemReward" class="form-select" aria-label="Item reward list">
                                    <option selected><?php echo i8ln('Select reward item'); ?></option>
                                    <?php
                                    foreach ($items as $key => $value) {
                                        if (in_array($key, $showItems)) {
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo i8ln($value['name']); ?></option>
                                        <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="collapse" id="itemAmountList">
                                <select id="itemAmount" name="itemAmount" class="form-select" aria-label="Item amount">
                                    <option selected><?php echo i8ln('Select item amount'); ?></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>
                            <div class="collapse" id="dustAmountList">
                                <select id="dustAmount" name="dustAmount" class="form-select" aria-label="Stardust amount">
                                    <option selected><?php echo i8ln('Select stardust amount'); ?></option>
                                    <option value="200">200</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="1500">1500</option>
                                    <option value="2000">2000</option>
                                </select>
                            </div>
                        </label>
                        <button type="button" class="btn btn-primary btn-sm" onclick="manualQuestData(event);" class="submitting-quest"><i class="fas fa-binoculars"></i> <?php echo i8ln('Submit Quest'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Manual Raid Modal -->
<?php if (! $noGyms && ! $noRaids && ! $noManualRaids) { ?>
    <div class="modal fade" id="manualRaidModal" tabindex="-1" aria-labelledby="manualRaidModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manualRaidModalLabel"><?php echo i8ln('Submit a Raid Report'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-grid">
                        <input type="hidden" id="manualraidgymid" name="manualraidgymid" value=""/>
                        <div class="switch-container">
                            <div class="pokemon-list raid-submission">
                                <input type="hidden" id="manualraidpokemonid" name="manualraidpokemonid" value="">
                                <span class="pokemon-icon-sprite" data-value="egg_1" data-label="Level 1" onclick="pokemonRaidFilter(event);"><span class="egg_1 inner-bg" style="background: url('static/sprites/raid/egg/1.png');background-size:100%"></span><span class="egg-number">1</span></span>
                                <!-- Not in game <span class="pokemon-icon-sprite" data-value="egg_2" data-label="Level 2" onclick="pokemonRaidFilter(event);"><span class="egg_2 inner-bg" style="background: url('static/sprites/raid/egg/2.png');background-size:100%"></span><span class="egg-number">2</span></span> -->
                                <span class="pokemon-icon-sprite" data-value="egg_3" data-label="Level 3" onclick="pokemonRaidFilter(event);"><span class="egg_3 inner-bg" style="background: url('static/sprites/raid/egg/3.png');background-size:100%"></span><span class="egg-number">3</span></span>
                                <!-- Not in game <span class="pokemon-icon-sprite" data-value="egg_4" data-label="Level 4" onclick="pokemonRaidFilter(event);"><span class="egg_4 inner-bg" style="background: url('static/sprites/raid/egg/4.png');background-size:100%"></span><span class="egg-number">4</span></span> -->
                                <span class="pokemon-icon-sprite" data-value="egg_5" data-label="Level 5" onclick="pokemonRaidFilter(event);"><span class="egg_5 inner-bg" style="background: url('static/sprites/raid/egg/5.png');background-size:100%"></span><span class="egg-number">5</span></span>
                                <span class="pokemon-icon-sprite" data-value="egg_6" data-label="Level 6" onclick="pokemonRaidFilter(event);"><span class="egg_6 inner-bg" style="background: url('static/sprites/raid/egg/6.png');background-size:100%"></span><span class="egg-number">6</span></span>
                                <span class="pokemon-icon-sprite" data-value="egg_7" data-label="Level 7" onclick="pokemonRaidFilter(event);"><span class="egg_7 inner-bg" style="background: url('static/sprites/raid/egg/7.png');background-size:100%"></span><span class="egg-number">7</span></span>
                                <span class="pokemon-icon-sprite" data-value="egg_8" data-label="Level 8" onclick="pokemonRaidFilter(event);"><span class="egg_8 inner-bg" style="background: url('static/sprites/raid/egg/8.png');background-size:100%"></span><span class="egg-number">8</span></span>
                                <?php
                                $pokemonJson = file_get_contents('static/dist/data/pokemon.min.json');
                                $pokemon = json_decode($pokemonJson, true);
                                foreach ($raidBosses as $raidBoss) {
                                    echo '<span class="pokemon-icon-sprite" data-value="' . $raidBoss . '" data-label="' . $pokemon[$raidBoss]['name'] . '" onclick="pokemonRaidFilter(event);"><img src="' . getIcon($iconFolderArray['pokemon'], 'pokemon/', '.png', $raidBoss) . '" style="width:48px;height:48px;"/></span>';
                                } ?>
                                <div class="mon-name" style="display:none;"></div>
                                <div class="switch-container timer-cont" style="text-align:center;display:none">
                                    <h5 class="timer-name" style="margin-bottom:0;"></h5>
                                    <select id="egg_time" name="egg_time" class="egg_time" style="display:none;">
                                        <option value="60" selected>60</option>
                                        <option value="59">59</option>
                                        <option value="58">58</option>
                                        <option value="57">57</option>
                                        <option value="56">56</option>
                                        <option value="55">55</option>
                                        <option value="54">54</option>
                                        <option value="53">53</option>
                                        <option value="52">52</option>
                                        <option value="51">51</option>
                                        <option value="50">50</option>
                                        <option value="49">49</option>
                                        <option value="48">48</option>
                                        <option value="47">47</option>
                                        <option value="46">46</option>
                                        <option value="45">45</option>
                                        <option value="44">44</option>
                                        <option value="43">43</option>
                                        <option value="42">42</option>
                                        <option value="41">41</option>
                                        <option value="40">40</option>
                                        <option value="39">39</option>
                                        <option value="38">38</option>
                                        <option value="37">37</option>
                                        <option value="36">36</option>
                                        <option value="35">35</option>
                                        <option value="34">34</option>
                                        <option value="33">33</option>
                                        <option value="32">32</option>
                                        <option value="31">31</option>
                                        <option value="30">30</option>
                                        <option value="29">29</option>
                                        <option value="28">28</option>
                                        <option value="27">27</option>
                                        <option value="26">26</option>
                                        <option value="25">25</option>
                                        <option value="24">24</option>
                                        <option value="23">23</option>
                                        <option value="22">22</option>
                                        <option value="21">21</option>
                                        <option value="20">20</option>
                                        <option value="19">19</option>
                                        <option value="18">18</option>
                                        <option value="17">17</option>
                                        <option value="16">16</option>
                                        <option value="15">15</option>
                                        <option value="14">14</option>
                                        <option value="13">13</option>
                                        <option value="12">12</option>
                                        <option value="11">11</option>
                                        <option value="10">10</option>
                                        <option value="9">9</option>
                                        <option value="8">8</option>
                                        <option value="7">7</option>
                                        <option value="6">6</option>
                                        <option value="5">5</option>
                                        <option value="4">4</option>
                                        <option value="3">3</option>
                                        <option value="2">2</option>
                                        <option value="1">1</option>
                                    </select>
                                    <select id="mon_time" name="mon_time" class="mon_time" style="display:none;">
                                        <option value="45" selected>45</option>
                                        <option value="44">44</option>
                                        <option value="43">43</option>
                                        <option value="42">42</option>
                                        <option value="41">41</option>
                                        <option value="40">40</option>
                                        <option value="39">39</option>
                                        <option value="38">38</option>
                                        <option value="37">37</option>
                                        <option value="36">36</option>
                                        <option value="35">35</option>
                                        <option value="34">34</option>
                                        <option value="33">33</option>
                                        <option value="32">32</option>
                                        <option value="31">31</option>
                                        <option value="30">30</option>
                                        <option value="29">29</option>
                                        <option value="28">28</option>
                                        <option value="27">27</option>
                                        <option value="26">26</option>
                                        <option value="25">25</option>
                                        <option value="24">24</option>
                                        <option value="23">23</option>
                                        <option value="22">22</option>
                                        <option value="21">21</option>
                                        <option value="20">20</option>
                                        <option value="19">19</option>
                                        <option value="18">18</option>
                                        <option value="17">17</option>
                                        <option value="16">16</option>
                                        <option value="15">15</option>
                                        <option value="14">14</option>
                                        <option value="13">13</option>
                                        <option value="12">12</option>
                                        <option value="11">11</option>
                                        <option value="10">10</option>
                                        <option value="9">9</option>
                                        <option value="8">8</option>
                                        <option value="7">7</option>
                                        <option value="6">6</option>
                                        <option value="5">5</option>
                                        <option value="4">4</option>
                                        <option value="3">3</option>
                                        <option value="2">2</option>
                                        <option value="1">1</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="manualRaidData(event);"><i class="fas fa-binoculars" style="margin-right:10px;"></i><?php echo i8ln('Submit Raid'); ?></button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo i8ln('Close') ?></button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Stats Modal -->
<?php if (! $noFullStats) { ?>
    <div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-overview-stats-tab" data-bs-toggle="tab" data-bs-target="#nav-overview-stats" type="button" role="tab" aria-controls="nav-overview-stats" aria-selected="true">
                            <i class="fas fa-tachometer-alt" style="color:black;font-size:28px;"></i>
                        </button>
                        <button class="nav-link" id="nav-pokemon-stats-tab" data-bs-toggle="tab" data-bs-target="#nav-pokemon-stats" type="button" role="tab" aria-controls="nav-pokemon-stats" aria-selected="true">
                            <img src="static/images/pokemon.png" style="filter: brightness(0.5);" width="30" height="30">
                        </button>
                        <button class="nav-link" id="nav-reward-stats-tab" data-bs-toggle="tab" data-bs-target="#nav-reward-stats" type="button" role="tab" aria-controls="nav-reward-stats" aria-selected="true">
                            <img src="static/images/reward.png" width="30" height="30">
                        </button>
                        <button class="nav-link" id="nav-shiny-stats-tab" data-bs-toggle="tab" data-bs-target="#nav-shiny-stats" type="button" role="tab" aria-controls="nav-shiny-stats" aria-selected="true">
                            <img src="static/images/stats/sparkles.png" width="30" height="30">
                        </button>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 0;">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-overview-stats" role="tabpanel" aria-labelledby="nav-overview-stats-tab">
                            <!-- Overview -->
                            <div class="card text-center p-0 m-4">
                                <div class="card-header text-light"><?php echo i8ln('Overview'); ?></div>
                                <div class="card-body bg-light">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/sprites/reward/item/1.png" width="64" height="64" />
                                                        <h4 class="pokemon-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Pokémon'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/neutral.png" width="64" height="64" />
                                                        <h4 class="gym-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Gyms'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/raid.png" width="64" height="64" />
                                                        <h4 class="raid-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Raids'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/pokestop.png" width="64" height="64" />
                                                        <h4 class="pokestop-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Pokéstops'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Teams -->
                            <div class="card text-center p-0 m-4">
                                <div class="card-header text-light"><?php echo i8ln('Teams'); ?></div>
                                <div class="card-body bg-light">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item neutral">
                                                        <img src="static/images/stats/neutral.png" width="64" height="64" />
                                                        <h4 class="neutral-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Neutral Gyms'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item mystic">
                                                        <img src="static/images/stats/mystic.png" width="64" height="64" />
                                                        <h4 class="mystic-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Mystic Gyms'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item valor">
                                                        <img src="static/images/stats/valor.png" width="64" height="64" />
                                                        <h4 class="valor-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Valor Gyms'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item instinct">
                                                        <img src="static/images/stats/instinct.png" width="64" height="64" />
                                                        <h4 class="instinct-count"><?php echo i8ln('loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Instinct Gyms'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Pokéstops -->
                            <div class="card text-center p-0 m-4">
                                <div class="card-header text-light"><?php echo i8ln('Pokéstops'); ?></div>
                                <div class="card-body bg-light">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/quest.png" width="64" height="64" />
                                                        <h4 class="quest-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Field Research'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/rocket.png" width="64" height="64" />
                                                        <h4 class="rocket-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Invasions'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/lure.png" width="64" height="64" />
                                                        <h4 class="normal-lure-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Normal Lure'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/glacial-lure.png" width="64" height="64" />
                                                        <h4 class="glacial-lure-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Glacial Lure'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/mossy-lure.png" width="64" height="64" />
                                                        <h4 class="mossy-lure-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Mossy Lure'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/magnetic-lure.png" width="64" height="64" />
                                                        <h4 class="magnetic-lure-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Magnetic Lure'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/rainy-lure.png" width="64" height="64" />
                                                        <h4 class="rainy-lure-count"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Rainy Lure'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Spawnpoints -->
                            <div class="card text-center p-0 m-4">
                                <div class="card-header text-light"><?php echo i8ln('Spawnpoints'); ?></div>
                                <div class="card-body bg-light">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/spawnpoint.png" width="64" height="64" />
                                                        <h4 class="spawnpoint-total"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Total'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/found.png" width="64" height="64" />
                                                        <h4 class="spawnpoint-found"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Timer Found'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="list-group">
                                                    <div class="list-group-item">
                                                        <img src="static/images/stats/missing.png" width="64" height="64" />
                                                        <h4 class="spawnpoint-missing"><?php echo i8ln('Loading...'); ?> <i class="fas fa-spinner fa-spin"></i></h4>
                                                        <p><?php echo i8ln('Timer Missing'); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-pokemon-stats" role="tabpanel" aria-labelledby="nav-pokemon-stats-tab">
                            <table id="pokemonTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo i8ln('ID'); ?></th>
                                        <th><?php echo i8ln('Pokémon'); ?></th>
                                        <th><?php echo i8ln('Type'); ?></th>
                                        <th><?php echo i8ln('Count'); ?></th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-reward-stats" role="tabpanel" aria-labelledby="nav-reward-stats-tab">
                            <table id="rewardTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo i8ln('Type'); ?></th>
                                        <th><?php echo i8ln('Reward'); ?></th>
                                        <th><?php echo i8ln('Count'); ?></th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane fade" id="nav-shiny-stats" role="tabpanel" aria-labelledby="nav-shiny-stats-tab">
                            <table id="shinyTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th><?php echo i8ln('Pokémon'); ?></th>
                                        <th><?php echo i8ln('Shiny Count'); ?></th>
                                        <th><?php echo i8ln('Shiny Rate'); ?></th>
                                        <th><?php echo i8ln('Sample Size'); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- End of Modals -->



