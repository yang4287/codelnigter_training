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

    // dataTable 
    var table;

    // 搜尋條件
    var searchFormData;

    /**
     * HTML 樣板產生器
     * 
     * @param {object} el - Element Templates
     * @param {array} data - Template Render Data
     * @param {function} created - Callback Method
     * @return {HTMLElement}
     */
    var tmpl = function tmpl( el, data, created ) {
      var $el = $( el );

      var templateHTML = /script|template/i.test( $el.prop( 'tagName' ) )
          ? $el.html()
          : $el.prop( 'outerHTML' );

      var $compiledEl = $(
          ( templateHTML || '' ).replace( /{{ *(.*?) *}}/g, ( match, p1 ) => {
              try {
                  return (
                      [data || {}].concat( p1.split( '.' ) ).reduce( ( a, b ) => {
                          return a[b];
                      } ) || ''
                  );
              } catch ( e ) {
                  return '';
              }
          } )
      );

      if ( typeof created === 'function' ) {
          created( $compiledEl, data );
      }

      return $compiledEl;
    }

    /**
     * 建構子
     */
    var _construct = function() {
      _initialize();
    };

    /**
     * 初始化
     */
    var _initialize = function() {
      /**
       * 讀取全部
       */

      // 創建dataTable，初始頁資料表
      createTable();

      /**
       * 事件綁定
       */
      _evenBind();
      
    };

    /**
     * 事件綁定
     */
    var _evenBind = function() {
      /**
       * 監聽搜尋按鈕，按下搜尋後，dataTable資料表重新載入
       */
      $('#search-btn').on('click', function() {
        table.destroy();
        createTable();
      });

      /**
       * 監聽上傳檔案表格，執行匯入
       */
      $('#uploadForm').submit(function(e) {
        // 防止跳轉
        e.preventDefault();

        // 儲存表格輸入內容
        var formData = new FormData(this);
        
        // 使用ajax匯入
        $.ajax({
          method: 'POST',
          url: '/index.php/account_info/import/',
          processData: false,
          contentType: false,
          data: formData,
        }).done(function(data) {
            // 執行成功，顯示成功  
            $.rustaMsgBox({ mode: 'success', content: '匯入成功!' });

            // 重新載入資料表內容
            table.destroy();
            createTable();
          })
          .fail(function(jqXHR) {
            // 錯誤處理，取得錯誤訊息
            var data = jqXHR.responseText;
            var jsonData = JSON.parse(data);
            var msg = jsonData.message;
            // 顯示失敗，並回傳錯誤訊息
            $.rustaMsgBox({ 
              mode: 'warning', 
              content: '匯入失敗!' + msg,
              fadeOut:true, 
              // 顯示4秒
              fadeTimer: 4000,
             });
          });
      });

      /**
       * 監聽匯出按鈕，取當前頁資料表內容執行匯出
       * 當前頁資料表，可用前一次的ajax參數取得
       * 此區負責整理參數
       */
      $('.export-btn').on('click', function() {

        // 欄位搜尋條件整理
        var condiStringUrl =
          '&condition[birth]=' +
          (table.ajax.params()['condition[birth]'] ? table.ajax.params()['condition[birth]'] : '') +
          '&condition[name]=' +
          (table.ajax.params()['condition[name]'] ? table.ajax.params()['condition[name]'] : '') +
          '&condition[gender]=' +
          (table.ajax.params()['condition[gender]']
            ? table.ajax.params()['condition[gender]']
            : '') +
          '&condition[email]=' +
          (table.ajax.params()['condition[email]'] ? table.ajax.params()['condition[email]'] : '') +
          '&condition[account]=' +
          (table.ajax.params()['condition[account]']
            ? table.ajax.params()['condition[account]']
            : '');

        // 取得關鍵字
        var searchText = searchFormData['search[value]'] ?? '';

        // 取得當前頁第一筆資料起始索引
        var start = table.page.info().start;

        // 取得每頁顯示數量
        var length = table.page.info().length;

        // 取得指定排序欄位之索引，用於提供orderColumn找到欄位名稱
        var orderColumnIndex = table.ajax.params()['order'][0]['column'];

        // 取得指定排序方式
        var orderMethod = table.ajax.params()['order'][0]['dir'];

        // 取得指定排序欄位名稱
        var orderColumn = table.ajax.params()['columns'][orderColumnIndex]['data'];

        // 執行匯出涵式
        exportTable(condiStringUrl, searchText, orderColumn, orderMethod, start, length);
      });

      /**
       * 監聽批次刪除按鈕鍵，顯示確認彈窗，按下確認執行批次刪除
       */
      $('.delete-batch-btn').on('click', function() {
        // 取得勾選id陣列
        var idArray = $("[name='id[]']").serializeArray();

        // 檢查有無勾選欲刪除項目，無的話則跳出警告訊息，並跳出執行
        if (!idArray.length) {
          $.rustaMsgBox({
            mode: 'warning',
            content: '尚未勾選欲刪除之項目',
            fadeOut: true,
            // 顯示4秒
            fadeTimer: 4000,
          });
          return;
        }

        // 建立二次確認popover彈窗內容
        var popoverContrnt = '<div><p>確定刪除?</p><div class="btn btn-small btn-danger popover-delete-btn m-2" >確定</div><div class="btn btn-small btn-default  popover-close-btn">取消</div></div>';
        $(this).popover({
          html:true,
          title: '危險',
          content:popoverContrnt,
        });

        // 批次刪除按鈕鍵按下時，開啟二次確認popover彈窗
        $(this).popover('show');

        // 監聽取消按紐，按下時關閉二次確認popover彈窗
        $('.popover-close-btn').on('click', function() {
          $('.delete-batch-btn').popover('hide');
        });

        // 監聽二次確認鍵，按下時執行批次刪除
        $('.popover-delete-btn').on('click', function() {
          // 使用ajax批次刪除，並將欲刪除的id陣列傳入
          $.ajax({
            method: 'DELETE',
            url: self._ajaxUrls.accountApi,
            dataType: 'json',
            data: idArray,
          }).done(function(data) {
              // 執行成功，關閉popover彈窗
              $('.delete-batch-btn').popover('hide');

              // 顯示成功  
              $.rustaMsgBox({ mode: 'success', content: '刪除成功!' });

              // 重新載入dataTable資料表
              table.destroy();
              createTable();
            })
            .fail(function(jqXHR) {
              // 錯誤處理，顯示失敗，並回傳錯誤訊息
              $.rustaMsgBox({
                mode: 'warning',
                content: '刪除失敗!' + jqXHR.responseJSON.message,
                fadeOut: true,
                // 顯示4秒
                fadeTimer: 4000,
              });
            });
        });
      
        return;
        
        // 關閉所有彈窗，避免導致視窗重複彈出
        BootstrapDialog.closeAll();
        // 有勾選項目的話，執行二次確認彈窗
        BootstrapDialog.show({
          message: '確定刪除?',
          type: 'type-danger',
          buttons: [
            {
              label: '確定',
              cssClass: 'btn-danger',
              action: function(dialogRef) {

                // 關閉彈窗
                dialogRef.close();

                // 使用ajax批次刪除，並將欲刪除的id陣列傳入
                $.ajax({
                  method: 'DELETE',
                  url: self._ajaxUrls.accountApi,
                  dataType: 'json',
                  data: idArray,
                }).done(function(data) {
                    // 執行成功，顯示成功  
                    $.rustaMsgBox({ mode: 'success', content: '刪除成功!' });

                    // 重新載入dataTable資料表
                    table.destroy();
                    createTable();
                  })
                  .fail(function(jqXHR) {
                    // 錯誤處理，顯示失敗，並回傳錯誤訊息
                    $.rustaMsgBox({
                      mode: 'warning',
                      content: '刪除失敗!' + jqXHR.responseJSON.message,
                      fadeOut: true,
                      // 顯示4秒
                      fadeTimer: 4000,
                    });
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

      /**
       * 監聽新增按鈕，建立新增表格，並彈出新增彈窗顯示在彈窗上，按下確認後執行新增
       */
      $('.add-btn').on('click', function() {
 
        // 關閉所有彈窗
        BootstrapDialog.closeAll();
        BootstrapDialog.show({
          title : '新增',
          onshown: function () {
            // 取得彈窗表單內容
            createDialogContent('add')
          },
          onshow: function (dialog) {
            // 取得彈窗表單結構
            createDialogStructure(dialog)
          },
          buttons: [
            {
              icon: 'glyphicon glyphicon-send',
              label: '送出',
              cssClass: 'btn-primary',
              action: function(dialogRef) {
                // html表單驗證
                var isFormPassing = document.forms['Form'].checkValidity();

                // 執行html表單驗證，未驗證成功則跳出內建驗證提示窗，並跳出執行
                if (!isFormPassing) {
                  document.querySelector('#Form').reportValidity();
                  return;
                }

                // 取得新增表格的輸入內容
                var formData = $("[name='Form']").serializeArray();

                // 按下彈窗確認按鈕時，禁止再次點擊按鈕，避免重複執行送出
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);

                // 使用ajax新增一筆，並將新增內容傳入
                $.ajax({
                  method: 'POST',
                  url: self._ajaxUrls.accountApi,
                  data: formData,
                  dataType: 'json',
                }).done(function(data) {
                    // 執行成功，關閉彈窗，顯示成功訊息
                    dialogRef.close();
                    $.rustaMsgBox({ mode: 'success', content: '新增成功!' });

                    // 重新載入dataTable資料表內容
                    table.destroy();
                    createTable();
                  })
                  .fail(function(jqXHR) {
                    // 錯誤處理，顯示失敗，並回傳錯誤訊息
                    $.rustaMsgBox({
                      mode: 'warning',
                      content: '新增失敗' + jqXHR.responseJSON.message,
                      fadeOut: true,
                      // 顯示5秒
                      fadeTimer: 5000,
                    });
                    
                    // 啟用確認按鈕
                    dialogRef.enableButtons(true);
                    dialogRef.setClosable(true);
                  });
                }
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
    };

    /**
     * 事件綁定，針對dataTable每一列的綁定事件
     */
    var _eventRow = function($row) {

      /**
       * 監聽每一筆資料的編輯按鈕，按下後顯示編輯彈窗，按下確認執行編輯
       */
      $row.on('click', '.edit-btn', function() {
        // 取得點擊該筆資料列內容之id
        var id = $(this).data('id');

        // 關閉所有彈窗
        BootstrapDialog.closeAll();
        BootstrapDialog.show({
          title : '編輯',
          onshown: function () {
            // 取得彈窗表單內容
            createDialogContent('edit',id)
          },
          onshow: function ( dialog ) {
            // 取得彈窗表單結構
            createDialogStructure(dialog)
          },
          buttons: [
            {
              icon: 'glyphicon glyphicon-send',
              label: '送出',
              cssClass: 'btn-primary',
              action: function(dialogRef) {
                // html表單驗證
                var isFormPassing = document.forms['Form'].checkValidity();

                // 執行html表單驗證，未驗證成功則跳出內建驗證提示窗，並跳出執行
                if (!isFormPassing) {
                  document.querySelector('#Form').reportValidity();
                  return;
                }

                // 取得新增表格的輸入內容
                var formData = $("[name='Form']").serializeArray();

                // 按下彈窗確認按鈕時，禁止再次點擊按鈕，避免重複執行送出
                dialogRef.enableButtons(false);
                dialogRef.setClosable(false);

                // 使用ajax編輯一筆，並將編輯內容傳入
                $.ajax({
                  method: 'PUT',
                  url: self._ajaxUrls.accountApi + '/' + id,
                  data: formData,
                  dataType: 'json',
                  }).done(function(data) {
                    // 執行成功，關閉彈窗，顯示成功訊息
                    dialogRef.close();
                    $.rustaMsgBox({ mode: 'success', content: '修改成功!' });
                    
                    // 重新載入dataTable資料表內容
                    table.destroy();
                    createTable();
                    })
                    .fail(function(jqXHR) {
                      // 處理回傳資料
                      $.rustaMsgBox({
                        mode: 'warning',
                        content: '編輯失敗!' + jqXHR.responseJSON.message,
                        fadeOut: true,
                        // 顯示5秒
                       fadeTimer: 5000,
                      });

                      // 啟用確認按鈕
                      dialogRef.enableButtons(true);
                      dialogRef.setClosable(true);

                    });
                }
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

      /**
       * 監聽每一筆資料的刪除按鈕，按下後顯示二次確認彈窗，按下確認執行刪除
       */
      $row.on('click', '.delete-btn', function() {
        // 取得點擊該筆資料列內容之id
        var id = $(this).data('id');

        // 建立二次確認popover彈窗內容
        var popoverContrnt = '<div><p>確定刪除?</p><div class="btn btn-small btn-danger popover-delete-btn m-2" >確定</div><div class="btn btn-small btn-default  popover-close-btn">取消</div></div>';
        $(this).popover({
          html:true,
          title: '危險',
          content:popoverContrnt,
          placement:'left',
        });

        // 刪除按鈕鍵按下時，開啟二次確認popover彈窗
        $(this).popover('show');

        // 監聽取消按紐，按下時關閉二次確認popover彈窗
        var thisRow = $(this);
        $('.popover-close-btn').on('click', function() {
          $(thisRow).popover('hide');
        });

        // 監聽二次確認鍵，按下時執行刪除
        $('.popover-delete-btn').on('click', function() {
          // 使用ajax刪除一筆，並將欲刪除的id傳入
          $.ajax({
            method: 'DELETE',
            // 刪除id為**的資料
            url: self._ajaxUrls.accountApi + '/' + id,
            dataType: 'json',
          }).done(function(data) {
              // 執行成功，關閉popover彈窗
              $('.delete-batch-btn').popover('hide');

              // 顯示成功訊息
              $.rustaMsgBox({ mode: 'success', content: '刪除成功!' });

              // 重新載入dataTable資料表內容
              table.destroy();
              createTable();
            })
            .fail(function(jqXHR) {
              // 錯誤處理，顯示失敗，並回傳錯誤訊息
              $.rustaMsgBox({
                mode: 'warning',
                content: '刪除失敗!' + jqXHR.responseJSON.message,
                fadeOut: true,
                // 顯示4秒
                fadeTimer: 4000,
              });
            });
        });
       
        return;
        // 關閉所有彈窗
        BootstrapDialog.closeAll();
        // 顯示二次確認彈窗
        BootstrapDialog.show({
          message: '確定刪除?',
          type: 'type-danger',
          buttons: [
            {
              label: '確定',
              cssClass: 'btn-danger',
              action: function(dialogRef) {
                // 關閉彈窗
                dialogRef.close();

                // 使用ajax刪除一筆，並將欲刪除的id傳入
                $.ajax({
                  method: 'DELETE',
                  // 刪除id為**的資料
                  url: self._ajaxUrls.accountApi + '/' + id,
                  dataType: 'json',
                }).done(function(data) {
                    // 執行成功，顯示成功訊息
                    $.rustaMsgBox({ mode: 'success', content: '刪除成功!' });

                    // 重新載入dataTable資料表內容
                    table.destroy();
                    createTable();
                  })
                  .fail(function(jqXHR) {
                    // 錯誤處理，顯示失敗，並回傳錯誤訊息
                    $.rustaMsgBox({
                      mode: 'warning',
                      content: '刪除失敗!' + jqXHR.responseJSON.message,
                      fadeOut: true,
                      // 顯示4秒
                      fadeTimer: 4000,
                    });
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
    };

    /**
     * *************** 功能函式 ***************
     */


    /**
     * 創建公司和縣市選單
     */
    function createMenu() {

      /// 使用ajax查詢縣市列表
      $.ajax({
        method: 'GET',
        url: '/index.php/account_info/getCity',
        dataType: 'json',
      }).done(function(data) {
        // 迴圈建立選單內容
        $.each(data.data, function (index, value) {
          $("#city").append('<option value=' + value.city_id + '>' + value.city_name + '</option>');
        });
      }).fail(function(jqXHR) {
        // 顯示失敗，並回傳錯誤訊息
        $.rustaMsgBox({ 
          mode: 'warning', 
          content: '縣市選單產生失敗!' +  jqXHR.responseJSON.message,
          fadeOut:true, 
          // 顯示4秒
          fadeTimer: 4000,
        });
      });

      // 使用ajax查詢公司列表 
      $.ajax({
        method: 'GET',
        url: '/index.php/account_info/getCompany',
        dataType: 'json',
      }).done(function(data) {
        // 迴圈建立選單內容
        $.each(data.data, function (index, value) {
          $("#company").append('<option value=' + value.c_id + '>' + value.c_name + '</option>');
        });
      }).fail(function(jqXHR) {
        // 顯示失敗，並回傳錯誤訊息
        $.rustaMsgBox({ 
          mode: 'warning', 
          content: '公司選單產生失敗!' + jqXHR.responseJSON.message,
          fadeOut:true, 
          // 顯示4秒
          fadeTimer: 4000,
         });
        $('button').attr('disabled', true);
        });
       
    }

    /**
     * 創建新增或編輯Dialog之表單內容
     */
    function createDialogContent(mode,id = null){

        // 取得表單中公司和縣市選單內容
        createMenu();

        // 取得當前日期，限制生日欄位不得大於今天
        var datePicker = new Date().toISOString().split('T')[0];
        $("[name='Form'] #birth" ).attr('max',datePicker);

        switch (mode) {
          // 新增
          case 'add':
            // 設定表單內容為空
            $("[name='Form'] #account" ).val('');
            $("[name='Form'] #name" ).val('');
            $("[name='Form'] #birth" ).val('');
            $("[name='Form'] #email" ).val('');
            $("[name='Form'] #note" ).val('');
            $("[name='Form'] #gender" ).val('');
            $("[name='Form'] #city" ).val('');
            $("[name='Form'] #company" ).val('');
              break;
          // 修改
          case 'edit':
             // 使用ajax讀取一筆
              $.ajax({
              method: 'GET',
              // 讀取id為**的資料
               url: self._ajaxUrls.accountApi + '/' + id,
                 dataType: 'json',
               }).done(function(data) {
                // 將讀到的資料寫入表單內容
                $("[name='Form'] #account" ).val(data.data.account);
                $("[name='Form'] #name" ).val(data.data.name);
                $("[name='Form'] #birth" ).val(data.data.birth);
                $("[name='Form'] #email" ).val(data.data.email);
                $("[name='Form'] #note" ).val(data.data.note);
                $("[name='Form'] #gender" ).val(data.data.gender).change();
                $("[name='Form'] #city" ).val(data.data.city_id).change();
                $("[name='Form'] #company" ).val(data.data.c_id).change();
            }).fail(function(jqXHR) {
                // 顯示失敗，並回傳錯誤訊息
                $.rustaMsgBox({ 
                  mode: 'warning', 
                  content: '讀取失敗!請稍號再試!' + jqXHR.responseJSON.message ,
                  fadeOut:true, 
                  // 顯示4秒
                  fadeTimer: 4000,
                 });
              });
              break;
      }
      
    }

    /**
     * 創建新增或編輯Dialog之表單結構
     */
    function createDialogStructure(dialog) {
      // 取得彈窗主內容區塊
      var modalBody = dialog.getModalBody();
      var $bodyTemp = $("#template-sticky-edit");
      // 產生實體表單樣板物件，產生預渲染表單樣板物件    
      modalBody.append(tmpl($bodyTemp));
    }

    /**
     * 匯出功能涵式
     */
    function exportTable(_condition, _search, _order_column, _order_method, _start, _length) {
      //下載excel檔案
      window.open(
        '/index.php/account_info/export/?' +
          _condition +
          '&search=' +
          _search +
          '&order_by=' +
          _order_column +
          '&order_by_method=' +
          _order_method +
          '&start=' +
          _start +
          '&length=' +
          _length,
        '_blank'
      );
    }

    /**
     * 創建dataTable資料表內容涵式
     */
    function createTable() {

      // 取得搜尋表單內容
      var formData = $("[name='searchForm']").serializeArray();

      // 給予初始頁面或重新載入，未輸入任何搜尋條件時之預設內容
      searchFormData = { condition: null };

      // 過濾搜尋欄位為空白(即'')，非空白才將值儲存至要傳送至ajax的所有搜尋資訊表(searchFormData)
      // 也就是全空白按搜尋會顯示全部內容
      for (var item in formData) {
        if (formData[item].value != '') {
          searchFormData[formData[item].name] = formData[item].value;
        }
      }

      // 建立dataTable
      table = $('.ctrl-table').DataTable({
        // 用於分頁功能
        processing: true,
        serverSide: true,
        searchable: true,

        // 使用ajax查詢符合條件或顯示全部資料列，傳入所有搜尋資訊表
        ajax: {
          url: self._ajaxUrls.accountApi,
          type: 'GET',
          dataType: 'json',
          data: searchFormData,
          // 資料加載失敗
          error: function (jqXHR, error, code) {
            // 顯示失敗，並回傳錯誤訊息
            $.rustaMsgBox({ 
              mode: 'warning', 
              content: '帳戶資料讀取失敗!' + jqXHR.responseJSON.message,
              fadeOut:true, 
              // 顯示4秒
              fadeTimer: 4000,
            });
            // 禁用按鈕
            $('button').attr('disabled', true);
            // dataTable按鈕禁用失敗，待修改
            // table.buttons().disable();
           
        }
        },
        // 在建立每一筆資料列時，執行綁定事件
        createdRow: function(row, data, dataIndex) {
          var $row = $(row);
          _eventRow($row);
        },
        // 設定欄位資訊
        columns: [
          // 此欄位為提供批次刪除的勾選欄位
          {
            data: null,
            render: function(data, type, row) {
              return '<input value="' + row.id + '" name="id[]" type="checkbox" aria-label="...">';
            },
          },
          // 不顯示id
          { data: 'id', visible: false },
          { data: 'account' },
          { data: 'name' },
          {
            data: 'gender',
            // 轉換資料格式
            render: function(data, type, row) {
              return data == 0 ? '男' : '女';
            },
          },
          {
            data: 'birth',
            // 轉換資料格式
            render: function(data, type, row) {
              const arr = data.split('');
              arr[4] = '年';
              arr[7] = '月';
              arr[10] = '日';
              data = arr.join('');
              return data;
            },
          },
          { data: 'email' },
          { data: 'note' },
          { data: 'city_name' },
          { data: 'c_name' },
          // 編輯按鈕欄位
          {
            data: null,
            render: function(data, type, row) {
              return (
                '<button  type="button"  class="btn btn-default btn-sm edit-btn float-left" data-id="' +
                row.id +
                '" "><i  class="fas fa-pen color_green " ></i></button>'
              );
            },
          },
          // 刪除按鈕欄位
          {
            data: null,
            render: function(data, type, row) {
              return (
                '<button type="button" class="btn btn-default btn-sm delete-btn float-left" data-id="' +
                row.id +
                '"><i class="fas fa-trash text-danger" ></i></button>'
              );
            },
          },
        ],
        // 預設排序欄位
        order: [[1, 'adc']],
        columnDefs: [
          {
            // 關閉某些欄位排序
            targets: [0, 8, 9],
            orderable: false,
          },
        ],
        //關閉搜尋
        dom: 'lBrtip', 
      });
    }
    _construct();
  };

  // Give the init function the Object prototype for later instantiation
  obj.fn.init.prototype = obj.prototype;

  // Alias prototype function
  $.extend(obj, obj.fn);
})(window, document, $);
