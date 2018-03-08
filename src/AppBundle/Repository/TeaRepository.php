<?php

namespace AppBundle\Repository;

use AppBundle\Entity\TeaText\BookmarkBlock;
use AppBundle\Entity\TeaText\Font;
use AppBundle\Entity\TeaText\MediaBlock;
use AppBundle\Entity\TeaText\TextBlock;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use PDO;

class TeaRepository extends BaseRepository
{
    const SEARCH_BY_TYPE = 'type';
    const SEARCH_BY_NAME = 'name';
    const SEARCH_LIMIT = 10;

    /**
     *  Gets tea to feature that is not in the previously featured teas that are provided as id array.
     *  if no such tea is available, a random tea is featured
     * @param int[] $previouslyFeatured
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTeaToFeature($previouslyFeatured = [])
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $query = $queryBuilder
            ->select('t, RAND() AS HIDDEN r')
            ->from('AppBundle:Tea', 't')
            ->where('t.published = true');
        if (!empty($previouslyFeatured))
            $query->andWhere($queryBuilder->expr()->notIn('t.id', $previouslyFeatured));
        $teaQuery = $query
            ->orderBy('r', 'ASC')
            ->setMaxResults(1)
            ->getQuery();

        $tea = $teaQuery->getOneOrNullResult();
        if (!$tea) {
            $tea = $this->getRandomTea();
        }

        return $tea;
    }

    // returns a random tea entity

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getRandomTea()
    {
        $tea = $this->getEntityManager()->createQueryBuilder()
            ->select('t, RAND() AS HIDDEN r')
            ->from('AppBundle:Tea', 't')
            ->where('t.published = true')
            ->orderBy('r', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $tea;
    }

    //gets a tea for tea details page.

    /**
     * @param $teaId
     * @param $userId
     * @return mixed|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function displayTeaById($teaId, $userId)
    {
        $sql = "SELECT t.id, t.name, tt.name as tea_type, t.title, t.history, t.water_title, t.water, t.storage_title, t.storage, cover_image, IF(tf.id IS NULL, FALSE, TRUE) as is_favourite, n.id as note_id, n.note as note_text, n.updated_at as note_updated_at " .
            "FROM tea t " .
            "LEFT JOIN tea_type tt on t.tea_type_id = tt.id " .
            "LEFT JOIN tea_favourite tf on t.id = tf.tea_id and tf.user_id = :userId " .
            "LEFT JOIN note n on t.id = n.tea_id and n.user_id = :userId " .
            "WHERE t.id = :teaId;";

        $em = $this->getEntityManager();
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute(['teaId' => $teaId, 'userId' => $userId]);
        $tea = $stmt->fetch(PDO::FETCH_ASSOC);

        return empty($tea) ? null : $this->processDisplayTea($tea);
    }

    /**
     * @param $id
     */
    public function incrementViewCount($id)
    {
        $tea = $this->getTea($id);
        if (!$tea) return;

        $tea->setViewCount($tea->getViewCount() + 1);
        $tea->setIndex(false);
        $em = $this->getEntityManager();
        $em->persist($tea);
        try {
            $em->flush();
        } catch (OptimisticLockException $e) {
        }
    }

    /**
     * @param $teaId
     * @param $user
     * @return array|bool
     */
    public function favouriteTea($teaId, $user)
    {
        /** @var TeaFavouriteRepository $teaFavouriteRepository */
        $teaFavouriteRepository = $this->getEntityManager()->getRepository('AppBundle:TeaFavourite');
        if ($favTea = $teaFavouriteRepository->getFavouriteTea($user, $this->getReference('Tea', $teaId))) {
            $teaFavouriteRepository->deleteFavouriteTea($favTea);
            $isFavourite = false;
        }
        else {
            $teaFavouriteRepository->putFavouriteTea($user, $this->getReference('Tea', $teaId));
            $isFavourite = true;
        }
        return $isFavourite;
    }

