<?php
defined('BASEPATH') or exit('No direct script access allowed');
use marshung\io\IO;

class Account_info extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

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

        // 載入account_info_component
        $this->load->library('account_info_component');

        // 行為分類
        switch ($method) {
            case 'POST':
                // 資料格式轉換
                $this->account_info_component->transData($data);

                // 新增一筆資料
                $this->_create($data);
                break;
            case 'GET':
                $formData = $this->input->get();
                if (empty($id)) {
                    // 讀取全部資料
                    $this->_list($formData);

                } else {
                    // 讀取一筆資料
                    $this->_read($id);
                }
                break;

            case 'PATCH':
            case 'PUT':
                //格式轉換
                $this->account_info_component->transData($data);

                // 更新一筆資料
                $this->_update($data, $id);
                break;
            case 'DELETE':
                if (empty($id) && empty($data['id'])) {
                    // 錯誤
                    http_response_code(404);
                    echo json_encode(['code' => 400, 'message' => 'No Delete ID']);
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
     * 匯出
     *
     * @return string
     */
    public function export()
    {
        // 取得前端傳遞的參數
        $formData = $this->input->get();

        $option = [];

        // 載入account_info_component
        $this->load->library('account_info_component');
        
        // 參數整理，直接丟$formData
        $this->account_info_component->setOption($option,
            $formData['condition'],
            $formData['search'],
            $formData['order_by'],
            $formData['order_by_method'],
            $formData['length'],
            $formData['start']);

        // 載入帳戶資料庫
        $this->load->model('Account_info_model');

        // 使用Account_info_model中的getBy函式依照參數搜尋條件，取得符合條件的帳戶資料
        $datas = $this->Account_info_model->getBy('id, account, name, gender, birth, email, note, city_id, c_id', $option);

        // 執行匯出
        return $this->account_info_component->exportConfig($datas);

    }

    /**
     * 匯入
     *
     * @return string
     */
    public function import()
    {

        try {
            // IO物件建構
            $io = new IO();

            // 匯入處理 - 取得匯入資料
            $datas = $io->import($builder = 'Excel', $fileArgu = 'fileupload');

            // 載入account_info_component
            $this->load->library('account_info_component');

            // 增加gump驗證器
            $this->account_info_component->addGumpValidator();

            // 載入資料庫
            $this->load->database();

            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的getBy函式取的所有id
            $id_list = array_column($this->Account_info_model->getBy('id'), 'id');

            // 批次新增陣列
            $postBatchList = [];
            // 批次修改陣列
            $putBatchList = [];

            // 逐一讀取匯入的資料，分類成兩個陣列，一個為批次新增，另一個為批次修改
            foreach ($datas as $index => $data) {

                // 若匯入的id有在資料庫裡，則進行修改。反之則進行新增
                if (in_array($data['id'], $id_list)) {
                    // 驗證資料
                    $this->account_info_component->validAccountGump($data, 'PUT');

                    // 加入修改陣列
                    $putBatchList[] = $data;
                } else {
                    // 驗證資料
                    $this->account_info_component->validAccountGump($data, 'POST');

                    // 加入新增陣列
                    $postBatchList[] = $data;
                }

            }

            // 交易開始
            $this->db->trans_start();

            // 執行批次新增
            if ($postBatchList) {
                $this->Account_info_model->postBatch($postBatchList);
            }

            // 執行批次修改
            if ($putBatchList) {
                $this->Account_info_model->putBatch($putBatchList);
            }

            // 交易結束
            $this->db->trans_complete();

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '匯入成功!']);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 讀取具有條件的資料，含分頁功能
     *
     * @return string
     */
    protected function _list($formData)
    {
        try {
            // 載入帳戶資料庫
            $this->load->model('Account_info_model');

            // 載入account_info_component
            $this->load->library('account_info_component');

            // 將前端傳遞參數進行整理，先處理欄位查詢跟關鍵字
            $option = [];
            $this->account_info_component->setOption($option, $formData['condition'], $formData['search']['value']);

            // 依條件搜尋後的筆數
            $filteredCount = count($this->Account_info_model->getBy('*', $option));

            // 原始資料筆數
            $allCount = count($this->Account_info_model->getBy('*'));

            // 將前端傳遞參數進行整理，排序、關鍵字、每頁顯示筆數、資料起始索引
            $this->account_info_component->setOption($option,
                $formData['condition'],
                $formData['search']['value'],
                $formData['columns'][$formData['order'][0]['column']]['data'],
                $formData['order'][0]['dir'],
                $formData['length'],
                $formData['start']);

            // 整理成DataTable的資料型態
            $data['draw'] = $formData['draw'];
            $data['recordsTotal'] = $allCount;
            $data['recordsFiltered'] = $filteredCount;

            // 註解
            $accountList = $this->Account_info_model->getBy('id, account, name, gender, birth, email, note, city_id, c_id', $option);

            // 取得對公司對映表
            $companyMap = $this->account_info_component->companyMap();

            // 取得縣市對映表
            $cityMap = $this->account_info_component->cityMap();

            foreach ($accountList as $index => &$account) {
                // 增加公司名稱，和縣市名稱欄位
                $account['c_name'] = $companyMap[$account['c_id']]['c_name'] ?? '';
                $account['city_name'] = $cityMap[$account['city_id']]['city_name'] ?? '';
            }

            $data['data'] = $accountList;

            // 輸出JSON
            echo json_encode($data);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 讀取一筆
     *
     * @param  int      $id 目標資料id
     * @return string
     */
    protected function _read($id)
    {

        try {
            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');
            // 載入account_info_component
            $this->load->library('account_info_component');

            // 驗證主鍵id是否存在且有效
            $this->account_info_component->accountIdValidator($id);

            // 使用Account_info_model中的getBy函式查詢資料，利用主鍵查詢，得到一筆帳戶資料
            $option['condition']['id'] = $id;
            $account = $this->Account_info_model->getBy('id, account, name, gender, birth, email, note, city_id, c_id', $option)[0];

            // 取得對公司對映表
            $companyMap = $this->account_info_component->companyMap();

            // 取得縣市對映表
            $cityMap = $this->account_info_component->cityMap();

            // 增加公司名稱，和縣市名稱欄位
            $account['c_name'] = $companyMap[$account['c_id']]['c_name'] ?? '';
            $account['city_name'] = $cityMap[$account['city_id']]['city_name'] ?? '';

            // 輸出JSON
            echo json_encode(['code' => 200, 'data' => $account]);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 新增一筆
     *
     * @param  array    $data
     * @return string
     */
    protected function _create($data)
    {

        try {
            // 載入account_info_component
            $this->load->library('account_info_component');

            // 增加gump驗證器
            $this->account_info_component->addGumpValidator();
            // 驗證資料
            $this->account_info_component->validAccountGump($data, 'POST');

            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的post函式新增資料
            $this->Account_info_model->post($data);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '帳號：' . $data['account'] . '新增成功!']);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 更新一筆
     *
     * @param  array    $data 資料內容
     * @param  int      $id   主鍵
     * @return string
     */
    protected function _update($data, $id)
    {

        try {

            // 載入account_info_component
            $this->load->library('account_info_component');

            // 驗證主鍵id是否存在且有效
            $this->account_info_component->accountIdValidator($id);

            // 因前端請求參數未有id，這裡的id是在url裡，因此在$data新增id資訊，以便丟進model
            $data['id'] = $id;

            // 增加gump驗證器
            $this->account_info_component->addGumpValidator();
            // 驗證資料
            $this->account_info_component->validAccountGump($data, 'PUT');

            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的put函式修改資料
            $this->Account_info_model->put($data, $id);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '帳號：' . $data['account'] . '修改成功!']);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
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
            // 載入account_info_component
            $this->load->library('account_info_component');

            // 若id為整數則轉成陣列
            $id = (array) $id;

            // 驗證主鍵id是否存在且有效
            $this->account_info_component->accountIdValidator($id);

            // 載入帳號資訊資料庫
            $this->load->model('Account_info_model');

            // 使用Account_info_model中的delete函式刪除資料
            $this->Account_info_model->delete($id);

            // 輸出JSON
            echo json_encode(['code' => 200, 'message' => '刪除成功!']);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 查詢公司列表
     *
     *
     * @return string
     */
    public function getCompany()
    {

        try {
            // 載入account_info_component
            $this->load->library('account_info_component');

            // 取得公司列表
            $companyList = $this->account_info_component->companyList();

            // 輸出JSON
            echo json_encode(['code' => 200, 'data' => $companyList]);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 查詢縣市列表
     *
     *
     * @return string
     */
    public function getCity()
    {

        try {
            // 載入account_info_component
            $this->load->library('account_info_component');

            // 取得縣市列表
            $cityList = $this->account_info_component->cityList();

            // 輸出JSON
            echo json_encode(['code' => 200, 'data' => $cityList]);
        } catch (Exception $e) {
            // 錯誤
            http_response_code(400);

            // 輸出JSON
            echo json_encode(['code' => 400, 'message' => $e->getMessage()]);
        }
    }

}
