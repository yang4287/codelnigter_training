<?php
defined('BASEPATH') or exit('No direct script access allowed');

use app\libraries\NueipLibrary;
use marshung\io\IO;

class Account_info_component extends NueipLibrary
{

    /**
     * @param $params
     */
    public function __construct()
    {

        $this->CI = &get_instance();

    }

    /**
     * 驗證帳戶資料
     *
     * @param    array  $data   帳戶資料
     * @param    string $method 請求方法
     * @return
     */
    public function validAccount($data, $method)
    {
        // 載入驗證相關套件
        $this->CI->load->helper('url');
        $this->CI->load->helper('form');
        $this->CI->load->library('form_validation');

        // 重設驗證資訊
        $this->CI->form_validation->reset_validation();

        // 設定要驗證的資料
        $this->CI->form_validation->set_data($data);

        // 驗證規則
        $this->CI->form_validation->set_rules('name', 'Name', 'required|min_length[1]|max_length[30]');
        $this->CI->form_validation->set_rules('gender', 'Gender', 'required');
        $this->CI->form_validation->set_rules('birth', 'Birth', 'required|valid_date', array(
            'valid_date' => 'The %s field can not be the valid date.',
        ));
        $this->CI->form_validation->set_rules('email', 'Email', 'required');
        $this->CI->form_validation->set_rules('city_id', 'City', 'required');
        $this->CI->form_validation->set_rules('c_id', 'Company', 'required');

        //載入資料庫，使用is_unique需載入資料庫
        $this->CI->load->model('Account_info_model');

        //若是新增，需驗證帳號是否存在。若是更新，則先檢查帳號有無變動，有變動再驗證帳號是否存在
        if ($method == 'POST') {
            $this->CI->form_validation->set_rules(
                'account', '帳號',
                'required|alpha_numeric|min_length[5]|max_length[15]|is_unique[account_info.account]', array(
                    'is_unique' => 'This %s already exists.',
                ));
        } else {
            $option['condition']['id'] = $data['id'];
            $old_data = $this->CI->Account_info_model->getBy('*', $option)[0];

            if ($old_data['account'] != $data['account']) {
                $this->CI->form_validation->set_rules(
                    'account', '帳號',
                    'required|alpha_numeric|min_length[5]|max_length[15]|is_unique[account_info.account]', array(
                        'is_unique' => 'This %s already exists.',
                    ));
            }
        }

        //執行驗證規則，失敗則拋出驗證錯誤訊息
        if ($this->CI->form_validation->run() == false) {
            throw new Exception($this->CI->form_validation->error_string());
        }

    }
    /**
     * 驗證帳戶主鍵是否存在且有效
     * 
     * @param    array  $id  主鍵，陣列是因批次刪除有id陣列
     * @return
     */
    public function accountIdValidator($id)
    {
        // 驗證規則列表
        $rulesList = [
            'id' => 'required|valid_account_id',
        ];

        // 載入資料庫，建立is_unique驗證器需載入資料庫
        $this->CI->load->model('Account_info_model');

        // 建立valid_account_id驗證器，檢查主鍵是否存在且有效
        GUMP::add_validator("valid_account_id", function ($field, array $input, array $params, $value) {
            // 取得主鍵
            $option['condition']['id'] = $input['id'];
            // 驗證該主鍵是否查詢得到存在且有效資料，回傳查詢符合數量是否等於查詢id數量
            return count($this->CI->Account_info_model->getBy('id',$option)) == count($input['id']) ;
        }, 'The {field} field not exists');

        //建立驗證物件
        $gump = new GUMP();

        // 執行驗證資料與規則
        $gump->validate(['id'=>(array) $id], $rulesList);
        
        // 驗證失敗則拋出驗證錯誤訊息
        if ($gump->get_readable_errors()) {
            // 取得錯誤訊息，此為陣列型態
            $errors = $gump->get_readable_errors();
            $errmsg = '';
            foreach ($errors as $key => $value) {
                // 將錯誤訊息儲存成字串
                $errmsg .= $value . '</br>';
            }
            throw new Exception($errmsg);
        }

    }

