<?php
namespace PEAR\Satis\Command;

use PEAR\Satis\Event;
use PEAR\Satis\Provider;
use Symfony\Component\Console;
use Symfony\Component\EventDispatcher\EventDispatcher;

class BuildSatisJson extends Console\Command\Command
{
    const ARG_ORG = 'org';
    const ARG_TOKEN = 'oauth-token';

    private $appRoot;

    private $dispatcher;

    private $twig;

    /**
     * @param string            $appRoot
     * @param \Twig_Environment $twig
     * @param EventDispatcher   $dispatcher
     *
     * @return self
     */
    public function __construct($appRoot, \Twig_Environment $twig, EventDispatcher $dispatcher)
    {
        $this->appRoot = $appRoot;
        $this->dispatcher = $dispatcher;
        $this->twig = $twig;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('pear-satis:update')
            ->setDescription('Update satis.json with git-repositories')
            ->addArgument(self::ARG_TOKEN, Console\Input\InputArgument::OPTIONAL, 'If set, token is used. Otherwise ENV.')
            ->addArgument(self::ARG_ORG, Console\Input\InputArgument::IS_ARRAY, 'Which org(s) do you want to spider?')
        ;
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $token = $this->getToken($input);

        $organisations = $input->getArgument(self::ARG_ORG);

        $output->writeln("Crawling: " . implode(', ', $organisations));

        $this->dispatcher->dispatch(Event::CRAWLING);

        $github = new Provider\Github($organisations, $token, $this->dispatcher);
        $repositories = $github->provide(include $this->appRoot . '/var/IgnoredRepositories.php');

        $satisConfiguration = $this->appRoot . '/satis.json';
        $status = file_put_contents(
            $satisConfiguration,
            $this->twig->render('satis.json.twig', ['repositories' => $repositories])
        );

        if (false === $status) {
            $output->writeln("Failed to write: {$satisConfiguration}");
            return;
        }
        $output->writeln("Success!");
    }

    private function getToken(Console\Input\InputInterface $input)
    {
        $token = $input->getArgument(self::ARG_TOKEN);
        if (!empty($token)) {
            return $token;
        }

        $token = getenv('GITHUB_OAUTH_TOKEN');
        if (!empty($token)) {
            return $token;
        }

        throw new \RuntimeException("Missing OAuth token for Github");
    }
}
