<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// Little helper to wrap the content in my site wrapper.
function display_page($template,$data,$ng_template = false)
{
  $CI =& get_instance();
  $user_info = $CI->User_Model->get_user_info();
  $data['has_made_a_suggestion'] = $CI->Snack_Model->has_user_submitted_a_suggestion($user_info['user_id']);
  $data['app_data'] = config_item('app_data');
  if ($ng_template)
    $data['angular'] = $CI->load->view($ng_template,$data,TRUE);
  $e = $CI->session->flashdata('error_message');
  if ($e)
    $data['error_message'] = $e;
  $data['page_data'] = $CI->load->view($template,$data,TRUE);
  $CI->load->view('common/template',$data);
} // end function display_page($title,$data))

// send json/jsonp using the data array in $data
function send_json($html,$callback='')
{   
  //$callback=$this->input->get('callback');
  if ($callback=='')
  {   
    // json
    header('Content-Type: application/json; charset=utf8');
    echo json_encode($html);
  }   
  else
  { // jsonp
    header('Content-Type: text/javascript; charset=utf8');
    header('Access-Control-Allow-Origin: http://www.example.com/');
    header('Access-Control-Max-Age: 3628800');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo $callback."(".json_encode($html,JSON_NUMERIC_CHECK).");";
  }   
} // end function send_json($html)

/* End of file my_helper.php */
/* Location: ./application/helpers/my_helper.php */
?>
