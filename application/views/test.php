<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Codeigniter 3 Ajax Pagination using Jquery Example - ItSolutionStuff.com</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
    <style type="text/css">
      html, body { font-family: 'Raleway', sans-serif; }
      a{ color: #007bff; font-weight: bold;}
    </style>
  </head> 
  <body>
      
   <div class="container">
    <div class="card">
      <div class="card-header">
        Codeigniter Ajax Pagination Example - ItSolutionStuff.com
      </div>
      <div class="card-body">
        <p>關鍵字</p><input type="text" name="search">
        <p>每頁顯示數量</p><input type="number" name="perpage">
        <button type="button" id="search-btn">搜尋</button>
           <!-- Posts List -->
           <table class="table table-borderd" id='postsList'>
             <thead>
              <tr>
                <th>S.no</th>
                <th>Title</th>
              </tr>
             </thead>
             <tbody></tbody>
           </table>
           
           <!-- Paginate -->
           <div id='pagination'></div>
      </div>
    </div>
   </div>
 
   <!-- Script -->

   <script type='text/javascript'>
   $(document).ready(function(){
    loadPagination(0);
    
    var perpage,search;
     
     $('#search-btn').on('click',function(){
      
        perpage = $('[name="perpage"]').val();
        search = $('[name="search"]').val();
       loadPagination(0,perpage,search);
     });
 
     $('#pagination').on('click','a',function(e){
       e.preventDefault(); 
       var pageno = $(this).attr('data-ci-pagination-page'); 
       console.log(pageno);
       loadPagination(pageno,perpage,search);
     });
 
     function loadPagination(pagno,_perpage,_search){
       $.ajax({
         url: '/account_info/ajax/',
         type: 'get',
         dataType: 'json',
         data:{page:pagno, per_page:_perpage,search:_search},
         success: function(response){
            $('#pagination').html(response.pagination);
            console.log(response.pagination);
            createTable(response.result,response.row);
         }
       });
     }
 
     function createTable(result,sno){
       sno = Number(sno);
       $('#postsList tbody').empty();
       for(index in result){
          
          var account = result[index].account;
          var name = result[index].name;
          var gender = result[index].gender;
          gender = gender==0 ? '男' : '女';
          var birth = result[index].birth;
          var email = result[index].email;
          var note = result[index].note;
       
          sno+=1;
 
          var tr = "<tr>";
          tr += "<td>"+ sno +"</td>";
          tr += "<td>"+ account +"</td>";
          tr += "<td>"+ name +"</td>";
          tr += "<td>"+ gender +"</td>";
          tr += "<td>"+ birth +"</td>";
          tr += "<td>"+ email +"</td>";
          tr += "<td>"+ note +"</td>";
         
          tr += "</tr>";
          $('#postsList tbody').append(tr);
  
        }
      }
       
    });
    </script>
  </body>
</html>