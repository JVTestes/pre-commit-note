# Pre commit hook

Checks in our pre-commit hook

**INSTALLATION:**

The recommended way to install this library is through [Composer](http://getcomposer.org):

add `jv-testes/pre-commit-note` as a composer dependency.

composer.json
```bash
"require-dev": {
    ...
    "jv-testes/pre-commit-note": ">=1.0.7"
}
```

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

**Enable slack**

Edit config.xml

    ...
    <run>
        <slack>true</slack>
    </run>
    
    <slackConfig>
        <username>webhookbot</username>
        <channel>#channel</channel>
        <icon>:scissors:</icon>
        <url>https://hooks.slack.com/services/YOU/KEY</url>
    </slackConfig>
    ...

Remembering to set up the hooks


Custom config
--------------

Copy arquives `vendor/jv-testes/pre-commit/config/*` to [project]
