<?php
	require_once dirname(__FILE__) . "/Bandit.php";
	class UCB1Bandit implements Bandit {
		private $rewards;
		private $count_rewards;

		// N number of arms
		public function __construct($n) {
			$this->rewards = array_fill(0, $n, 0);
			$this->count_rewards = array_fill(0, $n, 0);
		}

		public function next($context=null) {
			if(($i = array_search(0, $this->count_rewards)) !== false)	// play each arm once.
				return $i;

			$values = array();
			for($i = 0; $i < count($rewards); $i++) {
				$values[$i] = $this->averageFunction($i) + $this->paddingFunction($i);
			}

                        $maxes = array_keys($values, max($values));
                        return array_rand($maxes);
		}

		protected function paddingFunction($i) {
			$total = array_sum($this->count_rewards);
			return sqrt(2 * log($this->count_rewards[$i]/$total));
		}

		protected function averageFunction($i) {
			return $this->rewards[$i] / $this->count_rewards[$i];
		}

		public function reward($picked, $reward, $context=null) {
			$this->rewards[$picked] += $reward;
			$this->count_rewards[$picked]++;;
		}
	}
