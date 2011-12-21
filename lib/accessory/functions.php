<?php
function ll_transpose($rows)
{
   $columns = array();
   for($i=0;$i<count($rows);$i++)
   {
      for($k = 0; $k<count($rows[$i]); $k++)
      {
         $columns[$k][$i] = $rows[$i][$k];
      }
   }
   return $columns;
}

function ll_mean($array) {
   return array_sum($array)/count($array);
}

function ll_variance($array) {
   $mean = ll_mean($array);

   $sum_difference = 0;
   $n = count($array);

   for($i=0; $i<$n; $i++) {
      $sum_difference += pow(($array[$i] - $mean),2);
   }

   $variance = $sum_difference / $n;
   return $variance;
}
?>