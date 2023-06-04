<?php

/**
 * 縣市資料管理Model
 *
 * 提供通用函式 讀取
 *
 * @author Yuhui.Yang 2023-05-29
 */
class City_model extends CI_Model
{

    /**
     * 資料表名稱
     */
    protected $table = "city";

    /**
     * 欄位資料
     */
    protected $tableColumns = [
        'city_id',
        'city_code',
        'city_name',
    ];

    public function __construct()
    {
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    /**
     * 取得資料 - 從主鍵
     *
     *
     *
     * @param  int    $city_id 目標主鍵資料
     * @param  string $col     輸出欄位
     * @return array  $result 回傳結果
     */
    public function get($city_id, $col = '*')
    {
        // 建構查詢語句
        $result = $this->db->select($col)->from($this->table)->where('city_id', $city_id)->get()->result_array();

        // 執行查詢、取回資料並回傳，執行失敗則拋出錯誤訊息
        if (!($result)) {
            throw new Exception('資料庫異常');
        }
        return $result;
    }

    /**
     * 取得資料 - 從查詢條件
     *
     * 格式：
     * $conditions = [
     *      '欄位名' => '欄位值string/int/array',
     * ];
     *
     * @param  array  $conditions 查詢條件
     * @param  string $col        輸出欄位
     * @return array  $result 回傳結果
     */
    public function getBy($col = '*', $conditions = [])
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

        // 執行查詢、取回資料並回傳，執行失敗則拋出錯誤訊息
        $result = $query->get()->result_array();
        if (!($result)) {
            throw new Exception('資料庫異常');
        }
        return $result;
    }

}
