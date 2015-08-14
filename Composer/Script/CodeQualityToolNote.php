<?php

namespace PreCommitNote\Composer\Script;

$fileDir = dirname(dirname(__FILE__));
$vendorDir = dirname(dirname(dirname(dirname($fileDir))));

define('VENDOR_DIR', $vendorDir);

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PreCommit\Composer\Script\CodeQualityTool;

class CodeQualityToolNote extends CodeQualityTool
{
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $xml = null;

        $config = dirname(VENDOR_DIR).DIRECTORY_SEPARATOR.'config.xml';
        if (file_exists($config)) {
            $xml = simplexml_load_file($config);
        }
        
        $archive = null;
        $fatalError = [];
        $errorJS = 0;
        $errorPHP = 0;
        $warningPHP = 0;
        
        $output->writeln('<fg=white;options=bold;bg=red>Code Quality Tool Note</fg=white;options=bold;bg=red>');
        $output->writeln('<info>Fetching files</info>');
        $files = $this->extractCommitedFiles();

        $output->writeln('<info>Running PHPLint</info>');
        $phpLint = $this->phpLint($files);
        if ($phpLint !== true) {
            $this->output->writeln(sprintf('<error>%s</error>', $phpLint['line']));
            $this->output->writeln(sprintf('<error>%s</error>', $phpLint['error']));
            $errorPHP += count($fatalError[]);
        }

        if (!isset($xml->run->phpcsfix) || ($xml->run->phpcsfix == 'true')) {
            $output->writeln('<info>Checking code style</info>');
            $codeStyle = $this->codeStyle($files);
            if ($codeStyle !== true) {
                $this->output->writeln(sprintf('<error>%s</error>', $codeStyle));
            }
        }

        $output->writeln('<info>Checking code style with PHPCS</info>');
        $codeStylePsr = $this->codeStylePsr($files);
        if ($codeStylePsr !== true) {
            $this->output->writeln(sprintf('<error>%s</error>', $codeStylePsr));
            
            $pos = strpos($codeStylePsr, 'FOUND');
            $rest = substr($codeStylePsr, $pos, 100);
            $explode = explode(' ', $rest);

            $errorPHP += $explode[1];
            $warningPHP += $explode[4];
        }

        $output->writeln('<info>Checking code style with PHPCS JS</info>');
        $codeStyleJS = $this->codeStyleJS($files);
        if ($codeStyleJS !== true) {
            $this->output->writeln(sprintf('<error>%s</error>', $codeStyleJS));

            $pos = strpos($codeStyleJS, 'FOUND');
            $rest = substr($codeStyleJS, $pos, 30);
            $explode = explode(' ', $rest);

            $errorJS = $explode[1];
        }

        $output->writeln('<info>Checking code mess with PHPMD</info>');
        $phpmd = $this->phPmd($files);
        
        if ($phpmd !== true) {
            $this->output->writeln(sprintf('<error>%s</error>', $phpmd['file']));
            $this->output->writeln(sprintf('<error>%s</error>', $phpmd['errorOutput']));
            $this->output->writeln(sprintf('<error>%s</error>', $phpmd['error']));

            //count line erros
            $explode = array_filter(explode("\n", $phpmd['error']));
            $warningPHP += count($explode);
        }

        if (!isset($xml->run->phpunit) || ($xml->run->phpunit == 'true')) {
            $output->writeln('<info>Running unit tests</info>');
            if (!$this->unitTests()) {
                throw new \Exception('Fix the fucking unit tests!');
            }
        }

        $author = null;
        exec('git config --get user.name', $author);

        $message = 'note A. Good work!';

        if (count($fatalError) >= 1) {
            $message = 'note C. warnings ('.$warningPHP.' in PHP) errors ('.$errorJS.' in JS, '.$errorPHP.' in PHP)';
        }

        if (($errorPHP >= 1) || ($errorJS >= 1) || ($warningPHP >= 1) && (count($fatalError) <= 0)) {
            $message = 'note B. warnings ('.$warningPHP.' in PHP) errors ('.$errorJS.' in JS, '.$errorPHP.' in PHP)';
        }

        $output->writeln('<info>Commit '.$author[0].': '.$message.'</info>');
    }
}
