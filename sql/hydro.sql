//--------------------------------------
// Everything in this file is known to work with mariadb/mysql. May need slight adjustments for PGSQL.
//--------------------------------------

//--------------------------------------
// Required for Hydro Database
//--------------------------------------
ALTER TABLE pokestops
ADD (quest_id SMALLINT(4), reward_id SMALLINT(4), quest_submitted_by VARCHAR(200), edited_by VARCHAR(200));

ALTER TABLE forts
ADD (edited_by VARCHAR(200), submitted_by varchar(200) DEFAULT NULL);

ALTER TABLE raids
ADD form smallint(6) DEFAULT NULL;

ALTER TABLE fort_sightings
ADD guard_pokemon_form SMALLINT(6) NULL DEFAULT NULL AFTER guard_pokemon_id;

ALTER TABLE gym_defenders
ADD form SMALLINT(6) NULL DEFAULT NULL AFTER pokemon_id;

