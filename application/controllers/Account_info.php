<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_info extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('accountInfo');
    }

    /**
     * @param $date
     */
    public function valid_date($date)
    {
        $today = date('Y-m-d');
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!($d && $d->format('Y-m-d') === $date && strtotime($d->format('Y-m-d')) <= strtotime($today))) {
            $this->form_validation->set_message('valid_date', 'The {field} field can not be the valid date');
            return false;
        } else {
            return true;
        };
    }

    /**
     * AJAX controller.
     */
    public function ajax($id = null)
    {
        // 參數處理
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        // 此處應有對傳入參數$_POST消毒的處理，此處簡化
        //parse_str(file_get_contents('php://input'), $data);
        $data = $this->input->input_stream();

        // 行為分類
        switch ($method) {
            case 'POST':
                // 帳號不分大小寫
                $data['account'] = strtolower($data['account']);

                // 性別格式轉換
                $data['gender'] = intval($data['gender']);

                $this->form_validation->reset_validation();

                //載入資料庫
                $this->load->model('Account_info_model');
                $this->form_validation->set_rules(
                    'account', '帳號',
                    'required|alpha_numeric|min_length[5]|max_length[15]|is_unique[account_info.account]', array(
                        'is_unique' => 'This %s already exists.',
                    ));

                $this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[30]');
                $this->form_validation->set_rules('gender', 'Gender', 'required');
                $this->form_validation->set_rules('birth', 'Birth', 'required|callback_valid_date');
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

                if ($this->form_validation->run() == false) {
                    http_response_code(400);
                    echo $this->form_validation->error_string();
                } else {
                    // 新增一筆資料
                    $this->_create($data);
                }

                break;
            case 'GET':
                // $data = $this->input->get();
                if (empty($id)) {
                    // 讀取全部資料
                    $this->_list();
                } else {
                    // 讀取一筆資料
                    $this->_read($id);
                }
                break;

            case 'PATCH':
            case 'PUT':
                // 帳號不分大小寫
                $data['account'] = strtolower($data['account']);

                // 性別格式轉換
                $data['gender'] = intval($data['gender']);

                $this->form_validation->set_data($data);

                //載入資料庫
                $this->load->model('Account_info_model');

                $old_data = $this->Account_info_model->getBy(['id' => $id])[0];

                if ($old_data['account'] != $data['account']) {
                    $this->form_validation->set_rules(
                        'account', '帳號',
                        'required|alpha_numeric|min_length[5]|max_length[15]|is_unique[account_info.account]', array(
                            'is_unique' => 'This %s already exists.',
                        ));
                }
                $this->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[30]');
                $this->form_validation->set_rules('gender', 'Gender', 'required');
                $this->form_validation->set_rules('birth', 'Birth', 'required|callback_valid_date');
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

                if ($this->form_validation->run() === false) {
                    http_response_code(400);
                    echo $this->form_validation->error_string();
                } else {
                    // 更新一筆資料
                    $this->_update($data, $id);
                }

                break;
            case 'DELETE':
                if (empty($id) && empty($data['id'])) {

                    // 錯誤
                    http_response_code(404);
                    echo 'No Delete ID';
                    exit;
                } elseif (!empty($id)) {
                    //刪除一筆資料
                    $this->_delete($id);
                } elseif (empty($id) && count(array($data['id']))) {
                    //刪除多筆資料

                    $this->_delete($data['id']);
                }
                break;
        }
    }

    /**
     * 讀取全部
     *
     * @return array
     */
    protected function _list()
    {
        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的get函式取得資料
            $data = $this->Account_info_model->get();

            // 輸出JSON
            echo json_encode(['code' => 200, 'data' => $data]);
        } catch (Exception $e) {
            // http_response_code(400);
            // echo json_encode(['code' => 400, 'message' => $e]);
            return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['code' => 400, 'message' => $e->getMessage()]));
        }
    }

    /**
     * 讀取一筆
     *
     * @param  int     $id 目標資料id
     * @return array
     */
    protected function _read($id)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式新增資料
            $data = $this->Account_info_model->getBy(['id' => $id]);

            // 輸出JSON
            echo json_encode(['code' => 200, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'message' => $e]);
        }
    }

    /**
     * 新增一筆
     *
     * @param  array   $data
     * @return array
     */
    protected function _create($data)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式新增資料
            $this->Account_info_model->post($data);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '帳號：' . $data['account'] . '新增成功!', 'data' => $data]);
        } catch (Exception $e) {

            return $this->output->set_content_type('application/json')->set_status_header(400)->set_output(json_encode(['code' => 400, 'message' => $e->getMessage()]));
        }
    }

    /**
     * 更新一筆
     *
     * @param  array   $data 資料內容
     * @return array
     */
    protected function _update($data, $id)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式修改資料
            $this->Account_info_model->put($data, $id);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '帳號：' . $data['account'] . '修改成功!', 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'message' => $e]);
        }
    }

    /**
     * 刪除一筆或多筆
     *
     * @param  int|array $id 目標資料id
     * @return string
     */
    protected function _delete($id)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式修改資料
            $this->Account_info_model->delete($id);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '刪除成功!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'message' => $e]);
        }
    }

}
