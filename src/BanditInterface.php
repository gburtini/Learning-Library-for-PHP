<?php

namespace Giuseppe\LearningLibrary;

interface BanditInterface
{
    public function next($context=null);
    public function reward($picked, $value, $context=null);
}
