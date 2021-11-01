<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Console\Command;

use Exception;
use InvalidArgumentException;
use Magento\Framework\App\Filesystem\DirectoryList as AppDirectoryList;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Design\Theme\ListInterface;
use Magento\Framework\View\Design\Theme\ThemeList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Elgentos\PrismicIO\Model\RouteFactory;
use Elgentos\PrismicIO\Model\Route\StoreFactory as RouteStoreFactory;

class Scaffold extends Command
{
    public const CUSTOM_TYPE_ARGUMENT = 'custom-type',
        REPLACE_NAME                  = '{type}';

    public ThemeList $themeList;

    /** @var File */
    public File $io;

    /** @var Reader */
    public Reader $moduleReader;

    /** @var DirectoryList */
    public DirectoryList $directoryList;

    /** @var RouteFactory */
    public RouteFactory $routeFactory;

    /** @var RouteStoreFactory */
    public RouteStoreFactory $routeStoreFactory;

    /** @var OutputInterface */
    protected OutputInterface $output;

    /** @var InputInterface */
    protected InputInterface $input;

    /** @var bool|mixed|string|null */
    protected $theme;

    /** @var string */
    protected string $customType;

    /** @var string */
    protected string $stubDir;

    public function __construct(
        ThemeList $themeList,
        DirectoryList $directoryList,
        File $io,
        Reader $moduleReader,
        RouteFactory $routeFactory,
        RouteStoreFactory $routeStoreFactory,
        string $name = null
    ) {
        parent::__construct($name);
        $this->themeList         = $themeList;
        $this->io                = $io;
        $this->moduleReader      = $moduleReader;
        $this->directoryList     = $directoryList;
        $this->routeFactory      = $routeFactory;
        $this->routeStoreFactory = $routeStoreFactory;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->input      = $input;
        $this->output     = $output;
        $this->customType = $input->getArgument(self::CUSTOM_TYPE_ARGUMENT);
        $this->stubDir    = $this->moduleReader
                ->getModuleDir('', 'Elgentos_PrismicIO') . '/stubs/';

        $this->askThemeCode();
        $this->copyLayoutStubs();
        $this->copyTemplateStubs();
        $this->createPrismicRoute();
        $this->showPrismicJson();

        return Cli::RETURN_SUCCESS;
    }

    protected function configure()
    {
        $this->setName('prismic:scaffold');
        $this->setDescription('Scaffold a custom type for Prismic');
        $this->setDefinition(
            [
                new InputArgument(
                    self::CUSTOM_TYPE_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Prismic custom type'
                ),
            ]
        );
        parent::configure();
    }

    private function askThemeCode(): void
    {
        $choices = [];
        $themes  = $this->themeList->getColumnValues('code');

        foreach ($themes as $key => $theme) {
            $choices[$key + 1] = $theme;
        }

        $question = new ChoiceQuestion(
            '<question>Please select a theme:</question>',
            $choices
        );
        $question->setValidator(
            function ($typeInput) use ($themes) {
                if (!isset($themes[$typeInput - 1])) {
                    throw new InvalidArgumentException('Invalid theme');
                }

                return $themes[$typeInput - 1];
            }
        );

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $this->theme    = $questionHelper
            ->ask($this->input, $this->output, $question);
    }

    private function copyLayoutStubs(): void
    {
        try {
            $this->copyStubs('layout', '*.xml.stub');
        } catch (Exception $e) {
            $this->output->writeln('Could not copy layouts; ' . $e->getMessage());
        }
    }

    private function copyTemplateStubs(): void
    {
        try {
            $this->copyStubs('templates', '*.phtml.stub');
        } catch (Exception $e) {
            $this->output->writeln('Could not copy templates; ' . $e->getMessage());
        }
    }

    private function copyStubs(string $type, string $fileTypeMask): void
    {
        $destinationDir = $this->directoryList->getPath(AppDirectoryList::APP) .
            '/design/frontend/' . $this->theme . '/Elgentos_PrismicIO/' . $type . '/';

        foreach (glob($this->stubDir . 'default/' . $type . '/' . $fileTypeMask) as $file) {
            $destinationFilename = str_replace(
                static::REPLACE_NAME,
                $this->customType,
                substr(basename($file), 0, -5)
            );

            $this->io->mkdir($destinationDir, 0770, true);
            $this->io->cp($file, $destinationDir . $destinationFilename);
            $this->output->writeln('Copied ' . $destinationFilename . ' into ' . $destinationDir);
            $this->replaceTypeWithinFile($destinationDir . $destinationFilename);
        }
    }

    private function replaceTypeWithinFile(string $file): void
    {
        file_put_contents(
            $file,
            str_replace(static::REPLACE_NAME, $this->customType, file_get_contents($file))
        );
    }

    private function createPrismicRoute(): void
    {
        $defaultRoutePrefix  = '/' . $this->customType;
        $routePrefixQuestion = new Question(
            'Route prefix: [' . $defaultRoutePrefix . '] ',
            $defaultRoutePrefix
        );
        $defaultStoreId      = 1;
        $storeIdQuestion     = new Question(
            'Store ID: [' . $defaultStoreId . '] ',
            $defaultStoreId
        );

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $routePrefix    = $questionHelper->ask(
            $this->input,
            $this->output,
            $routePrefixQuestion
        );
        $storeId        = (int) $questionHelper->ask(
            $this->input,
            $this->output,
            $storeIdQuestion
        );
        $routePrefix    = '/' . trim($routePrefix, '/');
        $route          = $this->routeFactory->create();
        $route->setData(
            [
                'title' => $this->customType,
                'content_type' => $this->customType,
                'route' => $routePrefix,
                'status' => 1
            ]
        );

        try {
            $route->save();
        } catch (Exception $e) {
            $this->output->writeln('Could not save route; ' . $e->getMessage());
        }

        $routeStore = $this->routeStoreFactory->create();
        $routeStore->setData(
            [
                'route_id' => $route->getId(),
                'store_id' => $storeId
            ]
        );

        try {
            $routeStore->save();
            $this->output->writeln('Route ' . $routePrefix . ' created for store 1');
        } catch (Exception $e) {
            $this->output->writeln('Could not save route store; ' . $e->getMessage());
        }
    }

    private function showPrismicJson(): void
    {
        $this->output->writeln(
            'Create a new custom type called \'' . $this->customType .
            '\' in Prismic and paste the following JSON into the JSON editor field'
        );
        $this->output->writeln('');

        $this->output->writeln(
            file_get_contents($this->stubDir . 'default/json/json.stub')
        );
    }
}