    /**
     * 增加Gump驗證規則
     *
     * @return
     */
    public function addGumpValidator()
    {
        // 載入資料庫，建立is_unique驗證器需載入資料庫
        $this->CI->load->model('Account_info_model');

        // 建立account_is_unique驗證器
        GUMP::add_validator("account_is_unique", function ($field, array $input, array $params, $value) {
            // 從Account_info_model使用getAccountCounts涵式取得該帳號數量(包含被軟刪除的帳號)
            return $this->CI->Account_info_model->getAccountCounts($input['account']) == 0;
        }, 'The  {field}  already exists.');

        // 建立valid_date驗證器，有限制日期為今天以前
        GUMP::add_validator("valid_date", function ($field, array $input, array $params, $value) {
            $today = date('Y-m-d');
            $d = DateTime::createFromFormat('Y-m-d', $input['birth']);
            // 驗證日期是否為合理日期，以及小於等於今天日期
            return ($d && $d->format('Y-m-d') === $input['birth'] && strtotime($d->format('Y-m-d')) <= strtotime($today));
        }, 'The {field} field can not be the valid date，日期必須小於等於今天');

       
    }

    
    /**
     * 使用gump驗證帳戶資料
     *
     * @param    array  $data   帳戶資料
     * @param    string $method 請求方法
     * @return
     */
    public function validAccountGump($data, $method)
    {
        

        // 驗證規則列表
        $rulesList = [
            'name' => 'required|max_len,30|min_len,1',
            'gender' => 'required',
            'birth' => 'required|valid_date',
            'email' => 'required|valid_email',
            'city_id' => 'required',
            'c_id' => 'required',
        ];

        //若是新增，需驗證帳號是否存在。若是更新，則先檢查帳號有無變動，有變動再驗證帳號是否存在
        if ($method == 'POST') {
            // 將帳號驗證規則加入驗證規則列表
            $rulesList['account'] = 'required|alpha_numeric|min_len,5|max_len,15|account_is_unique';
        } elseif ($method == 'PUT') {
            // 取得主鍵
            $option['condition']['id'] = $data['id'];
            // 依主鍵查詢舊有資料
            $old_data = $this->CI->Account_info_model->getBy('*', $option)[0];

            if ($old_data['account'] != $data['account']) {
                // 將帳號驗證規則加入驗證規則列表
                $rulesList['account'] = 'required|alpha_numeric|min_len,5|max_len,15|account_is_unique';
            }
        }

        //建立驗證物件
        $gump = new GUMP();

        // 執行驗證資料與規則
        $gump->validate($data, $rulesList);

        // 驗證失敗則拋出驗證錯誤訊息
        if ($gump->get_readable_errors()) {
            // 取得錯誤訊息，此為陣列型態
            $errors = $gump->get_readable_errors();
            $errmsg = '';
            foreach ($errors as $key => $value) {
                // 將錯誤訊息儲存成字串
                $errmsg .= $value . '</br>';
            }
            throw new Exception($errmsg);
        }
    }

    /**
     * 轉換資料型態
     *
     * @param  array $data
     * @return array $data
     */
    public function transData(&$data)
    {
        // 帳號不分大小寫
        $data['account'] = strtolower($data['account']);

        // 確保性別格式為整數
        $data['gender'] = intval($data['gender']);

        return $data;
    }

    /**
     * 整理搜尋條件
     *
     * 格式：
     * $option = [
     *      'condition' => ['欄位名' => '欄位值string/int/array'],
     *      'search' => '關鍵字string',
     *      'offset' => '每頁顯示資料筆數int',
     *      'row_index' => '從第幾筆資料開始int',
     *      'order_by' => [
     *              'column' => '欄位string',
     *              'method' => '升冪/降冪string'],
     * ];
     * @param  array   $option
     * @param  array   $condition     欄位搜尋
     * @param  string  $search        搜尋關鍵字
     * @param  string  $orderByColumn 排序欄位名稱
     * @param  string  $orderByMethod 排序方法(升冪/降冪)
     * @param  integer $offest        每頁顯示數量
     * @param  integer $rowIndex      該頁第一筆資料列起始索引
     * @return array   $option
     */
    public function setOption(&$option, $condition = null, $search = null, $orderByColumn = null, $orderByMethod = null, $offest = null, $rowIndex = null)
    {
        // 若還沒設定排序且有傳遞排序資訊時，更新排序資訊
        if (!isset($option['order_by']) && !is_null($orderByColumn) && !is_null($orderByMethod)) {
            $option['order_by']['column'] = $orderByColumn;
            $option['order_by']['method'] = $orderByMethod;
        }

        // 若還沒設定搜尋關鍵字有傳遞搜尋關鍵字資訊時，更新搜尋關鍵字
        if (!isset($option['search']) && !is_null($search)) {
            $option['search'] = $search;
        }

        // 若還沒設定頁數且有傳遞頁數資訊時，更新頁數資訊
        if (!isset($option['offset']) && !isset($option['row_index']) && !is_null($offest) && !is_null($rowIndex)) {
            $option['offset'] = $offest;
            $option['row_index'] = $rowIndex;
        }

        // 依照欄位搜尋
        // 若還沒設定依欄位搜尋條件且有傳遞該資訊時，更新欄位搜尋條件
        if (!isset($option['condition']) && !is_null($condition) && $condition != '') {

            // 避免資料狀態欄位在搜尋條件中
            unset($option['condition']['date_create']);
            unset($option['condition']['user_create']);
            unset($option['condition']['date_update']);
            unset($option['condition']['user_update']);
            unset($option['condition']['date_delete']);
            unset($option['condition']['user_delete']);
            unset($option['condition']['rec_status']);

            // 過濾欄位為空白的搜尋條件
            foreach ($condition as $key => $value) {
                if ($value != "") {
                    $option['condition'][$key] = $value;
                }

            }
        }

        return $option;
    }

