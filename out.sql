PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;
CREATE TABLE results (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE, date_created DATETIME DEFAULT (CURRENT_TIMESTAMP) NOT NULL, id_user INTEGER REFERENCES user (id) NOT NULL);
CREATE TABLE IF NOT EXISTS "competition" (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, id_user INTEGER NOT NULL REFERENCES user (id), date_created DATETIME DEFAULT (CURRENT_TIMESTAMP) NOT NULL, title TEXT NOT NULL, description TEXT, proposition VARCHAR UNIQUE, date_event TIMEDATE NOT NULL, town VARCHAR NOT NULL, id_results INTEGER REFERENCES results (id) UNIQUE);
CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name varchar NOT NULL);
CREATE TABLE IF NOT EXISTS "team" (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, name varchar NOT NULL, id_category INTEGER REFERENCES category (id) NOT NULL, UNIQUE (name, id_category));
CREATE TABLE user (id integer PRIMARY KEY AUTOINCREMENT, username varchar UNIQUE, id_role integer, date_created timestamp, password varchar);
CREATE TABLE IF NOT EXISTS "result" (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, id_team INTEGER REFERENCES team (id) NOT NULL, id_results INTEGER REFERENCES results (id) NOT NULL, time_run DECIMAL(5, 2) NULL, time_run_2 DECIMAL(5, 2), valid_run BOOL DEFAULT (TRUE) NOT NULL);
CREATE TABLE IF NOT EXISTS "photo" (id INTEGER PRIMARY KEY AUTOINCREMENT UNIQUE NOT NULL, id_user INTEGER REFERENCES user (id) NOT NULL, date_created DATETIME NOT NULL DEFAULT (CURRENT_TIMESTAMP), id_competition INTEGER NOT NULL REFERENCES competition (id), name VARCHAR NOT NULL );
COMMIT;