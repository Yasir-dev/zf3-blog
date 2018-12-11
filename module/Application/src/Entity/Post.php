<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Post entity class representing a post
 *
 * PHP version 7
 *
 * @author yasir khurshid <yasir.khurshid@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * Post status
     */
    const STATUS = ['draft', 'publish'];

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
     * @ORM\Column(name="title")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content")
     */
    private $content;

    /**
     * @var int
     *
     * @ORM\Column(name="status")
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="date_created")
     */
    private $dateCreated;

    /**
     * @var Traversable
     *
     * @ORM\OneToMany(targetEntity="\Application\Entity\Comment", mappedBy="post")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     */
    private $comments;

    /**
     * @var \Traversable
     *
     * @ORM\ManyToMany(targetEntity="\Application\Entity\Tag", inversedBy="posts")
     * @ORM\JoinTable(name="post_tag",
     *      joinColumns={@ORM\JoinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags     = new ArrayCollection();
    }

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
     * @return Post
     */
    public function setId(string $id)
    {
        $this->id = (int) $id;

        return $this;
    }

    /**
     * Return title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title Title
     *
     * @return Post
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

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
     * @return Post
     */
    public function setContent(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus():int
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param string $status Status
     *
     * @return Post
     */
    public function setStatus(string $status)
    {
        $this->status = (int) $status;

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
     * @return Post
     */
    public function setDateCreated(string $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Return comments
     *
     * @return \Traversable
     */
    public function getComments(): \Traversable
    {
        return $this->comments;
    }

    /**
     * Add a comment
     *
     * @param Comment $comment Comment
     *
     * @return Post
     */
    public function setComment(Comment $comment): Post
    {
        $this->comments = $comment;

        return $this;
    }

    /**
     * Return tags
     *
     * @return Traversable
     */
    public function getTags(): \Traversable
    {
        return $this->tags;
    }

    /**
     * Set Tag
     *
     * @param array $tag Tag
     *
     * @return Post
     */
    public function setTag(Tag $tag): Post
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param string $tag
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }
}
