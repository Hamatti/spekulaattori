<?php


function get_tournament_count($season_id) {
	$db_result = mysql_query("SELECT count(*) as count FROM tournament WHERE season_id = " . $season_id);
	$count = mysql_fetch_assoc($db_result);
	$count = $count['count'];

	return $count;
}

function get_stats_query($season_id, $playoff = 0) {
	$query = <<<QUERY
	SELECT
		player.name as name,
		COALESCE(games, 0) as games,
		COALESCE(wins, 0) as wins,
		COALESCE(draws, 0) as draws,
		COALESCE(losses, 0) as losses,
	  	COALESCE(goals_for,0) as goals_for,
	  	COALESCE(goals_against,0) as goals_against,
	  	(COALESCE(goals_for,0) - COALESCE(goals_against,0)) as goal_difference,
        (COALESCE(wins,0)*2 + COALESCE(draws,0)) as points,
        ROUND((goals_for / games), 2) as scoredpergame,
        ROUND((goals_against / games),2) as againstpergame,
        ROUND(((goals_for + goals_against) / games),2) as goalspergame

	FROM

		(SELECT player.id as p, count(*) as games
		FROM player, game, tournament, season
		WHERE (player.id = home or player.id = away) AND t_id = tournament.id AND season_id = season.id AND season.id = {$season_id}
		GROUP BY player.id) g

	LEFT JOIN

		(SELECT player.id as p, count(*) as wins
		FROM game, player, tournament, season
		WHERE ((home = player.id AND hg > ag) OR (away = player.id AND ag > hg)) AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
		GROUP BY player.id) w ON g.p = w.p

	LEFT JOIN

		(SELECT player.id as p, count(*) as draws
		FROM game, player, tournament, season
		WHERE ((home = player.id AND hg = ag) OR (away = player.id AND ag = hg)) AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
		GROUP BY player.id) d ON g.p = d.p

	LEFT JOIN

		(SELECT player.id as p, count(*) as losses
		FROM game, player, season, tournament
		WHERE ((home = player.id AND hg < ag) OR (away = player.id AND ag < hg)) AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
		GROUP BY player.id) l ON g.p = l.p

	LEFT JOIN

		(SELECT ags.pid as p, ag+hg as goals_for
		FROM
			(SELECT player.id as pid, sum(ag) as ag
			FROM game, player, tournament, season
			WHERE away = player.id AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
			GROUP BY player.id) ags,
			(SELECT player.id as pid, sum(hg) as hg
			FROM game, player, tournament, season
			WHERE home = player.id AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
			GROUP BY player.id) hgs
		WHERE ags.pid = hgs.pid ) gf ON g.p = gf.p

	LEFT JOIN

		(SELECT ags.pid as p, ag+hg as goals_against
		FROM
			(SELECT player.id as pid, sum(ag) as ag
			FROM game, player, tournament, season
			WHERE home = player.id AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
			GROUP BY player.id) ags,
			(SELECT player.id as pid, sum(hg) as hg
			FROM game, player, tournament, season
			WHERE away = player.id AND t_id = tournament.id AND season_id = season.id AND  season.id = {$season_id}
			GROUP BY player.id) hgs
		WHERE ags.pid = hgs.pid) ga ON g.p = ga.p

	LEFT JOIN
  		player ON g.p = player.id

  	ORDER BY points DESC, games ASC

QUERY;

	return $query;
}
function sort_by_total($a, $b) {
    $a_total = $a['total'];
    $b_total = $b['total'];
    unset($a['total']);
    unset($b['total']);
    if($a_total == $b_total) {
        /* If totals are equal, the player with highest single tournament wins
         */
        $a_max = max($a);
        $b_max = max($b);
        if($a_max == $b_max) {
            $nth_highest = 1;
            while($a_max == $b_max && $nth_highest < 9) {
                $a_max = get_highest($a, $nth_highest);
                $b_max = get_highest($b, $nth_highest);
                $nth_highest++;
            }
            return ($a_max < $b_max) ? 1 : -1;
        }
        else {
            return ($a_max < $b_max) ? 1 : -1;
        }
    }
	return ($a_total < $b_total) ? 1 : -1;
}

function old_sort($a, $b) {
    return ($a['total'] < $b['total']) ? 1 : -1;
}

function get_highest($array, $nth) {
    arsort($array);
    $array = array_values($array);
    return $array[$nth];
}

function count_best_six($player) {
    return count_best_n($player, 6);
}

function count_best_three($player) {
    return count_best_n($player, 3);
}

function count_best_15($player) {
	return count_best_n($player, 15);
}

function count_best_n($player, $n) {
    if(count($player) > $n) {
        $player['total'] = count_new_total($player, $n);
    }
    return $player;
}

function count_new_total($player, $n) {
    unset($player['total']);
    $points = array_values($player);
    arsort($points);
    $points = array_values($points);
    $total = array_sum(array_slice($points, 0, $n));
    return $total;
}

function get_limit($points, $n = 6) {
    unset($points['total']);
    rsort($points);
    if(count($points) >= $n) {
        return $points[$n-1];
    }
    else {
        return $points[count($points)-1];
    }
}




function get_players($season_id) {
	$player_names = array();
	$query = "SELECT DISTINCT player.name as name FROM player, rankingpoints, tournament, season WHERE player.id = p_id AND t_id = tournament.id AND tournament.season_id = season.id AND season.id = " . $season_id;
	$result = mysql_query($query);
	while($r = mysql_fetch_assoc($result)) {
		$player_names[] = $r['name'];
	}
	return $player_names;
}

function get_tournaments($season_id) {
	$tournaments = array();
	$query = "SELECT DISTINCT t_id FROm rankingpoints, tournament, season WHERE t_id = tournament.id AND season_id = season.id AND season.id = " . $season_id . " ORDER BY t_id";
	$result = mysql_query($query);

	while($r = mysql_fetch_assoc($result)) {
		$tournaments[] = $r['t_id'];
	}

	return $tournaments;
}

function get_player_info($tournaments, $player_names, $season) {
	$player_info = array();
	foreach ($tournaments as $t) {
		$tmp_players = $player_names;
		$query = "SELECT player.name as name, points FROM player, rankingpoints WHERE p_id = player.id AND rankingpoints.t_id = " . $t;
		$result = mysql_query($query);

		while($r = mysql_fetch_assoc($result)) {

			$player_name = $r['name'];
			if(($key = array_search($player_name, $tmp_players)) !== false) {
				unset($tmp_players[$key]);
			}
			if(!array_key_exists($player_name, $player_info)) {
				$player_info[$player_name] = array();
			}
			$player_info[$player_name][$t] = $r['points'];
		}
		foreach ($tmp_players as $player) {
			$player_info[$player][$t] = 0;
		}

	}


	$query = "SELECT player.name as name, sum(points) as points FROM player, rankingpoints, season, tournament WHERE player.id = rankingpoints.p_id AND rankingpoints.t_id = tournament.id AND tournament.season_id = season.id AND season.id = " . $season . " GROUP BY player.id";
	$result = mysql_query($query);
	while($r = mysql_fetch_assoc($result)) {

		$player_info[$r['name']]['total'] = $r['points'];
	}

	return $player_info;

}

function get_filename_by_tournament($t_id) {
	$query = "SELECT filename FROM tournament WHERE id = " . $t_id;
	$result = mysql_query($query);
	$assoc = mysql_fetch_assoc($result);

	return  $assoc['filename'];

}

?>
