<?php


namespace AppBundle\Command;

use AppBundle\Service\ApproveAllStepService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\ProgressBar;
use AppBundle\Service\WineryApproveAllStepService;
use Psr\Log\LoggerInterface;


class CheckWineryStep extends ContainerAwareCommand
{

    private $approveAllStepService;
    private $logger;


    public function __construct(ApproveAllStepService $approveAllStepService, LoggerInterface $logger)
    {
        parent::__construct();
        $this->approveAllStepService = $approveAllStepService;
        $this->logger = $logger;

    }


    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:check-step')
            // the short description shown while running "php bin/console list"
            ->setDescription('Check of steps date approve.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to approve steps by default.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();
        $wineryRepo = $this->getContainer()->get("AppBundle\Repository\WineryRepository");
        $winery = $wineryRepo->findAll();

        foreach ($winery as $item) {
            $this->logger->info('Updated Winery: - START -');
            $updatedWinery = $this->approveAllStepService->approveAllStep($item);
            $em->persist($updatedWinery);
            $this->logger->info('Updated Winery: ' . $updatedWinery);
            $this->logger->info('Updated Winery: - DONE -');
        }

        $em->flush();

//        $output->writeln('<info>DONE</info>');

    }
}