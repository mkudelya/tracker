<?php
namespace Oro\TrackerBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     */
    protected $fullname;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $avatar;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $avatarFile;

    /**
     * Temporary avatar
     * @var $tempAvatar
     */
    protected $tempAvatar;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="users")
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
     * Sets avatar file.
     *
     * @param UploadedFile $avatarFile
     */
    public function setAvatarFile(UploadedFile $avatarFile = null)
    {
        $this->avatarFile = $avatarFile;

        if (isset($this->avatar)) {
            $this->tempAvatar = $this->avatar;
            $this->avatar = null;
        } else {
            $this->avatar = '';
        }
    }

    /**
     * Get avatar file.
     *
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
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

    public function getAbsoluteAvatarPath()
    {
        return null === $this->avatar
            ? null
            : $this->getUploadRootAvatarDir().'/'.$this->avatar;
    }

    public function getWebAvatarPath()
    {
        return null === $this->avatar
            ? null
            : '/'.$this->getUploadAvatarDir().'/'.$this->avatar;
    }

    protected function getUploadRootAvatarDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadAvatarDir();
    }

    protected function getUploadAvatarDir()
    {
        return 'uploads/avatars';
    }

    public function preUpload()
    {
        if (null !== $this->getAvatarFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->avatar = $filename.'.'.$this->getAvatarFile()->guessExtension();
        }
    }

    public function upload()
    {

        if (null === $this->getAvatarFile()) {
            return;
        }

        $this->removeAvatar();
        $this->preUpload();

        $this->getAvatarFile()->move(
            $this->getUploadRootAvatarDir(),
            $this->avatar
        );

        $this->avatarFile = null;
    }

    protected function removeAvatar()
    {
        $avatar = $this->getUploadRootAvatarDir()."/".$this->getAvatar();

        if (is_file($avatar)) {
            unlink($avatar);
            $this->setAvatar("");
        }
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
