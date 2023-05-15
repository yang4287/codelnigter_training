/**
 * 說明：
 * <li>1. 頁面函式只會初始化一次
 * <li>2. 如果是多頁面組合時，可能被其他頁面呼叫，因此需使用namespane:Page，以方便外部呼叫或試調
 *
 * 執行順序：
 * 1. 註冊$(document).ready()函式，但先不執行
 * 2. $(document).ready()之外的程式碼依序執行 - 建構變數、函式obj
 * 3. 執行$(document).ready()內註冊的函式
 * 4. 確定window.Page是否存在，不存在則初始化
 * 5. 執行obj()物件，並將結果存入window.Page[name]
 * 6. obj()回傳內容為 new obj.fn.init(options);
 * 7. 實例化obj.fn.init(options);並在最後執行函式 _construct(_options);
 */

// IIFE 立即執行函式
(function(window, document, $, undefined) {
  // 使用嚴格模式
  'use strict';

  // DOM下載完後執行
  $(document).ready(function() {
    // init this page
    window.Page = window.Page || new function() {}();
    window.Page[name] = obj();
  });

  // Class Name
  var name = '{name}';
  // Version
  var version = '{version}';
  // Default options
  var defaults = {};

  /**
      * *************** Object Build ***************
      */

  // Define a local copy of Object
  var obj = function(options) {
    return new obj.fn.init(options);
  };

  // Prototype arguments
  obj.fn = obj.prototype = {
    // Object Name
    _name: name,

    // Default options
    _defaults: defaults,

    // AJAX URL
    _ajaxUrls: {
      accountApi: 'account_info/ajax',
    },
  };

  /**
      * Javascript物件
      */
  obj.fn.init = function(options) {
    /**
          * *************** Object Argument Setting ***************
          */
    var self = this;
    var _options = options || {};
    // Ajax Response - jqXHR(s)
    var _jqXHRs;

    /**
          * *************** 屬性設定 ***************
          */

    /**
          * *************** 物件必要函式 ***************
          */

    /**
          * 建構子
          */
    var _construct = function() {
      console.log('_construct');

      _initialize();
    };

    /**
          * 解構子
          */
    var _destruct = function() {};

    /**
          * 初始化
          */
    var _initialize = function() {
      /**
              * 讀取全部
              */
      $('.ctrl-table').empty();
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi,
        dataType: 'json',
      }).done(function(data) {
        console.log(data);
        /**
       * 陣列資料配合$.each建立表格
        */
        // 建立變數
        var tmp, table, thead, tbody, tr, th, td;
        // 建立暫存容器
        tmp = $('<div></div>');
        // 建立thead區塊資料
        thead = $('<thead></thead>').appendTo(tmp);
        // 建立tbody區塊資料
        tbody = $('<tbody></tbody>').appendTo(tmp);

        // 建立標題
        tr = $('<tr class="bg-info"></tr>').appendTo(thead);
        th = $('<th></th>').appendTo(tr);
        $.each(data.data[0], function(key, value) {
          if (key != 'id') {
            th = $('<th>' + key + '</th>').appendTo(tr);
          }
        });
        th = $('<th></th>').appendTo(tr);

        // 建立內容
        $.each(data.data, function(index1, value1) {
          tr = $('<tr></tr>').appendTo(tbody);
          td = $('<td> <input value="'+value1.id+'" name="id[]" type="checkbox" aria-label="..."></td>').appendTo(tr);
          $.each(value1, function(key, value2) {
            if (key != 'id') {
              if (key == 'gender' && value2 == 0) {
                value2 = '男';
              } else if (key == 'gender' && value2 == 1) {
                value2 = '女';
              }

              if (key == 'birth') {
                const arr = value2.split('');

                arr[4] = '年';
                arr[7] = '月';
                arr[10] = '日';
                value2 = arr.join('');
              }
              td = $('<td>' + value2 + '</td>').appendTo(tr);
            }
          });
          td = $(
            '<td><button   data-toggle="modal" data-target="#myModal"  type="button"  class="btn btn-default btn-sm edit-btn float-left" data-id="' +
              value1.id +
              '" "><i  class="fas fa-pen color_green " ></i></button><button type="button" class="btn btn-default btn-sm delete-btn float-left" data-id="' +
              value1.id +
              '"><i class="fas fa-trash text-danger" ></i></button></td>'
          ).appendTo(tr);
        });
        console.log('table done');

        // 取得table元件
        table = $('.ctrl-table');
        // 將暫存容器內容移至table元件
        tmp.children().appendTo(table);

        _evenBind();
      });

      // /**
      //   * 讀取一筆
      //   */
      // $.ajax({
      //     method: 'GET',
      //     // 讀取id為3的資料
      //     url: self._ajaxUrls.accountApi + '/3',
      //     dataType: 'json',
      // }).done(function (data) {
      //     // 處理回傳資料
      //     $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      // });

      // /**
      //   * 更新一筆
      //   */
      // $.ajax({
      //     method: 'PUT',
      //     url: self._ajaxUrls.accountApi,
      //     data: { name: 'John', location: 'Boston' },
      //     dataType: 'json',
      // }).done(function (data) {
      //     // 處理回傳資料
      //     $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      // });

      // /**
      //   * 刪除錯誤 No Delete ID
      //   */
      // $.ajax({
      //     method: 'DELETE',
      //     url: self._ajaxUrls.accountApi,
      //     dataType: 'json',
      // }).done(function (data) {
      //     // 處理回傳資料
      //     $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      // }).fail(function (jqXHR) {
      //     // 錯誤處理
      //     $('<div>' + jqXHR.responseText + '</div>').appendTo($('.ctrl-message'));
      //     console.log(jqXHR);
      // });

      // /**
      //   * 刪除一筆
      //   */
      // $.ajax({
      //     method: 'DELETE',
      //     // 刪除id為2的資料
      //     url: self._ajaxUrls.accountApi + '/2',
      //     dataType: 'json',
      // }).done(function (data) {
      //     // 處理回傳資料
      //     $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      // }).fail(function (jqXHR) {
      //     // 錯誤處理
      //     $('<div>' + jqXHR.responseText + '</div>').appendTo($('.ctrl-message'));
      //     console.log(jqXHR);
      // });

      /**
              * 事件綁定
              */
      // _evenBind();
    };

    /**
          * 事件綁定
          */
    var _evenBind = function() {
      console.log('_evenBind');
      
      $('.delete-batch-btn').on('click', function() {
        console.log($(this).data('id'));
      
        var idArray = $("[name='id[]']").serializeArray();
        console.log('傳送修改');
        console.log(idArray);
        BootstrapDialog.closeAll();

        BootstrapDialog.show({
          message: '確定刪除?' + $("[name='id[]']").val(),
          type: 'type-danger',
          buttons: [
            {
              label: '確定',
              cssClass: 'btn-danger',
              action: function(dialogRef) {
                dialogRef.close();
                $.ajax({
                  method: 'DELETE',
                  // 刪除id為2的資料
                  url: self._ajaxUrls.accountApi,
                  dataType: 'json',
                  data:idArray,
                })
                  .done(function(data) {
                    
                    $.rustaMsgBox({ 'mode' : 'success' ,'content':'刪除成功!'});
                 
  
                    _initialize();
                  })
                  .fail(function(jqXHR) {
                    // 錯誤處理
                   
                    $.rustaMsgBox({ 'mode' : 'warning','content':'刪除失敗!'+jqXHR.responseTex });
                  
                    console.log(jqXHR);
                  });
              },
            },
            {
              label: 'Close',
              action: function(dialogRef) {
                dialogRef.close();
              },
            },
          ],
        });
        
      });
      


      $('.delete-btn').on('click', function() {
        console.log($(this).data('id'));
        var id = $(this).data('id');
        BootstrapDialog.closeAll();

        BootstrapDialog.show({
          message: '確定刪除?',
          type: 'type-danger',
          buttons: [
            {
              label: '確定',
              cssClass: 'btn-danger',
              action: function(dialogRef) {
                dialogRef.close();
                $.ajax({
                  method: 'DELETE',
                  // 刪除id為2的資料
                  url: self._ajaxUrls.accountApi + '/' + id,
                  dataType: 'json',
                })
                  .done(function(data) {
                    
                    $.rustaMsgBox({ 'mode' : 'success' ,'content':'刪除成功!'});
                 
                    _initialize();
                  })
                  .fail(function(jqXHR) {
                    // 錯誤處理
                    $.rustaMsgBox({ 'mode' : 'warning','content':'刪除失敗!'+jqXHR.responseTex });
                    console.log(jqXHR);
                  });
              },
            },
            {
              label: 'Close',
              action: function(dialogRef) {
                dialogRef.close();
              },
            },
          ],
        });

        /**
        * 刪除一筆
        */
      });

      $('.edit-btn').on('click', function() {
        console.log($(this).data('id'));
        var id = $(this).data('id');
        BootstrapDialog.closeAll();

        /**
        * 讀取一筆
        */
        $.ajax({
          method: 'GET',
          // 讀取id為**的資料
          url: self._ajaxUrls.accountApi + '/' + id,
          dataType: 'json',
        }).done(function(data) {
          console.log(data);
          var datePicker = new Date().toISOString().split('T')[0];
          // 處理回傳資料
          var $editForm = $('<div></div>');
          if (data.data[0].gender == 0) {
            $editForm.append(
              '<form class="form" id="editForm" name="editForm" ><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="account">帳號</label><input  type="text" pattern="[A-Za-z0-9]+" maxlength=15 minlength=5 required class="form-control" id="account" name="account" value="' +
                data.data[0].account +
                '"></div><div class="form-group col-4 col-md-4"><label for="name">姓名</label><input type="text" minlength="1" maxlength="30" required class="form-control" id="name" name="name" value="' +
                data.data[0].name +
                '"></div><div class="form-group col-4 col-md-4"><label for="gender">性別</label><select class="form-control" id="gender" name="gender" required ><option value="0" selected>男</option><option value="1">女</option></select></div></div><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="birth">生日</label><input type="date" required class="form-control" id="birth" name="birth" max="' +
                datePicker +
                '" value="' +
                data.data[0].birth +
                '"></div><div class="form-group col-4 col-md-4"><label for="email">Email</label><input type="email" required class="form-control" id="email" name="email" value="' +
                data.data[0].email +
                '"></div><div class="form-group col-4 col-md-4"><label for="note">Note</label><input type="text" class="form-control" id="note" name="note"  value="' +
                data.data[0].note +
                '"></div></div></form>'
            );
          } else {
            $editForm.append(
              '<form class="form" id="editForm" name="editForm" ><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="account">帳號</label><input  type="text" pattern="[A-Za-z0-9]+" maxlength=15 minlength=5 required class="form-control" id="account" name="account" value="' +
                data.data[0].account +
                '"></div><div class="form-group col-4 col-md-4"><label for="name">姓名</label><input type="text" minlength="1" maxlength="30" required class="form-control" id="name" name="name" value="' +
                data.data[0].name +
                '"></div><div class="form-group col-4 col-md-4"><label for="gender">性別</label><select class="form-control" id="gender" name="gender" required ><option value="0" >男</option><option value="1" selected>女</option></select></div></div><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="birth">生日</label><input type="date" required class="form-control" id="birth" name="birth" max="' +
                datePicker +
                '" value="' +
                data.data[0].birth +
                '"></div><div class="form-group col-4 col-md-4"><label for="email">Email</label><input type="email" required class="form-control" id="email" name="email" value="' +
                data.data[0].email +
                '"></div><div class="form-group col-4 col-md-4"><label for="note">Note</label><input type="text" class="form-control" id="note" name="note" value="' +
                data.data[0].note +
                '"></div></div></form>'
            );
          }
          var dialog = new BootstrapDialog({
            title: '修改',
            message: $editForm,
            buttons: [
              {
                icon: 'glyphicon glyphicon-send',
                label: '送出',
                cssClass: 'btn-primary',

                action: function(dialogRef) {
                  var isFormPassing = document.forms['editForm'].checkValidity();
                  console.log(isFormPassing);
                  if (!isFormPassing) {
                      document.querySelector('#editForm').reportValidity();
                      return;
                  } else {
                
                  dialogRef.enableButtons(false);
                  dialogRef.setClosable(false);
                  
                  var formData = $("[name='editForm']").serializeArray();
                  console.log('傳送修改');
                  console.log(formData);
                  $.ajax({
                    // 傳送方法
                    method: 'PUT',
                    // 目標網址
                    url: self._ajaxUrls.accountApi + '/' + id,
                    // 傳送資料
                    data: formData,
                    // 回傳資料格式
                    dataType: 'json',
                    })
                    .done(function(data) {
                      // 輸出至console
                      console.log(data);
                      dialogRef.close();

                      $.rustaMsgBox({ 'mode' : 'success' ,'content':'修改成功!'});

                      _initialize();
                    })
                    .fail(function(jqXHR) {
                      // 處理回傳資料
                      $.rustaMsgBox({ 'mode' : 'warning' ,'content':jqXHR.responseText});
                     
                    
                      dialogRef.enableButtons(true);
                      dialogRef.setClosable(true);

                      console.log(jqXHR);
                    });
                  }},
              },
              {
                label: 'Close',
                action: function(dialogRef) {
                  dialogRef.close();
                },
              },
            ],
          });
          dialog.open();
        });
      });

      /**
             * 打開新增的談窗appendTo
             */

      $('.add-btn').on('click', function() {
        var datePicker = new Date().toISOString().split('T')[0];
        var $addForm = $('<div></div>');
        $addForm.append(
          '<form  id="addForm" class="form"  name="addForm" ><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="account">帳號</label><input type="text" pattern="[a-zA-Z0-9]+" maxlength=15 minlength=5 required class="form-control" id="account" name="account"></div><div class="form-group col-4 col-md-4"><label for="name">姓名</label><input type="text" minlength="1" maxlength="30" required class="form-control" id="name" name="name"></div><div class="form-group col-4 col-md-4"><label for="gender">性別</label><select class="form-control" id="gender" name="gender" required><option value="0">男</option><option value="1">女</option></select></div></div><div class="row m-0"><div class="form-group col-4 col-md-4"><label for="birth">生日</label><input type="date" max="' +
            datePicker +
            '" required class="form-control" id="birth" name="birth"></div><div class="form-group col-4 col-md-4"><label for="email">Email</label><input type="email" required class="form-control" id="email" name="email"></div><div class="form-group col-4 col-md-4"><label for="note">Note</label><input type="text" class="form-control" id="note" name="note"></div></div></form>'
        );

        BootstrapDialog.closeAll();

        var dialog = new BootstrapDialog({
          title: '新增',
          message: $addForm,
          cssClass: 'add-modal',
          buttons: [
            {
              icon: 'glyphicon glyphicon-send',
              label: '送出',
              cssClass: 'btn-primary add-submit',

              action: function(dialogRef) {
                
                var isFormPassing = document.forms['addForm'].checkValidity();
                console.log(isFormPassing);
                if (!isFormPassing) {
                  document.querySelector('#addForm').reportValidity();
                  return;
                } else {
                  var formData = $("[name='addForm']").serializeArray();


                  //e.preventDefault();//防止跳轉
                  dialogRef.enableButtons(false);
                  dialogRef.setClosable(false);

                  $.ajax({
                    // 傳送方法
                    method: 'POST',
                    // 目標網址
                    url: self._ajaxUrls.accountApi,
                    // 傳送資料
                    data: formData,
                    // 回傳資料格式
                    dataType: 'json',
                  })
                    .done(function(data) {
                      // 輸出至console
                      console.log(data);
                      dialogRef.close();

                      $.rustaMsgBox({ 'mode' : 'success','content':'新增成功!' });

                      _initialize();
                    })
                    .fail(function(jqXHR) {
                      // 處理回傳資料
                      console.log(jqXHR.responseText);
                      $.rustaMsgBox({ 'mode' : 'warning' ,'content':jqXHR.responseText});
                      console.log(dialogRef);
                      dialogRef.enableButtons(true);
                      dialogRef.setClosable(true);

                      console.log(jqXHR);
                    });
                }
              },
            },
            {
              label: 'Close',
              action: function(dialogRef) {
                dialogRef.close();
              },
            },
          ],
        });
        dialog.open();
      });

      /**
              * 事件 - 增加
              */

      /**
              * 事件 - 清除規
              */
    };

    /**
          * *************** 功能函式 ***************
          */

    /**
          * *************** 事件函式 ***************
          */

    /**
          * 事件 - 送出
          */
    var _submit = function(e) {
      return this;
    };

    /**
          * 事件 - 清除
          */
    var _clear = function(e) {
      return this;
    };

    /**
          * 事件 - 增加
          */
    var _add = function(e) {
      return this;
    };

    /**
          * *************** 私有函式 ***************
          */

    /**
          * *************** Run Constructor ***************
          */
    _construct();
  };

  // Give the init function the Object prototype for later instantiation
  obj.fn.init.prototype = obj.prototype;

  // Alias prototype function
  $.extend(obj, obj.fn);
})(window, document, $);
