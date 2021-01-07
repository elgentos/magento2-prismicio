<?php

/**
 * Copyright - elgentos ecommerce solutions (https://elgentos.nl)
 */

declare(strict_types=1);

namespace Elgentos\PrismicIO\Console\Command;

use Exception;
use InvalidArgumentException;
use Magento\Framework\App\Filesystem\DirectoryList as AppDirectoryList;
use Magento\Framework\Exception\FileSystemException;
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
    private const CUSTOM_TYPE_ARGUMENT = 'custom-type',
        REPLACE_NAME                   = '{type}';

    /** @var ThemeList */
    public $themeList;

    /** @var File */
    public $io;

    /** @var Reader */
    public $moduleReader;

    /** @var DirectoryList */
    public $directoryList;

    /** @var RouteFactory */
    public $routeFactory;

    /** @var RouteStoreFactory */
    public $routeStoreFactory;

    /** @var OutputInterface */
    protected $output;

    /** @var InputInterface */
    protected $input;

    /** @var bool|mixed|string|null */
    protected $theme;

    /** @var string */
    protected $customType;

    /** @var string */
    protected $stubDir;

    /**
     * Scaffold constructor.
     *
     * @param ListInterface     $themeList
     * @param DirectoryList     $directoryList
     * @param File              $io
     * @param Reader            $moduleReader
     * @param RouteFactory      $routeFactory
     * @param RouteStoreFactory $routeStoreFactory
     * @param string|null       $name
     */
    public function __construct(
        ListInterface $themeList,
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

    /**
     * Execute the scaffold command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
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
    }

    /**
     * Configure the command.
     *
     * @return void
     */
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

    /**
     * Add step to ask for the theme to work with.
     *
     * @return void
     */
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

    /**
     * Copy the layout stubs.
     *
     * @return void
     */
    private function copyLayoutStubs(): void
    {
        try {
            $this->copyStubs('layout', '*.xml.stub');
        } catch (Exception $e) {
            $this->output->writeln('Could not copy layouts; ' . $e->getMessage());
        }
    }

    /**
     * Copy the template stubs.
     *
     * @return void
     */
    private function copyTemplateStubs(): void
    {
        try {
            $this->copyStubs('templates', '*.phtml.stub');
        } catch (Exception $e) {
            $this->output->writeln('Could not copy templates; ' . $e->getMessage());
        }
    }

    /**
     * Copy the actual stubs from the given type to the set theme.
     *
     * @param string $type
     * @param string $fileTypeMask
     *
     * @return void
     */
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

    /**
     * Replace the type placeholder with the type
     *
     * @param string $file
     *
     * @return void
     */
    private function replaceTypeWithinFile(string $file): void
    {
        file_put_contents(
            $file,
            str_replace(static::REPLACE_NAME, $this->customType, file_get_contents($file))
        );
    }

    /**
     * Create a new Prismic route in Magento.
     *
     * @return void
     */
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

    /**
     * Display the JSON for the subs for Prismic.
     *
     * @return void
     */
    private function showPrismicJson()
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
