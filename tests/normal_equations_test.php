<?php
   require_once "../lib/accessory/matrix.php";
   require_once "../lib/parametric/regression.php";

   class NormalEquationsTest extends PHPUnit_Framework_TestCase {
     function testNormalRegression()
     {

         $xs = array(
            array(1,1),
            array(1,2),
            array(1,3),
            array(1,4)
         );
         $ys = array(2,4,6,8);


         $result = (_ll_normal_equation($xs, $ys));
//         $result2 = _ll_gradient_descent($xs, $ys, null, 0.10, 1000);
         $this->assertEquals($result[0], 0);
         $this->assertEquals($result[1], 2);
     }
   }

