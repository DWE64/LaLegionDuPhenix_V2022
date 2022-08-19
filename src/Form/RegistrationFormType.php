<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
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
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('registration.name'),
            ])
            ->add('firstname', TextType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('registration.firstname'),
            ])
            ->add('birthday', TextType::class,[
                'mapped'=>false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control date',
                    'data-toggle'=>'date-picker',
                    'data-single-date-picker'=>"true",

                ],
                'label'=>$this->translator->trans('registration.birthday'),
                'label_attr'=>[
                    'class'=>'form-label'
                ],
            ])
            /*->add('birthday', BirthdayType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control date',
                    'data-toggle'=>'date-picker',
                    'data-single-date-picker'=>"true",

                ],
                'label'=>$this->translator->trans('registration.birthday'),
                'label_attr'=>[
                    'class'=>'form-label'
                ],
            ])*/
            ->add('address', TextType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('registration.address'),
            ])
            ->add('postalCode', IntegerType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('registration.postal_code'),
            ])
            ->add('city', TextType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control'
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=>$this->translator->trans('registration.city'),
            ])
            ->add('email', EmailType::class,[
                'mapped' => false,
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> $this->translator->trans('registration.mail_placeholder'),
                ],
                'label_attr'=>[
                    'class'=>'form-label'
                ],
                'label'=> $this->translator->trans('registration.mail'),
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'label'=> $this->translator->trans('registration.password'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('registration.message_password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                                   'message' => $this->translator->trans('registration.message_term_agree'),
                               ]),
                ],
                'label'=> $this->translator->trans('registration.accept_term_agree'),
                'label_attr'=>[
                    'class' => 'form-check-label',
                ],
                'attr'=>[
                    'class'=>'form-check-input'
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}