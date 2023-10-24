<?php

namespace App\Utils\AbstractClasses;

use App\Twig\AppExtension;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class CategoryTreeAbstract
{
    public $categoriesArrayFromDb;
    public $categoryList;
    protected static $dbconnection;

    public $slugger;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface  $urlgenerator)
    {
        $this->entityManager = $entityManager;
        $this->urlgenerator = $urlgenerator;
        $this->categoriesArrayFromDb = $this->getCategories();
        $this->slugger = new AppExtension();
    }
    abstract public function getCategoryList(array $categoriesArray);

    public function buildTree(int $parentId = null) :array
    {
        $subcategory = [];
        foreach ( $this->categoriesArrayFromDb as $category)
        {
            if($category['parent_id'] == $parentId)
            {
                $children = $this->buildTree($category['id']);
                if ($children)
                {
                    $category['children'] = $children;
                }
                $subcategory[] = $category;
            }
        }
        return $subcategory;
    }
    private function getCategories()
    {
        if (self::$dbconnection)
        {
            return self::$dbconnection;
        } else {
            $conn = $this->entityManager->getConnection();
            $sql = "SELECT * FROM categories";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return self::$dbconnection = $stmt->fetchAll();
        }

    }
}