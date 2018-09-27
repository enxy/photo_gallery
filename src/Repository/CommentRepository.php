<?php
/**
 * Comment repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
/**
 * Class CommentRepository.
 *
 * @package Repository
 */
class CommentRepository{
     /**
     * Doctrine DBAL connection.
     *
     * @var \Doctrine\DBAL\Connection $db
     */
    protected $db;
    /**
     * CommentRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Save data.
     * @param $comment
     * @param $id
     * @param $user
     * @param $idUser
     * @throws DBALException
     */
    public function save($comment,$id, $user, $idUser){
        $this->db->beginTransaction();

        try {
            $currentDateTime = new \DateTime();
            $comment['dateAdded'] = $currentDateTime->format('Y-m-d H:i:s');
            $comment['username'] =$user;
            $comment['idPhoto'] =$id;
	        $comment['idUser'] = $idUser;
            $this->db->insert('comment', $comment);
            $this->db->commit();
        }catch(DBALException $e){
            $this->db->rollBack();
            throw $e;
        }
    }
    /**
     * Seletct photo's comments.
     *
     * @param int $id
     *
     * @return array Result
     */
    public function selectPhotoComments($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('comment')->where('idPhoto = :id')
            ->setParameter(':id', $id, \PDO::PARAM_STR);
        $result = $queryBuilder->execute()->fetchAll();
        return $result;
    }
   /**
     * Find comment author.
     *
     * @param int $id Element id
     *
     * @return array Result
     */
    public function findCommentAuth($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $comments = $this->selectPhotoComments($id);
        $author=[];
        for ($i = 0; $i < count($comments); $i++):
            $queryBuilder->select('name','surname')->from('users')->where('id = :id')->setParameter(':id',$comments[$i]['idUser']);
            $author[$i] = $queryBuilder->execute()->fetchAll();
            $author[$i]['comment'] = $comments[$i];
        endfor;
        return $author;
    }
     /**
     * Remove record.
     *
     * @param int $comment Comment
     *
     * @return boolean Result
     */
    public function deleteComment($comment){
        return $this->db->delete('comment', ['idComment'=>$comment]);
    }

    /**
     * Save record.
     * @param $number
     * @param $photoId
     * @return int
     */
    public function saveRating($number, $photoId){

        return $this->db->insert('rating', array('number'=>$number, 'photoId'=>$photoId, 'total_rating'=>$number));
    }
     /**
     * Seletct rating.
     *
     * @param int $id
     *
     * @return array Result
     */
    public function selectRating($id)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('number')->from('rating')->where('photoId=:id')->setParameter(':id', $id);

        return $queryBuilder->execute()->fetchAll();
    }

}