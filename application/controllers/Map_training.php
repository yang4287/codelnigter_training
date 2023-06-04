<?php
defined('BASEPATH') or exit('No direct script access allowed');
use nueip\helpers\ArrayHelper;

class Map_training extends CI_Controller
{

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->load->view('mapTraining');
    }

    /**
     * 取得指定部門的所有員工資料
     *
     */
    public function getDeptAccount($d_id)
    {
        

        // 載入帳號資訊資料庫
        $this->load->model('Account_info_model');

        $option['contion']['d_id'] = $d_id;

        return $this->Account_info_model->get( 'id, account, name, gender, birth, email, note, d_id',$option);
        
    }

    /**
     * 建構部門對映表
     *
     * {"2":{
     *       "d_code":"d0047",
     *       "d_name":"\u90e8\u95803266",
     *       "d_level":"\u7d44",
     *       "date_start":"2020-01-01",
     *       "date_end":"9999-12-31",
     *       "remark":"\u90e8\u958046"
     *      },
     * "3":{
     *       "d_code":"d0047",
     *       "d_name":"\u90e8\u95803266",
     *       "d_level":"\u7d44",
     *       "date_start":"2020-01-01",
     *       "date_end":"9999-12-31",
     *       "remark":"\u90e8\u958046"
     *      }
     * }
     *
     */
    public function deptMap()
    {
        


        // 載入部門資訊資料庫
        $this->load->model('Dept_info_model');

        $deptList = $this->Dept_info_model->getBy( 'd_id, d_code, d_name, d_level, date_start, date_end, remark');
        $deptMap = array_column($deptList, null, 'd_id');

        return $deptMap;

    }

    /**
     * 員工資料關聯部門資料
     *
     */
    public function get()
    {
        $this->load->library('account_info_component');
        $this->account_info_component->getAccountMapCompany();

    }

}
