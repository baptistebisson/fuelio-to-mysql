<?php

namespace App\Command;

use App\Entity\Cost;
use App\Entity\CostCategory;
use App\Entity\Data;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync')]
class FuelioToMysqlCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Sync fuelio data with MYSQL database')
            ->addOption(
                'folder', null, InputOption::VALUE_REQUIRED,
                'Folder name containing fuelio CSV files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pdo = new \PDO($_ENV['MYSQL_DSN'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD']);

        $folder = $input->getOption('folder');

        if (!file_exists($folder)) {
            $output->writeln("<error>Folder '$folder' not found</error>");
        }

        // Looking for csv files
        $files = [];
        foreach (scandir($folder) as $file) {
            if (preg_match_all('/vehicle-\d+-sync\.csv/m', $file, $matches)) {
                $split = explode('-', $file);
                $files[$split[1]] = $folder.'/'.$file;
            }
        }

        $output->writeln('Found '.count($files).' files');

        foreach ($files as $vehicle => $file) {
            $output->writeln('Processing '.$file);

            /**
             * @var Data[] $data
             */
            $data = [];
            /**
             * @var CostCategory[] $costCategories
             */
            $costCategories = [];
            /**
             * @var Cost[] $costs
             */
            $costs = [];

            // Opening file
            if (($handle = fopen($file, 'r')) !== false) {
                $getData = false;
                $getCosts = false;
                $getCostCategories = false;

                // Looping over each rows
                while (($line = fgetcsv($handle)) !== false) {
                    if (str_starts_with($line[0], '##')) {
                        // We are at the end of a section, need to stop getting data
                        $getData = false;
                        $getCosts = false;
                        $getCostCategories = false;
                    }

                    if (!$getData && isset($line[0]) && 'Data' === $line[0]) {
                        $getData = true;
                        continue;
                    }

                    if (str_contains($line[0], '## CostCategories')) {
                        $getCostCategories = true;
                        continue;
                    }
                    if ('CostTitle' === $line[0]) {
                        $getCosts = true;
                        continue;
                    }

                    if ($getData) {
                        $dataItem = new Data();
                        $dataItem->setVehicleId($vehicle);
                        $dataItem->setDate(new \DateTime($line[0]));
                        $dataItem->setOdo((int) $line[1]);
                        $dataItem->setFuel((float) $line[2]);
                        $dataItem->setPrice((float) $line[4] ?? null);
                        $dataItem->setVolumePrice((float) $line[13] ?? null);
                        $dataItem->setNotes('' !== $line[9] ? $line[9] : null);
                        $dataItem->setAverage((float) $line[5]);
                        $dataItem->setCity('' !== $line[8] ? $line[8] : null);
                        $data[] = $dataItem;
                    }

                    if ($getCostCategories) {
                        $costCategory = new CostCategory();
                        $costCategory->setId((int) $line[0]);
                        $costCategory->setName($line[1]);
                        $costCategories[] = $costCategory;
                    }

                    if ($getCosts) {
                        $cost = new Cost();
                        $cost->setVehicleId($vehicle);
                        $cost->setDate(new \DateTime($line[1]));
                        $cost->setTitle($line[0]);
                        $cost->setNotes('' !== $line[4] ? $line[4] : null);
                        $cost->setOdo('' !== $line[2] ? (int) $line[2] : null);
                        $cost->setCost((float) $line[5]);
                        $cost->setCostCategory((int) $line[3]);
                        $costs[] = $cost;
                    }
                }

                // Inserting data to database
                $output->writeln('Found a total of '.count($costCategories).' data');
                foreach ($data as $datum) {
                    $sth = $pdo->prepare(
                        'INSERT INTO consumption (vehicle_id, date, odo, fuel, price, volume_price, notes, average, city)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE key UPDATE odo=?, fuel=?, price=?, volume_price=?, notes=?, average=?, city=?'
                    );
                    try {
                        $sth->execute(
                            [
                                $datum->getVehicleId(),
                                $datum->getDate()->format('Y-m-d H:i:s'),
                                $datum->getOdo(),
                                $datum->getFuel(),
                                $datum->getPrice(),
                                $datum->getVolumePrice(),
                                $datum->getNotes(),
                                $datum->getAverage(),
                                $datum->getCity(),
                                $datum->getOdo(),
                                $datum->getFuel(),
                                $datum->getPrice(),
                                $datum->getVolumePrice(),
                                $datum->getNotes(),
                                $datum->getAverage(),
                                $datum->getCity(),
                            ]
                        );
                    } catch (\Exception $exception) {
                        $output->writeln("<error>$exception</error>");
                    }
                }

                $output->writeln('Found a total of '.count($costCategories).' categories');
                foreach ($costCategories as $costCategory) {
                    $sth = $pdo->prepare('INSERT INTO cost_category (id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name=?');
                    try {
                        $sth->execute([$costCategory->getId(), $costCategory->getName(), $costCategory->getName()]);
                    } catch (\Exception $exception) {
                        $output->writeln("<error>$exception</error>");
                    }
                }

                $output->writeln('Found a total of '.count($costs).' costs');
                foreach ($costs as $cost) {
                    $sth = $pdo->prepare(
                        'INSERT INTO cost (vehicle_id, date, title, odo, notes, cost, cost_category)
VALUES (?, ?, ?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE title=?, odo=?, notes=?, cost=?, cost_category=?'
                    );
                    try {
                        $sth->execute(
                            [
                                $cost->getVehicleId(),
                                $cost->getDate()->format('Y-m-d H:i:s'),
                                $cost->getTitle(),
                                $cost->getOdo(),
                                $cost->getNotes(),
                                $cost->getCost(),
                                $cost->getCostCategory(),
                                $cost->getTitle(),
                                $cost->getOdo(),
                                $cost->getNotes(),
                                $cost->getCost(),
                                $cost->getCostCategory()]
                        );
                    } catch (\Exception $exception) {
                        $output->writeln("<error>$exception</error>");
                    }
                }
            } else {
                $output->writeln("<error>Cannot open '$file'</error>");
            }
        }

        $output->writeln('<info>Done!</info>');

        return Command::SUCCESS;
    }
}
