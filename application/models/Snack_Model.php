<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Snack_Model extends CI_model{

  private $app_data;

  public function __construct()
  {
    parent::__construct();
    $this->app_data = config_item('app_data');
  } // end __construct()

  // function case_vote
  // checks to see if the vote is valid before casting it.
  // returns the function status, and an error message (if there is one)
  public function cast_vote(&$resp,$snack_id,$user_id)
  {
    // validation checking..
    // too many votes?
    $current_votes = $this->get_votes_raw(array('user_id'=>$user_id));
    if (count($current_votes)>=$this->app_data['max_monthly_votes'])
    {
      $this->set_error($resp, 'User has met or exceeded their monthly voting quota.  Cast vote has been rejected.');
      return false;
    } // end if result

    // duplicate vote? (case shouldn't be allowed by the DB.. but just in case..
    $current_votes = $this->get_votes_raw(array('user_id'=>$user_id,'snack_id'=>$snack_id));
    if (count($current_votes)>0)
    {
      $this->set_error($resp, 'User has met or exceeded their monthly voting quota.  Cast vote has been rejected.');
      return false;
    } // end if result

    // cast the vote!
    $insert = array(
      'user_id'   => $user_id,
      'snack_id'  => $snack_id,
      'date'      => date('Y-m-d')
    );
    if (! $this->db->insert('users_snacks',$insert))
    {
      $this->set_error($resp,'There was an issue casting your vote.');
      return false;
    }

    $resp['status'] = 'success';
    return true;
  } // end public function cast_vote($snack_id,$user_id)

  // function fetch_snack_list
  // returns an array of snacks, using $params as a filter (where statement)
  public function fetch_snack_list($params=array())
  {
    if (!isset($params['s.date_deleted'])) $params['date_deleted'] = '0000-00-00';

    $this->db->select("s.*, spt.snack_purchase_type, count(usn.user_id) as num_votes");
    $this->db->from("snacks as s");
    $this->db->join("snack_purchase_type as spt","s.snack_purchase_type_id = spt.snack_purchase_type_id");
    $this->db->join("users_snacks as usn","usn.snack_id = s.snack_id and usn.date like '".date('Y-m')."%'","left");
    $this->db->where($params);
    $this->db->group_by('s.snack_id');
    $qr = $this->db->get();
    if ($qr->num_rows()>0)
    {
      return $qr->result_array();
    }
    else
      return array();
  } // end public function fetch_snack_list($params=array())

  // function get_snack_shopping_list
  // returns the shopping list (name + location + location)
  // selects all "Always" items first, then the highest ranked "Electable Items".. up until we get to limit 10.
  public function get_snack_shopping_list()
  {
    $this->db->select('s.snack_name, s.snack_location_short, s.snack_location_long');
    $this->db->from('snacks as s');
    $this->db->join('users_snacks as usn',"s.snack_id = usn.snack_id and usn.date like '".date('Y-m')."%'",'left');
    $where = "s.snack_purchase_type_id = '1' or (s.snack_purchase_type_id = 2 and usn.user_id is not null)";
    $this->db->where($where,null,false);
    $this->db->group_by('s.snack_id');
    $this->db->order_by('s.snack_purchase_type_id asc, count(usn.user_id) desc');
    $this->db->limit(10);
    $qr = $this->db->get();
    if ($qr->num_rows()>0)
      return $qr->result_array();
    else
      return array();
  } // end public function get_snack_shopping_list()

  // function get_votes_raw
  // returns an array of raw vote data, using $params as a filter (where statement)
  public function get_votes_raw($params)
  {
    if (isset($params['date']))
    {
      $like = array('date'=>substr($params['date'],0,7));
      unset($params['date']);
    }
    else
    {
      $like = array('date'=>date('Y-m'));
    } // end if-else date

    $this->db->from('users_snacks as usn');
    $this->db->where($params);
    $this->db->like($like);
    $qr = $this->db->get();
    if ($qr->num_rows()>0)
      return $qr->result_array();
    else
      return array();
  } // end public function get_votes_raw($params)

  // function handle_suggestion
  // 1) Checks to see if the user has submitted a suggestion yet this month
  // 2) Validates the suggestion
  // 3) Saves the suggestion locally to the database
  // 4) Then submits the data to the webservice
  // 5) calls load_webservice_snacks to complete the synchronization
  //
  // parameter indices: 'snackOptions','suggestion_name','suggestion_location','user_id'
  // if snackOptions is valid, it will be used, otherwise suggestion_name/suggestion_location will be used (also, if valid)
  public function handle_suggestion(&$data,$parameters)
  {
    // Check to see if the user has submitted a suggestion yet this month
    if ($this->has_user_submitted_a_suggestion($parameters['user_id']))
    {
      $this->set_error($data,'Sorry, you have already submitted a suggestion this month.');
      return false;
    } // end if a suggestion was already submitted

    $this->db->trans_begin(); // start Transactional mode .. manual mode

    // Plan A: See if "snackOptions" is valid
    $snack_id = false; // if this is a positive integer, then it is valid existing record; if "new" then please create new; else there is a validation error
    if (isset($parameters['snackOptions']) && $parameters['snackOptions']>0)
    {
      $snack_id = intval($parameters['snackOptions']);
      $this->db->select('snack_purchase_type_id');
      $this->db->from('snacks');
      $this->db->where(array(
        'snack_id' => $snack_id,
        'snack_purchase_type_id' => 3
      ));
      $qr = $this->db->get();
      if ($qr->num_rows()>0)
      { // do nothing, just free some memory
        $qr->free_result();
      }
      else
      { // reset snack_id.. will have to try plan B
        $snack_id = false;
      }
    } // end if snackOptions is potentialy valid

    // Plan B: validate the other two fields
    // not putting a whole lot into this mapping, outside of making the test case insensitive and trimming.
    //! warning: there is a potential for falsely identifying a duplicate item if the location is similar to an existing one
    //!    example: URLs.
    //!    Suggestion: we should be validating against the snack_location_long field instead, except that the webservice doesn't really support it.
    if ($snack_id ===false)
    {
      if (!isset($parameters['suggestion_name']) || strlen(trim($parameters['suggestion_name']))==0 || 
        !isset($parameters['suggestion_location']) || strlen(trim($parameters['suggestion_location']))==0)
          $this->set_error($data,'Sorry, I did not receive a suggestion.  Please try again');

      $query = "SELECT snack_id, snack_purchase_type_id FROM snacks";
      $query.=" WHERE snack_name like '%".strtolower(addslashes($parameters['suggestion_name']))."%'";
      $query.=" AND snack_location_short like '%".strtolower(addslashes(substr($parameters['suggestion_location'],0,50)))."%';";
      $qr = $this->db->query($query);
      if ($qr->num_rows()>0)
      {
        $r = $qr->row_array();
        if ($r['snack_purchase_type_id']==3)
          $snack_id = $r['snack_id']; // we found the snack.. treat it like Plan A
        else
          $this->set_error($data, 'Sorry, You have made an invalid suggestion: it may be already suggested, on the active purchase list, or be a banned product.');
        $qr->free_result();
      }
      else
      {
        $snack_id = 'new';
      }
    } // end if Try plan B

    if ($snack_id == 'new')
    {
      // create the snack entry
      $insert = array(
        'snack_name'              => $parameters['suggestion_name'],
        'snack_location_short'    => substr($parameters['suggestion_location'],0,50),
        'snack_location_long'     => $parameters['suggestion_location'],
        'snack_purchase_type_id'  => 2
      ); // insert
      if (! $this->db->insert('snacks',$insert))
        $this->set_error($data,'There was an issue writing to the database');

      $snack_id = $this->db->insert_id();

      $rv = $this->submit_suggestion_to_webservice($insert);
      if ($rv !== true)
        $this->set_error($data,$rv);
    } // create the snack!

    if (is_int($snack_id))
    {
      // update the snack_status;
      if (! $this->db->query("update snacks set snack_purchase_type_id='2' where snack_id='".$snack_id."';"))
        $this->set_error($data,'There was an issue writing to the database');
      // log the user's suggestion for the month
      $insert = array(
        'snack_id'   => $snack_id,
        'user_id'    => $parameters['user_id'],
        'date'       => date('Y-m-d')
      ); // end insert
      if (! $this->db->insert('users_snacks_suggestions',$insert))
        $this->set_error($data,'There was an issue writing to the database');

      if (!isset($data['status']))
      {
        $data['status'] = 'success';
        $this->db->trans_commit();
      }
      else
      {
        $this->db->trans_rollback();
      } // end if-else success

      $this->load_webservice_snacks();
    } // end if I have a valid (integer) snack_id
  } // end public function handle_suggestion(&$data,$parameters)

  // function has_user_submitted_a_suggestion
  // returns true if the user has submitted a suggestion, else false.
  public function has_user_submitted_a_suggestion($user_id)
  {
    // Check to see if the user has submitted a suggestion yet this month
    $qr = $this->db->query("select user_id from users_snacks_suggestions where user_id = '".$user_id."' and date like '".date('Y-m')."%';");
    if ($qr->num_rows()>0)
      return true;
    else
      return false;
  } // end public function has_user_submitted_a_suggestion($user_id)

  // function load_webservice_snacks
  // Attempts to read snack data from the webservice and store it locally to the database,
  // synchronizing records if it can.
  // returns true on success, otherwise an error string
  public function load_webservice_snacks()
  {
    $uri = $this->app_data['api_host'].'snacks/?ApiKey='.$this->app_data['api_key'];
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_resp = curl_exec ($ch);
    if(curl_error($ch))
      return 'Error: '.curl_error($ch);
    curl_close ($ch);

    if (!$this->isJson($server_resp))
      return 'Invalid page';

    $snacks = json_decode($server_resp,true);

    if (isset($snacks['message']))
      return $snacks['message'];

    // time to compare and import.. have to do one at a time because I don't want to flush my database.
    foreach ($snacks as $s)
    {
      // locate a match?
      $snack_id = false;
      $snack_purchase_type_id = false;
      $qr = $this->db->query("select snack_id, snack_purchase_type_id from snacks where webservice_snack_id='".addslashes($s['id'])."';");
      if ($qr->num_rows()>0)
      {
        $r = $qr->row_array();
        $snack_id = $r['snack_id'];
        $snack_purchase_type_id = $r['snack_purchase_type_id'];
        $qr->free_result();
      } 
      else // attempt a name-location match
      {
      $qr = $this->db->query("select snack_id, snack_purchase_type_id from snacks where snack_name='".addslashes($s['name'])."' and snack_location_short='".addslashes($s['purchaseLocations'])."';");
        if ($qr->num_rows()>0)
        {
          $r = $qr->row_array();
          $snack_id = $r['snack_id'];
          $snack_purchase_type_id = $r['snack_purchase_type_id'];
          $qr->free_result();
        } // end if there is an alternative match
      } // end if-else webservice_snack_id match

      $insert = array(
        'snack_name'              => $s['name'],
        'snack_purchase_type_id'  => ($s['optional']?3:1),
        'snack_location_short'    => $s['purchaseLocations'],
        'snack_location_long'     => $s['purchaseLocations'],
        'last_date_purchased'     => date('Y-m-d',strtotime($s['lastPurchaseDate'])),
        'webservice_snack_id'     => $s['id']
      );
      if ($snack_id===false)
      {
        if (! $this->db->insert('snacks',$insert))
        {
          $resp['status'] = 'error';
          $resp['message'] = 'There was an issue reading from the webservice.';
        }
      }
      else
      {
        // don't want to overwrite this.. This DB probably has better data.
        unset($insert['snack_location_long']);

        // snack_purchase_type needs to check to see if it changed alot from what came from the webservice
        // I don't want to accidentally reset from stage 2/3 and vice versa.  I want to recognize all other state changes.
        if (in_array($snack_purchase_type_id,array(2,3)) && $s['optional'])
          unset($insert['snack_purchase_type_id']);

        $this->db->where(array('snack_id'=>$snack_id));
        if (! $this->db->update('snacks',$insert))
        {
          $resp['status'] = 'error';
          $resp['message'] = 'There was an issue reading from the webservice.';
        }
      } // end if-else we need to insert (vs update)
    } // end foreach $snack
    return true;
  } // end public function load_webservice_snacks()

  /*********************/
  /* Private Functions */
  /*********************/

  // function set_error
  // quick helper function to set both status and message values.
  private function set_error(&$resp,$message)
  {
    $resp['status'] = 'error';
    $resp['message'] = $message;
    return true;
  } // end private function set_error(&$resp,$message)

  // function submit_suggestion_to_webservice
  // send the suggestion to the webservice
  // params have "snack_name" and "snack_location_short", there are others too, but we don't need them (yet)
  // longitude and latitude are not being filled in: insufficient data .. would need to update the Web interface and have the user provide exact address or pindrops for this to be available
  private function submit_suggestion_to_webservice($params)
  {
    $uri = $this->app_data['api_host'].'snacks/?ApiKey='.$this->app_data['api_key'];
    $dpkg = array(
      'name'      => $params['snack_name'],
      'location'  => $params['snack_location_short'],
      'latitude'  => '',
      'longitude' => ''
    );
    $payload = json_encode($dpkg);
    $ch = curl_init($uri);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_resp = curl_exec ($ch);
    if(curl_error($ch))
      return 'Error: '.curl_error($ch);
    curl_close ($ch);

    if (!$this->isJson($server_resp))
      return 'Invalid page';

    $snacks = json_decode($server_resp,true);

    if (isset($snacks['message']))
      return $snacks['message'];
    else
      return true;
  } // end private function submit_suggestion_to_webservice($params)

  // utilities
  private function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
  }
} // end class Snack_Model
