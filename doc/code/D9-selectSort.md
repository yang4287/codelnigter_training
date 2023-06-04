# 選擇排序法

紀錄最小的位置，內迴圈回合結束才交換

    function swap(&$x, &$y) {
        $t = $x;
        $x = $y;
        $y = $t;
    }

   
    function selection_sort(&$arr) {
        for ($i = 0; $i < count($arr) - 1; $i++) {
            $min = $i;
            for ($j = $i + 1; $j < count($arr); $j++)
                if ($arr[$min] > $arr[$j])
                    $min = $j;  //紀錄最小位置
            swap($arr[$min], $arr[$i]); //交換
        }
    }