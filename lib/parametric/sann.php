<?php
require_once dirname(__FILE__) . '/../accessory/functions.php';

## Default annealing functions #################################################
function linear_cooling($t, $max=10, $subratio=0.05)
{
	return $max - ($subratio*$t);
}

function exponential_cooling($t, $maxt=1000, $initT=10, $alpha=0.9)
{
	if ($t >= $maxt)
		return 0;
	else
		return $initT*pow($alpha, $t);
}

function unbounded_exponential_cooling($t, $initT=10, $alpha=0.99, $delta=0.0001)
{
	$T = $initT*pow($alpha, $t);
	if ($T < $delta)
		$T = 0;
	return $T;
}

function logarithmic_cooling($t, $maxt=151, $c=2.0)
{
	if ($t >= $maxt)
	{
		return 0;
	}
	else
	{
		$return = $c/(log($t+1, 2) + 1);
		return $return;
	}
}

function default_accept($curr, $candidate, $t, $T, $currVal, $candVal, $norm)
{
	$delta		= $norm*($candVal - $currVal);
	
	// If the new score is better than the old score.
	if ($delta > 0)
		return true;
	
	// Randomly accept new worse solutions.
	$p = exp(-1*$delta/$T);
	$q = unirandf();
	if ($p > $q)
	{
		return true;
	}
	
	return false;
}
################################################################################

## Simulated Annealing Algorithms ##############################################
// Discrete Simulated annealing.
// $nF returns a randomly selected neighbour of next.
// $currS passed in as initial state to start from.
function sann($currS, $objF, $nF, $coolingF, $acceptF, $minimize=true)
{
	$norm = 1;
	if ($minimize)
	{
		$norm = -1;
	}
	
	$currVal = $objF($currS);
	$bestS	 = $currS;
	$bestVal = $currVal;
	$t = 0;
	$T;
	while (($T = $coolingF($t)) > 0)
	{
		#var_dump("temperature: " . $T);
		$candidateS	= $nF($currS);
		$candVal	= $objF($currS);
		if ($acceptF($currS, $candidate, $t, $T, $curVal, $candVal,
				$norm))
		{
			$currS		= $candidateS;
			$currVal	= $candVal;
			
			if ($norm*($candVal - $bestVal) > 0)
			{
				$bestVal = $candVal;
				$bestS   = $candidateS;
			}
		}
		$t++;
	}
	return $bestS;
}
################################################################################
