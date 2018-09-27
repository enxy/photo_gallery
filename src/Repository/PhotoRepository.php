<?php
/**
 * Photo repository.
 */
namespace Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
/**
 * Class PhotoRepository.
 *
 * @package Repository
 */
class PhotoRepository
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
    const NUM_ITEMS =4;
    /**
     * Value for tag repository.
     *
     * @var null|TagRepository
     */
    protected $tagRepository = null;
    /**
     * CommentRepository constructor.
     *
     * @param \Doctrine\DBAL\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->tagRepository = new TagRepository($db);
    }

    /**
     * Save data.
     * @param $photo
     * @param $userId
     * @throws DBALException
     */
    public function save($photo, $userId)
    {
        $this->db->beginTransaction();

        try {
            $currentDateTime = new \DateTime();
            $photo['date_edited'] = $currentDateTime->format('Y-m-d H:i:s');
            //$tagsIds = isset($photo['tags']) ? $photo['tags'] : [];
            $tagsIds = isset($photo['tags']) ? array_column($photo['tags'], 'id') : [];
            unset($photo['tags']);

            if (isset($photo['idPhoto']) && ctype_digit((string) $photo['idPhoto'])) {
                // update record
                $photoId = $photo['idPhoto'];
                unset($photo['idPhoto']);
                $this->removeLinkedTags($photoId);
                $this->addLinkedTags($photoId, $tagsIds);
                $this->db->update('photo', $photo, ['id' => $photoId]);
            } else {
                // add new record
                $photo['date_added'] = $currentDateTime->format('Y-m-d H:i:s');

                $this->db->insert('photo', $photo);
                $photoId = $this->db->lastInsertId();
                $this->addLinkedTags($photoId, $tagsIds);
                $this->userPhoto($userId, $photoId);
            }
            $this->db->commit();
        } catch (DBALException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Save record.
     * @param $data
     * @param $id
     */
    public function saveChanges($data, $id){
        $currentDateTime = new \DateTime();
        $date['date_edited'] = $currentDateTime->format('Y-m-d H:i:s');
        $this->db->update('photo', ['description'=>$data['description'], 'is_public'=>$data['is_public'], 'date_edited'=>$date['date_edited']], ['idPhoto' => $id]);
    }
    /**
     * Fetch user's photos.
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder Result
     */
    public function queryUserPhotos(){
        $queryBuilder = $this->db->createQueryBuilder();

        return $queryBuilder->select('idPhoto', 'path','description')->from('photo');
    }
    /**
     * Find user photos..
     *
     * @return array Result
     */
    public function findUserPhotos(){
        $queryBuilder = $this->queryUserPhotos();
        return $queryBuilder->execute()->fetchAll();
    }
    /**
     * Remove record.
     *
     * @param int $id Id
     *
     * @return boolean Result
     */
    public function deleteUserPhoto($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('tagId')->from('photo_tag')->where('photoId=:id')->setParameter(':id', $id);
        $tagIdToDelete = $queryBuilder->execute()->fetchAll();

        $this->db->delete('photo_tag', array('photoId'=>$id));
            foreach($tagIdToDelete as $data):
             $queryBuilder->select('*')->from('photo_tag')->where('tagId=:tagId')->setParameter(':tagId', $data['tagId']);
             $elem = $queryBuilder->execute()->fetchAll();
             if(empty($elem)):
                $this->db->delete('tag', array('tagId'=>$data['tagId']));
             endif;
                endforeach;

        return $this->db->delete('photo', array('idPhoto'=>$id));
    }
    /**
     * Select user photo.
     *
     * @param int $id
     *
     * @return array Result
     */
    public function selectUserPhoto($id){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('*')->from('photo')->where('idPhoto = :id')
            ->setParameter(':id', $id);
        $result = $queryBuilder->execute()->fetch();
        $result['tags'] = $this->findLinkedTags($result['idPhoto']);
        if(isset($result['tags'])):
            $result['tag_name'] = $this->selectTagsById($result['tags'],$result);
        endif;
        return $result;
    }

    /**
     * Select photo tags.
     * @param $data
     * @param $result
     * @return mixed
     */
    public function selectTagsById($data,$result){
        $tag_index=0;
        foreach($data as $value):
            foreach($value as $key=>$value2):
                if($key == 'tagId'):
                    $result['tag_name'][$tag_index] = $this->findTagsNames($value2);
                    $tag_index++;
                endif;
            endforeach;
        endforeach;
	return $result['tag_name'];
    }
    /**
     * Find ids of linked tags.
     * @param int $idPhoto IdPhoto
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
     * Find linked tags.
     * @param int $photoId PhotoId
     *
     * @return array Result
     */
    public function findLinkedTags($photoId)
    {
        $tagsIds = $this->findLinkedTagsIds($photoId);

        return is_array($tagsIds)
            ? $this->tagRepository->findById($tagsIds)
            : [];
    }
    /**
     * Find tag's names.
     * @param int $tagId TagId
     *
     * @return array Result
     */
    public function findTagsNames($tagId){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('name')->from('tag')->where('tagId =:tagId')->setParameter(':tagId',$tagId);
        $result['tag_name']=$queryBuilder->execute()->fetch();

        if(isset($result['tag_name'])) return $result['tag_name'];

    }
    /**
     * Remove linked tags.
     * @param int $photoId PhotoId
     *
     * @return boolean Result
     */
    public function removeLinkedTags($photoId)
    {
        return $this->db->delete('photo_tag', ['photoId' => $photoId]);
    }
    /**
     * Remove linked tags.
     * @param int $tagId TagId
     *
     * @return boolean Result
     */
    public function removeLinkedTag($tagId){

        return $this->db->delete('photo_tag', ['tagId' => $tagId]);
    }

    /**
     * Add linked tags.
     * @param $photoId
     * @param $tagsIds
     */
    protected function addLinkedTags($photoId, $tagsIds)
    {
        if (!is_array($tagsIds)) {
            $tagsIds = [$tagsIds];
        }

        foreach ($tagsIds as $tagId) {
            $this->db->insert(
                'photo_tag',
                [
                    'photoId' => $photoId,
                    'tagId' => $tagId,
                ]
            );
        }
    }

    /**
     * Insert user photos.
     * @param $userId
     * @param $photoId
     * @return int
     */
    public function userPhoto($userId, $photoId){
        return $this->db->insert('photo_user', ['userId'=>$userId, 'photoId'=>$photoId]);
    }

    /**
     * Insert user photo.
     * @param $idPhoto
     * @return array
     */
    public function checkEditOption($idPhoto){
        $queryBuilder = $this->db->createQueryBuilder();
        $queryBuilder->select('userId')->from('photo_user')->where('photoId=:photoId')->setParameter(':photoId', $idPhoto);
        return $queryBuilder->execute()->fetchAll();
    }
}