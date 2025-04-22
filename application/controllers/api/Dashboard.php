<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Dashboard extends API_Controller
{
    public function __construct() {
        parent::__construct();  
        $this->load->helper('api_helper');   
        $this->load->model("Salary_model");
        $this->load->model("Lunch_model");
   
    }
    /**
     * demo method 
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */
    public function test()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);

        if ($user_info['status'] == true) {
            echo json_encode($user_info);
            exit();
        }else{
            echo json_encode($user_info);
            exit();
        }
    }

    /**
     * view method
     *
     * @link [api/user/view]
     * @method POST
     * @return Response|void
     */
    public function view()
    {
        header("Access-Control-Allow-Origin: *");

        // API Configuration [Return Array: User Token Data]
        $user_data = $this->_apiConfig([
            'methods' => ['POST'],
            'requireAuthorization' => true,
        ]);

        // return data
        $this->api_return(
            [
                'status' => true,
                "result" => [
                    'user_data' => $user_data['token_data']
                ],
            ],
        200);
    }

    public function index(){
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;



            $from = date('Y-01-01');
            $to = date('Y-m-t');
            $early_leaves = $this->db->select('attendance_date,early_time')->where('employee_id', $userid)->where('early_status', 1)->where('attendance_date BETWEEN "' . $from . '" and "' . $to . '"')->order_by('id', 'desc')->get('xin_attendance_time')->result();
            $late_ins = $this->db->select('attendance_date,late_time')->where('employee_id', $userid)->where('late_status', 1)->where('attendance_date BETWEEN "' . $from . '" and "' . $to . '"')->order_by('id', 'desc')->get('xin_attendance_time')->result();

            $data['early_leaves'] = $early_leaves;
            $data['late_ins'] = $late_ins;





                $this->api_return([
                    'status' => true,
                    'message' => 'successful',
                    'data' => $data,
                ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
            ], 401);
        }
    }
}