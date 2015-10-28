<?php

namespace AppBundle\Form\Handler;

use AppBundle\Exception\InvalidFormException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;

class FormHandler
{
    private $em;
    private $formFactory;
    private $formType;

    public function __construct(ObjectManager $objectManager, FormFactoryInterface $formFactory, FormTypeInterface $formType)
    {
        $this->em = $objectManager;
        $this->formFactory = $formFactory;
        $this->formType = $formType;
    }

    public function processForm($object, array $parameters, array $config)
    {
        $form = $this->formFactory->create($this->formType, $object, array(
            'method'            => $config['method'],
            'csrf_protection'   => false,
        ));

        $form->submit($parameters, "PATCH" !== $config['method']);

        if ( ! $form->isValid()) {
            throw new InvalidFormException($form);
        }

        $data = $form->getData();

        if ($config['persist'] == true) {
            $this->persist($data);
        }

        return $data;
    }

    public function persist($object)
    {
        $this->em->persist($object);
        $this->em->flush();
    }

    public function delete($object)
    {
        $this->em->remove($object);
        $this->em->flush();

        return true;
    }
}