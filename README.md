diarmuidie/envpopulate
=============

PHP tool to interactively populate a `.env` file based on an `.env.example` file whenever Composer installs or updates.

Based on the similar package [Incenteev/ParameterHandler](https://github.com/Incenteev/ParameterHandler). Go check that out if you need to save  parameters to a YAML file instead of a env file :+1:.

Developed by [Diarmuid](https://diarmuid.ie/).

[![Latest Stable Version](https://poser.pugx.org/diarmuidie/envpopulate/v/stable)](https://packagist.org/packages/diarmuidie/envpopulate)
[![License](https://poser.pugx.org/diarmuidie/envpopulate/license)](https://packagist.org/packages/diarmuidie/envpopulate)

Installation
------------

You can install EnvPopulate through [Composer](https://getcomposer.org):

```shell
$ composer require diarmuidie/envpopulate
```

To trigger the EnvPopulate script on composer install and updates you have to add the following scripts to your `composer.json` file:

```JSON
{
    "scripts": {
        "post-install-cmd": [
            "Diarmuidie\\EnvPopulate\\ScriptHandler::populateEnv"
        ],
        "post-update-cmd": [
            "Diarmuidie\\EnvPopulate\\ScriptHandler::populateEnv"
        ]
    }
}
```

#### Optional
If you want to change the location of the example or generated env file, or add multiple files you can also add an extra section to the `composer.json` file:
```JSON
"extra": {
    "env-populate": {
        "files": [
            {
                "example-file": ".env.server.dist",
                "generated-file": ".env.server"
            },
            {
                "example-file": ".env.dist",
                "generated-file": ".env"
            }
        ]
    }
}
```

You can also specify if you want to not run the env populate scripts when in non-interactive mode (automated systems):
```JSON
"extra": {
    "env-populate": {
        "run-non-interactively": false
    }
}
```


Usage
-----

Make sure you have an `.env.example` file in the root of your project (or in a different location by [setting the extra options](#optional).

The script will run every time you do a `composer install` or `composer update`. Press enter to accept the default value or type to overwrite it.

See it in action:

[![asciicast](https://asciinema.org/a/7tkeaspz0wqahr314p7khlehh.png)](https://asciinema.org/a/7tkeaspz0wqahr314p7khlehh)

#### Disabling in Automated Environments

You can disable the EnvPopulate script in automated environments (CI Server like Travis or Jenkins for example). You have two options to do this:

- Run composer with `--noscripts` option. This will stop any scripts from being run (including EnvPopulate).

- Run Composer in non interactive mode (`--no-interaction`). This will cause EnvPopulate to take the default values from `.env.example`, when generating the `.env` file, without asking for user input.

Contributing
---------

Feel free to contribute features, bug fixes or just helpful advice :smile:

1. Fork this repo
2. Create a feature branch
3. Submit a PR
...
4. Profit :sunglasses:

## Running Tests
You can run the tests through Docker:

```
# Build the dockerfile:
$ docker build . --tag envpopulate:latest

# Run the tests
$ docker run envpopulate:latest composer run-script test
```

Changelog
---------

See the [CHANGELOG.md](https://github.com/diarmuidie/EnvPopulate/blob/master/CHANGELOG.md) file.


Authors
-------

- [Diarmuid](http://diarmuid.ie)


License
-------

The MIT License (MIT)

Copyright (c) 2016 Diarmuid

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
