<?php
   require_once "../lib/unsupervised/kmeans.php";

   class KMeansTest extends PHPUnit_Framework_TestCase
   {
      public function testInitCentroids()
      {
            $xs = array(
                array(1,2,3,4),
                array(4,2,5,1),
                array(9,1,5,4),
                array(2,5,2,1),
                array(9,9,11,12),
                array(99, 1, 1, 2),
                array(44,21,41, 2)
            );

            $shown_up = array();
            for($i=0; $i<300; $i++) {
               $centroids = _ll_init_centroids($xs, 2);
               foreach($centroids as $centroid)
               {
                  // make sure they were assigned to points that exist.
                  $position = array_search($centroid, $xs);
                  $this->assertTrue($position !== false);
                  $shown_up[$position] = true;
               }
               $this->assertEquals(count($centroids), 2);
               $this->assertFalse(_ll_init_centroids($xs, 100));
            }

            $this->assertTrue(count($shown_up) == count($xs), "Some \$xs did not show up in 300 samples (shown up = " . count($shown_up) . ", xs = " . count($xs) . ".");

      }

      public function testDistanceToCentroid()
      {
         $result = __ll_distance_to_centroid(array(1,1,1,1), array(3,3,3,3));
         $this->assertEquals(4, $result);
         $result = __ll_distance_to_centroid(array(1,1,1,1), array(3,1,3,3));
         $this->assertEquals(3.46, round($result, 2));
         $result = __ll_distance_to_centroid(array(1,2), array(2,1));
         $this->assertEquals(sqrt(2), $result);
         $result = __ll_distance_to_centroid(array(0,0), array(0,0));
         $this->assertEquals(0, $result);
         $result = __ll_distance_to_centroid(array(5.3,5.3), array(5.3,5.3));
         $this->assertEquals(0, $result);
         $result = __ll_distance_to_centroid(array(1000), array(20));
         $this->assertEquals(980, $result);
      }

      public function testFindClosestCentroid()
      {
         $x = array(1,1,1,1);
         $centroids = array(
             array(2,2,2,2),
             array(3,3,3,3),
             array(4,4,4,4),
             array(5,5,5,5),
             array(1,3,3,5),
             array(1,6,7,8),
             array(9,9,9,9),
             array(100,100,100,100)
         );

         $this->assertEquals(0,(_ll_closest_centroid($x, $centroids)));
         for($i=0;$i<100;$i++) {
            shuffle($centroids);
            $expected = array_search(array(2,2,2,2), $centroids);
            $this->assertEquals($expected,(_ll_closest_centroid($x, $centroids)));
         }

         $x = array(100,100,100,100);
         $expected = array_search(array(100,100,100,100), $centroids);
         $this->assertEquals($expected,(_ll_closest_centroid($x, $centroids)));

         $x = array(6,4,6,4);
         $expected = array_search(array(5,5,5,5), $centroids);
         $this->assertEquals($expected,(_ll_closest_centroid($x, $centroids)));
      }

      public function testFlip()
      {
         $array = array(
                     array(1,2),
                     array(1,2)
         );

         $this->assertEquals(array(array(1,1), array(2,2)), __ll_flip($array));


         // non square test.
         $array = array(
                     array(5,9,8),
                     array(1,2,3)
         );

         $this->assertEquals(array(array(5,1), array(9,2), array(8,3)), __ll_flip($array));


         $array = array(
                     array(0.1,0.2,0.3),
                     array(1,2,3),
                     array(-1,-2,-3)
         );

         $this->assertEquals(array(array(0.1,1,-1), array(0.2,2,-2), array(0.3,3,-3)), __ll_flip($array));
      }

      public function testRepositionCentroids() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
      }

      public function testkMeans() {
                 $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
      }
   }
?>