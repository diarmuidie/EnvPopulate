<?php

namespace Diarmuidie\EnvPopulate\File;

interface FileInterface
{
    public function __construct($filename);
    public function fileExists();
    public function load();
    public function getVariables();
    public function setVariable($name, $value);
    public function save();
}
