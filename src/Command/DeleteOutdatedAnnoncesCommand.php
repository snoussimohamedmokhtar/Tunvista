<?php

namespace App\Command;

use App\Repository\AnnonceRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteOutdatedAnnoncesCommand extends Command
{
protected static $defaultName = 'app:delete-outdated-annonces';

private $annonceRepository;

public function __construct(AnnonceRepository $annonceRepository)
{
parent::__construct();
$this->annonceRepository = $annonceRepository;
}

protected function configure()
{
$this->setDescription('Deletes outdated annonces.');
}

protected function execute(InputInterface $input, OutputInterface $output)
{
// Delete outdated annonces
$this->annonceRepository->deleteExpiredAnnouncements();

$output->writeln('Outdated annonces have been deleted.');

return Command::SUCCESS;
}
}
