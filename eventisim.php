<?php

require_once 'eventisim.civix.php';

function eventisim_civicrm_buildForm ( $formName, &$form ){
  if ("CRM_Event_Form_Search" != $formName) 
    return;
  $skip_columns = array();
  $values=$form->GetVar("_values");
  if (is_array($values) && array_key_exists("event_id",$values)) {
      $skip_columns[]="event_id";
  }
  $cids;
  $rows=$form->getTemplate()->get_template_vars("rows");
  if (empty($rows))
    return;
  foreach ($rows as $row) {
    $cids[]= (int) $row["contact_id"];
  }
  $cids = implode (",",$cids);
  $query  = "SELECT id, organization_name as employer FROM civicrm_contact WHERE id in ($cids)";
  $dao = CRM_Core_DAO::executeQuery($query, $params);
  $orgs = array();
  while ($dao->fetch()) {
    $orgs[$dao->id] = $dao->employer;
  }
  foreach ($rows as &$row) {
    if (array_key_exists($row["contact_id"],$orgs))
      $row["employer"] = $orgs[$row["contact_id"]];
    else
      $row["employer"] = null; 
  }
 
  $ref=$rows[0];
  foreach ($ref as $k => $field) {
    $skip=true;
    foreach($rows as $row) {
      if ($row[$k] != $field) {
        $skip=false;
        break;
      }
    }
    if ($skip) {
      $skip_columns[]=$k;
    }
  }
  $fieldalias=array ("fee_level"=>"participant_fee_level","fee_amount"=>"participant_fee_amount");
  $columnHeaders=$form->getTemplate()->get_template_vars("columnHeaders");
  array_splice( $columnHeaders, 2, 0, array(0=>array("field"=>"employer","name"=>"Organisation")) );
  foreach ($columnHeaders as $k=>&$header) {
    if (array_key_exists("sort",$header)) {
      if (array_key_exists($header["sort"],$fieldalias)) {
        $header["field"] = $fieldalias[$header["sort"]];
      } else {
        $header["field"] = $header["sort"];
      }
    } else {
        $header["field"] = null;
    }
    if (in_array($header["field"],$skip_columns)) {
      unset($columnHeaders[$k]);
    }
  }

  $form->assign("rows",$rows);
  $form->assign("columnHeaders",$columnHeaders);
  $form->assign("skip_columns",$skip_columns);
}

/**
 * Implementation of hook_civicrm_config
 */
function eventisim_civicrm_config(&$config) {
  _eventisim_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function eventisim_civicrm_xmlMenu(&$files) {
  _eventisim_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function eventisim_civicrm_install() {
  return _eventisim_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function eventisim_civicrm_uninstall() {
  return _eventisim_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function eventisim_civicrm_enable() {
  return _eventisim_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function eventisim_civicrm_disable() {
  return _eventisim_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function eventisim_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventisim_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function eventisim_civicrm_managed(&$entities) {
  return _eventisim_civix_civicrm_managed($entities);
}
