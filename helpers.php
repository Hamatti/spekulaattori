<?php
function calculate_points($points) {
        arsort($points);
        $best_five = array_slice($points, 0, 5);
        $total = array_sum($best_five);
        return $total;
    }

    function read_csv($fname) {
        $csv_in = fopen($fname, 'r');
        $points = array();
        while (($line = fgetcsv($csv_in)) !== FALSE) {
            $player = $line[0];
            $pts = array_slice($line, 2);
            $points[$player] = $pts;
            $points[$player]['total'] = calculate_points($pts);
        }
        fclose($csv_in);
       return $points;
    }

    function get_point($place) {
        $point_table = array(85, 75, 70, 65, 60, 58, 56, 54,
            50, 48, 46, 44, 42, 40, 38, 36,
            34, 33, 32, 31, 30, 29, 28, 27,
            26, 25, 24, 23, 22, 21, 20, 19,
            18, 17, 16, 15, 14, 13, 12, 11,
            10, 9, 8, 7, 6, 5, 4, 3, 2, 1);
        $i = $place - 1;
        if ($i > 50) {
            return 0;
        }
        else {
            return $point_table[$i];
        }
    }

?>
