<?php

/**
 * VIP顧客資料管理Model
 *
 * 提供通用函式 新增、讀取、更新、刪除
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
        'c_id',
        'city_id',
        'date_create',
        'user_create',
        'date_update',
        'user_update',
        'date_delete',
        'user_delete',
        'rec_status',
    ];

    public function __construct()
    {
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    /**
     * 取得該帳號數量
     *
     * 只有本函式可取出 rec_status==1 or 0 的資料
     *
     * @param  string $account 帳號
     * @return int    $result 該帳號數量0/1
     */
    public function getAccountCounts($account)
    {
        // 查詢建構
        $query = $this->db->select('*')
            ->from($this->table)
            ->where('account', $account);

        // 執行查詢、取回資料並回傳
        return $query->count_all_results();

    }

    /**
     * 依條件取得資料
     *
     * 本函式只能取出 rec_status==1 的資料
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
     *
     * @param  string   $col    輸出欄位
     * @param  array    $option 篩選條件
     * @return array
     */
    public function getBy($col = '*', $option = null)
    {
        // 查詢建構
        $query = $this->db->select($col)
            ->from($this->table)
            ->where('rec_status', 1);

        // 加入查詢條件
        if (isset($option['condition']) && is_array($option['condition'])) {
            foreach ($option['condition'] as $key => $where) {
                if (is_array($where)) {
                    // 加入陣列查詢條件
                    $query->where_in($key, $where);
                } else {
                    // 加入單一查詢條件
                    $query->where($key, $where);
                }
            }
        }

        // 加入所有欄位關鍵字模糊查詢
        if (isset($option['search'])) {
            $query->group_start()
                ->like('account', $option['search'], 'both')
                ->or_like('name', $option['search'], 'both')
                ->or_like('birth', $option['search'], 'both')
                ->or_like('email', $option['search'], 'both')
                ->or_like('note', $option['search'], 'both')
                ->group_end();
        }

        // 查詢限制資料筆數，用於分頁功能
        if (isset($option['offset']) && isset($option['row_index'])) {
            $query->limit($option['offset'], $option['row_index']);
        }

        // 加入排序
        if (isset($option['order_by'])) {
            $query->order_by($option['order_by']['column'], $option['order_by']['method']);
        }

        // 執行查詢、取回資料並回傳
        return $query->get()->result_array();
    }

    /**
     * 新增資料
     *
     * @param    array $data 帳戶資料
     * @return
     */
    public function post($data)
    {
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        // 寫入 date_create, user_create(未知，暫用0), rec_status
        $data['date_create'] = date('Y-m-d H:i:s');
        $data['user_create'] = 0;
        $data['rec_status'] = 1;

        // 移除 date_update, user_update, date_delete, user_delete
        unset($data['date_update']);
        unset($data['user_update']);
        unset($data['date_delete']);
        unset($data['user_delete']);

        // 新增資料 - 寫入失敗時拋除異常，$this->db->trans_status()
        if (!($this->db->insert($this->table, $data))) {
            throw new Exception('資料庫異常');
        }
    }

    /**
     * 更新資料 - 從主鍵
     *
     * @param    array $data 帳戶資料
     * @param    array $id   主鍵
     * @return
     */
    public function put($data, $id)
    {
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        // 檢查有無主鍵
        if ($id) {
            // 寫入 date_update, user_update(未知，暫用0)
            $data['date_update'] = date('Y-m-d H:i:s');
            $data['user_update'] = 0;

            // 移除 date_create, user_create, date_delete, user_delete, rec_status
            unset($data['date_create']);
            unset($data['user_create']);
            unset($data['date_delete']);
            unset($data['user_delete']);
            unset($data['rec_status']);

            // 更新資料 - 寫入失敗時拋除異常
            if (!($this->db->where('id', $id)->update($this->table, $data))) {
                throw new Exception('資料庫異常');
            }

        } else {
            // 報錯-沒有主鍵欄位
            throw new Exception('沒有主鍵欄位: id');
        }

    }

    /**
     * 刪除資料 - 從主鍵
     * @param    array $id          欲刪除的主鍵值
     * @param    bool  $forceDelete 是否強制刪除 false時為軟刪除
     * @return
     */
    public function delete($id, $forceDelete = false)
    {
        // 刪除條件
        $this->db->where_in('id', $id);

        if ($forceDelete) {
            // 直接刪除 - CI SQL Builder有限定需有where才可以執行delete
            if (!($this->db->delete($this->table))) {
                throw new Exception('資料庫異常');
            }
        } else {
            // 標記成刪除狀態 - 本練習中無法得知操作者id，暫不處理user_delete值
            $data['date_delete'] = date('Y-m-d H:i:s');
            $data['user_delete'] = 0;
            $data['rec_status'] = 0;

            // 軟刪除資料 - 軟刪除失敗時拋除異常
            if (!($this->db->update($this->table, $data))) {
                throw new Exception('資料庫異常');
            }

        }

    }

    /**
     * 批次寫入資料
     *
     * 整批處理時，有一筆錯誤，整批都不可以處理
     *
     * @param    array $datas
     * @return
     */
    public function postBatch($datas)
    {
        foreach ($datas as $key => &$data) {
            // 過濾可用欄位資料
            $data = array_intersect_key($data, array_flip($this->tableColumns));

            // 移除主鍵欄位 - 新增時不帶入主鍵值，以便主鍵由sql自行增加
            unset($data['id']);

            // 寫入 date_create, user_create(未知，暫用0), rec_status
            $data['date_create'] = date('Y-m-d H:i:s');
            $data['user_create'] = 0;
            $data['rec_status'] = '1';

            // 移除 date_update, user_update, date_delete, user_delete
            unset($data['date_update']);
            unset($data['user_update']);
            unset($data['date_delete']);
            unset($data['user_delete']);

        }
        // 執行批次新增，失敗拋出錯誤訊息
        if (!$this->db->insert_batch($this->table, $datas)) {
            throw new Exception('資料庫異常');
        }
    }

    /**
     * 批次更新資料
     *
     * 整批處理時，有一筆錯誤，整批都不可以處理
     *
     * @param    array $datas
     * @return
     */
    public function putBatch($datas)
    {
        foreach ($datas as $key => &$data) {
            // 過濾可用欄位資料
            $data = array_intersect_key($data, array_flip($this->tableColumns));
            // 檢查有無主鍵
            if (isset($data['id'])) {

                // 寫入 date_update, user_update(未知，暫用0)
                $data['date_update'] = date('Y-m-d H:i:s');
                $data['user_update'] = 0;

                // 移除 date_create, user_create, date_delete, user_delete, rec_status
                unset($data['date_create']);
                unset($data['user_create']);
                unset($data['date_delete']);
                unset($data['user_delete']);
                unset($data['rec_status']);
            } else {
                // 報錯-沒有主鍵欄位
                throw new Exception('沒有主鍵欄位: id');
            }
        }

        // 執行批次修改，失敗拋出錯誤訊息
        if (!$this->db->update_batch($this->table, $datas, 'id')) {
            throw new Exception('資料庫異常');
        }

    }

}
