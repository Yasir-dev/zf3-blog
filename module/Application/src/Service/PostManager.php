<?php

namespace Application\Service;

use Application\Entity\Comment;
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

    public function updatePost(array $data, Post $post)
    {
        $post->setTitle($data['title'])
            ->setContent($data['content'])
            ->setStatus($data['status']);

        $this->addTags($data['tags'], $post);

        $this->entityManager->flush();
    }

    public function deletePost(Post $post)
    {
        foreach ($post->getComments() as $comment) {
            $this->entityManager->remove($comment);
        }

        foreach ($post->getTags() as $tag) {
            $post->removeTag($tag);
        }

        $this->entityManager->remove($post);
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

    public function addComment(Post $post, array $data)
    {
        $comment = (new Comment())
            ->setPost($post)
            ->setAuthor($data['author'])
            ->setContent($data['comment'])
            ->setDateCreated(date('Y-m-d H:i:s'));

        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function convertTagsToString($post)
    {
        $tags = $post->getTags();

        $tagCount = count($tags);
        $tagsStr = '';
        $i = 0;
        foreach ($tags as $tag) {
            $i ++;
            $tagsStr .= $tag->getName();
            if ($i < $tagCount)
                $tagsStr .= ', ';
        }

        return $tagsStr;
    }

    public function getCommentCountString(Post $post):string
    {
        $count = $post->getComments()->count();

        return ($count === 1 ? "$count comment" : "$count comments");
    }

    /**
     * Return post status
     *
     * @param Post $post
     *
     * @return string
     */
    public function getPostStatus(Post $post):string
    {
        return Post::STATUS[$post->getStatus()] ?? 'Unknown';
    }

    /**
     * Return tag cloud
     *
     * @return array
     */
    public function getTagCloud()
    {
        $cloud = [];
        $tagCloud = [];

        $posts = $this->entityManager->getRepository(Post::class)
            ->findPostsWithTag();

        $postsCount = \count($posts);

        $tags = $this->entityManager->getRepository(Tag::class)
            ->findAll();

        /**
         * @var Tag $tag
         */
        foreach ($tags as $tag) {
            $postsHavingTag = $this->entityManager->getRepository(Post::class)
                ->findPostsByTag($tag->getName())->getResult();

            $count = \count($postsHavingTag);
            if ($count > 0) {
                $cloud[$tag->getName()] = $count;
            }
        }

        foreach ($cloud as $name => $count) {
            $tagCloud[$name] = \number_format($count/$postsCount, 2);
        }

        return $tagCloud;
    }
}
