<?php
namespace Oro\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects")
 * @UniqueEntity("code")
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $label;

    /**
     * @ORM\Column(type="text")
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $code;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="projects")
     * @ORM\JoinTable(name="project_members")
     **/
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     **/
    protected $issues;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="user")
     **/
    protected $activities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set label
     *
     * @param string $label
     * @return Project
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Project
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Project
     */
    public function setCode($code)
    {
        if (!empty($code)) {
            $code = strtoupper(preg_replace('/[^a-zA-z_-\d]+/', '', $code));
        }

        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add users
     *
     * @param \Oro\TrackerBundle\Entity\User $users
     * @return Project
     */
    public function addUser(\Oro\TrackerBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Oro\TrackerBundle\Entity\User $users
     */
    public function removeUser(\Oro\TrackerBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add issues
     *
     * @param \Oro\TrackerBundle\Entity\Issue $issues
     * @return Project
     */
    public function addIssue(\Oro\TrackerBundle\Entity\Issue $issues)
    {
        $this->issues[] = $issues;

        return $this;
    }

    /**
     * Remove issues
     *
     * @param \Oro\TrackerBundle\Entity\Issue $issues
     */
    public function removeIssue(\Oro\TrackerBundle\Entity\Issue $issues)
    {
        $this->issues->removeElement($issues);
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getIssues()
    {
        return $this->issues;
    }

    /**
     * Add activities
     *
     * @param \Oro\TrackerBundle\Entity\Activity $activities
     * @return Project
     */
    public function addActivity(\Oro\TrackerBundle\Entity\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Oro\TrackerBundle\Entity\Activity $activities
     */
    public function removeActivity(\Oro\TrackerBundle\Entity\Activity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities()
    {
        return $this->activities;
    }
}
