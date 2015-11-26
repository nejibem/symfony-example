<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * AppBundle\Entity\User
 *
 * @ORM\Table(name="my_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, AdvancedUserInterface, EquatableInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="password_reset_key", type="string", length=32, nullable=true)
     */
    private $passwordResetKey;

    /**
     * @ORM\Column(name="auto_login_key", type="string", length=32)
     */
    private $autoLoginKey;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime", nullable=true)
     */
    private $updatedDate;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="user_group")
     *
     */
    private $groups;

    /**
     * @var \UserLogin
     *
     * @ORM\OneToMany(targetEntity="UserLogin", mappedBy="user")
     */
    private $userLogins;


    /**
     *
     */
    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
        $this->autoLoginKey = md5(uniqid(null, true));
        $this->groups = new ArrayCollection();
        $this->userLogins = new ArrayCollection();
    }


    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }


    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set passwordResetKey
     *
     * @param string $passwordResetKey
     * @return User
     */
    public function setPasswordResetKey($passwordResetKey)
    {
        $this->passwordResetKey = $passwordResetKey;

        return $this;
    }

    /**
     * Get passwordResetKey
     *
     * @return string
     */
    public function getPasswordResetKey()
    {
        return $this->passwordResetKey;
    }

    /**
     * @param $autoLoginKey
     */
    public function setAutoLoginKey($autoLoginKey)
    {
        $this->autoLoginKey = $autoLoginKey;
    }

    /**
     * @return string
     */
    public function getAutoLoginKey()
    {
        return $this->autoLoginKey;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->groups->toArray();
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        return $this->id === $user->getId();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            ) = unserialize($serialized);
    }


    /**
     * @return bool
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isActive;
    }

    /**
     * Add groups
     *
     * @param \AppBundle\Entity\Group $group
     * @return User
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;
    
        return $this;
    }

    /**
     * Remove groups
     *
     * @param \AppBundle\Entity\Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Add userLogin
     *
     * @param \AppBundle\Entity\UserLogin $userLogin
     * @return UserLogin
     */
    public function addUserLogin(UserLogin $userLogin)
    {
        $this->userLogins[] = $userLogin;

        return $this;
    }

    /**
     * Remove userLogin
     *
     * @param \AppBundle\Entity\UserLogin $userLogin
     */
    public function removeUserLogin(UserLogin $userLogin)
    {
        $this->userLogins->removeElement($userLogin);
    }

    /**
     * Get userLogins
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserLogins()
    {
        return $this->userLogins;
    }

    /**** misc functions, not generated by doctrine ****/

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        if( $this->createdDate == null )
        {
            $this->createdDate = new \DateTime('now');
        }
        else
        {
            $this->updatedDate = new \DateTime('now');
        }
    }

    /**
     * @return bool
     */
    public function hasAdminAccess()
    {
        foreach( $this->groups as $group )
        {
            if( $group->getRole() == 'ROLE_ADMIN' )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('email', new Email());
        $metadata->addPropertyConstraint('username', new NotBlank());
        $metadata->addPropertyConstraint('password', new Length(array('min'=>5,'max'=>'10')));

        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'email',
            'message' => 'This Email already exists.',
        )));

        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'username',
            'message' => 'This Username already exists.',
        )));
    }

    public function generatePasswordResetKey()
    {
        $this->passwordResetKey = md5(uniqid(null, true));
    }

}