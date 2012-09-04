<?php

  function getDateRange($period = null) {
    $date_start = date('Y-m-01');
    $date_end   = date('Y-m-d');

    if($period == 'month') {
      $date_end   = date('Y-m-t');
    }

    $dates['start'] = $date_start;
    $dates['end']   = $date_end;
    return $dates;
  }

  function getWorkingDays($period = null)
  {

    $dates = getDateRange($period);
    $date_start = strtotime( $dates['start'] );
    $date_end   = strtotime( $dates['end'] );

    // Sets the Count
    $count = 0;
    // iterates through each day till the end day is back at the start date
    while (date("Y-m-d", $date_start) <= date("Y-m-d",$date_end)){
      $count = (date("w", $date_end) != 0 && date("w", $date_end) != 6) ? $count +1 : $count;
      $date_end = $date_end - 86400;
    }
    return $count;
  }

  function getActualWorkingHoursInRange($config,$employees,$range = null) {

      $days = getWorkingDays($range);
      $hours = 0;

      foreach ($employees as $user) {
        if(isset($config["working_hours_per_day"][$user->email]) && $config["working_hours_per_day"][$user->email] >= 0) {
          $hours += $config["working_hours_per_day"][$user->email] * $days;
        }
        else {
          $hours += $config["working_hours_per_day"]["default"] * $days;
        }
      }

      return round($hours);
  }

  function determineRankingGroup($hours_registered, $hours_goal) {
    $performance = round($hours_registered/$hours_goal*100);

    if($performance >= 110) {
      $group = "A-karmahunter";
    } elseif ($performance < 110 && $performance >= 95) {
      $group = "B-goalie";
    } elseif ($performance < 95 && $performance >= 70) {
      $group = "C-karmauser";
    } else {
      $group = "D-slacker";
    }
    return $group;
  }

  function sortByOneKey(array $array, $key) {

      $result = array();
      $values = array();

      foreach ($array as $id => $value) {
          $values[$id] = isset($value[$key]) ? $value[$key] : '';
      }

      arsort($values);

      foreach ($values as $key => $value) {
          $result[] = $array[$key];
      }

      return $result;
  }

?>