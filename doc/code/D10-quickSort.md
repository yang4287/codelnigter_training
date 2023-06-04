# 快速排序法
在序列中找出一個元素作為支點(pivot)，比支點小的元素移動到支點元素的左邊，比支點大的元素移動到支點元素的右邊，接著再用同樣的方法繼續對支點的左邊子陣列和右邊子陣列進行排序。

    function quick_sort($arr) {
        $len = count($arr);

        if ($len <= 1)
            return $arr;

        $left = $right = array();
        $mid_value = $arr[0]; //支點(pivot)

        for ($i = 1; $i < $len; $i++)
            if ($arr[$i] < $mid_value)
                $left[] = $arr[$i]; //比支點的元素移動到支點元素的左邊
            else
                $right[] = $arr[$i];//比支點大的元素移動到支點元素的右邊

        return array_merge(quick_sort($left), (array)$mid_value, quick_sort($right));
    }


quick_sort([40,30,10])

    len = 3
    3 > 1
    middle = 40
    i=1 30<40
        left[] = 30  => left=[30]
    i=2 10<40
        left[] = 10  => left=[30,10]

    array_merge(quick_sort($left), (array)$mid_value, quick_sort($right))

        quick_sort($left)
        len = 2
        middle = 30
        i=1 10<30
            left[] = 10 => left = [10]
        合併 left middle right => [10,30]
        

    合併 left middle right => [10,30,40]


