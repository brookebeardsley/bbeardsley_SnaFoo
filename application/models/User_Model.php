<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_model{

  public function __construct()
  {
    parent::__construct();
  } // end __construct()

  // function get_user_info()
  // makes sure that the user is logged in...
  // since we don't have password, just devices + browsers, the only identifer we have are session_ids
  // .. so that is their username.
  // returns the user_id and the user_name (not used)
  public function get_user_info()
  {
    $this->db->select("user_id, user_name");
    $this->db->from("users as u");
    $this->db->where(array('user_session_id'=>session_id()));
    $qr = $this->db->get();
    if ($qr->num_rows()>0)
    {
      return $qr->row_array();
    }
    else
    { // looks like we can't find the "user" .. time to create one.  Sure hope that they don't clear their cache / session data on their end! (else, we will have chicago voting!)
      $insert = array(
        'user_session_id' => session_id(),
        'user_name'       => 'New User'
      );
      $this->db->insert('users',$insert);
      return array(
        'user_id' => $this->db->insert_id(),
        'user_name' => 'New User'
      );
    } // end if-else if user exists in DB
  } // end public function get_user_info()
} // end class User_Model
