<?php

use CodeIgniter\CLI\CLI;
CLI::write('[' . get_class($exception) . ']', 'light_gray', 'red');
CLI::write($message);
CLI::write('at ' . CLI::color(clean_path($exception->getFile()) . ':' . $exception->getLine(), 'green'));
CLI::newLine();
$last = $exception;

while ($prevException = $last->getPrevious()) {
    $last = $prevException;
    $prevExceptionClass = get_class($prevException);

    CLI::write('  Caused by:');
    CLI::write('  [' . $prevExceptionClass . ']', 'red');
    CLI::write('  ' . $prevException->getMessage());
    CLI::write('  at ' . CLI::color(clean_path($prevException->getFile()) . ':' . $prevException->getLine(), 'green'));
    CLI::newLine();
}

// The backtrace
if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) {
    $backtraces = $last->getTrace();

    if ($backtraces) {
        CLI::write('Backtrace:', 'green');
    }

    foreach ($backtraces as $i => $error) {
        $padFile  = '    '; // 4 spaces
        $padClass = '       '; // 7 spaces
        $c        = str_pad($i + 1, 3, ' ', STR_PAD_LEFT);

        if (isset($error['file'])) {
            $filepath = clean_path($error['file']) . ':' . $error['line'];

            CLI::write($c . $padFile . CLI::color($filepath, 'yellow'));
        } else {
            CLI::write($c . $padFile . CLI::color('[internal function]', 'yellow'));
        }

        $function = '';

        if (isset($error['class'])) {
            $type = ($error['type'] === '->') ? '()' . $error['type'] : $error['type'];
            $function .= $padClass . $error['class'] . $type . $error['function'];
        } elseif (!isset($error['class']) && isset($error['function'])) {
            $function .= $padClass . $error['function'];
        }

        $args = implode(
            ", ",
            array_map(
                static function ($value) {
                    // return match (true) {
                    //     is_object($value) => "Object($value::class)",
                    //     is_array($value)  => count($value) ? '[...]' : '[]',
                    //     $value === null   => 'null', // return the lowercased version
                    //     default           => var_export($value, true),
                    // };
                    if(is_object($value)) {
                        return "Object(" . get_class($value) . ")";
                    }
                    if(is_array($value)) {
                        return count($value) ? '[...]' : '[]';
                    }
                    if($value == null) {
                        return "null";
                    }
                    return var_export($value, true);
                },
                array_values($error['args'] ?? [])
            )
        );

        $function .= '(' . $args . ')';

        CLI::write($function);
        CLI::newLine();
    }
}
