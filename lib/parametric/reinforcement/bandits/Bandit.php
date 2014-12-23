<?php
	interface Bandit {
		public function next($context=null);
		public function reward($picked, $value, $context=null);
	}
?>
