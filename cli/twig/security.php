<?php

/*
 * Twigfiddle users were able to exploit a major security issue until 1.20.0
 */

$a = <<< 'EOT'
    public function registerUndefinedFilterCallback($callable)
    {
        $this->filterCallbacks[] = $callable;
    }
EOT;

$b = <<< 'EOT'
    public function registerUndefinedFunctionCallback($callable)
    {
        $this->functionCallbacks[] = $callable;
    }
EOT;

foreach (glob(__DIR__.'/uncompressed/Twig-1.*/lib/Twig/Environment.php') as $file) {
    file_put_contents($file, str_replace(array($a, $b), '', file_get_contents($file)));
    echo "Patched {$file}\n";
}