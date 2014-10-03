<?php
namespace PEAR\Satis\Command;

use Aws\Common\Aws;
use Aws\S3\S3Client;
use Symfony\Component\Console;

class UploadToBucket extends Console\Command\Command
{
    const ARG_BUCKET = 'BUCKET';
    const ARG_KEY_ID = 'AWS_ACCESS_KEY_ID';
    const ARG_SECRET = 'AWS_SECRET_ACCESS_KEY';

    private $appRoot;

    public function __construct($appRoot)
    {
        $this->appRoot = $appRoot;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('pear-satis:upload')
            ->setDescription('Update to Amazon S3 back')
            ->addArgument(self::ARG_BUCKET, Console\Input\InputArgument::REQUIRED, 'Which bucket do you want to upload to.')
            ->addArgument(self::ARG_KEY_ID, Console\Input\InputArgument::OPTIONAL, 'If set, key id is used. Otherwise ENV.')
            ->addArgument(self::ARG_SECRET, Console\Input\InputArgument::OPTIONAL, 'If set, secret is used. Otherwise ENV.')
        ;
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $s3 = $this->getAmazonClient($input->getArgument(self::ARG_KEY_ID), $input->getArgument(self::ARG_SECRET));
        $bucket = $input->getArgument(self::ARG_BUCKET);

        $localDirectory = $this->getOutputDir();

        $s3->uploadDirectory(
            $localDirectory,
            $bucket,
            '',
            [
                'params' => [
                    'ACL' => 'public-read',
                ],
                'concurrency' => 20,
                'debug' => true,
            ]
        );

        $output->writeln("Successfully synced to {$bucket}!");
    }

    /**
     * @param $key
     * @param $secret
     *
     * @return S3Client
     */
    private function getAmazonClient($key, $secret)
    {
        if ($this->isEnvSetup()) {
            return S3Client::factory();
        }

        $aws = Aws::factory([
            'key' => $key,
            'secret' => $secret,
        ]);
        return $aws->get('S3');
    }

    private function getOutputDir()
    {
        $config = json_decode(file_get_contents($this->appRoot . '/satis.json'), true);
        return $this->appRoot . '/' . $config['output-dir']; // this is a nice assumption
    }

    private function isEnvSetup()
    {
        foreach ([self::ARG_KEY_ID, self::ARG_SECRET] as $key) {
            if (false === getenv($key)) {
                return false;
            }
        }

        return true;
    }
}
