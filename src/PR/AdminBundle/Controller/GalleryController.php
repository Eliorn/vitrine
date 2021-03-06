<?php

namespace PR\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Forms;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use PR\VitrineBundle\Entity\Gallery;
use PR\VitrineBundle\Entity\Image;

class GalleryController extends Controller
{


    public function galleryAction()
    {
      $em = $this->getDoctrine()->getManager();
      $galleryRepository = $em->getRepository('PRVitrineBundle:Gallery');
          $listGalleries = $galleryRepository->findAll();
      return $this->render('PRAdminBundle:Admin:gallery.html.twig', array(
                'listGalleries' => $listGalleries,
              )
      );
    }


    


    public function galleryNewAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $galleryRepository = $em->getRepository('PRVitrineBundle:Gallery');
      $gallery= new Gallery();
      $lastOrder = $galleryRepository->getMaxOrder();
      
      $lastOrder = ++$lastOrder[0]['max_order'];
     
      if ($request->request->get('action')== 'Valider'){

        if (is_dir($this->get('kernel')->getRootDir().'/../web/data/galleries/'.strtolower($request->request->get('form')['Title']))){
              $request->getSession()->getFlashBag()->add('error', "Une galerie avec le même nom existe déjà ! ");
        }else{
          

          $gallery->setTitle($request->request->get('form')['Title']);
          $gallery->setDescription($request->request->get('form')['Description']);
          $gallery->setCaption($request->request->get('form')['Caption']);
          $gallery->setDirectory(strtolower($request->request->get('form')['Title']));
          $gallery->setGalleryOrder($lastOrder);
          $em->persist($gallery);
          $em->flush();
          mkdir($this->get('kernel')->getRootDir().'/../web/data/galleries/'.strtolower($request->request->get('form')['Title']));

          $request->getSession()->getFlashBag()->add('success', "La création de la galerie a été effectuée");

        }
      }else if ($request->request->get('action')== 'Réinitialiser'){
        $request->getSession()->getFlashBag()->add('warn', "Retour au début !");
      }

      $formBuilder = $this->createFormBuilder($gallery)
                          ->add('Title',    TextType::Class , array('empty_data' => 'Votre titre ici'))
                          ->add('Description',  TextType::Class , array('empty_data' => 'La description de la galerie'))
                          ->add('Caption', TextType::Class);

      $form= $formBuilder->getForm();

