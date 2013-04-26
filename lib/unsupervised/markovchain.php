<?php
	define("HMM_START_TOKEN", "--HMM_START_TOKEN--");

	/*
		Example usage:

			$string = file_get_contents("input");
	
			$mm = new MarkovChain();
			$mm->train($string, 8);
			var_dump( implode(" -> ", $mm->generate(100)) );

		Its theoretically possible to return the model and "look up" conditional probabilities of events. Also, we store things space separated in the hash which is obviously a source of potential error if your tokenizer does not tokenize on spaces. 
	*/

	class MarkovChain {
		protected $model;
		protected $degree;

		// corpus can be either a string, which will be tokenized or an array which is assumed to already be tokenized.
		public function train($corpus, $degree = 1, $token = "/[\s,]+/") {
			if(!is_array($corpus)) 
				$corpus = $this->tokenize($corpus, $token);
	
			$counts = array();
			$prev = array_fill(0, $degree, HMM_START_TOKEN);
			$this->degree = $degree;

			foreach($corpus as $name) {
				$name = trim($name);
				if($name == "")
					continue;

				$use = $prev;
				for($i = 0; $i < $this->degree; $i++) {
					$pstring = $this->prepareIndex($use);

					if(!isset($this->model[$pstring]))
						$this->model[$pstring] = array();
				
					if(!isset($this->model[$pstring][$name]))
						$this->model[$pstring][$name] = 1;
					else
						$this->model[$pstring][$name] += 1;
				
					if(!isset($counts[$pstring]))
						$counts[$pstring] = 1;
					else
						$counts[$pstring] += 1;
					
					array_shift($use);
				}

				array_shift($prev);
				$prev[] = $name;				
			}

			foreach($this->model as $prev=>$curr) {
				foreach($curr as $k=>$v) {
					$curr[$k] /= $counts[$prev];
				}
				$this->model[$prev] = $curr;
			}

			return true;
		}

		// extend the class to override the tokenizer (or just pass in tokenized in to train)
		protected function tokenize($corpus, $token) {
			return preg_split($token, $corpus);
		}

		protected function prepareIndex($array) {
			// if the tokenizer doesn't tokenize on space, this is a bug; a multi-depth array is a potential memory expensive solution.
			// another potential solution is to use hashes instead here -- we can hash dependent on the depth of the array in order to get a probabilistic solution.
			return implode(" ", $array);
		}

		public function generate($length = null, $start = HMM_START_TOKEN) {
			if(!is_array($start)) {
				$start = array($start);
			}

			if (count($start) != $this->degree) {
				$s = array_fill(0, $this->degree, HMM_START_TOKEN);
				$diff = $this->degree - count($start);
				foreach($start as $val) {
					$s[$diff++] = $val;
				}
				$start = $s;
			}

			$return = array(implode(" ", $start));
			$curr = $start;
			for($i = 0; $i < $length; $i++) {
				$next = $this->generateOne($curr);
				$return[] = $next;
				array_shift($curr);
				$curr[] = $next; 
			}
			return $return;
		}


		protected function generateOne($start) {
			// this does "fall back" generation -- i.e., if there's no matches at the degree N state, try out the degree N-1 state; the degree 1 state will always have a match.
			for($i = 0; $i < $this->degree; $i++) {
				$pstring = $this->prepareIndex($start);
				if(!isset($this->model[$pstring])) {
					array_shift($start);
					continue;
				} 

				$probabilities = $this->model[$pstring];
				$rand = (rand() / getrandmax());
			
				foreach($probabilities as $word => $p) {
					$rand -= $p;
					if($rand <= 0)
						return $word;
				}
			}
		}
	}
?>
