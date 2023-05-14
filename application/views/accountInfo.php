<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>寰宇國際時尚美學館</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.9/js/bootstrap-dialog.min.js"></script>

    <script src="<?= JS_DIR; ?>accountInfo/accountInfo.js"></script>
    <script src="<?= JS_DIR; ?>jquery.rustaMsgBox.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= CSS_DIR; ?>accountInfo.css">
    </script>
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />
</head>

<body>

    <div class="container">
        <!-- Title -->
        <div class="row">
            <div class="col-sm-12">
                <h1>寰宇國際時尚美學館 VIP顧客管理系統</h1>
            </div>
        </div>
        <script>

</script>
</head>
<body>


        <!-- Controll Form -->
        <div>
            <form class="form">
                <div class="row m-0">
                    <div class="form-group  col-md-4">
                        <label for="account">帳號</label>
                        <input type="text" class="form-control" id="account">
                    </div>
                    <div class="form-group col-4 col-md-4">
                        <label for="name">姓名</label>
                        <input type="text" class="form-control" id="name">
                    </div>
                    <div class="form-group col-4 col-md-4">
                        <label for="gender">性別</label>
                        <input type="text" class="form-control" id="gender">
                    </div>
                </div>
                <div class="row m-0">
                    <div class="form-group  col-md-4">
                        <label for="birth">生日</label>
                        <input type="date" class="form-control" id="birth">
                    </div>
                    <div class="form-group  col-md-4">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2" align="left">
                        <button type="submit" class="btn btn-default">搜尋

                        </button>
                    </div>
                </div>
            </form>
        </div>
        <br>
        <div>
            <div class="row">
                <div class="col-md-2 col-xs-6" align="left">



                    <button type="button" class="btn btn-danger float-left export-btn">批次刪除</button>

                </div>
                <div class="col-md-10 col-xs-6" align="right">
                    <button type="button" class="btn btn-warning float-right add-btn">新增</button>


                    <button type="button" class="btn btn-green float-right">匯入</button>


                    <button type="button" class="btn btn-light float-right export-btn">匯出</button>

                </div>
            </div>


            <br>
            <!-- Table -->
            <div style="overflow: auto;">
                <table class="table table-striped table-bordered table-hover ctrl-table" >

                </table>
            </div>

            <!-- Pagination -->
            <div class="float-right">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </div>
            <div class="floating-menu">
                <button class="btn btn-primary" id="btn-open">Open dialog</button>
            </div>
        </div>
        
    </div>



    <!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">新增</h4>
                </div>
                <div class="modal-body">
                <form class="form" name="addForm" >
                 <div class="row m-0">
                <div class="form-group col-4 col-md-4">
                    <label for="account">帳號</label>
                    <input type="text" required class="form-control" id="account" name="account">
                </div>
                <div class="form-group col-4 col-md-4">
                    <label for="name">姓名</label>
                    <input type="text" required class="form-control" id="name" name="name">
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
                        <input type="text" required class="form-control" id="note" name="note">
                    </div>
                </div>
                </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary add-btn-submit" form="addForm">送出</button>
            </div>
        </div>
    </div> -->



</body>
<script type="text/javascript">

</script>

</html>