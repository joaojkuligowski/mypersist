<?php
require_once 'vendor/autoload.php';

use ReflectionClass;
use ReflectionMethod;

function generateReadme($className, $filePath = 'DOCS.md')
{
  $reflection = new ReflectionClass($className);
  $doc = "# {$reflection->getShortName()}\n\n";
  $doc .= $reflection->getDocComment() . "\n\n";
  $doc .= "## Methods\n\n";

  foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
    $doc .= "### {$method->getName()}\n\n";
    $doc .= $method->getDocComment() . "\n\n";

    // Exemplo de chamada
    $params = array_map(function ($param) {
      return '$' . $param->getName();
    }, $method->getParameters());

    $doc .= "Example:\n";
    $doc .= "```php\n";
    $doc .= "\$instance->{$method->getName()}(" . implode(', ', $params) . ");\n";
    $doc .= "```\n\n";
  }

  file_put_contents($filePath, $doc);
}

generateReadme(Joaojkuligowski\Mypersist\Base::class);
