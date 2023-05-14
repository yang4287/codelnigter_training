<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_info extends CI_Controller
{

    public function index()
    {
        $this->load->view('accountInfo');
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

                $this->load->helper(array('form', 'url'));

                $this->load->library('form_validation');
                $this->form_validation->set_rules(
                    'account', 'Account',
                    'required|min_length[5]|max_length[15]|is_unique[account_info.account]',
                    array(
                            'required'      => 'You have not provided %s.',
                            'is_unique'     => 'This %s already exists.'
                    )
            );
                
                $this->form_validation->set_rules('name', 'Name', 'required');
                $this->form_validation->set_rules('gender', 'Gender', 'required');
                $this->form_validation->set_rules('birth', 'Birth', 'required');
                $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
                if ($this->form_validation->run() == FALSE)
                {
                    http_response_code(400);
                    echo '格式有誤!
                    ';
                }
                else
                {
                        // 新增一筆資料
                            $this->_create($data);
                }
                
                break;
            case 'GET':
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
                // 更新一筆資料
                $this->_update($data, $id);
                break;
            case 'DELETE':
                if (empty($id)) {
                    // 錯誤
                    http_response_code(404);
                    echo 'No Delete ID';
                    exit;
                } else {
                    //刪除一筆資料
                    $this->_delete($id);
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
     * @param int $id 目標資料id
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
     * @param array $data 資料內容
     * @return array
     */
    protected function _update($data,$id)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式修改資料
            $this->Account_info_model->put($data,$id);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '帳號：' . $data['account'] . '修改成功!','data'=>$data]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['code' => 400, 'message' => $e]);
        }
    }

    /**
     * 刪除一筆
     *
     * @param int $id 目標資料id
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
