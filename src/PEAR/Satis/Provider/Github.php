<?php
namespace PEAR\Satis\Provider;

class Github
{
    private $org;

    private $token;

    /**
     * @param string $org
     * @param string $token
     *
     * @return self
     */
    public function __construct($org, $token)
    {
        $this->org = $org;
        $this->token = $token;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function provide()
    {
        $url = sprintf(
            "https://api.github.com/orgs/%s/repos?type=public&access_token=%s",
            $this->org,
            $this->token
        );

        $request = $this->getRequest();

        $repositories = [];

        while (true) {

            $response = $request->setUrl($url)->send();

            $body = json_decode($response->getBody(), true);

            if (200 !== $response->getStatus()) {
                throw new \RuntimeException($body['message']);
            }

            foreach ($body as $repository) {
                $repositories[] = $repository['full_name'];
            }

            $url = $this->extractNext($response->getHeader('Link'));
            if (false === $url) {
                break;
            }
        }

        return $repositories;
    }

    private function extractNext($linkHeader)
    {
        if (false === strpos($linkHeader, 'rel="next"')) {
            return false;
        }

        $linkHeaders = explode(',', $linkHeader);
        foreach ($linkHeaders as $header) {
            if (false === strpos($header, 'rel="next"')) {
                continue;
            }

            list($link, $relation) = explode(';', $header);
            return substr($link, 1, -1);
        }

        return false;
    }

    private function getRequest()
    {
        $request = new \HTTP_Request2;
        $request->setMethod(\HTTP_Request2::METHOD_GET);
        return $request;
    }
}
