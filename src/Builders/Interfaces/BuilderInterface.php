<?php
namespace App\Builders\Interfaces;

interface BuilderInterface
{
    /**
     * @return self
     */
    public function reset(): self;

    /**
     * @return array
     */
    public function build(): array;

    /**
     * @return self
     */
    public function withTimestamps(): self;
}