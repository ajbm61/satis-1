<?php
namespace PEAR\Satis;

use Symfony\Component\EventDispatcher;

class Event
{
    const CRAWLING = 'buildsatis.crawling';
    const CRAWLED_ORG = 'buildsatis.crawled_org';
    const REPO_IGNORED = 'buildsatis.repo_ignored';
    const REPO_IS_FORK = 'buildsatis.repo_is_fork';

    public function onCrawl(EventDispatcher\Event $event)
    {
        //var_dump(self::CRAWLING);
    }

    public function onCrawledOrg(EventDispatcher\Event $event)
    {
        var_dump(self::CRAWLED_ORG);
    }

    public function onRepoIgnored(Event\BuildSatisJson $event)
    {
        //var_dump(self::REPO_IGNORED, $event->getRepository());
    }

    public function onRepoIsFork(Event\BuildSatisJson $event)
    {
        //var_dump(self::REPO_IS_FORK, $event->getRepository());
    }
}
