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

        $archive = null;
        $fatalError = [];
        $errorJS = 0;
        $errorPHP = 0;

        $output->writeln('<fg=white;options=bold;bg=red>Code Quality Tool Note</fg=white;options=bold;bg=red>');
        $output->writeln('<info>Fetching files</info>');
        $files = $this->extractCommitedFiles();

        $output->writeln('<info>Running PHPLint</info>');
        $phpLint = $this->phpLint($files);
        if ($phpLint !== true) {
            $fatalError[] = $phpLint['error'];
        }

        $output->writeln('<info>Checking code style</info>');
        $codeStyle = $this->codeStyle($files);
        if ($codeStyle !== true) {
            $this->output->writeln(sprintf('<error>%s</error>', trim($codeStyle)));
        }

        $output->writeln('<info>Checking code style with PHPCS</info>');
        $codeStylePsr = $this->codeStylePsr($files);
        if ($codeStylePsr !== true) {
            $errorPHP++;
        }

        $output->writeln('<info>Checking code style with PHPCS JS</info>');
        $codeStyleJS = $this->codeStyleJS($files);
        if ($codeStyleJS !== true) {
            $errorJS++;
        }

        $output->writeln('<info>Checking code mess with PHPMD</info>');
        $phpmd = $this->phPmd($files);
        if ($phpmd !== true) {
            $this->output->writeln($phpmd['file']);
            $this->output->writeln(sprintf('<error>%s</error>', trim($phpmd['errorOutput'])));
            $this->output->writeln(sprintf('<info>%s</info>', trim($phpmd['error'])));
        }

        $output->writeln('<info>Running unit tests</info>');
        if (!$this->unitTests()) {
            throw new \Exception('Fix the fucking unit tests!');
        }

        $hash = null;
        $author = null;
        exec('git log --pretty=format:"%h" | head -n 1', $hash);
        exec('git log --pretty=format:"%an" | head -n 1', $author);
        
        $output->writeln('<info>"Commit do '.$author[0].' ('.$hash[0].'): nota C. '.count($fatalError).' errors ('.$errorJS.' em JS, '.$errorPHP.' em PHP)"</info>');
    }
    
}
