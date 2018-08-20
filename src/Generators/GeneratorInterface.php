<?php

namespace Bmadeiro\LaravelProject\Generators;

interface GeneratorInterface
{
    public function getStub();

    public function getType();

    public function generate($data = []);
}
