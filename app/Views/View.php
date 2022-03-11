<?php
namespace App\Views;

class View
{
    private string $path;
    private array $variables;

    public function __construct(?string $path, array $variables = [])
    {
        $this->path = $path;
        $this->variables = $variables;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getVariables()
    {
        return $this->variables;
    }
}