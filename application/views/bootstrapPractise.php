<?php
    defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap 3 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />
</head>

<body>

    <div class="container">
        <!-- Title -->
        <div class="row">
            <div class="col-sm-12">
                <h1>bootstrap 練習</h1>
            </div>
        </div>
        <!-- Nav-->
        <div class="row p-5">
            <ul class="nav nav-pills">

                <li role="presentation" class="active"><a href="#">Home</a></li>
                <li role="presentation"><a href="#">Profile</a></li>
                <li role="presentation"><a href="#">Messages</a></li>

                <li role="presentation" class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        Dropdown <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li role="presentation" class="active"><a href="#">Home</a></li>
                        <li role="presentation"><a href="#">Profile</a></li>
                        <li role="presentation"><a href="#">Messages</a></li>
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Controll Form -->
        <div class="row">
            <div class="col-sm-4 form-group">
                <input type="password" class="form-control" id="pwd">
            </div>
            <div class="col-sm-4"><button class="btn btn-default">搜尋</button></div>
            <div class="col-sm-4"><button class="btn btn-warning float-right" data-toggle="modal" data-target="#myModal">新增</button></div>

        </div>

        <!-- Table -->
        <div class="row">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>&nbsp</th>
                        <th>學號</th>
                        <th>姓名</th>
                        <th>電話</th>
                        <th>mail</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-pen color_green ml-3"></i> <i class="fas fa-trash text-danger ml-3"></i></td>
                        <td>10101</td>
                        <td>Doe</td>
                        <td>0900000000</td>
                        <td>10101@stududent.com</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-pen color_green ml-3"></i> <i class="fas fa-trash text-danger ml-3"></i></td>
                        <td>10101</td>
                        <td>Doe</td>
                        <td>0900000000</td>
                        <td>10101@stududent.com</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-pen color_green ml-3"></i> <i class="fas fa-trash text-danger ml-5"></i></td>
                        <td>10101</td>
                        <td>Doe</td>
                        <td>0900000000</td>
                        <td>10101@stududent.com</td>
                    <tr>
                        <td><i class="fas fa-pen color_green ml-3"></i> <i class="fas fa-trash text-danger ml-5"></i></td>
                        <td>10101</td>
                        <td>Doe</td>
                        <td>0900000000</td>
                        <td>10101@stududent.com</td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Popovers -->
        <button type="button" class="btn btn-default" data-container="body" data-toggle="popover" data-placement="left" data-content="bootstrap練習">
            Popovers
        </button>
        <button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="bootstrap練習">Tooltip</button>





        <!-- Pagination -->
        <div class="row float-right">
            <ul class="pagination">
                <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
            </ul>
        </div>
    </div>


    <!-- The Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">新增</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="form1">
                        <div class="form-group ">
                            <label for="student_id">學號</label>
                            <input type="text" class="form-control" id="student_id">
                        </div>
                        <div class="form-group">
                            <label for="name">姓名</label>
                            <input type="text" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label for="tel">電話</label>
                            <input type="tel" class="form-control" id="tel">
                        </div>
                        <div class="form-group ">
                            <label for="mail">mail</label>
                            <input type="mail" class="form-control" id="mail">
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary" form="form1">送出</button>
                </div>
            </div>
        </div>

</body>
<script>
$(function(){$("[data-toggle='popover']").popover();});
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
</html>