    /**
     * 匯出
     *
     * @return string
     */
    public function exportConfig($datas)
    {

        // 結構定義-簡易模式
        $defined = array(
            'id' => '流水號',
            'account' => '帳號',
            'name' => '姓名',
            'gender' => '性別',
            'birth' => '出生年月日',
            'email' => '信箱',
            'note' => '備註',
            'city_id' => '居住縣市',
            'c_id' => '公司',
        );

        // IO物件建構
        $io = new IO();

        // 手動建構相關物件
        $io->setConfig()
            ->setBuilder()
            ->setStyle();

        // 載入外部定義
        $conf = $io->getConfig()
            ->setTitle($defined)
            ->setContent($defined);

        // 建構縣市對映表
        $cityMap = $this->cityMap();
        foreach ($cityMap as $city_id => $item) {
            $cityConfig[] = array(
                'value' => $city_id,
                'text' => $item['city_name'],
            );
        }

        // 建構公司對映表
        $companyMap = $this->companyMap();
        foreach ($companyMap as $c_id => $item) {
            $companyConfig[] = array(
                'value' => $c_id,
                'text' => $item['c_name'],
            );
        }

        // 建構外部對映表
        $listMap = array(
            'gender' => array(
                array(
                    'value' => 0,
                    'text' => '男',
                ),
                array(
                    'value' => 1,
                    'text' => '女',
                ),
            ),
            'city_id' => $cityConfig,
            'c_id' => $companyConfig,
        );

        // 載入外部對映表
        $conf->setList($listMap);

        // 必要欄位設定 - 提供讀取資料時驗証用 - 有設定，且必要欄位有無資料者，跳出 - 因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列，導致在讀取excel時有讀不完狀況。
        $conf->setOption([
            'account', 'name', 'gender', 'birth', 'email', 'city_id', 'c_id',
        ], 'requiredField');

        // 匯出處理 - 建構匯出資料 - 手動處理
        return $io->setData($datas)->exportBuilder();
    }

    /**
     * 建構公司對映表
     *
     * @return array
     */
    public function companyMap()
    {

        // 載入公司資訊資料庫
        $this->load->model('Company_info_model');

        // 取得所有公司資料
        $companyList = $this->Company_info_model->getBy('c_id, c_name');

        // 建構對映表
        $companyMap = array_column($companyList, null, 'c_id');

        return $companyMap;

    }

    /**
     * 建構縣市對映表
     *
     * @return array
     */
    public function cityMap()
    {

        // 載入縣市資訊資料庫
        $this->load->model('City_model');

        // 取得所有縣市資料
        $cityList = $this->City_model->getBy('city_id, city_name');

        // 建構對映表
        $cityMap = array_column($cityList, null, 'city_id');

        return $cityMap;

    }

    /**
     * 取得公司清單
     *
     * @return array
     */
    public function companyList()
    {

        // 載入公司資訊資料庫
        $this->load->model('Company_info_model');

        // 取得所有公司資料
        $cityList = $this->Company_info_model->getBy('c_id, c_name');

        return $cityList;

    }

    /**
     * 取得縣市清單
     *
     * @return array
     */
    public function cityList()
    {

        // 載入縣市資訊資料庫
        $this->load->model('City_model');

        // 取得所有縣市資料
        $cityList = $this->City_model->getBy('city_id, city_name');

        return $cityList;

    }
}
