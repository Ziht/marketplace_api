<?php
declare(strict_types=1);

namespace Marketplace\Service;

/**
 * Class UserService
 * @package Marketplace\Service
 */
class UserService
{
    /**
     * @return int
     */
    public function getCurrentUserId()
    {
        return 1;
    }

    /**
     * @return string
     */
    public function getCurrentUserLogin()
    {
        return 'admin';
    }
}