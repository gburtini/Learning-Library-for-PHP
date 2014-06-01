<?php
require_once dirname(__FILE__) . '/../LearningLibrary.php';

## Partition Problem Test ######################################################
// How to partition numbers into two sets that are roughly equal in value.

function partition_objective($partitioning)
{
	$sum1 = 0;
	foreach ($partitioning[0] as $element)
	{
		$sum1 += $element;
	}
	
	$sum2 = 0;
	foreach ($partitioning[1] as $element)
	{
		$sum2 += $element;
	}
	
	return abs($sum1 - $sum2);
}

function partition_neighbour($partitioning)
{
	$which = mt_rand(0, 1);
	if (count($partitioning[$which]) == 0)
	{
		$which = abs($which - 1);
	}
	$opposite = abs($which - 1);
	
	if (count($partitioning[$which]) == 0)
		return $partitioning;
	
	// Move the element.
	$toMove = mt_rand(0, count($partitioning[$which]) - 1);
	$partitioning[$opposite][] = $partitioning[$which][$toMove];
	unset($partitioning[$which][$toMove]);
	$partitioning[$which]	   = array_values($partitioning[$which]);
	$partitioning[$opposite]   = array_values($partitioning[$opposite]);
	return $partitioning;
}

$initP = array(
	array(
		1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
	),
	array(
	)
);
$objF = 'partition_objective';
$P = sann($initP,
		$objF,
		'partition_neighbour',
		#'logarithmic_cooling',
		#'linear_cooling',
		#'exponential_cooling',
		'unbounded_exponential_cooling',
		'default_accept',
	true);
$solutionScore = $objF($P);
echo "Objective of found solution: " . $solutionScore  . "\n";
var_dump($P);
################################################################################
