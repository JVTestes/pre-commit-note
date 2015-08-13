<?php

namespace PreCommitNote\Composer\Script;

use PreCommit\Composer\Script\Hooks as PreCommitHooks;

class Hooks extends PreCommitHooks
{
    public static function postHooks(Event $event)
    {
        $io = $event->getIO();
        $gitHook = static::gitDir().
            DIRECTORY_SEPARATOR.'hooks'.
            DIRECTORY_SEPARATOR.'pre-commit';

        $docHook = ROOT_DIR.
            DIRECTORY_SEPARATOR.'vendor'.
            DIRECTORY_SEPARATOR.'jv-testes'.
            DIRECTORY_SEPARATOR.'pre-commit-note'.
            DIRECTORY_SEPARATOR.'hooks'.
            DIRECTORY_SEPARATOR.'pre-commit';

        symlink($docHook, $gitHook);
        chmod($gitHook, 0777);

        $io->write('<info>Pre-commit created!</info>');

        return true;
    }
}
