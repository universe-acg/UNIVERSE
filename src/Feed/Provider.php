<?php
    /*
 * AUTHOR: ALEXANDROS KOURTIS
 */
    
    namespace App\Feed;
    
    use App\Service\AppServices;
    use FeedIo\Feed\Item;
    use FeedIo\Feed\Item\Media;
    use FeedIo\Feed\Node\Category;
    use MaxMind\Db\Reader\InvalidDatabaseException;
    use Psr\Cache\InvalidArgumentException;
    use Symfony\Component\Asset\Packages;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
    
    class Provider
    {
        protected $services;
        protected $router;
        protected $assets;
        
        public function __construct(AppServices $services, UrlGeneratorInterface $router, Packages $assets)
        {
            $this->services = $services;
            $this->router = $router;
            $this->assets = $assets;
        }
        
        /**
         * @throws InvalidArgumentException
         * @throws InvalidDatabaseException
         */
        protected function getEvents()
        {
            foreach ($this->services->getEvents(array("limit" => $this->services->getSetting("feed_events_limit")))->getQuery()->getResult() as $event) {
                $item = new Item;
                $item->setTitle($event->getName());
                $item->getAuthor($event->getOrganizer()->getName());
                $item->setLink($this->router->generate("event", ["slug" => $event->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL));
                $media = new Media();
                $media->setUrl($this->services->getSetting("website_url") . $this->assets->getUrl($event->getImagePath()));
                $item->addMedia($media);
                $category = new Category();
                $category->setLabel($event->getCategory()->getName());
                $item->addCategory($category);
                $item->setLastModified($event->getUpdatedAt());
                yield $item;
            }
        }
    }
