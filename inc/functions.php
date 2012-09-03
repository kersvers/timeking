<?php

  function getDateRange($period = null) {
    $date_start = strtotime('first day of this month');
    $date_end   = strtotime("now");

    // check for first day of month, then show last months data
    if($date_end < $date_start) {
      // lets set the start date to the first day of last month
      $date_start = strtotime('first day of last month');
        //$date_end = $date_start;
    }

    if($period == 'month') {
      $date_end   = strtotime(sprintf("last day of %s",date("F",$date_start)));
    }

    $dates['start'] = $date_start;
    $dates['end']   = $date_end;
    return $dates;
  }

  function getWorkingDays($period = null)
  {

    $dates = getDateRange($period);
    $date_start = $dates['start'];
    $date_end   = $dates['end'];

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