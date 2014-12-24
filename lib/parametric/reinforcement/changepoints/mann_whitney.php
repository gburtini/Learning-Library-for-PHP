<?php
	// Itearted Mann-Whitney. Takes a $list of values and a threshold (as a fraction of values) and returns a list of changepoints according to utilizing the Mann Whitney (rank-sum) statistic. See Pettitt, 1979. The data should be detrended before being specified as $list.
	function iteratedMannWhitney($list, $threshold) {
		$changepoints = array();
		for($i = 0; $i < count($list); $i++) {
			$sum = $counts = 0;
			for($j = $i; $j < count($list); $j++) {
				for($k = 0; $k < $i; $k++) {
					$sum += ll_sign($list[$k] - $list[$j]);
					$counts++;
				}
			}
			if(abs($sum)/$counts > $threshold)
				$changepoints[] = $i;
		}
		return $changepoints;
	}

