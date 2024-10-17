<?php
require_once __DIR__ . '/vendor/autoload.php';

use ReflectionClass;
use ReflectionMethod;

// Generate README
function generateReadme($className, $readmeFile = 'DOCS.md', $htmlFile = 'docs/DOCS.html')
{
  $reflection = new ReflectionClass($className);
  $doc = "# {$reflection->getShortName()}\n\n";

  // Add link to the HTML documentation
  $doc .= "[Full Documentation](DOCS.html)\n\n";
  $doc .= $reflection->getDocComment() . "\n\n";
  $doc .= "## Methods\n\n";

  foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
    $doc .= "### {$method->getName()}\n\n";
    $doc .= $method->getDocComment() . "\n\n";

    // Example usage
    $params = array_map(fn($param) => '$' . $param->getName(), $method->getParameters());
    $doc .= "Example usage:\n";
    $doc .= "```php\n";
    $doc .= "\$instance->{$method->getName()}(" . implode(', ', $params) . ");\n";
    $doc .= "```\n\n";
  }

  file_put_contents($readmeFile, $doc);
  generateHtmlDocumentation($reflection, $htmlFile);
}

// Generate HTML documentation with Bootstrap 5
function generateHtmlDocumentation(ReflectionClass $reflection, $filePath)
{
  $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$reflection->getShortName()} Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h1 class="mb-4">{$reflection->getShortName()} Documentation</h1>
    <p><a href="../README.md" class="btn btn-primary">View README</a></p>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Class Overview</h5>
            <p>{$reflection->getDocComment()}</p>
        </div>
    </div>
    <h2 class="mt-5">Methods</h2>
HTML;

  foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
    $docComment = $method->getDocComment();
    $params = implode(', ', array_map(fn($param) => '$' . $param->getName(), $method->getParameters()));
    $html .= <<<HTML
    <div class="card mt-3">
        <div class="card-body">
            <h5 class="card-title">{$method->getName()}</h5>
            <p class="card-text">{$docComment}</p>
            <h6>Example usage:</h6>
            <pre><code>&#36;instance->{$method->getName()}($params);</code></pre>
        </div>
    </div>
HTML;
  }

  $html .= <<<HTML
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
HTML;

  file_put_contents($filePath, $html);
}

generateReadme(Joaojkuligowski\Mypersist\Base::class);
