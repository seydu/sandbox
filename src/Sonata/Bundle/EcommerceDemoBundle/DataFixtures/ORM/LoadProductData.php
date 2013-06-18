<?php

namespace Sonata\Bundle\EcommerceDemoBundle\DataFixtures\ORM;

use Application\Sonata\ProductBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\Component\Product\ProductManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadProductData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function getOrder()
    {
        return 50;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $this->createCategories();
        $this->createProducts();
    }

    public function createCategories()
    {
        $faker = $this->getFaker();
        $em    = $this->getEntityManager();

        $categoryManager = $this->getCategoryManager();

        $description = sprintf('<p>Category description goes here&nbsp;: <em>%s</em></p>', $this->getFaker()->sentence(20));

        $this->addReference('category-mac', $category_mac = $categoryManager->createCategory());
        $category_mac->setName('Mac');
        $category_mac->setDescription($description);
        $category_mac->setRawDescription($description);
        $category_mac->setDescriptionFormatter('richhtml');
        $category_mac->setSubdescription($faker->sentence(20));
        $category_mac->setEnabled(true);
        $em->persist($category_mac);

        $this->addReference('category-pc', $category_pc = $categoryManager->createCategory());
        $category_pc->setName('PC');
        $category_pc->setDescription($description);
        $category_pc->setRawDescription($description);
        $category_pc->setDescriptionFormatter('richhtml');
        $category_pc->setSubdescription($faker->sentence(20));
        $category_pc->setEnabled(true);
        $em->persist($category_pc);

        $this->addReference('category-mac-mba', $category_mba = $categoryManager->createCategory());
        $category_mba->setName('MacBook Air');
        $category_mba->setDescription($description);
        $category_mba->setRawDescription($description);
        $category_mba->setDescriptionFormatter('richhtml');
        $category_mba->setSubdescription($faker->sentence(20));
        $category_mba->setEnabled(true);
        $category_mba->setParent($category_mac);
        $em->persist($category_mba);

        $this->addReference('category-mac-mbp', $category_mbp = $categoryManager->createCategory());
        $category_mbp->setName('MacBook Pro');
        $category_mbp->setDescription($description);
        $category_mbp->setRawDescription($description);
        $category_mbp->setDescriptionFormatter('richhtml');
        $category_mbp->setSubdescription($faker->sentence(20));
        $category_mbp->setEnabled(true);
        $category_mbp->setParent($category_mac);
        $em->persist($category_mbp);

        $this->addReference('category-mac-apps', $category_app = $categoryManager->createCategory());
        $category_app->setName('Applications');
        $category_app->setDescription($description);
        $category_app->setRawDescription($description);
        $category_app->setDescriptionFormatter('richhtml');
        $category_app->setSubdescription($faker->sentence(20));
        $category_app->setEnabled(true);
        $category_app->setParent($category_mac);
        $em->persist($category_app);

        $this->addReference('category-pc-laptop', $category_laptop = $categoryManager->createCategory());
        $category_laptop->setName('Laptops');
        $category_laptop->setDescription($description);
        $category_laptop->setRawDescription($description);
        $category_laptop->setDescriptionFormatter('richhtml');
        $category_laptop->setSubdescription($faker->sentence(20));
        $category_laptop->setEnabled(true);
        $category_laptop->setParent($category_pc);
        $em->persist($category_laptop);

        $this->addReference('category-pc-desktop', $category_desktop = $categoryManager->createCategory());
        $category_desktop->setName('Desktop');
        $category_desktop->setDescription($description);
        $category_desktop->setRawDescription($description);
        $category_desktop->setDescriptionFormatter('richhtml');
        $category_desktop->setSubdescription($faker->sentence(20));
        $category_desktop->setEnabled(true);
        $category_desktop->setParent($category_pc);
        $em->persist($category_desktop);

        $this->addReference('category-pc-software', $category_pc_software = $categoryManager->createCategory());
        $category_pc_software->setName('Software');
        $category_pc_software->setDescription($description);
        $category_pc_software->setRawDescription($description);
        $category_pc_software->setDescriptionFormatter('richhtml');
        $category_pc_software->setSubdescription($faker->sentence(20));
        $category_pc_software->setEnabled(true);
        $category_pc_software->setParent($category_pc);
        $em->persist($category_pc_software);

        $em->flush();
    }

    public function createProducts()
    {
        $hardwareCategories = array(
            $this->getReference('category-pc-desktop'),
            $this->getReference('category-pc-laptop'),
            $this->getReference('category-mac-mbp'),
            $this->getReference('category-mac-mba'),
        );

        $softwareCategories = array(
            $this->getReference('category-mac-apps'),
            $this->getReference('category-pc-software'),
        );

        $key = 0;

        foreach ($hardwareCategories as $hardwareCategory) {
            for ($i = 1; $i <= 50; $i++) {
                $this->createProduct($this->getHardwareProductManager(), $key, $hardwareCategory);
                $key++;
            }
        }

        foreach ($softwareCategories as $softwareCategory) {
            for ($i = 1; $i <= 50; $i++) {
                $this->createProduct($this->getSoftwareProductManager(), $key, $softwareCategory);
                $key++;
            }
        }
    }

    protected function createProduct(ProductManagerInterface $manager, $key, Category $category)
    {
        $description = sprintf('<p>Product description goes here&nbsp;: <em>%s</em></p>', $this->getFaker()->sentence(20));
        $image = $this->getImage();

        $product = $manager->create();
        $product->setName('Product '.$key);
        $product->setSku(sprintf('%06d', $key));
        $product->setSlug('product-'.$product->getSku());
        $product->setDescription($description);
        $product->setRawDescription($description);
        $product->setDescriptionFormatter('richhtml');
        $product->setPrice(rand(150, 2500));
        $product->setVat(19.60);
        $product->setStock(rand(0, 10000));
        $product->setEnabled(true);
        $product->setImage($image);
        $this->getEntityManager()->persist($product);

        $productCategory = $this->getProductCategoryManager()->createProductCategory();
        $productCategory->setProduct($product);
        $productCategory->setCategory($category);
        $productCategory->setEnabled(true);
        $productCategory->setCreatedAt(new \DateTime());
        $productCategory->setUpdatedAt(new \DateTime());
        $this->getEntityManager()->persist($productCategory);

        $this->addDeliveriesToProduct($product);

        $this->getEntityManager()->flush();
    }

    public function addDeliveriesToProduct($product)
    {
        $delivery_fr = $this->getDeliveryManager()->createDelivery();
        $delivery_fr->setCountryCode('FR');
        $delivery_fr->setCode('free');
        $delivery_fr->setEnabled(true);
        $delivery_fr->setPerItem(2);
        $delivery_fr->setProduct($product);
        $delivery_fr->setCreatedAt(new \DateTime());
        $delivery_fr->setUpdatedAt(new \DateTime());
        $this->getEntityManager()->persist($delivery_fr);

        $delivery_us = $this->getDeliveryManager()->createDelivery();
        $delivery_us->setCountryCode('US');
        $delivery_us->setCode('free');
        $delivery_us->setEnabled(true);
        $delivery_us->setPerItem(2);
        $delivery_us->setProduct($product);
        $delivery_us->setCreatedAt(new \DateTime());
        $delivery_us->setUpdatedAt(new \DateTime());
        $this->getEntityManager()->persist($delivery_us);
    }

    /**
     * @return MediaInterface
     */
    public function getImage()
    {
        return $this->getReference('sonata-media-0');
    }

    /**
     * @return \Sonata\Component\Product\CategoryManagerInterface
     */
    public function getCategoryManager()
    {
        return $this->container->get('sonata.category.manager');
    }

    /**
     * @return \Sonata\Component\Product\DeliveryManagerInterface
     */
    public function getDeliveryManager()
    {
        return $this->container->get('sonata.delivery.manager');
    }

    /**
     * @return \Sonata\Component\Product\ProductManagerInterface
     */
    public function getHardwareProductManager()
    {
        return $this->container->get('sonata.demo.product.manager.hardware');
    }

    /**
     * @return \Sonata\Component\Product\ProductManagerInterface
     */
    public function getSoftwareProductManager()
    {
        return $this->container->get('sonata.demo.product.manager.software');
    }

    /**
     * @return \Sonata\ProductBundle\Entity\ProductCategoryManager
     */
    public function getProductCategoryManager()
    {

        return $this->container->get('sonata.product_category.product');
    }

    /**
     * @return \Faker\Generator
     */
    public function getFaker()
    {
        return $this->container->get('faker.generator');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }
}