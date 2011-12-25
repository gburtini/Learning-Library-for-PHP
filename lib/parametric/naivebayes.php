<?php

global $LL_NB_STOP_WORDS;
$LL_NB_STOP_WORDS = array("the", "a", "an");
define("LL_NB_HASH_FUNCTION", "crc32");// crc32 is the fastest built in hash function.

// $xs is a bunch of "strings" and ys are their labels.
function ll_naivebayes($xs, $ys, $testStrings) {
   $wordCounts = _ll_computeWordCounts($xs);
   //$wordCounts = _ll_computeTotalWordCounts($wordCounts);

   $probWordsGivenTopic = array();   // probability of each word in a given topic.
   $countTopics = array();
   $totalWords = 0;
   foreach($wordCounts as $i=>$xWordCounts) {
      $totalWordsTopic = array_sum($xWordCounts);
      $countTopics[$i] = $total_wordsTopic;

      foreach($xCount as $hash=>$count) {
         $probWordsGivenTopic[$i][$hash] = ($count/$totalWordsTopic);
      }
   }

   $probTopics = array(); // probability of a given topic (number of words / total words), i.e., relative frequency of topics in terms of words
   foreach($countTopics as $i=>$topicCount) {
      $probTopics[$i] = ($topicCount/$totalWords);
   }

   if(!is_array($testStrings))
      $testStrings = array($testStrings);

   // process the input testStrings array
   $return = array();
   foreach($testStrings as $i=>$string) {
      $testStringWords = _ll_computeWordCount($string);
      $topicsPosterior = array();

      foreach($probTopics as $key=>$probTopic) {
         $p = $probTopic;

         foreach($testStringWords as $hash=>$count) {
            if(isset($probWordsGivenTopic[$key][$hash]))
               $p *= $probWordsGivenTopic[$key][$hash] * $count;
         }
         $topicsPosterior[$key] = $p;
      }
      sort($topicsPosterior);
      $return[$i] = $topicsPosterior;
   }
   return $return;
}

function _ll_computeTotalWordCounts($wordCounts) {
   $total = array();
   foreach($wordCounts as $wc) {
      foreach($wc as $hash=>$value) {
         $total[$hash] += $value;
      }
   }
   return $total;
}

function _ll_computeWordCounts($strings) {
   $wcs = array();
   foreach($strings as $string) {
      $wcs[] = _ll_computeWordCount($string);
   }
   return $wcs;
}

function _ll_computeWordCount($string) {
      $string = trim($string);
      $string = explode(' ', $string);
      natcasesort($string);
      $hash = LL_NB_HASH_FUNCTION;

      $words = array();
      for($i=0, $count = count($string); $i<$count; $i++) {
         $word = trim($string[$i]);
         if(preg_match('/[^a-zA-Z\']/', $word))
            continue;

         $hash = (string) $hash($word);
         if(!isset($words[$hash]))
            $words[$hash] = 1; //$words[$hash] = array('word'=>$word, 'count'=>1);
         else
            $words[$hash]++; //$words[$hash]['count']++;
      }

      return $words;
}
?>
