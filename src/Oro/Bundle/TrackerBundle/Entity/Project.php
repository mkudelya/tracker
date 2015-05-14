<?php

namespace Oro\Bundle\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Oro\Bundle\UserBundle\Entity\User;

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
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User", inversedBy="projects")
     * @ORM\JoinTable(name="project_members")
     **/
    protected $members;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     **/
    protected $issues;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="project")
     **/
    protected $activities;

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
     * Set id
     *
     * @param integer $id
     * @return integer
     */
    public function setId($id)
    {
        return $this->id = (int) $id;
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
     * Add issues
     *
     * @param \Oro\Bundle\TrackerBundle\Entity\Issue $issues
     * @return Project
     */
    public function addIssue(Issue $issues)
    {
        $this->issues[] = $issues;

        return $this;
    }

    /**
     * Remove issues
     *
     * @param \Oro\Bundle\TrackerBundle\Entity\Issue $issues
     */
    public function removeIssue(Issue $issues)
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
     * @param \Oro\Bundle\TrackerBundle\Entity\Activity $activities
     * @return Project
     */
    public function addActivity(Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Oro\Bundle\TrackerBundle\Entity\Activity $activities
     */
    public function removeActivity(Activity $activities)
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

    /**
     * @param \Oro\Bundle\UserBundle\Entity\User $user
     * @return bool
     */
    public function hasMember($user)
    {
        $members = $this->getMembers();

        if ($members->count()) {
            foreach ($members as $member) {
                if ($member->getId() == $user->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    /**
     * Add members
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $members
     * @return Project
     */
    public function addMember(User $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $members
     */
    public function removeMember(User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }
}
