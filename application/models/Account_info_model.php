<?php

/**
 * VIP顧客資料管理Model
 *
 * 提供通用函式 新增、讀取、更新、刪除、批次讀取、批次新增、批次更新、批次刪除 示範
 *
 * @author Yuhui.Yang 2023-05-12
 */
class Account_info_model extends CI_Model
{

    /**
     * 資料表名稱
     */
    protected $table = "account_info";

    /**
     * 欄位資料
     */
    protected $tableColumns = [
        'id',
        'account',
        'name',
        'gender',
        'birth',
        'email',
        'note',
    ];

    public function __construct()
    {
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    /**
     * 取得所有資料
     *
     *
     * @param  string  $col 輸出欄位
     * @return array
     */
    public function get($col = '*')
    {
        return $this->db->select($col)->from($this->table)->get()->result_array();
    }

    /**
     * 取得資料 - 從帳號
     *
     *
     * @param  int     $account 目標帳號資料
     * @param  string  $col     輸出欄位
     * @return array
     */
    // public function get($account, $col = '*')
    // {
    //     return $this->db->select($col)->from($this->table)->where('d_id', $d_id)->where('rec_status', '1')->get()->result_array();
    // }

    /**
     * 取得資料 - 從查詢條件
     *
     * 本函式只能取出 rec_status==1 的資料
     *
     * 格式：
     * $conditions = [
     *      '欄位名' => '欄位值string/int/array',
     * ];
     *
     * @param  array    $conditions 查詢條件
     * @param  string   $col        輸出欄位
     * @return array
     */
    public function getBy($conditions = [], $col = '*')
    {
        // 查詢建構
        $query = $this->db->select($col)->from($this->table);

        // 加入查詢條件
        foreach ($conditions as $key => $where) {
            if (is_array($where)) {
                // 加入陣列查詢條件
                $query->where_in($key, $where);
            } else {
                // 加入單一查詢條件
                $query->where($key, $where);
            }
        }

        // 執行查詢、取回資料並回傳
        return $query->get()->result_array();
    }

    /**
     * 新增資料
     *
     * @param  array $data 帳戶資料
     * @return int
     */
    public function post($data)
    {
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 0
        return $this->db->insert($this->table, $data);
    }

    /*
     * 更新資料 - 從主鍵
     *
     * @param  array $data 部門資料
     * @param  array $id 部門資料
     * @return int
     */
    public function put($data,$id)
    {
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        

        // 檢查有無主鍵
        if ($id) {
            // 取出主鍵值並移除$data中主鍵欄位
            

           
            return $this->db->where('id', $id)->update($this->table, $data);
        } else {
            // 報錯-沒有主鍵欄位
            throw new Exception('沒有主鍵欄位: id', 400);
        }

        
    }

    /*
     * 刪除資料 - 從主鍵
     *
     * @param  array|int $d_id        欲刪除的主鍵值
     * @param  bool      $forceDelete 是否強制刪除 false時為軟刪除
     * @return bool
     */
    public function delete($id)
    {
        $id = (array) $id;

        // 刪除條件
        $this->db->where_in('id', $id);

        
        // 直接刪除 - CI SQL Builder有限定需有where才可以執行delete
         return $this->db->delete($this->table);
        
    }

    // /**
    //  * 批次寫入資料 - 未完成，請補完
    //  *
    //  * 整批處理時，有一筆錯誤，整批都不可以處理
    //  *
    //  * @param  [type] $datas
    //  * @return void
    //  */
    // public function postBatch($datas)
    // {
    //     foreach ($datas as $key => $data) {
    //         // 過濾可用欄位資料
    //         $data = array_intersect_key($data, array_flip($this->tableColumns));

    //         // 移除主鍵欄位 - 新增時不帶入主鍵值，以便主鍵由sql自行增加
    //         unset($data['d_id']);

    //         // 寫入 date_create, user_create(未知，暫用0), rec_status
    //         $datas[$key]['date_create'] = date('Y-m-d H:i:s');
    //         $datas[$key]['user_create'] = 0;
    //         $datas[$key]['rec_status'] = '1';

    //         // 移除 date_update, user_update, date_delete, user_delete
    //         unset($datas[$key]['date_update']);
    //         unset($datas[$key]['user_update']);
    //         unset($datas[$key]['date_delete']);
    //         unset($datas[$key]['user_delete']);

    //     }
    //     // 批次寫入資料表
    //     $res = $this->db->insert_batch($this->table, $datas);

    //     // 成功時回傳插入列數，失敗時回傳 FALSE
    //     return $res ? $this->db->affected_rows($datas) : false;
    // }

    /*
     * 批次更新資料 - 未完成，請補完
     *
     * 整批處理時，有一筆錯誤，整批都不可以處理
     *
     * @param  [type] $datas
     * @return void
     */
    // public function putBatch($datas)
    // {
    //     $res = 0;
    //     foreach ($datas as $key => $data) {
    //         // 過濾可用欄位資料
    //         $data = array_intersect_key($data, array_flip($this->tableColumns));
    //         // 檢查有無主鍵
    //         if (isset($data['d_id'])) {
    //             // 取出主鍵值並移除$data中主鍵欄位
    //             $d_id = $data['d_id'];
    //             // unset($datas[$key]['d_id']);

    //             // 寫入 date_update, user_update(未知，暫用0)
    //             $datas[$key]['date_update'] = date('Y-m-d H:i:s');
    //             $datas[$key]['user_update'] = 0;

    //             // 移除 date_create, user_create, date_delete, user_delete, rec_status
    //             unset($datas[$key]['date_create']);
    //             unset($datas[$key]['user_create']);
    //             unset($datas[$key]['date_delete']);
    //             unset($datas[$key]['user_delete']);
    //             unset($datas[$key]['rec_status']);
    //         } else {
    //             // 報錯-沒有主鍵欄位
    //             throw new Exception('沒有主鍵欄位: d_id', 400);
    //         }

    //     }

    //     // 批次更新資料 - 成功時回傳更新列數，失敗時回傳 FALSE
    //     $res = $this->db->update_batch($this->table, $datas, 'd_id') ? $this->db->affected_rows($datas) : false;
    //     return $res;

    // }
}