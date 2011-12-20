<?php

// $xs is a two dimensional array (one dimension being observations, the inner dimension being integral "dimensions" of the observation)
function ll_kmeans($xs, $k)
{
   $centroids = _ll_init_centroids($xs, $k);
   $belongs_to = array();
   do {
      for($i=0;$i<count($xs);$i++)
      {
         // I reversed the order here (to store the centroids as indexes in the array)
         // for complexity reasons.

         $belongs_to[_ll_closest_centroid($xs[$i], $centroids)][] = $i;
      }

      $old_centroids = $centroids;
      $centroids = _ll_reposition_centroids($centroids, $belongs_to, $xs);

      $continue = ($old_centroids == $centroids);
   } while($continue);

   return $belongs_to;
}

function _ll_reposition_centroids($centroids, $belongs_to, $xs)
{
   for($index=0; $index<count($centroids); $index++)
   {
      $my_observations = $belongs_to[$index];
      $my_obs_values = array();
      foreach($my_observations as $obs)
      {
         $my_obs_values[] = $xs[$obs];
      }
      $my_obs_values = __ll_flip($my_obs_values);

      $new_position = array();
      foreach($my_obs_values as $new_dimension)
      {
         // compute the average of all the observation's positions for the centroids new position.
         $new_position[] = array_sum($new_dimension) / count($new_dimension);
      }

      $centroids[$index] = $new_position;
   }
   return $centroids;
}

function __ll_flip($rows)
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

function _ll_closest_centroid($x, $centroids)
{
   $smallest = null;
   $smallest_distance = PHP_INT_MAX;
   foreach($centroids as $index=>$centroid)
   {
      $distance = __ll_distance_to_centroid($x, $centroid);
      if($distance < $smallest_distance)
      {
         $smallest = $index;
         $smallest_distance = $distance;
      }
   }
   return $smallest;
}

function __ll_distance_to_centroid($x, $centroid)
{
   if(count($x) != count($centroid))
      return false;

   $distance = 0;
   for($i=0;$i<count($x);$i++)
   {
      $distance += pow($x[$i] - $centroid[$i], 2);
   }

   return sqrt($distance);
}

function _ll_init_centroids($xs, $k)
{
   $centroids = array();
   for($i=0;$i<$k;$i++)
   {

      $temp_array = array();
      $random = rand(0, count($xs)); // random row from data.
      for($b=0;$b<count($xs[$random]);$b++) {
         $temp_array = $xs[$random][$b];
      }
      $centroids[] = $temp_array;
   }

   return $centroids;
}

?>