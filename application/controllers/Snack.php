<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Snack extends CI_Controller {

  private $user_info = array();

  public function __construct()
  {
    parent::__construct();
    $this->user_info = $this->User_Model->get_user_info();
  } // end construct

  /* ***************************************** */
  /* HTML landing pages                        */
  /* ***************************************** */

	public function index()
	{
    $this->voting();
	} // end public function index()

  public function maintenance()
  {
    $data = array();
    display_page('snack/maintenance',$data);
  } // end public function maintenance()

  public function shopping()
  {
    $rv = $this->Snack_Model->load_webservice_snacks(); // get the latest
    if ($rv!==true)
    {
      $this->maintenance();
      return false;
    }

    $data = array();
    $data['shopping_list'] = $this->Snack_Model->get_snack_shopping_list();
    display_page('snack/shopping_list',$data);
  } // end public function shopping()

  public function suggestions()
  {
    $data = array();
    $data['potential_snack_suggestions'] = $this->Snack_Model->fetch_snack_list(
      array(
        's.snack_purchase_type_id' => 3
    )); // end fetch_snack_list
    display_page('snack/suggestions',$data);
  } // end public function suggestions()

  public function voting()
  {
    $rv = $this->Snack_Model->load_webservice_snacks(); // get the latest
    if ($rv!==true)
    {
      $this->maintenance();
      return false;
    }

    $data = array();
    $data['snack_list']      = $this->Snack_Model->fetch_snack_list();
    $data['user_votes']      = $this->Snack_Model->get_votes_raw(
         array(
           'user_id'=>$this->user_info['user_id']
          ));
    display_page('snack/voting',$data,'snack/voting_ng');
  }  // end public function voting()

  /* ***************************************** */
  /* API pages                                 */
  /* These are listed with the protocol name   */
  /* as the first section of the function name */
  /* (get, post, ng)                           */
  /* ***************************************** */

  public function get_snack_vote_data()
  {
    $data = array();
    $data['snack_list']      = $this->Snack_Model->fetch_snack_list();
    $data['user_votes']      = $this->Snack_Model->get_votes_raw(
         array(
           'user_id'=>$this->user_info['user_id']
          ));
    $data['status'] = 'success';
    send_json($data);
  } // end public function get_snack_vote_data()

  public function ng_cast_vote()
  {
    $data = array();
    $post_vals = json_decode(file_get_contents('php://input'),true); // Angular doesn't send data to $_POST
    $this->Snack_Model->cast_vote($data,$post_vals['snack_id'],$this->user_info['user_id']);
    send_json($data);
  } // end public function ng_cast_vote()

  public function post_suggestion()
  {
    $data = array();
    $params = $this->input->post();
    $params['user_id'] = $this->user_info['user_id'];
    $this->Snack_Model->handle_suggestion($data,$params);
    if ($data['status'] == 'success')
      header("Location: ".base_url());
    else
    {
      $this->session->set_flashdata('error_message',$data['message']);
      header("Location: ".base_url('Snack/suggestions'));
    }
  } // end public function post_suggestion()
} // end class Snack
