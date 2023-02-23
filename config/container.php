<?php

namespace ICanBoogie\Console;

use ICanBoogie\Binding\SymfonyDependencyInjection\ConfigBuilder;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;

return fn(ConfigBuilder $config) => $config
    ->add_compiler_pass(AddConsoleCommandPass::class);
