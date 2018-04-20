<?php
function sum_of_best_five_results($points) {
    /* For an array of points, calculate the sum of top five points */
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
        $points[$player]['total'] = sum_of_best_five_results($pts);
    }
    fclose($csv_in);
    return $points;
}

function sort_by_total($a, $b) {
    $a_total = $a['total'];
    $b_total = $b['total'];
    unset($a['total']);
    unset($b['total']);
    if($a_total == $b_total) {
        /* If totals are equal, the player with highest single tournament wins*/
        $a_max = max($a);
        $b_max = max($b);
        if($a_max == $b_max) {
            $nth_highest = 1;
            while($a_max == $b_max && $nth_highest < 7) {
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


function get_point($place) {
    $point_table = array(
        85, 75, 70, 65, 60, 58, 56, 54,
        50, 48, 46, 44, 42, 40, 38, 36,
        34, 33, 32, 31, 30, 29, 28, 27,
        26, 25, 24, 23, 22, 21, 20, 19,
        18, 17, 16, 15, 14, 13, 12, 11,
        10, 9, 8, 7, 6, 5, 4, 3, 2, 1
    );

    $i = $place - 1;
    return $i > 50 ? 0 : $point_table[$i];
}

function read_and_clean_input($input) {
  $input = explode("\n", $input);

  if($input[0] == null) {
    $input = array();
  }

  return array_map('trim', $input);
}

function add_and_sort($points, $input) {
  // Copy to avoid side effects
  $temp_points = $points;

  $placement = 1;

  foreach($input as $name) {
      $pts = get_point($placement);
      $temp_points[$name][6] = $pts;
      unset($temp_points[$name]['total']);
      $temp_points[$name]['total'] = sum_of_best_five_results($temp_points[$name]);

      $placement++;
  }
  uasort($temp_points, 'sort_by_total');

  return $temp_points;
}

function get_highest($array, $nth) {
    arsort($array);
    $array = array_values($array);
    // Sometimes the array is not correct here. It doesn't seem to affect but it prints to errlog.
    return $array[$nth];
}

?>
