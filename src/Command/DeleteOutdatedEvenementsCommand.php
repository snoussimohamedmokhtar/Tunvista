<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOutdatedEvenementsCommand extends Command
{
    protected static $defaultName = 'app:delete-outdated-evenements';

    private $evenementRepository;

    public function __construct(EvenementRepository $evenementRepository)
    {
        parent::__construct();
        $this->evenementRepository = $evenementRepository;
    }

    protected function configure()
    {
        $this->setDescription('Deletes outdated evenements.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
// Delete outdated annonces
        $this->evenementRepository->deleteExpiredEvents();

        $output->writeln('Outdated evenements have been deleted.');

        return Command::SUCCESS;
    }
}
