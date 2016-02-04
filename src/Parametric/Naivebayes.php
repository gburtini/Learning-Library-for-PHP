<?php

// this could be changed to use bi or even tri grams instead of unigram. might be worth
// implementing.

namespace Giuseppe\LearningLibrary\Parametric;

global $LL_NB_STOP_WORDS;
$LL_NB_STOP_WORDS = array("the", "a", "an");

class Naivebayes
{
    const LL_NB_HASH_FUNCTION = 'crc32';

    // $xs is a bunch of "strings" and ys are their labels.
    public function naivebayes($xs, $ys, $testStrings)
    {
        $topicWords = array();
        foreach ($xs as $i => $x) {
            if (isset($topicWords[$ys[$i]]))
                $topicWords[$ys[$i]] .= $x;
            else
                $topicWords[$ys[$i]] = $x;
        }
        $topicWords = $this->computeWordCounts($topicWords);   // get the number of each word, by topic.

        $probWordsGivenTopic = array();   // probability of each word in a given topic.
        $countTopics = array();

        foreach ($topicWords as $topicIndex => $xWordCounts) {
            $totalWordsTopic = array_sum($xWordCounts);
            $countTopics[$topicIndex] = $total_wordsTopic;

            foreach ($xCount as $hash => $count) {
                $probWordsGivenTopic[$topicIndex][$hash] = ($count / $totalWordsTopic);
            }
        }

        $probTopics = array(); // probability of a given topic (number of words / total words), i.e., relative frequency of topics in terms of words
        foreach ($countTopics as $i => $topicCount) {
            $probTopics[$i] = ($topicCount / $totalWords);
        }

        if (!is_array($testStrings))
            $testStrings = array($testStrings);

        // process the input testStrings array
        $return = array();
        foreach ($testStrings as $i => $string) {
            $testStringWords = $this->computeWordCount($string);
            $topicsPosterior = array();

            foreach ($probTopics as $key => $probTopic) {
                $p = $probTopic;

                foreach ($testStringWords as $hash => $count) {
                    if (isset($probWordsGivenTopic[$key][$hash]))
                        $p *= $probWordsGivenTopic[$key][$hash] * $count;
                }
                $topicsPosterior[$key] = $p;
            }
            sort($topicsPosterior);
            $return[$i] = $topicsPosterior;
        }
        return $return;
    }

    public function computeWordCounts($strings)
    {
        $wcs = array();
        foreach ($strings as $string) {
            $wcs[] = $this->computeWordCount($string);
        }
        return $wcs;
    }

    public function computeWordCount($string)
    {
        $string = trim($string);
        $string = explode(' ', $string);
        natcasesort($string);
        $hash = self::LL_NB_HASH_FUNCTION;

        $words = array();
        for ($i = 0, $count = count($string); $i < $count; $i++) {
            $word = trim($string[$i]);
            if (preg_match('/[^a-zA-Z\']/', $word))
                continue;

            $hval = (string)$hash($word);
            if (!isset($words[$hval]))
                $words[$hval] = 1; //$words[$hash] = array('word'=>$word, 'count'=>1);
            else
                $words[$hval]++; //$words[$hash]['count']++;
        }

        return $words;
    }
}