<?php
    defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>員工帳戶管理系統</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.css" />

    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>
    <script src="<?=JS_DIR;?>accountInfo/accountInfo.js"></script>

    <script src="<?=JS_DIR;?>jquery.rustaMsgBox.js"></script>

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />
</head>

<body>
    <div class="container">
        <!-- Title -->
        <div class="row">
            <div class="col-sm-12">
                <h1>員工帳戶管理系統</h1>
            </div>
        </div>


        <!-- 搜尋區塊 -->
        <div>
            <form class="searchForm" name="searchForm">
                <div class="row m-0">
                    <div class="form-group  col-md-4">
                        <label for="account">帳號</label>
                        <input type="text" name="condition[account]" class="form-control" id="account">
                    </div>
                    <div class="form-group col-4 col-md-4">
                        <label for="name">姓名</label>
                        <input type="text" name="condition[name]"  class="form-control" id="name">
                    </div>
                    <div class="form-group col-4 col-md-4">
                        <div class="radio ">
                            <label>
                                <input type="radio" name="condition[gender]" id="genderRadios1" value="0">
                                男
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="condition[gender]" id="genderRadios2" value="1">
                                女
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row m-0">
                    <div class="form-group  col-md-4">
                        <label for="birth">生日</label>
                        <input type="date" name="condition[birth]" class="form-control" id="birth">
                    </div>
                    <div class="form-group  col-md-4">
                        <label for="email">Email</label>
                        <input type="email" name="condition[email]" class="form-control" id="email">
                    </div>
                    <div class="form-group  col-md-3">
                            <label for="search">搜尋關鍵字</label>
                            <input type="text" class="form-control" id="search" name = "search[value]">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-4 col-md-3">
                        <button type="button" class="btn btn-default" id="search-btn">搜尋</button>
                    <input  class="btn btn-light"  type="reset" value="重設">
                    </div>
                </div>
            </form>
        </div>
        <br>

        <!-- 執行功能操作區塊 -->
        <div>
            <div class="row">
                <div class="col-md-4 col-xs-4" align="left">
                    <button type="button" class="btn btn-danger float-left delete-batch-btn" data-placement="right" >批次刪除</button>
                </div>
                <div class="col-md-8 col-xs-8" align="right">
                <form name="uploadForm" id="uploadForm">
                    <button type="button" class="btn btn-warning float-right add-btn">新增</button>
                    <button type="button" class="btn btn-light float-right export-btn">匯出</button>
                    <input type="file" style="display: inline" accept=".xlsx" name="fileupload" id="inputFile" required />
                    <button type="submit" id='import-btn' class="btn btn-green float-right " >匯入</button>
                </form>
                </div>
            </div>
        </div>
            <br>
            <!-- 資料表呈現區塊 -->
        <div class="" style="overflow: auto;width:100%">
            <table class="table table-striped table-bordered table-hover ctrl-table" >
                <thead>
                <tr>
                    <th>select</th>
                    <th>id</th>
                    <th>account</th>
                    <th>name</th>
                    <th>gender</th>
                    <th>birth</th>
                    <th>email</th>
                    <th>note</th>
                    <th>居住縣市</th>
                    <th>公司</th>
                    <th>編輯</th>
                    <th>刪除</th>
                </tr>
                </thead>
                </table>
            </div>
        </div>
    </div>
</html>
<!-- 編輯和新增樣板 -->
<script type="text/template" id="template-sticky-edit">
    <form  id="Form" class="form"  name="Form" >
        <div class="row m-0">
            <div class="form-group col-4 col-md-4">
                <label for="account">帳號</label>
                <input type="text" pattern="[a-zA-Z0-9]+" maxlength=15 minlength=5 required class="form-control" id="account" name="account">
            </div>
            <div class="form-group col-4 col-md-4">
                <label for="name">姓名</label>
                <input type="text" minlength="1" maxlength="30" required class="form-control" id="name" name="name">
            </div>
            <div class="form-group col-4 col-md-4">
                <label for="gender">性別</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="0">男</option>
                    <option value="1">女</option>
                </select>
            </div>
        </div>
        <div class="row m-0">
            <div class="form-group col-4 col-md-4">
                <label for="birth">生日</label>
                <input type="date" required class="form-control" id="birth" name="birth">
            </div>
            <div class="form-group col-4 col-md-4">
                <label for="email">Email</label>
                <input type="email" required class="form-control" id="email" name="email">
            </div>
            <div class="form-group col-4 col-md-4">
                <label for="note">Note</label>
                <input type="text" class="form-control" id="note" name="note">
            </div>
        </div>
        <div class="row m-0">
            <div class="form-group col-4 col-md-4">
                <label for="city">居住縣市</label>
                <select class="form-control" id="city" name="city_id" required >
                </select>
            </div>
            <div class="form-group col-4 col-md-4">
                <label for="company">公司</label>
                <select class="form-control" id="company" name="c_id" required >
                </select>
            </div>
        </div>
    </form>
</script>
