<?php
/*
 * Density-Based Clustering
 *
 * Domenica Arlia, Massimo Coppola. "Experiments in Parallel Clustering with DBSCAN". Euro-Par 2001: Parallel Processing: 7th International Euro-Par Conference Manchester, UK August 28–31, 2001, Proceedings. Springer Berlin.
 * Hans-Peter Kriegel, Peer Kröger, Jörg Sander, Arthur Zimek (2011). "Density-based Clustering". WIREs Data Mining and Knowledge Discovery 1 (3): 231–240. doi:10.1002/widm.30.
 */

function ll_dbscan($data, $e, $minimumPoints=10) {
   $clusters = array();
   $visited = array();

   foreach($data as $index=>$datum) {
      if(in_array($index, $visited))
         continue;

      $visited[] = $index;

      $regionPoints = _ll_points_in_region(array($index=>$datum), $data, $e);
      if(count($regionPoints) >= $minimumPoints) {
         $clusters[] = _ll_expand_cluster(array($index=>$datum), $regionPoints, $e, $minimumPoints, &$visited);
      }
   }
   
   return $clusters;
}

function _ll_points_in_region($point, $data, $epsilon) {
   $region = array();
   foreach($data as $index=>$datum) {
      if(ll_euclidian_distance($point, $datum) < $epsilon) {
         $region[$index] = $datum;
      }
   }
   return $region;
}

function _ll_expand_cluster($point, $data, $epsilon, $minimumPoints, &$visited) {
   $cluster = array( $point );

   foreach($data as $index=>$datum) {
      if(!in_array($index, $visited)) {
         $visited[] = $index;
         $regionPoints = _ll_points_in_region(array($index=>$datum), $data, $epsilon);

         if(count($regionPoints) > $minimumPoints) {
            $cluster = _ll_join_clusters($regionPoints, $cluster);
         }
      }

      // supposed to check if it belongs to any clusters here.
      // only add the point if it isn't clustered yet.
      $cluster[] = array($index=>$datum);
   }
   return $cluster;
}

function _ll_join_clusters($one, $two) {
   return array_merge($one, $two);
}


?>
