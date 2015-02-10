<?php
namespace Oro\TrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects")
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
     *
     * @Assert\NotBlank(message="Please enter project label.")
     * @Assert\Length(
     *     min=3,
     *     max="255",
     *     minMessage="The label is too short.",
     *     maxMessage="The label is too long."
     * )
     */
    protected $label;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Please enter project summary.")
     * @Assert\Length(
     *     min=3,
     *     minMessage="The summary is too short."
     * )
     */
    protected $summary;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Please enter project code.")
     * @Assert\Length(
     *     min=1,
     *     max="255",
     *     minMessage="The code is too short.",
     *     maxMessage="The code is too long."
     * )
     */
    protected $code;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="projects")
     **/
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="project")
     **/
    protected $issues;

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
}
