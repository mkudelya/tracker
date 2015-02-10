<?php
namespace Oro\TrackerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter your full name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max="255",
     *     minMessage="The fullname is too short.",
     *     maxMessage="The fullname is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $fullname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="Project", inversedBy="users")
     * @ORM\JoinTable(name="users_projects")
     **/
    protected $projects;

    /**
     * @ORM\ManyToMany(targetEntity="Issue", inversedBy="collaborators")
     * @ORM\JoinTable(name="issue_collaborators")
     **/
    protected $issues;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Set fullname
     *
     * @param string $fullname
     * @return User
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Add projects
     *
     * @param \Oro\TrackerBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\Oro\TrackerBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Oro\TrackerBundle\Entity\Project $projects
     */
    public function removeProject(\Oro\TrackerBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add issues
     *
     * @param \Oro\TrackerBundle\Entity\Issue $issues
     * @return User
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
}
