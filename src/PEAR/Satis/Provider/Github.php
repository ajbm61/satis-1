<?php
namespace PEAR\Satis\Provider;

class Github
{
    private $org;

    private $request;

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
        if ($this->request instanceof \HTTP_Request2) {
            return $this->request;
        }

        $this->request = new \HTTP_Request2;
        $this->request->setMethod(\HTTP_Request2::METHOD_GET);
        return $this->request;
    }

    /**
     * @param \HTTP_Request2_Response $response
     *
     * @return array
     * @throws \RuntimeException
     */
    private function parseResponse(\HTTP_Request2_Response $response)
    {
        $body = json_decode($response->getBody(), true);

        if (JSON_ERROR_NONE !== json_last_error()) {

            $msg = sprintf(
                "Failed to decode response. Error: %s (%d), Response was: %s",
                json_last_error_msg(),
                json_last_error(),
                $response->getBody()
            );

            throw new \RuntimeException($msg);
        }

        if (200 !== $response->getStatus()) {
            throw new \RuntimeException($body['message']);
        }

        return $body;
    }
}
