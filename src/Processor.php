<?php

namespace Diarmuidie\EnvPopulate;

use Composer\IO\IOInterface;
use Diarmuidie\EnvPopulate\File\Factory\FileFactory;

class Processor
{
    protected $io;
    protected $fileFactory;

    protected $exampleFile;
    protected $generatedFile;

    protected $defaultConfig = array(
        'example-file' => '.env.example',
        'generated-file' => '.env'
    );

    public function __construct(IOInterface $io, FileFactory $fileFactory)
    {
        $this->io = $io;
        $this->fileFactory = $fileFactory;
    }

    public function processFile(array $config)
    {
        $procesedConfig = $this->processConfig($config);

        $this->loadFiles(
            $procesedConfig['example-file'],
            $procesedConfig['generated-file']
        );

        $unsetValues = $this->findUnsetValues();
        if (!empty($unsetValues)) {
            $this->getUnsetValues($unsetValues);
            $this->saveFile();
        }
    }

    protected function processConfig($config)
    {
        return array_merge($this->defaultConfig, $config);
    }

    protected function loadFiles($exampleFile, $generateFile)
    {
        $this->exampleFile = $this->fileFactory->create($exampleFile);
        if (!$this->exampleFile->fileExists()) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The example env file "%s" does not exist. Check your example-file config or create it.',
                    $exampleFile
                )
            );
        }
        $this->exampleFile->load();

        $this->generatedFile = $this->fileFactory->create($generateFile);
        if ($this->generatedFile->fileExists()) {
            $this->generatedFile->load();
        }
    }

    protected function findUnsetValues()
    {
        $exampleValues = $this->exampleFile->getVariables();
        $generatedValues = $this->generatedFile->getVariables();

        $unsetEnvValues = array_diff_key($exampleValues, $generatedValues);

        return array_keys($unsetEnvValues);
    }

    protected function getUnsetValues(array $unsetValues)
    {
        if ($this->io->isInteractive()) {
            $this->interactiveGetUnsetValues($unsetValues);
        } else {
            $this->nonInteractiveGetUnsetValues($unsetValues);
        }
    }

    protected function interactiveGetUnsetValues(array $unsetValues)
    {
        $exampleValues = $this->exampleFile->getVariables();

        $isStarted = false;
        foreach ($unsetValues as $key) {
            if (!$isStarted) {
                $isStarted = true;
                $this->io->write(
                    '<comment>Some parameters are missing from '
                    . $this->generatedFile->getFilename()
                    . '. Please provide them.</comment>'
                );
            }

            $default = $exampleValues[$key];
            $value = $this->io->ask(
                sprintf('<question>%s</question> (<comment>%s</comment>): ', $key, $this->convertToString($default)),
                $default
            );
            $this->generatedFile->setVariable($key, $value);
        }
    }

    protected function nonInteractiveGetUnsetValues(array $unsetValues)
    {
        $exampleValues = $this->exampleFile->getVariables();

        foreach ($unsetValues as $key) {
            $this->generatedFile->setVariable($key, $exampleValues[$key]);
        }
    }

    protected function saveFile()
    {
        $this->generatedFile->save();
    }

    protected function convertToString($value)
    {
        if (is_bool($value)) {
            $value = ($value) ? 'true' : 'false';
        }
        return (string) $value;
    }
}
