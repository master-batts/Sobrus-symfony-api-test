<?php

namespace App\State\Processors\BlogArticle;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateBlogArticleProcessor implements ProcessorInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface, private ProcessorInterface $processorInterface,private Security $security)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        //dd($this->security->getUser()->getId());
        $data->setAuthorId($this->security->getUser());

        return $this->processorInterface->process($data,$operation,$uriVariables,$context);
    }
}
