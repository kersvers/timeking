<?php
 
/**
 * Set your credentials here. You must be an admin on the Harvest account for
 * this to work properly. You may want to store this in a separate file and include
 * that file here.
 *   include('settings.php');
 */
$harvest_user = ''; // Your Harvest username, usually an email address
$harvest_pass = ''; // Your Harvest password
$harvest_account = ''; // The {myaccount} portion of your Harvest url: {myaccount}.harvestapp.com
// date_default_timezone_set('America/Los_Angeles'); // Set your timezone if it is not set in your php.ini
 
include('settings.php'); // config 
include('functions.php'); 

// Include the HarvestAPI class
require_once 'HarvestAPI.php';
// Register the HarvestAPI autoloader
spl_autoload_register(array('HarvestAPI', 'autoload'));
 
$harvestAPI = new HarvestReports();
$harvestAPI->setUser($config["harvest_user"]);
$harvestAPI->setPassword($config["harvest_pass"]);
$harvestAPI->setAccount($config["harvest_account"]);

function getEntries($harvestAPI, $config) {

  $dates = getDateRange();

  $date_start = strtotime( $dates['start'] );
  $date_end   = strtotime( $dates['end'] );

  $range = new Harvest_Range(date('Ymd', $date_start), date('Ymd', $date_end));

  $users = $harvestAPI->getActiveUsers();
  $return = array();

  $total_hours        = 0;
  $workdays_in_range  = getWorkingDays(); // until today
  $employees          = array();

  foreach ($users->data as $user) {

    if($user->get("is-contractor") == "true")
    {
      // ignore contractors, they do not play our game :-)
      continue;
    }
    
    // this is a real user, count him in!
    $employees[] = $user;

    // determine optimal hours logged for this user
    $hours_goal = $workdays_in_range * $config["working_hours_per_day"]["default"];
    if(isset($config["working_hours_per_day"][$user->email]) && $config["working_hours_per_day"][$user->email] > 0) {
      // we have a user defined daily working schedule
       $hours_goal = $workdays_in_range * $config["working_hours_per_day"][$user->email];
    }

    $activity = $harvestAPI->getUserEntries($user->id, $range);
    $hours_registered = 0;
    
    foreach ($activity->data as $entry) {
      $hours_registered += $entry->hours;
    }
    
    $return[1][] = array(
      'name' => $user->first_name . " " . substr($user->last_name,0,1), 
      'hours_registered' => $hours_registered, 
      'hours_goal' => $hours_goal,
      'performance' => round($hours_registered/$hours_goal*100),
      'group' => determineRankingGroup($hours_registered, $hours_goal)
      );
    $total_hours += $hours_registered;
    
  }
  
  $return[0] = round($total_hours);
  $return[2] = $employees;
  
  return $return;
  
}

$entries = getEntries($harvestAPI, $config);

$total = $entries[0];
$employees = $entries[2];
$ranking = sortByOneKey($entries[1], 'performance');
//shuffle($ranking);

$json = array();
$json['succes'] = true;
$json['hours_total_registered'] = $total;
$json['hours_total_month']      = getActualWorkingHoursInRange($config, $employees, "month");
$json['hours_until_today']      = getActualWorkingHoursInRange($config, $employees, null);
$json['ranking']                = $ranking;

echo json_encode($json);

?>