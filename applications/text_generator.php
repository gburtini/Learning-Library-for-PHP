<?php
	ini_set("memory_limit", "8G");
	// Uses a Markov Chain to generate text.
	/*
		Synopsis:
			php text_generator.php training_file length [degree] [start word]
		
		training_file is a path to the file to train the Markov Chain on.
		length is the number of words to output
		degree is the "order" or degree of the Markov Chain to use; must be integral; larger will result in more coherent, but less unique text.
		start word is the first word (or phrase) of your output; it must be less than or equal to the degree in length.


		Note that our tokenizer sucks (its a regular expression on [\s+],). You can pretokenize both $string (the file read in) and $start if you like in the below to improve the performance w.r.t. punctuation and the like.
	*/

	require_once "../lib/unsupervised/markovchain.php";

	if(count($argv) < 3) 
		die("Synopsis:  php text_generator.php training_file length [degree] [start word]\n");

	$train = $argv[1];
	$length = intval($argv[2]);
	if(isset($argv[3])) {
		if(is_numeric($argv[3]))
			$degree = intval($argv[3]);
		else
			$start = $argv[3];
	
		if(isset($argv[4])) {
			if(is_numeric($argv[4]) && !isset($degree))
				$degree = intval($argv[4]);
			else
				$start = $argv[4];
		}
	}

	if(!isset($degree))
		$degree = 1;
	if(!isset($start))
		$start = HMM_START_TOKEN;

	if(!is_readable($train)) {
		die("Training file <{$train}> is not readable.");
	}

	$string = file_get_contents($train);	
	$mm = new MarkovChain();
	$mm->train($string, $degree);
		
	$generated = $mm->generate($length, $start);
	while($generated[0] == HMM_START_TOKEN) 
		array_shift($generated);
	
	echo "\n" . implode("", $generated);


