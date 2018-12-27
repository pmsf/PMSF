<?php
include(dirname(__FILE__).'/../config/config.php');
global $map, $fork, $db;


$db->update('pokestop',['quest_conditions' => null, 'quest_rewards' => null, 'quest_target' => null, 'quest_template' => null, 'quest_type' => null]);
echo 'updated pokestops';
