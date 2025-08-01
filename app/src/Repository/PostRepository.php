<?php

/**
 * Post repository.
 */

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Tag;
use App\Entity\User;
use App\Dto\PostListFiltersDto;

/**
 * PostRepository class.
 */
class PostRepository extends ServiceEntityRepository
{
    /**
     * PostRepository constructor.
     *
     * @param ManagerRegistry $registry The registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Query all posts.
     *
     * @param User               $author  The author of the posts
     * @param PostListFiltersDto $filters The filters for the post list
     *
     * @return QueryBuilder The query builder
     */
    public function queryAll(?User $author, PostListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('post')
            ->select(
                'partial post.{id, createdAt, updatedAt, title, status}',
                'partial category.{id, name}',
                'partial tags.{id, title}'
            )
            ->join('post.category', 'category')
            ->leftJoin('post.tags', 'tags');

        if (!$author instanceof User) {
            $queryBuilder->andWhere('post.status = :status')
                ->setParameter('status', 'published');
        }

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query posts by category.
     *
     * @param int $categoryId The category ID
     *
     * @return QueryBuilder The query builder
     */
    public function queryByCategory(int $categoryId): QueryBuilder
    {
        return $this->createQueryBuilder('post')
            ->andWhere('post.category = :category')
            ->setParameter('category', $categoryId)
            ->orderBy('post.createdAt', 'DESC');
    }

    /**
     * Save a post.
     *
     * @param Post $post The post to save
     *
     * @return void returns nothing
     */
    public function savePost(Post $post): void
    {
        $this->getEntityManager()->persist($post);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete a post.
     *
     * @param Post $post The post to delete
     *
     * @return void returns nothing
     */
    public function deletePost(Post $post): void
    {
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();
    }

    /**
     * Count posts by category.
     *
     * @param Category $category The category to count posts for
     *
     * @return int The count of posts in the category
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->createQueryBuilder('post');

        return $qb->select($qb->expr()->countDistinct('post.id'))
            ->andWhere('post.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count posts by tag.
     *
     * @param Tag $tag The tag to count posts for
     *
     * @return int The count of posts with the tag
     */
    public function countByTag(Tag $tag): int
    {
        $qb = $this->createQueryBuilder('post');

        return $qb->select($qb->expr()->countDistinct('post.id'))
            ->innerJoin('post.tags', 'tag')
            ->andWhere('tag.id = :tag')
            ->setParameter(':tag', $tag)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Query posts by tag.
     *
     * @param int $id The tag ID
     *
     * @return QueryBuilder The query builder
     */
    public function queryByTag(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('post')
            ->innerJoin('post.tags', 'tag')
            ->andWhere('tag.id = :id')
            ->setParameter('id', $id)
            ->orderBy('post.createdAt', 'DESC');
    }

    /**
     * Apply filters to the post list query.
     *
     * @param QueryBuilder       $queryBuilder The query builder
     * @param PostListFiltersDto $filters      The filters for the post list
     *
     * @return QueryBuilder The modified query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, PostListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('post.category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere('tags IN (:tag)')
                ->setParameter('tag', $filters->tag);
        }

        if ($filters->search) {
            $queryBuilder->andWhere('post.title LIKE :search OR post.content LIKE :search')
                ->setParameter('search', '%'.$filters->search.'%');
        }

        return $queryBuilder;
    }





    //    /**
    //     * @return Post[] Returns an array of Post objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Post
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
