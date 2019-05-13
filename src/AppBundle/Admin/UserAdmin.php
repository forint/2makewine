<?php

namespace AppBundle\Admin;

use AppBundle\Entity\User;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use AppBundle\Entity\UserTranslation;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserAdmin extends AbstractAdmin
{


    /**
     * change label on breadcrumb
     */
    public function configure()
    {
        parent::configure();
        $this->classnameLabel = "All User";
    }


    protected $baseRouteName = 'sonata_user';
    private $container;

    /**
     * UserAdmin constructor.
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param        $container
     */
    public function __construct(
        $code,
        $class,
        $baseControllerName,
        $container
    )
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->container = $container;
    }


   /* public function createQuery($context = 'list')
    {
//        dump($context = 'list');
//        SELECT count(DISTINCT o.id) as cnt FROM AppBundle\Entity\User o LEFT JOIN o.wineries s_wineries WHERE oid = :id
        $query = parent::createQuery($context);
//        $query->andWhere(
//            $query->expr()->eq($query->getRootAliases()[0] . '.id', ':id')
//        );

//        $query->setParameter('id', 2);
        $query
            ->andWhere($query->getRootAliases()[0] . '.isWineMaker = :user_id')
            ->setParameter('user_id', true);

        return $query;
    }
    */

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username', null, ['global_search' => true], null, [])
            ->add('isWineMaker', null, [
//                'show_filter' => true
            ])
            ->add('email', null, [
                'global_search' => true
            ], null, [])
            ->add('enabled', null, ['global_search' => true], null, [])
//            ->add('wineries', null, ['global_search' => true], null, [])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {

        /*
         * if ($this->hasRequest()) {
            $showFoo = $this->getRequest()->get('show_foo', null);

            if ($showFoo === '1') {
                $listMapper->add('foo');
            }
            dump($this->getRoutes()->getBaseRouteName());
        }
        */

        $listMapper
            ->addIdentifier('firstname')
            ->add('email')
            ->add('enabled')
            ->add('isWineMaker')
//            ->add('wineries')
            ->add('wineries', null, [
                'route' => [
                    'name' => 'show'
                ]
            ])
//            ->add('wineries', null, [
//                'associated_property'=>'id'
//            ])
            ->add('groups', 'choice', array(
                'multiple' => true,
                'delimiter' => ' | ',
                'choices' => User::class
            ))
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $user = $this->getSubject();
//        $container = $this->getConfigurationPool()->getContainer();


        $helper
            = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($user, 'imageFile', User::class);

        if (!$path) {
            $path = "/images/empty_avatar.png";
        }
        /*ADMIN IMG PREVIEW*/
//        img.admin-preview {
//        max-height: 50px;
//        max-width: 50px;
//        }

        $formMapper
            ->with('User info', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-primary',
                'description' => 'User general info',
            ])
            ->add('firstname', TextType::class, [
                'constraints' => new NotBlank()
            ])
            ->add('email', EmailType::class, [
                'constraints' => [new NotBlank(), new Email()],
            ])
            ->add('plainPassword', TextType::class);

//            ->add('plainPassword', PasswordType::class, [
//                'constraints' => new NotBlank(),
//                'required' => false,
//                'always_empty'=>false,
//
//            ]);


//        if ($user->getPassword()) {
//            $formMapper
//                ->add('plainPassword', TextType::class, [
//                    'constraints' => new NotBlank(),
//                    'label'=> 'OK'
//                ]);
//        }


//        if ($this->isCurrentRoute('create')) {
//            $formMapper
//                ->add('plainPassword', TextType::class, [
//                    'constraints' => new NotBlank()
//                ]);
//        }

        $formMapper
            ->add('enabled', null, ['label' => 'Is active'])
            ->add('groups', 'sonata_type_model', [
                'property' => 'name',
                'multiple' => true,
                'btn_add' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Avatar',
                'data_class' => null,
                'required' => false,
            ])
            ->end()
            /*
             ->with('Users wineries', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-info',
                'description' => 'Users wineries info',
            ])
            ->add('wineries', 'sonata_type_model', [
                'multiple'=>true
            ])
            */
            ->end()
            ->with('User Type', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-info',
                'description' => 'Users type - User / Winemaker',
            ])
            ->add('isWineMaker', 'sonata_type_choice_field_mask', [
                'choices' => User::getUserType(),
                'map' => [
                    true => ['winemakerDescription'],
                    false => ['wineries'],
                ],
                'placeholder' => 'Choose type of User',
                'required' => true,
                "label" => false,
//                'expanded' => true,
//                'multiple' => true,
            ])
            ->add('winemakerDescription', TextareaType::class, [
                "label" => "Winemaker legend",
            ])
            ->add('wineries', 'sonata_type_model', [
                'required' => false,
                'multiple' => true,
                'btn_add' => 'Add Winery'
            ])
            ->end()
            ->with('User Address', [
                'class' => 'col-md-4',
                'box_class' => 'box box-solid box-info',
                'description' => 'User Address',
            ])
            ->add('phone', TextType::class)
            ->add('country', CountryType::class, array('empty_data' => 'Country','label' => false))
            ->add('city', TextType::class)
            ->add('state', TextType::class)
            ->add('zipCode', TextType::class)
            ->add('address', TextType::class)
            ->end()
            ->setHelps([
                'imageFile' => "<img src=" . $path . " class='admin-preview' style='max-height: 100px; max-width: 100px'/>",
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('wineries');
    }

    public function validate(ErrorElement $errorElement, $object)
    {

        if ($object->isWineMaker() === null) {
            $errorElement
                ->with('isWineMaker')
                ->assertNotBlank()
                ->end();
        }

        if ($object->isWineMaker()) {
            $errorElement
                ->with('winemakerDescription')
                ->assertNotBlank()
                ->end();
        }

    }

    public function toString($object)
    {
        return $object instanceof User
            ? $object->getUserName()
            : 'User'; // shown in the breadcrumb on the create view
    }

}
