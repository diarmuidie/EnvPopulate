<?php

namespace Diarmuidie\EnvPopulate\File;

use M1\Env\Parser;

class Env implements FileInterface
{
    const FILE_HEADER = "# This file is auto-generated during the composer install\n# Changes will be overwritten";

    protected $envValues = array();
    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function fileExists()
    {
        return is_file($this->filename);
    }

    public function load()
    {
        $env = new Parser(file_get_contents($this->filename));
        $this->envValues = $env->getContent();
    }

    public function getVariables()
    {
        return $this->envValues;
    }

    public function setVariable($name, $value)
    {
        $this->envValues[$name] = $value;
    }

    public function save()
    {
        $contents = $this->formatContents();

        file_put_contents($this->filename, $contents);
    }

    protected function formatContents()
    {
        $contents = self::FILE_HEADER . "\n";

        foreach ($this->envValues as $name => $value) {
            $contents .= $name . "=" . $this->formatValueForEnv($value) . "\n";
        }

        return $contents;
    }

    public function formatValueForEnv($value)
    {
        if (is_bool($value)) {
            return ($value) ? 'true' : 'false';
        }

        if ($value === 'true' || $value === 'false') {
            return $value;
        }

        if (is_int($value)) {
            return $value;
        }

        if (empty($value)) {
            return '';
        }

        $singelAndDoubleQuotes = '"\'';

        return '"' . trim($value, $singelAndDoubleQuotes) . '"';
    }
}
