<?php
function ll_sign($n) {
	return ($n > 0) - ($n < 0);
}

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

function ll_euclidian_distance($a, $b) {
   if(count($a) != count($b))
      return false;

   $distance = 0;
   for($i=0;$i<count($a);$i++)
   {
      $distance += pow($a[$i] - $b[$i], 2);
   }

   return sqrt($distance);
}

function unirandf()
{
   return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
}
?>
