<?php

/**
 * Users administration.
 */
class Users extends base
{
    /**
     * Check if the user exists.
     *
     * @param mixed $db
     * @param int $id
     * @return object $user
     * @throws Exception
     */
    private static function checkUser($db, $id)
    {
        $query = queries::getUserQuery();
        $statement = $db->prepare($query);
        $statement->bindParam('id', $id);
        $statement->execute();
        $user = $statement->fetchObject();
        if (!$user) {
            throw new Exception(self::USER_NOT_FOUND, 404);
        }

        return $user;
    }

    /**
     * Get all users
     *
     * @param mixed $db
     * @return array
     */
    public static function getUsers($db)
    {
        $query = queries::getUsersQuery();
        $statement = $db->prepare($query);
        $statement->execute();

        return self::response('success', $statement->fetchAll(), 200);
    }

    /**
     * Get one user by id
     *
     * @param mixed $db
     * @param int $id
     * @return array
     */
    public static function getUser($db, $id)
    {
        try {
            $user = self::checkUser($db, $id);

            return self::response('success', $user, 200);
        } catch (Exception $ex) {
            return self::response('error', $ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Search users by name
     *
     * @param mixed $db
     * @param string $usersStr
     * @return array
     */
    public static function searchUsers($db, $usersStr)
    {
        $query = queries::searchUsersQuery();
        $statement = $db->prepare($query);
        $name = '%'.$usersStr.'%';
        $statement->bindParam('name', $name);
        $statement->execute();
        $users = $statement->fetchAll();
        if (!$users) {
            return self::response('error', self::USER_NAME_NOT_FOUND, 404);
        }

        return self::response('success', $users, 200);
    }

    /**
     * Create user
     *
     * @param mixed $db
     * @param mixed $request
     * @return array
     */
    public static function createUser($db, $request)
    {
        $input = $request->getParsedBody();
        if (empty($input['name'])) {
            return self::response('error', self::USER_NAME_REQUIRED, 400);
        }
        $query = queries::createUserQuery();
        $statement = $db->prepare($query);
        $statement->bindParam('name', $input['name']);
        $statement->execute();
        $user = self::checkUser($db, $db->lastInsertId());

        return self::response('success', $user, 200);
    }

    /**
     * Update user
     *
     * @param mixed $db
     * @param mixed $request
     * @param int $id
     * @return array
     */
    public static function updateUser($db, $request, $id)
    {
        try {
            $user = self::checkUser($db, $id);
            $input = $request->getParsedBody();
            if (empty($input['name']) && empty($input['email'])) {
                return self::response('error', self::USER_INFO_REQUIRED, 400);
            }
            $username = isset($input['name']) ? $input['name'] : $user->name;
            $email = isset($input['email']) ? $input['email'] : $user->email;
            $query = queries::updateUserQuery();
            $statement = $db->prepare($query);
            $statement->bindParam('id', $id);
            $statement->bindParam('name', $username);
            $statement->bindParam('email', $email);
            $statement->execute();
            $user = self::checkUser($db, $id);

            return self::response('success', $user, 200);
        } catch (Exception $ex) {
            return self::response('error', $ex->getMessage(), $ex->getCode());
        }
    }

    /**
     * Delete user
     *
     * @param mixed $db
     * @param int $id
     * @return array
     */
    public static function deleteUser($db, $id)
    {
        try {
            self::checkUser($db, $id);
            $query = queries::deleteUserQuery();
            $statement = $db->prepare($query);
            $statement->bindParam('id', $id);
            $statement->execute();

            return self::response('success', self::USER_DELETED, 200);
        } catch (Exception $ex) {
            return self::response('error', $ex->getMessage(), $ex->getCode());
        }
    }
}