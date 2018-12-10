<?php

namespace Application\Service;

use Application\Entity\Post;
use Application\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Zend\Filter\StaticFilter;

class PostManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * PostManager constructor.
     *
     * @param EntityManager $entityManager Entity Manager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addPost(array $data)
    {
        $post = (new Post())
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setStatus($data['status'])
            ->setDateCreated(\date('Y-m-d H:i:s'));

        $this->entityManager->persist($post);

        $this->addTags($data['tags'], $post);

        $this->entityManager->flush();
    }

    private function addTags(string $tagsString, Post $post)
    {
        foreach ($post->getTags() as $tag)
        {
            $post->removeTag($tag);
        }

        $tags = \explode(',', $tagsString);

        foreach ($tags as $tagName) {
            if (empty(StaticFilter::execute($tagName, 'StringTrim'))){
                continue;
            }

            $tag = $this->entityManager->getRepository(Tag::class)->findOneByName($tagName);

            if (null === $tag) {
                $tag = new Tag();
            }

            $tag->setName($tagName);
            $tag->setPost($post);

            $this->entityManager->persist($tag);
            $post->setTag($tag);
        }
    }
}