<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContactFormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'mapped'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'name'=>'name'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('contact.name'),
            ])
            ->add('firstname', TextType::class,[
                'mapped'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('contact.firstname'),
            ])
            ->add('phone', TextType::class,[
                'mapped'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('contact.phone'),
            ])
            ->add('subject', TextType::class,[
                'mapped'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('contact.subject'),
            ])
            ->add('email', EmailType::class,[
                'mapped' => false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> $this->translator->trans('contact.mail_placeholder'),
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=> $this->translator->trans('contact.mail'),
            ])
            ->add('message', TextareaType::class,[
                'mapped' => false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> $this->translator->trans('contact.message'),
                    'row'=>"5"
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=> $this->translator->trans('contact.message'),
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'contact'
            ]);

        ;
    }

}
