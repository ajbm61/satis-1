<?php
namespace PEAR\Satis\Command;

use PEAR\Satis\Provider;
use Symfony\Component\Console;

class BuildSatisJson extends Console\Command\Command
{
    const ARG_ORG = 'org';
    const ARG_TOKEN = 'oauth-token';

    private $appRoot;

    private $twig;

    /**
     * @param string            $appRoot
     * @param \Twig_Environment $twig
     *
     * @return self
     */
    public function __construct($appRoot, \Twig_Environment $twig)
    {
        $this->appRoot = $appRoot;
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

        $github = new Provider\Github($organisations, $token);
        $repositories = $github->provide(include $this->appRoot . '/var/IgnoredRepositories.php');

        file_put_contents(
            $this->appRoot . '/satis.json',
            $this->twig->render('satis.json.twig', ['repositories' => $repositories])
        );

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
