<?php

namespace App\State\Processors\BlogArticle;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PublishBlogArticleProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $processorInterface)
    {
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $data->setStatus('published');

        return $this->processorInterface->process($data,$operation,$uriVariables,$context);
    }
}
