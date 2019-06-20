<?php

namespace App\Command;

use App\OAuth1\OAuthRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExampleCommand extends Command
{
    protected static $defaultName = 'app:example';

    private $oauthClient;

    /**
     * ExampleCommand constructor.
     * @param HttpClientInterface $oauthPocClient
     */
    public function __construct(HttpClientInterface $oauthPocClient)
    {
        $this->oauthClient = $oauthPocClient;

        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setDescription('OAuth1 example using http-client');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $OAuthRequest = new OAuthRequest();

        $response = $OAuthRequest->request($this->oauthClient, 'GET', 'http://term.ie/oauth/example/request_token.php', []);

        dump($response->getContent());
    }
}
