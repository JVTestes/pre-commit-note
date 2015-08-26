<?php

namespace PreCommitNote\Composer\Script;

use Composer\Script\Event;
use PreCommit\Composer\Script\Hooks as PreCommitHooks;

class Hooks extends PreCommitHooks
{
    public static function postHooks(Event $event)
    {
        $io = $event->getIO();
        $gitHook = strval(static::config()->dir->git).
            DIRECTORY_SEPARATOR.'hooks'.
            DIRECTORY_SEPARATOR.'pre-commit';

        $docHook = strval(static::config()->dir->vendor).
            DIRECTORY_SEPARATOR.'jv-testes'.
            DIRECTORY_SEPARATOR.'pre-commit-note'.
            DIRECTORY_SEPARATOR.'pre-commit';

        if (file_exists($docHook)) {
            unlink($docHook);
        }

        $hook = fopen($docHook, 'w+');
        fwrite($hook, static::createHook());
        fclose($hook);

        copy($docHook, $gitHook);
        chmod($gitHook, 0777);
        
        $io->write('<info>Pre-commit created!</info>');

        return true;
    }

    protected static function createHook()
    {
        $load = strval(static::config()->dir->vendor).DIRECTORY_SEPARATOR.'autoload.php';

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $load = str_replace('\\', '\\\\', $load);
    }

        $hook = <<< EOT
#!/usr/bin/php

<?php

require_once "$load";

use PreCommitNote\Composer\Script\CodeQualityToolNote;

\$console = new CodeQualityToolNote('Code Quality Tool Note', '1.0.0');
\$console->run();

EOT;

        return $hook;
    }
}
