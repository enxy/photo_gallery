<?php
/**
 * User repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * Class UserRepository.
 *
 * @package Repository
 */
class UserRepository
{
    /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
    /**
     * TagRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    /**
     * Loads user by login.
     *
     * @param string $login User login
     * @throws UsernameNotFoundException
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function loadUserByLogin($login)
    {
        try {
            $user = $this->getUserByLogin($login);

            if (!$user || !count($user)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            $roles = $this->getUserRoles($user['id']);

            if (!$roles || !count($roles)) {
                throw new UsernameNotFoundException(
                    sprintf('Username "%s" does not exist.', $login)
                );
            }

            return [
                'login' => $user['login'],
                'password' => $user['password'],
                'roles' => $roles,
                'id' =>$user['id']
            ];
        } catch (DBALException $exception) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        } catch (UsernameNotFoundException $exception) {
            throw $exception;
        }
    }
    /**
     * Gets user data by login.
     *
     * @param string $login User login
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserByLogin($login)
    {
        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('u.id', 'u.login', 'u.password')
                ->from('users', 'u')
                ->where('u.login = :login')
                ->setParameter(':login', $login, \PDO::PARAM_STR);

            return $queryBuilder->execute()->fetch();
        } catch (DBALException $exception) {
            return [];
        }
    }

    /**
     * Gets user roles by User ID.
     *
     * @param integer $userId User ID
     * @throws \Doctrine\DBAL\DBALException
     *
     * @return array Result
     */
    public function getUserRoles($userId)
    {
        $roles = [];

        try {
            $queryBuilder = $this->db->createQueryBuilder();
            $queryBuilder->select('r.name')
                ->from('users', 'u')
                ->innerJoin('u', 'roles', 'r', 'u.role_id = r.id')
                ->where('u.id = :id')
                ->setParameter(':id', $userId, \PDO::PARAM_INT);
            $result = $queryBuilder->execute()->fetchAll();

            if ($result) {
                $roles = array_column($result, 'name');
            }

            return $roles;
        } catch (DBALException $exception) {
            return $roles;
        }
    }
    /**
     * Remove user.
     *
     * @param int $id Id
     *
     * @return boolean Result
     */
    public function deleteUser($id){

        return $this->db->delete('users', array('id'=>$id));
    }

    /**
     * Edit user.
     * @param $data
     * @param $id
     * @return int
     */
    public function editUser($data, $id){
        return $this->db->update('users', $data, array('id'=>$id));
    }

    /**
     * Edit user password.
     * @param $password
     * @param $id
     * @return int
     */
    public function editPassword($password, $id){
        return $this->db->update('users', ['password'=>$password], ['id'=>$id]);
    }
    /**
     * Save user.
     *
     * @param array $user User
     *
     * @return boolean Result
     */
    public function saveUser($user)
    {
        return $this->db->insert('users', $user);
    }

    /**
     * Save user photo.
     * @param $userId
     * @param $photoId
     * @return int
     */
    public function userPhoto($userId, $photoId){
        return $this->db->insert('photo_user', ['userId'=>$userId, 'photoId'=>$photoId]);
    }
    /**
     * Find photo id.
     * @param int $id Id
     * @return array Result
     */
    public function findPhotoId($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('photoId')->from('photo_user')->where('userId =:id')->setParameter(':id', $id);
        $photoId = $queryBuilder->execute()->fetchAll();
        return $photoId;
    }
    /**
     * Find user photos.
     * @param int $id Id
     * @return array Result
     */
    public function selectUserPhotos($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $photosIds = $this->findPhotoId($id);
        $i=0;
        $photo=[];
        foreach ($photosIds as $photoId){
            $queryBuilder->select('idPhoto', 'path','description')->from('photo')->where('idPhoto IN (:photoIds)')->setParameter(':photoIds', $photoId['photoId']);
            $result = $queryBuilder->execute()->fetchAll();
	  if(!empty($result)) $photo[$i] = $result;
            $i++;
        }
        return $photo;
    }
    /**
     * Find user data.
     * @param string $login Login
     * @return array Result
     */
    public function getUserData($login){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('users')->where('login=:login')->setParameter(':login', $login);

        return $queryBuilder->execute()->fetchAll();
    }
}