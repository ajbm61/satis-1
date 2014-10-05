<?php
namespace PEAR\Satis\Event;

use Symfony\Component\EventDispatcher;

class BuildSatisJson extends EventDispatcher\Event
{
    private $repository;

    public function __construct(array $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }
}
