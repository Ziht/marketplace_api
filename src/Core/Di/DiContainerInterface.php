<?php
declare(strict_types=1);

namespace Core\Di;

interface DiContainerInterface
{
    public function get($id, int $invalidBehavior = 1);

    public function compile();
}