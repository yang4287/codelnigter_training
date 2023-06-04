# 費式數列
### 0,1,1,2,3,5,8,13,21,34,.......
---
## 遞迴方法
耗時大  

    function fib($n) {
            if ($n == 0) return 0;
            if ($n == 1) return 1;
            return fib($n-1) + fib($n-2);//遞迴
    }
---
## 迴圈法
耗時少

    function fib($n) {
        $a = 0;
        $b = 1;
        if ($n == 0) return $a;
        if ($n == 1) return $b;
            for ($i = 2; $i < $n + 1; $i++){
                $t = $a; //暫時儲存，$a代表遞迴方法fib($n-2)的值
                $a = $b; //$b代表遞迴方法fib($n-1)的值
                $b = $a + $t; //累加，即遞迴方法fib($n-1)+ fib($n-2)的值
            }
        return $b;
    }