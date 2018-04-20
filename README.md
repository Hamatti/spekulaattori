# Spekulaattori
## A tool for speculation about final rankings in the season


### Background

Finnish Table Hockey Association (*SPJKL or SPÖL* amongst friends) hosts 7 ranking tournaments every season.
Out of these 7 tournaments, top N players are selected to World Championships or European Championships.

Before the final tournament, there's always speculation about who needs to finish to which position to qualify for the national team.
To replace old, cranky Excel files, Spekulaattori was created.

### Admin Usage

1. You need .rnk file from [http://poytajaakiekko.net/spjkl/tulokset/**SEASON**/ranking/MMavoin.rnk](http://poytajaakiekko.net/spjkl/tulokset/**SEASON**/ranking/MMavoin.rnk) to `utils/` folder.

2. Run `python rnk2csv.py --in=[.rnk file] --out [outputfile]` to generate compatible csv file.

3. Copy file to `inputs/`.

4. Edit `config.php` to set correct input file as well as the `$cut_number` to correspond how many top N players qualify. Set `$tournament_names` array to names of the tournaments for the season.

### User Usage

1. User provides a `\n` separated list of names in `[last_name] [first_name]` format and clicks `Submit`.

2. The app updates the rankings on the left side.

### Requirements

This software currently requires PHP to serve and Python >2.7 for generating .csv files.

### Contributions

Originally written by Juha-Matti Santala.
