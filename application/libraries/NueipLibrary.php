<?php
namespace app\libraries;

/**
 * NuEIP通用函式庫 - 提供共通性處理 - 繼承
 *
 * 使用： class oooo extends \app\libraries\NueipLibrary
 *
 * 統一使用習慣&系統支援：
 * 1. 在函式庫中也可以使用CI的 $this->XXX 方式取用CodeIgniter的Model,Library....
 * - 為讓Library可以使用CI的library autoload，不繼承CI_Model，而是直接取用其Magic Function: __get
 * 2. 提供權限參數$this->companyID, $this->userID
 * 3. ACL權限切換支援，提供介面函式loadACL($companyID = NULL, $userID = NULL)，以供CI loader統一切換權限
 * 4. 單例模式支援
 * 
 * @author Mars.Hung <tfaredxj@gmail.com> 2018-05-18
 *        
 */
class NueipLibrary
{

    /**
     * NuEIP登入帳號s_sn
     *
     * @var int $userID NuEIP user id
     */
    protected $userID;

    /**
     * NuEIP登入公司s_sn
     *
     * 注意：
     * 若取用此屬性，需注意單例模式下的帳轉流程，可能會取到前一家公司的companyID，建議使用 $this->companyID();
     *
     * @var int $companyID NuEIP company id
     */
    protected $companyID;

    /**
     * CI Instance
     * @var Object
     */
    protected $CI;
    
    /**
     * 對映表暫存
     *
     * @var array
     */
    protected $_map = array();

    /**
     * 單例模式
     * @var array
     */
    private static $_instance = [];
    
    public function __construct()
    {
        // Load ACL from application & init library
        $this->loadACL();
        
        // Get CI Instance
        $this->CI = & get_instance();
    }

    /**
     * CodeIgniter __get magic
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param	string	$key
     */
    public function __get($key)
    {
        // Debugging note:
        //	If you're here because you're getting an error message
        //	saying 'Undefined Property: system/core/Model.php', it's
        //	most likely a typo in your model code.
        return get_instance()->$key;
    }
    
    /**
     * *******************************************************************
     * ************************* Public Funciton *************************
     * *******************************************************************
     */
    
    /**
     * 初始化
     * 
     * 當複寫init()時，如需給定參數，可用func_get_args()、func_num_args()操作，或給定預設值init($option=[])方式複寫
     */
    public function init()
    {
        // 初始化全部對映表
        $this->_map = [];
    }

    /**
     * 單例模式
     * 
     * - 單例模式適用自行創建(new)但要共用同一個物件實體的狀況下使用
     * - 單例模式會被 權限函式轉換 $this->load->loadACL(); 處理到
     *
     * 注意：在同一個被繼承的父物件中的self::$static，在其所有子物件中是共通的 - 為給$this->load->loadACL()用
     *
     * @param string $id
     *            物件id，同一個id下的物件共用單例模式
     * @param bool $init
     *            重新初始化
     * @return $this
     */
    public static function getInstance($id = '', $init = false)
    {
        // 參數處理
        $id = empty($id) ? 'singleton' : $id;
        $className = get_called_class();
        
        // 未建立或需初始化時，才建立
        if (! isset(self::$_instance[$className][$id]) || $init) {
            self::$_instance[$className][$id] = new static();
        }
        
        return self::$_instance[$className][$id];
    }
    
    /**
     * 取得登入公司s_sn
     * 
     * @return number
     */
    public static function companyID()
    {
        return get_instance()->config->item('Company');
    }
    
    /**
     * 取得登入公司s_sn
     *
     * @return number
     */
    public static function getCsn()
    {
        return self::companyID();
    }
    
    /**
     * 取得登入人員s_sn
     *
     * @return number
     */
    public static function userID()
    {
        return get_instance()->config->item('UserNo');
    }
    
    /**
     * 取得登入人員s_sn
     *
     * @return number
     */
    public static function getUsn()
    {
        return self::userID();
    }
    
    /**
     * ****************************************************************
     * ************************* ACL Funciton *************************
     * ****************************************************************
     */
    
    /**
     * Load ACL from application
     *
     * @param int $companyID
     *            登入登公司s_sn
     * @param int $userID
     *            登入者員工s_sn
     */
    public function loadACL($companyID = NULL, $userID = NULL)
    {
        // NueipLibrary權限參數
        $this->companyID = $companyID ?? self::companyID();
        $this->userID = $userID ?? self::userID();

        // 初始化 對映表
        $this->init();

        return true;
    }
    
    /**
     * Load ACL from singleton application
     * 
     * Usage:
     * \app\libraries\NueipLibrary::loadSingletonACL($companyID, $userID);
     * 
     * @param int $companyID
     *            登入登公司s_sn
     * @param int $userID
     *            登入者員工s_sn
     */
    public static function loadSingletonACL($companyID = NULL, $userID = NULL)
    {
        // 遍歷單例物件，並變更ACL
        foreach (self::$_instance as $className => $v) {
            foreach ($v as $id => $obj) {
                $obj->loadACL($companyID, $userID);
            }
        }
        
        return true;
    }
    
