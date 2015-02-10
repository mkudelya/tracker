<?php
namespace Oro\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="activities")
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="activities")
     * @ORM\JoinColumn(name="issue_id", referencedColumnName="id")
     **/
    protected $issue;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Activity
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set issue
     *
     * @param \Oro\TrackerBundle\Entity\Issue $issue
     * @return Activity
     */
    public function setIssue(\Oro\TrackerBundle\Entity\Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \Oro\TrackerBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }
}
