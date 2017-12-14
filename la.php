<?php
require __DIR__ . '/vendor/autoload.php';

use DenisBeliaev\logAnalyzer\Exception\NoFileException;
use DenisBeliaev\logAnalyzer\Log;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

$app = new Silly\Application();

$app->command('list [log_name]', function ($log_name, OutputInterface $output) {
    try {
        if (empty($log_name))
            foreach (glob(__DIR__ . '/store/*.sqlite3') as $file) {
                $name = pathinfo($file)['filename'];
                $output->writeln($name);
            }
        else {
            if (empty($log_name))
                throw new BadFunctionCallException('Please, specify the name of the log');

            $Log = new Log($log_name);
            $output->writeln('Path | Updated | Timezone | Last Request | Last Size');
            foreach ($Log->sources as $source) {
                $output->writeln(implode(' | ', $source));
            }
        }
    } catch (Exception $e) {
        $output->writeln($e->getMessage());
    }
});

$app->command('remove log_name [source]', function ($log_name, $source, InputInterface $input, OutputInterface $output) {
    /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
    $helper = $this->getHelperSet()->get('question');
    try {
        $Log = new Log($log_name);
        if (empty($source)) {
            if ($helper->ask($input, $output, new ConfirmationQuestion("Delete $log_name? [y/N] ", false)))
                unlink($Log->dbPath);
        } else {
            if ($helper->ask($input, $output, new ConfirmationQuestion("Delete source $source from $log_name? [y/N] ", false)))
                $Log->removeSource($source);
        }
    } catch (Exception $e) {
        $output->writeln($e->getMessage());
    }
});

$app->command('add log_name source [timezone]', function ($log_name, $source, $timezone, OutputInterface $output) {
    try {
        try {
            $Log = new Log($log_name);
        } catch (NoFileException $e) {
            copy(__DIR__ . '/store/template.db', Log::getDbPath($log_name));
            $Log = new Log($log_name);
        }
        $Log->addSource($source, $timezone);
    } catch (Exception $e) {
        $output->writeln($e->getMessage());
    }
})->defaults(['timezone' => 'UTC']);

$cmd = new \Symfony\Component\Console\Command\ListCommand();
$app->add($cmd->setName('ls'));

$app->setDefaultCommand('ls');
$app->run();