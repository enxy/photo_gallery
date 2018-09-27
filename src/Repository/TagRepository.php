<?php
/**
 * Tag repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
/**
 * Class TagRepository.
 *
 * @package Repository
 */
class TagRepository
{
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
     * Select all tags.
     *
     * @return array Result
     */
    public function findAll(){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('tag');
        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Select a tag by id.
     *
     * @param string $name
     *
     * @return array Result
     */
    public function findOneByName($name)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('tag')->where('name = :name')
            ->setParameter(':name', $name, \PDO::PARAM_STR);
        $result = $queryBuilder->execute()->fetch();

        return !$result ? [] : $result;
    }
    /**
     * Select tags by id.
     *
     * @param array $ids Ids
     *
     * @return array Result
     */
    public function findById($ids)
    {
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('tag')->where('tagId IN (:ids)')
            ->setParameter(':ids', $ids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);

        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Save tag.
     * @param array $tag Tag
     *
     * @return boolean Result
     */
    public function save($tag)
    {
        if (isset($tag['id']) && ctype_digit((string) $tag['id'])) {
            // update record
            $id = $tag['tagId'];
            unset($tag['tagId']);

            $this->db->update('tag', $tag, ['id' => $id]);
            return $tag;
        } else {
            // add new record
            $this->db->insert('tag', $tag);
            $tag['id'] = $this->db->lastInsertId();

            return $tag;
        }
    }
    /**
     * Remove tag.
     * @param int $tag Tag
     *
     * @return boolean Result
     */
    public function deleteTag($tag){
            return $this->db->delete('tag', ['tagId'=>$tag]);
    }

    /**
     * Save changes
     * @param $tag
     * @param $id
     */
    public function saveChanges($tag, $id){
        $tagId = $this->save($tag);
        $this->db->insert('photo_tag', array('tagId'=>$tagId['id'], 'photoId'=>$id));
    }
}