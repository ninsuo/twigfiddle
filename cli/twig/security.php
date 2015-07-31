<?php

/*
 * Twigfiddle users are able to run any php function by registering callbacks:
 *
 * {{ _self.env.registerUndefinedFilterCallback("exec") }}
 * {{ _self.env.registerUndefinedFunctionCallback("exec") }}
 *
 * Then:
 *
 * {{ _self.env.getFilter("ls -l") }}
 *
 * This patch remove those functions from every Twig version.
 *
 * Thanks to @moro for his gentle warning.
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

foreach (glob(__DIR__.'/uncompressed/Twig-*/lib/Twig/Environment.php') as $file) {
    file_put_contents($file, str_replace(array($a, $b), '', file_get_contents($file)));
    echo "Patched {$file}\n";
}