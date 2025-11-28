<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\Security\JwtTokenService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class GenerateTestJwtTokenCommand extends Command
{
    protected static $defaultName = 'app:jwt:generate-test-token';

    private JwtTokenService $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        parent::__construct();
        $this->jwtTokenService = $jwtTokenService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate a long-lived JWT token for local/dev usage.')
            ->addOption(
                'subject',
                null,
                InputOption::VALUE_OPTIONAL,
                'Subject (sub) claim of the token',
                'dev-user'
            )
            ->addOption(
                'ttl',
                null,
                InputOption::VALUE_OPTIONAL,
                'Token time-to-live in seconds',
                '31536000' // 1 year
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subject = (string) $input->getOption('subject');
        $ttl     = (int) $input->getOption('ttl');

        $now = time();

        $claims = [
            'sub' => $subject,
            'iat' => $now,
            'exp' => $now + $ttl,
        ];

        $token = $this->jwtTokenService->encode($claims);

        $output->writeln('Generated JWT token:');
        $output->writeln($token);
        $output->writeln('');
        $output->writeln('You can put it in your .env.local as:');
        $output->writeln(sprintf('API_TOKEN=%s', $token));

        return Command::SUCCESS;
    }
}
