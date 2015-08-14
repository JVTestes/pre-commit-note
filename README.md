# Pre commit hook

Checks in our pre-commit hook

**INSTALLATION:**

The recommended way to install this library is through [Composer](http://getcomposer.org):

add `jv-testes/pre-commit-note` as a composer dependency.

composer.json
```bash
"require-dev": {
    ...
    "jv-testes/pre-commit-note": "1.0.0"
}
```

`php composer.phar update jv-testes/pre-commit-note 1.0.0`


**USAGE:**

When a developer clones the project, it just needs to:

Edit composer.json and add:

```
    "scripts": {
        "pre-update-cmd": "PreCommitNote\\Composer\\Script\\Hooks::preHooks",
        "pre-install-cmd": "PreCommitNote\\Composer\\Script\\Hooks::preHooks",
        "post-update-cmd": "PreCommitNote\\Composer\\Script\\Hooks::postHooks",
        "post-install-cmd": "PreCommitNote\\Composer\\Script\\Hooks::postHooks"
    }
```

Remembering to set up the hooks


Custom config
--------------

Copy arquives `vendor/jv-testes/pre-commit/config/*` to [project]
