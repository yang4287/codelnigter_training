# 插入排序法
逐一將原始資料加入已排序好資料中，並逐一與已排序好的資料作比較，找到對的位置插入

    function insertion_sort(&$arr) 
        {
        
            for ($i = 1; $i < count($arr); $i++) 
            {
                if ($arr[$i-1] > $arr[$i]) {//相鄰元素，左邊大於右邊時 
                    $temp = $arr[$i]; //暫時儲存右邊的值
                    for ($j = $i - 1; $j >= 0 && $arr[$j] > $temp; $j--)
                        $arr[$j + 1] = $arr[$j];//大的往後
                    $arr[$j + 1] = $temp; //小的往前插入
                }
            }
        }


insertion_sort([40,30,10])  

    i=1 40>30， 
            t = 30 
            j=0 
            arr[1] = arr[0] => [40,40,10]   40往後移    
        j=-1 arr[0] = 30    => [30,40,10] 已插入30     
    i=2  40>10  
            t = 10 
            j = 1   
            arr[2] = arr[1]  => [30,40,40] 40往後移     
            arr[1] = arr[0]  => [30,30,40] 30往後移     
        j=-1 arr[0] = 10      =>  [10,30,40] 已插入10    