<?php
namespace Somos\Console\Command;

use Doctrine\ORM\Configuration;
use Somos\Console\Command as SomosCommand;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class Init extends SomosCommand
{
    const TYPE_WEBAPP_FS     = 'Web Application (large, full-stack)';
    const TYPE_WEB_APP_MICRO = 'Web Application (small)';
    const TYPE_CLI_APP_FS    = 'Command Line Application (large, full-stack)';
    const TYPE_CLI_APP       = 'Command Line Application (small)';

    const DB_NO   = 'No';
    const DB_DBAL = 'Yes, use Doctrine DBAL (no ORM)';
    const DB_ORM  = 'Yes, use Doctrine ORM';

    private $options = [
        'name'                  => '',
        'http'                  => true,
        'cli'                   => true,
        'database'              => self::DB_ORM,
        'dsn'                   => "mysql://root@localhost:3306/{{name}}",
        'phpunit'               => true,
        'create_directory_tree' => true
    ];

    private $presets = [
        self::TYPE_WEBAPP_FS => [
        ],
        self::TYPE_CLI_APP_FS => [
            'http' => false,
        ],
        self::TYPE_WEB_APP_MICRO => [
            'cli'                   => false,
            'database'              => self::DB_DBAL,
            'phpunit'               => false,
            'create_directory_tree' => false
        ],
        self::TYPE_CLI_APP => [
            'http'                  => false,
            'database'              => self::DB_NO,
            'phpunit'               => false,
            'create_directory_tree' => false
        ],
    ];


    public function configure()
    {
        $this->setDescription('Initializes a new project using Somos as a full-stack web framework');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $question */
        $question = $this->getHelper('question');

        $welcomeMessage = <<< TEXT
<info>Welcome to Somos</info>.

In the next moments you will be making the first steps in getting your
application up and running. We will be asking you a few questions so that we
can generate a file called <info>somos.php</info> in the directory of your liking.

Depending on your answers we may also create a few <info>additional files and
folders</info> with a recommended layout. You are free to change this any way you
like, the provided layout is intended to that you can start using the
framework without having to spend more time setting it up if you do not want
to.

Are you ready? Here we go!

TEXT;

        $output->writeln($welcomeMessage);

        $this->options['name'] = $this->askProjectName($input, $output, $question);

        $preset = $this->askWhatPresetShouldBeSupported($input, $output, $question);
        $this->options = array_merge($this->options, $this->presets[$preset]);

        $this->options['dsn'] = str_replace('{{name}}', $this->options['name'], $this->options['dsn']);

        do {
            $http = $this->options['http'] ? 'Yes' : 'No';
            $cli = $this->options['cli'] ? 'Yes' : 'No';
            $dsnLine = $this->options['database'] != self::DB_NO
                ? "    For the database you connect with (DSN):      <info>{$this->options['dsn']}</info>\n"
                : "";
            $phpunit = $this->options['phpunit'] ? 'Yes' : 'No';
            $tree = $this->options['create_directory_tree'] ? 'Yes' : 'No';

            $explanation = <<<TEXT

Based on your selections we have gathered that you want to set up your
application to have the following traits:

    The name of your project is:                  <info>{$this->options['name']}</info>
[<info>1</info>] You want to handle web requests:              <info>{$http}</info>
[<info>2</info>] You want to handle command line activities:   <info>{$cli}</info>
[<info>3</info>] You want to use a database:                   <info>{$this->options['database']}</info>
{$dsnLine}[<info>4</info>] You want us to set up PHPUnit for testing:    <info>$phpunit</info>
[<info>5</info>] You want us to create a basic directory tree: <info>$tree</info>

TEXT;
            $output->writeln($explanation);

            $select = $question->ask(
                $input,
                $output,
                new Question(
                    "Press <info>enter</info> to confirm, or type the number of the trait that you want to change and "
                    . "press enter\n > ",
                    ''
                )
            );

            switch ($select) {
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    $this->options['database'] = $this->askToUseDatabase($input, $output, $question);
                    $this->options['dsn'] = $this->askForDsn(
                        $input,
                        $output,
                        $this->options['database'],
                        $question,
                        $this->options['name']
                    );
                    break;
                case 4:
                    $this->options['phpunit'] = $this->askToUsePhpunit($input, $output, $question);
                    break;
            }
        } while ($select != '');


        $explanation = <<< TEXT
Based on the above we will create a basic 'somos.php' file containing all
actions (including routes and commands) and handlers, and we will install the
following packages from packagist:

TEXT;

        $output->writeln($explanation);
        $packages = [];
        $packagesDev = [];
        if ($this->options['http']) {
            $packages[] = 'nikic/fast-route';
            $packages[] = 'phly/http';
        }
        if ($this->options['cli']) {
            $packages[] = 'symfony/console';
        }
        if ($this->options['database'] == self::DB_DBAL) {
            $packages[] = 'doctrine/dbal';
        }
        if ($this->options['database'] == self::DB_ORM) {
            $packages[] = 'doctrine/orm';
        }
        if ($this->options['phpunit']) {
            $packagesDev[] = 'phpunit/phpunit';
        }

        $output->writeln("Dependencies:");
        foreach ($packages as $package) {
            $output->writeln("  - $package");
        }

        $output->writeln("Dev dependencies:");
        foreach ($packagesDev as $package) {
            $output->writeln("  - $package");
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $question
     *
     * @return mixed
     */
    private function askProjectName(InputInterface $input, OutputInterface $output, $question)
    {
        do {
            $name = $question->ask(
                $input,
                $output,
                new Question("<question>What is the name of your project?</question>\n > ")
            );
            if (empty($name)) {
                $output->writeln("<error>We did not receive the name of your project, please try again.</error>\n");
                return $name;
            }
            return $name;
        } while (empty($name));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $question
     * @return mixed
     */
    private function askWhatPresetShouldBeSupported(InputInterface $input, OutputInterface $output, $question)
    {
        return $question->ask(
            $input,
            $output,
            new ChoiceQuestion(
                "<question>What type of application will you be developing?</question> <comment>Press enter to select "
                . "the first one</comment>",
                [self::TYPE_WEBAPP_FS, self::TYPE_WEB_APP_MICRO, self::TYPE_CLI_APP_FS, self::TYPE_CLI_APP],
                0
            )
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $question
     * @return mixed
     */
    private function askToUsePhpunit(InputInterface $input, OutputInterface $output, $question)
    {
        $phpunit = $question->ask(
            $input,
            $output,
            new ConfirmationQuestion(
                "<question>Do you want to use PHPUnit to do automated testing?</question> <comment>(press enter to use "
                . "it)</comment>\n > ",
                true
            )
        );
        return $phpunit;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $useDatabase
     * @param $question
     * @param $name
     * @return string
     */
    private function askForDsn(InputInterface $input, OutputInterface $output, $useDatabase, $question, $name)
    {
        $dsn = '';
        if ($useDatabase != self::DB_NO) {
            $dsn = $question->ask(
                $input,
                $output,
                new Question(
                    "<question>Please provide the DSN for your database</question> <comment>(press enter to use 'mysql://root@localhost:3306/$name')</comment>\n > ",
                    "mysql://root@localhost:3306/$name"
                )
            );
            return $dsn;
        }
        return $dsn;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $question
     * @return mixed
     */
    private function askToUseDatabase(InputInterface $input, OutputInterface $output, $question)
    {
        $useDatabase = $question->ask(
            $input,
            $output,
            new ChoiceQuestion(
                '<question>Do you expect to use a database?</question> <comment>(press enter to install Doctrine ORM)</comment>',
                [ self::DB_NO, self::DB_DBAL, self::DB_ORM ],
                2
            )
        );
        return $useDatabase;
    }
}