    /**
     * @param $text
     * @return array
     */
    public function search($text)
    {
        $text = strtolower($text);
        return $this->searchByKeyword($text, self::SEARCH_BY_NAME, 0, 10);
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\Tea|null|object
     */
    private function getTea($id)
    {
        $er = $this->getEntityManager()->getRepository("AppBundle:Tea");
        return $er->find($id);
    }

    /**
     * @param $description
     * @param bool $recursion
     * @return \Generator|void
     */
    private function parseDescription($description, $recursion = false)
    {
        $description = preg_split('/\r\n/', $description);
        if (empty($description)) return;
        foreach ($description as $descriptionBlock) {
            //special escape for images within paragraph.
            if ((strpos($descriptionBlock, '<img src') !== false || strpos($descriptionBlock, '<img alt') !== false) && strpos($descriptionBlock, '<p>') !== false) {
                $descriptionBlock =  preg_split('/\r\n/', str_replace('<img alt', "\r\n<img alt", $descriptionBlock));
                foreach ($descriptionBlock as $splitParagraph) {
                    yield $splitParagraph;
                }
            } else {
                yield trim($descriptionBlock);
            }
        }
    }

    /**
     * @param $keyword
     * @param $searchType
     * @param $offset
     * @param int $limit
     * @return array
     */
    private function searchByKeyword($keyword, $searchType, $offset, $limit = self::SEARCH_LIMIT): array
    {
        if ($searchType == 'type') {
            $searchParam = 'tt.name';
        } else {
            $searchParam = 't.name';
        }
        return $this->getEntityManager()->createQueryBuilder()
            ->select('t.id, t.name, tt.name as tea_type, t.coverImage as cover_image')
            ->from('AppBundle:Tea', 't')
            ->innerJoin('AppBundle:TeaType', 'tt', 't.teaType = tt.id')
            ->where("LOWER($searchParam) LIKE :name")
            ->andWhere('t.published = true')
            ->setParameter('name', "%$keyword%")
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param $tea
     * @return array
     */
    private function processDisplayTea($tea): array
    {
        $description = [];

        $description[] = $this->createMediaBlock(MediaBlock::TYPE_MAIN_TITLE, $tea);

        $fields = ['history', 'water', 'storage'];
        foreach ($fields as $field) {
            $description[] = $this->createMediaBlock($field, $tea);
        }

        $tea['description'] = $description;
        $tea['font'] = Font::getFontBlocks([Font::TYPE_TITLE, Font::TYPE_PARAGRAPH]);
        $tea['id'] = intval($tea['id']);
        $tea['is_favourite'] = $tea['is_favourite'] == 1 ? true : false;
        $tea['cover_image'] = 'tea/' . $tea['cover_image'];

        if (isset($tea['note_id'])) {
            $tea['note']['id'] = intval($tea['note_id']);
            $tea['note']['text'] = $tea['note_text'];
            $tea['note']['updated_at'] = $tea['note_updated_at'];
        }

        unset($description, $descripionBlock, $tea['history'], $tea['water'], $tea['storage'], $tea['note_id'], $tea['note_text'], $tea['preparation_title'], $tea['note_title'], $tea['preparation_title']);
        $parsedTea['tea'] = $tea;
        return $parsedTea;
    }

    /**
     * @param $field
     * @param $tea
     * @return array
     */
    private function createMediaBlock($field, $tea): array {
        if (!array_key_exists($field, $tea) && $field != MediaBlock::TYPE_MAIN_TITLE)
            return null;

        $block = [];
        $block['type'] = $field;
        $block['items'] = [];

        if ($field == MediaBlock::TYPE_MAIN_TITLE) {
            $block['items'][] = new MediaBlock($tea['name'], MediaBlock::TYPE_MAIN_TITLE);
        } else {
            switch ($field) {
                case 'history':
                    if (isset($tea['title']))
                        $block['items'][] = new MediaBlock($tea['title'], MediaBlock::TYPE_TITLE);
                    break;
                case 'water':
                    if (isset($tea['water_title']))
                        $block['items'][] = new MediaBlock($tea['water_title'], MediaBlock::TYPE_TITLE);
                    break;
                case 'storage':
                    if (isset($tea['storage_title']))
                        $block['items'][] = new MediaBlock($tea['storage_title'], MediaBlock::TYPE_TITLE);
                    break;
            }
            $block['items'] = array_merge($block['items'], $this->parseTeaDescription($tea[$field]));
        }
        return $block;
    }

    /**
     * @param $teaField
     * @return array
     */
    private function parseTeaDescription($teaField): array {
        $block = [];
        foreach ($this->parseDescription($teaField) as $descriptionBlock) {
            if (strpos($descriptionBlock, '<h1>') !== false) {
                $block[] = new MediaBlock(html_entity_decode(strip_tags($descriptionBlock), ENT_QUOTES), MediaBlock::TYPE_TITLE);
            } else if (strpos($descriptionBlock, '<img src') !== false || strpos($descriptionBlock, '<img alt') !== false) {
                $url = 'tea-content/' . array_slice(explode('/', explode('"', explode('src="', $descriptionBlock)[1])[0]), -1)[0];
                $block[] = new MediaBlock($url, MediaBlock::TYPE_IMAGE);
            } else if (strpos($descriptionBlock, '<p>') !== false) {
                $block[] = new MediaBlock(html_entity_decode(strip_tags($descriptionBlock), ENT_QUOTES), MediaBlock::TYPE_PARAGRAPH);
            }
        }
        return $block;
    }
}