      return $this->render('PRAdminBundle:Admin:gallery_new.html.twig', array(
                'form' => $form->createView(),
            )
      );
    }

    public function galleryDeleteAction(Request $request){
       

      $galleryName=$request->attributes->get('galleryName');

      $em = $this->getDoctrine()->getManager();
      $imageRepository = $em->getRepository('PRVitrineBundle:Image');
      $galleriesRepository = $em->getRepository('PRVitrineBundle:Gallery');

      $galleryDirectory=$this->get('kernel')->getRootDir().'/../web/data/galleries/'.$galleryName;
      $galleryDirectoryWeb='./data/galleries/'.$galleryName.'/';

      $galleryToDelete = $galleriesRepository->findOneBy(["title" => $galleryName]);

      $queryListImg = $imageRepository->createQueryBuilder('a')
                                      ->where('a.galleryPath=?1 ')
                                      ->orderBy('a.pictureOrder','ASC');
      $queryListImg->setParameters(array(1 => $galleryDirectoryWeb));
      $query = $queryListImg->getQuery();
      $listImg = $query->getResult();

      foreach ($listImg as $imageToDel){
        $em->remove($imageToDel);
        $em->flush();
        @unlink($galleryDirectory.'/'.$imageName);
      }
      
      $em->remove($galleryToDelete);
      $em->flush();

      @unlink($galleryDirectory);

      $request->getSession()->getFlashBag()->add('success', "La galerie a bien été supprimée.");
      return $this->redirectToRoute('admin_gallery'); 
    }


    public function galleryEditAction(Request $request){

      $galleryName=$request->attributes->get('gallery');
      $em = $this->getDoctrine()->getManager();
      $imageRepository = $em->getRepository('PRVitrineBundle:Image');
      $galleriesRepository = $em->getRepository('PRVitrineBundle:Gallery');
      $galleryDirectory=$this->get('kernel')->getRootDir().'/../web/data/galleries/'.$galleryName;
      $galleryDirectoryWeb='./data/galleries/'.$galleryName.'/';

      $queryListImg = $imageRepository->createQueryBuilder('a')
                                      ->where('a.galleryPath=?1 ')
                                      ->orderBy('a.pictureOrder','ASC');
      $queryListImg->setParameters(array(1 => $galleryDirectoryWeb));
      $query = $queryListImg->getQuery();
      $listImg = $query->getResult();

      return $this->render('PRAdminBundle:Admin:gallery_edit.html.twig',array(
                'galleryName' => $galleryName,
                'galleryDirectory' => $galleryDirectoryWeb,
                'listPictures' => $listImg
              ));

    }

    public function galleryChangeOrderAction(Request $request){

      $galleryName=$request->attributes->get('galleryName');
      $imageName=$request->attributes->get('image');
      $em = $this->getDoctrine()->getManager();
      $imageRepository = $em->getRepository('PRVitrineBundle:Image');
      $galleriesRepository = $em->getRepository('PRVitrineBundle:Gallery');
      $galleryDirectory=$this->get('kernel')->getRootDir().'/../web/data/galleries/'.$galleryName;
      $galleryDirectoryWeb='./data/galleries/'.$galleryName.'/';


      $newOrder=$request->request->get('newOrder');
      $oldOrderRequest=$imageRepository->createQueryBuilder('a')
                                ->where('a.galleryPath=?1 ')
                                ->andWhere('a.name=?2');
      $oldOrderRequest->setParameters(array(1 => $galleryDirectoryWeb, 2=>$imageName)); 
      $query = $oldOrderRequest->getQuery();
      $image= $query->getResult()[0];
      $oldOrder = $image->getPictureOrder();

      if($oldOrder < $newOrder){      
          $set = "picture_order = picture_order - 1";
          $where = "picture_order > $oldOrder and picture_order <= $newOrder and gallery_path='$galleryDirectoryWeb'";
      }else{
          $set = "picture_order = picture_order + 1";
          $where = "picture_order < $oldOrder and picture_order >= $newOrder and gallery_path='$galleryDirectoryWeb'";
      }
      
      $query = "update image set $set where $where";
      //Now update all so between $old and $new
      $conn = $em->getConnection();
      $stmt = $conn->prepare($query);
      $stmt->execute();

      // update New();
      $image->setPictureOrder($newOrder);
      $em->persist($image);
      $em->flush();
      
    

      $queryListImg = $imageRepository->createQueryBuilder('a')
                                      ->where('a.galleryPath=?1 ')
                                      ->orderBy('a.pictureOrder','ASC');
      $queryListImg->setParameters(array(1 => $galleryDirectoryWeb));
      $query = $queryListImg->getQuery();
      $listImg = $query->getResult();
   
      return $this->render('PRAdminBundle:Admin:gallery_edit.html.twig',array(
                'galleryName' => $galleryName,
                'galleryDirectory' => $galleryDirectoryWeb,
                'listPictures' => $listImg
              ));
    }


    public function galleryChangeThumbnailAction(Request $request){

      $galleryName=$request->attributes->get('galleryName');
      $imageName=$request->attributes->get('image');
      $em = $this->getDoctrine()->getManager();
      $imageRepository = $em->getRepository('PRVitrineBundle:Image');
      $galleriesRepository = $em->getRepository('PRVitrineBundle:Gallery');
      $galleryDirectory=$this->get('kernel')->getRootDir().'/../web/data/galleries/'.$galleryName;
      $galleryDirectoryWeb='./data/galleries/'.$galleryName.'/';

      $oldThumbnailRequest=$imageRepository->createQueryBuilder('a')
                                ->where('a.galleryPath=?1 ')
                                ->andWhere('a.name=?2');
      $oldThumbnailRequest->setParameters(array(1 => $galleryDirectoryWeb, 2=>"thumbnail.jpg")); 
      $query = $oldThumbnailRequest->getQuery();
      $imageOldThumb= $query->getResult()[0];
      
      
      $newThumbnailRequest=$imageRepository->createQueryBuilder('a')
                                ->where('a.galleryPath=?1 ')
                                ->andWhere('a.name=?2');
      $newThumbnailRequest->setParameters(array(1 => $galleryDirectoryWeb, 2=>$imageName)); 
      $query = $newThumbnailRequest->getQuery();
      $imageNewThumb= $query->getResult()[0];
      
      $oldName=$imageNewThumb->getName();



      $filesystem = new Filesystem();

      $filesystem->rename($galleryDirectoryWeb."thumbnail.jpg",$galleryDirectoryWeb."thumbnail_old.jpg");
      $filesystem->rename($galleryDirectoryWeb.$oldName,$galleryDirectoryWeb."thumbnail.jpg");
      $filesystem->rename($galleryDirectoryWeb."thumbnail_old.jpg",$galleryDirectoryWeb.$oldName);
      // update
      $imageNewThumb->setName("thumbnail.jpg"); 
      $imageOldThumb->setName($oldName);
      $em->persist($imageOldThumb);
      $em->persist($imageNewThumb);
      $em->flush();
      
    

      $request->getSession()->getFlashBag()->add('success', "La miniature de la galerie a bien été modifiée.<br/> Il faut rafraichir le cache navigateur ( Ctrl + F5 ) pour bien la voir apparaitre.");
      return $this->redirectToRoute('admin_gallery'); 
    }



    public function deleteImageFromGalleryAction(Request $request){
      
      $galleryName=$request->attributes->get('galleryName');
      $imageName=$request->attributes->get('image');
      $em = $this->getDoctrine()->getManager();
      $imageRepository = $em->getRepository('PRVitrineBundle:Image');

      $galleriesRepository = $em->getRepository('PRVitrineBundle:Gallery');
      $galleryDirectory=$this->get('kernel')->getRootDir().'/../web/data/galleries/'.$galleryName;
      $galleryDirectoryWeb='./data/galleries/'.$galleryName.'/';

      $imageToDel = $imageRepository->findOneBy(array('name' => $imageName, 'galleryPath' => $galleryDirectoryWeb));
      $sortToKeep = $imageToDel->getPictureOrder();
      $em->remove($imageToDel);
      $em->flush();
      @unlink($galleryDirectory.'/'.$imageName);
     
      $queryListImg = $imageRepository->createQueryBuilder('a')
                                      ->where('a.galleryPath=?1 ')
                                      ->andWhere("a.pictureOrder>=?2")
                                      ->orderBy('a.pictureOrder','ASC');
      $queryListImg->setParameters(array(1 => $galleryDirectoryWeb , 2 => $sortToKeep));
      $query = $queryListImg->getQuery();
      $listImg = $query->getResult();

      foreach ($listImg as $imageToSort){
        
        $imageToSort->setPictureOrder($imageToSort->getPictureOrder()-1);
        $em->persist($imageToSort);
        $em->flush();

      }


      $request->getSession()->getFlashBag()->add('success', "L'image a bien été supprimée.");
      return $this->redirectToRoute('admin_gallery_edit', ['gallery' => $galleryName]);


    }

}
