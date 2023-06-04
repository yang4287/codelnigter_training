# 冒泡排序

函數swap為交換
內迴圈在比較相鄰的元素。如果第一個比第二個大，就交換它們兩個。  
外圍迴圈代表每一回合結束會把最大的元素擺在後面。



        function swap(&$x, &$y) {
            $t = $x;
            $x = $y;
            $y = $t;
        }

        function bubble_sort(&$arr) {
            for ($i = 0; $i < count($arr) - 1; $i++)
                for ($j = 0; $j < count($arr) - 1 - $i; $j++)
                    if ($arr[$j] > $arr[$j + 1]) //相鄰元素左邊大於右邊時
                        swap($arr[$j], $arr[$j + 1]); //交換
        }

        $arr = array(21, 34, 3, 32, 82, 55, 89, 50, 37, 5, 64, 35, 9, 70);
        bubble_sort($arr);
        for ($i = 0; $i < count($arr); $i++)
            echo $arr[$i] . ' ';