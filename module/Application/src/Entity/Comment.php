<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment entity class representing a comment
 *
 * PHP version 7
 *
 * @author yasir khurshid <yasir.khurshid@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="comment")
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="content")
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="author")
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="date_created")
     */
    private $dateCreated;

    /**
     * @var Post
     *
     * @ORM\ManyToOne(targetEntity="\Application\Entity\Post", inversedBy="comments")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;

    /**
     * Return id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id Id
     *
     * @return Comment
     */
    public function setId(string $id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Return content
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content Content
     *
     * @return Comment
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Set author
     *
     * @param string $author Author
     *
     * @return Comment
     */
    public function setAuthor(string $author): Comment
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Return creation date
     *
     * @return string
     */
    public function getDateCreated():string
    {
        return $this->dateCreated;
    }

    /**
     * Set creation date
     *
     * @param string $dateCreated
     *
     * @return Comment
     */
    public function setDateCreated(string $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Return post
     *
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * Set post
     *
     * @param Post $post Post
     *
     * @return Comment
     */
    public function setPost(Post $post): Comment
    {
        $this->post = $post;
        return $this;
    }
}
