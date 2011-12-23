<?php
   class LL_Neuron {
      private $input;
      private $activationFunction;
      private $weights;

      function __construct($inputs, $weights, $activation='ll_sigmoid') {
         $this->input = $inputs;
         $this->weights = $weights;
         $this->activationFunction = $activation;
      }

      public function setWeights($weights)
      {
         if(count($weights) != $this->weights)
            trigger_error("Weights changed size. No can do.");

         $this->weights = $weights;
      }
      public function getWeights()
      {
         return $this->weights;
      }
      public function setInputs($input)
      {
         if(count($input) != $this->input)
            trigger_error("Inputs changed size. No can do.");

         $this->input = $input;
      }
      public function getInputs()
      {
         return $this->inputs;
      }

      public function output() {
         $logistic = 1;
         for($i=0; $i<count($this->input); $i++)
         {
            $logistic += $this->weights[$i] * $this->input[$i];
         }
         return $this->activationFunction($logistic);
      }
   }

   class LL_NeuralNetwork
   {
      private $inputNodes;
      private $outputNodes;
      private $hiddenNodes = array();

      private $alpha = 0.3;
      private $activationFunction = "ll_sigmoid";
      /* Algorithm thanks to: Chris Marriott, Ryan Shirley, CJ Baker, Thomas Tannahill */
      /* Understanding thanks to: Andrew Ng, Jiquan Ngiam, Chuan Yu Foo, Yifan Mai, Caroline Suen;
              http://ufldl.stanford.edu/wiki/index.php/UFLDL_Tutorial */

      function __construct($layers, $size) {
         for($i=0; $i<$layers; $i++) {
            $weights = $inputs = array_fill(0, $size, 0);

            $this->hiddenNodes[$i] = new LL_Neuron($inputs, $weights, $this->activationFunction);
         }
      }

      public function predict($inputNodes)
      {
         $this->inputNodes = $inputNodes;
         // iterates through the NN and computes an output array.
      }

      private function forwardPropagation() {

      }
      public function learn()
      {
         // trains the parameters in the array (via backprop?)
      }

      private function activation($value) {
         return $this->activationFunction($value);
      }
      public function derivative_activation($value) {
         // override this if you're not talking about tanh/sigmoid

         if($this->activationFunction == "tanh")
            return 1 - pow($this->activationFunction($value), 2);
         else
            return ($this->activationFunction($value) * (1-$this->activationFunction($value)));
      }

   }
?>