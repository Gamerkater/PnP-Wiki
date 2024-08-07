<?php

namespace App\Model;

class User
{
    private int $id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $username;
    private string $password;
    private bool $verified;
    private string $token;
    private bool $firstnamePublic;
    private bool $lastnamePublic;
    private string $profiletext;

    /**
     * @param int $id
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $username
     * @param string $password
     * @param bool $verified
     * @param string $token
     * @param bool $firstnamePublic
     * @param bool $lastnamePublic
     * @param string $profiletext
     */
    public function __construct(int $id, string $firstname, string $lastname, string $email, string $username, string $password, bool $verified, string $token, bool $firstnamePublic, bool $lastnamePublic, string $profiletext)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->username = $username;
        $this->password = $password;
        $this->verified = $verified;
        $this->token = $token;
        $this->firstnamePublic = $firstnamePublic;
        $this->lastnamePublic = $lastnamePublic;
        $this->profiletext = $profiletext;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function getVerified(): bool
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     */
    public function setVerified(bool $verified): void
    {
        $this->verified = $verified;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return bool
     */
    public function getFirstnamePublic(): bool
    {
        return $this->firstnamePublic;
    }

    /**
     * @param bool $firstnamePublic
     */
    public function setFirstnamePublic(bool $firstnamePublic): void
    {
        $this->firstnamePublic = $firstnamePublic;
    }

    /**
     * @return bool
     */
    public function getLastnamePublic(): bool
    {
        return $this->lastnamePublic;
    }

    /**
     * @param bool $lastnamePublic
     */
    public function setLastnamePublic(bool $lastnamePublic): void
    {
        $this->lastnamePublic = $lastnamePublic;
    }

    /**
     * @return string
     */
    public function getProfiletext(): string
    {
        return $this->profiletext;
    }

    /**
     * @param string $profiletext
     */
    public function setProfiletext(string $profiletext): void
    {
        $this->profiletext = $profiletext;
    }

}