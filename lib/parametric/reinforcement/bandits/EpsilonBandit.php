<?php
	require_once dirname(__FILE__) . "/Bandit.php";
	class EGreedyBandit implements Bandit {
		private $e;
		private $est_rewards;
		private $count_rewards;

		// N number of arms, E explore ratio
		public function __construct($n, $e=0.2) {
			$this->e = $e;
			$this->est_rewards = array_fill(0, $n, 0);
			$this->count_rewards = array_fill(0, $n, 0);
		}

		public function next($context=null) {
			if(rand()/getrandmax() < $e) {
				return $this->explore();
			} else {
				return $this->exploit();
			}
		}

		protected function explore() {
			return rand(0, $n);			
		}

		protected function exploit() {
			$maxes = array_keys($this->est_rewards, max($this->est_rewards));
			return array_rand($maxes);
		}

		public function reward($picked, $reward, $context=null) {
			$sum = $this->est_rewards[$picked] * $this->count_rewards[$picked];
			$this->est_rewards[$picked] = ($sum + $reward) / ($this->count_rewards[$picked] + 1);
			$this->count_rewards[$picked]++;
		}
	}