    /**
     * *************************************************
     * ************** Map Access Function **************
     * *************************************************
     */
    
    /**
     * 對映表取用 - 指定對映表
     *
     * @return mixed
     */
    public function getMap()
    {
        $res = $this->_map;

        // 遍歷 傳入的參數
        foreach (func_get_args() as $argv) {
            // 指定的對映表內容不為 null
            if (isset($res[$argv])) {
                // 將 對映表 向下拉一層
                $res = $res[$argv];
            } else {
                $res = [];
                break;
            }
        }

        return $res;
    }
    
    /**
     * 取得 唯讀資料庫
     *
     * @return \CI_DB_query_builder
     */
    public static function getDbr()
    {
        // 取得現在 unix 時間戳
        $now = time();

        // 取得Codeigniter實例
        $ci = &get_instance();

        // 若未連線到唯獨資料庫
        if (!isset($ci->nueip_dbr)) {
            // 載入唯讀 db
            $ci->nueip_dbr = $ci->load->database('slavedb', true);
        } elseif (!$ci->nueip_dbr->conn_id || $now - $ci->nueip_dbr->prevTime >= 300) { // 連結失敗||連結超時
            // 關閉 & 初始化
            $ci->nueip_dbr->close();
            $ci->nueip_dbr->initialize();
        }

        // 紀錄時間
        $ci->nueip_dbr->prevTime = $now;

        // 回傳 CI_DB_query_builder
        return $ci->nueip_dbr;
    }

    /**
     * 取得 唯讀資料庫 (!!!可允許被鎖表資料庫，主要處理內部備份與BI同步資料!!!)
     *
     * @return \CI_DB_query_builder
     */
    public static function getDbrDirty()
    {
        // 取得現在 unix 時間戳
        $now = time();

        // 取得Codeigniter實例
        $ci = &get_instance();

        // 若唯獨資料庫 dirty 未連線
        if (!isset($ci->nueip_dbr_dirty)) {
            // 載入 唯讀的備份 db
            $ci->nueip_dbr_dirty = $ci->load->database('backupdb', true);
        } elseif (!$ci->nueip_dbr_dirty->conn_id || $now - $ci->nueip_dbr_dirty->prevTime >= 300) { // 連結失敗||連結超時
            // 關閉 & 初始化
            $ci->nueip_dbr_dirty->close();
            $ci->nueip_dbr_dirty->initialize();
        }

        // 紀錄時間
        $ci->nueip_dbr_dirty->prevTime = $now;

        // 回傳 CI_DB_query_builder
        return $ci->nueip_dbr_dirty;
    }

    /**
     * 重新載入資料庫
     *
     * @param mixed $params Database configuration options
     * @return void
     */
    protected static function reloadDatabase($params = '')
    {
        // 取得Codeigniter實例
        $CI = &get_instance();

        // 判斷資料庫連線
        if (isset($CI->db)) {
            // $CI->db->close();
            // 移除連線
            unset($CI->db);
        }

        // 載入資料庫
        $CI->load->database($params);
    }

    /**
     * 載入 主要資料庫 (maindb)
     *
     * @return \CI_DB_query_builder
     */
    public static function loadMainDatabase()
    {
        $CI = &get_instance();

        // 判斷是否連線到主要資料庫
        if ($CI->db->isMainDatabase ?? false) {
            return $CI->db;
        }

        // 重新載入主要資料庫
        self::reloadDatabase('maindb');
        // 已連結資料庫 true
        $CI->db->isMainDatabase = true;

        // 回傳 CI_DB_query_builder
        return $CI->db;
    }

    /**
     * 載入 唯讀資料庫 (slavedb)
     *
     * @return \CI_DB_query_builder
     */
    public static function loadSlaveDatabase()
    {
        $CI = &get_instance();

        // 判斷是否連線到唯獨資料庫 (slavedb)
        if ($CI->db->isSlaveDatabase ?? false) {
            return $CI->db;
        }

        // 重新載入唯獨資料庫 (slavedb)
        self::reloadDatabase('slavedb');
        // 已連結資料庫 true
        $CI->db->isSlaveDatabase = true;

        // 回傳 CI_DB_query_builder
        return $CI->db;
    }

    /**
     * Alias of `loadSlaveDatabase()`
     *
     * @return \CI_DB_query_builder
     */
    public static function dbrStart()
    {
        // 載入唯獨資料庫 (slavedb)
        return self::loadSlaveDatabase();
    }

    /**
     * Alias of `loadMainDatabase()`
     *
     * @return CI_DB_query_builder
     */
    public static function dbrEnd()
    {
        // 載入主要資料庫
        return self::loadMainDatabase();
    }
}