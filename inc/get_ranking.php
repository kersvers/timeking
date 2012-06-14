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
 
// Include the HarvestAPI class
require_once 'HarvestAPI.php';
// Register the HarvestAPI autoloader
spl_autoload_register(array('HarvestAPI', 'autoload'));
 
$harvestAPI = new HarvestReports();
$harvestAPI->setUser($harvest_user);
$harvestAPI->setPassword($harvest_pass);
$harvestAPI->setAccount($harvest_account);

function getEntries($harvestAPI) {
  
  $range = new Harvest_Range(date('Ym01'), date('Ymd'));
  
  $users = $harvestAPI->getActiveUsers();
  $return = array();
  
  $total_hours = 0;

  foreach ($users->data as $user) {
    
    $activity = $harvestAPI->getUserEntries($user->id, $range);
    $hours_per_week = 0;
    
    foreach ($activity->data as $entry) {
       
      $hours_per_week += $entry->hours;
      
    }
    
    $return[1][] = array('name' => $user->first_name, 'hours' => $hours_per_week);
    $total_hours += $hours_per_week;
    
  }
  
  $return[0] = $total_hours;
  
  return $return;
  
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

$entries = getEntries($harvestAPI);

$total = $entries[0];
$ranking = sortByOneKey($entries[1], 'hours');

$json = array();
$json['succes'] = true;
$json['total'] = $total;
$json['ranking'] = $ranking;

echo json_encode($json);

?>