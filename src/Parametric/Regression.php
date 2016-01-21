<?php

namespace Giuseppe\LearningLibrary\Parametric;

use Giuseppe\LearningLibrary\ImproperDataException;

abstract class Regression implements RegressionInterface
{
    protected $xs;
    protected $ys;

    protected $parameters;
    protected $resetParameters;
    protected $trained;

    abstract public function train();

    public function __construct($xs, $ys, $initialParameters = null)
    {
        $this->setData($xs, $ys, $initialParameters);
    }

    public function setData($xs, $ys, $initialParameters = null)
    {
        if (count($xs) !== count($ys))
            throw new ImproperDataException("Your xs array is not the same length as your ys array. (you don't have 'labels' for all the input data).");

        $this->xs = $xs;
        $this->ys = $ys;

        $this->parameters = $initialParameters === null
            ? array_fill(0, count($xs[0]), 0)
            : $initialParameters;

        $this->trained = false;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function reset()
    {
        $this->parameters = $this->resetParameters;
    }

    // adds a one in the first column of each $x { $xs.
    public function addInterceptTerm()
    {
        for ($i = 0, $count = count($this->xs); $i < $count; $i++) {
            array_unshift($this->xs[$i], 1);
        }
    }

    public function predict($x)
    {
        if (!$this->trained)
            return false;

        $prediction = 0;
        foreach ($x as $index => $xi) {
            $prediction += $xi * $this->parameters[$index];
        }
        return $prediction;
    }
}
