<?php
/**
 * Gallery repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
/**
 * Class GalleryRepository.
 *
 * @package Repository
 */
class GalleryRepository
{
     /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
     /**
     * Number of items per page.
     *
     * const int NUM_ITEMS
     */
    const NUM_ITEMS = 4;
    /**
     * GalleryRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }
    /**
     * Query all records.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryAll()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('p.idPhoto, p.path')->from('photo', 'p');
    }
     /**
     * Finds gallery photos.
     *
     * @return array Result
     */
    public function findAll()
    {
        $queryBuilder = $this->queryAll();

        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Finds linked tags Ids.
     *
     * @param int $idPhoto Photo Id
     *
     * @return array Result
     */
    public function findLinkedTagsIds($idPhoto){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('tagId')->from('photo_tag')->where('photoId = :idPhoto')
           ->setParameter(':idPhoto', $idPhoto);
        $result = $queryBuilder->execute()->fetchAll();

        return isset($result) ? array_column($result, 'tagId') : [];
    }
     /**
     * Get records paginated.
     *
     * @param int $page Current page number
     *
     * @return array Result
     */
    public function findAllPaginated($page=1)
    {
        $queryBuilder = $this->queryAll();
        $queryBuilder->setFirstResult(($page - 1) * self::NUM_ITEMS)
            ->setMaxResults(self::NUM_ITEMS);
        $pagesNumber = $this->countAllPages();
        $data = $queryBuilder->execute()->fetchAll();
        $paginator = [
            'page' => ($page<1 || $page>$pagesNumber)? 1 :$page,
            'max_results' => self::NUM_ITEMS,
            'pages_number' => $pagesNumber,
            'data' => $data
        ];
        return $paginator;
    }
    /**
     * Count all pages.
     *
     * @return int Result
     */
    protected function countAllPages()
    {
        $pagesNumber = 1;

        $queryBuilder = $this->queryAll();
        $queryBuilder->select('COUNT(DISTINCT p.idPhoto) AS total_results')
            ->setMaxResults(1);

        $result = $queryBuilder->execute()->fetch();

        if ($result) {
            $pagesNumber =  ceil($result['total_results'] / self::NUM_ITEMS);
        } else {
            $pagesNumber = 1;
        }

        return $pagesNumber;
    }
    /**
     * Edit user.
     *
     * @param array $userData
     * * @return int Result
     */
    public function editUser($userData){
        $id = $userData['id'];
        unset($userData['id']);
        return $this->db->update('users', $userData, array('id'=> $id));
    }
    /**
     * Fetch all users.
     *
     * @return array Result
     */
    public function showUsers()
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')->from('users');

        return $qb->execute()->fetchALL();
    }
     /**
     * Save record.
     *
     * @param array $url Url
     * @return int Result
     */
    public function savePhoto($url){
        $name = $url['url'];
        unset($url['url']);

        return $this->db->insert('photos', ['url'=>$name]);
    }
     /**
     * Fetch all users.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryUsers()
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('*')->from('users');
    }
    /**
     * Find all users.
     *
     * @return array Result
     */
    public function findUsers()
    {
        $queryBuilder = $this->queryUsers();

        return $queryBuilder->execute()->fetchAll();
    }
     /**
     * Find all users.
     * @param integer $id
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    protected function queryUser($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('*')->from('users')->where('id= :id')->setParameter(':id', $id);
    }
     /**
     * Find user.
     * @param integer $id
     * @return array Result
     */
    public function findUser($id)
    {
        $queryBuilder = $this->queryUser($id);

        return $queryBuilder->execute()->fetch();
    }
}
