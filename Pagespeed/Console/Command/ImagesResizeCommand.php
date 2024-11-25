<?php
declare(strict_types=1);

namespace Swissup\Pagespeed\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Swissup\Pagespeed\Service\ImageResize;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Magento\Framework\ObjectManagerInterface;

class ImagesResizeCommand extends \Symfony\Component\Console\Command\Command
{
    const DEFAULT_LIMIT = 100000;

    /**
     *
     * @var integer
     */
    private $limit = self::DEFAULT_LIMIT;

    /**
     * @var ImageResize
     */
    private $resize;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Swissup\Pagespeed\Helper\Config
     */
    private $configHelper;

    /**
     * @var \Swissup\Pagespeed\Service\CheckImageOptimizerExisting
     */
    private $checker;

    /**
     * @param State $appState
     * @param ImageResize $resize
     * @param ObjectManagerInterface $objectManager
     * @param \Swissup\Pagespeed\Service\CheckImageOptimizerExisting $checker
     */
    public function __construct(
        State $appState,
        ImageResize $resize,
        ObjectManagerInterface $objectManager,
        \Swissup\Pagespeed\Service\CheckImageOptimizerExisting $checker,
        \Swissup\Pagespeed\Helper\Config $configHelper
    ) {
        $this->resize = $resize;
        $this->appState = $appState;
        $this->objectManager = $objectManager;
        $this->checker = $checker;
        $this->configHelper = $configHelper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $targetDirs = $this->configHelper->getResizeCommandTargetDirs();
        $targetDirs[] = 'pub/static/frontend';
        $targetDirs = implode('|', $targetDirs);

        $this->setName('swissup:pagespeed:images:optimize')
            ->addOption(
                'limit',
                'l',
                InputArgument::OPTIONAL,
                'limit --limit=10 (default: 100 000)',
                self::DEFAULT_LIMIT
            )
            ->addOption(
                'filename',
                'f',
                InputArgument::OPTIONAL,
                'filename filter --filename=1.png'
            )
            ->addOption(
                'with-custom',
                null,
                InputOption::VALUE_OPTIONAL,
                'If set, the task will resize images from pub/media/(' . $targetDirs . ')/*',
                true
            )
            ->addOption(
                'with-product',
                null,
                InputOption::VALUE_OPTIONAL,
                'If set, the task will resize catalog images',
                true
            )
            ->setDescription('Optimize images and create their responsive variants 0.5x 0.75x 2x 3x.')
            ->setAliases([
                'swissup:pagespeed:images:resize',
                'pagespeed:images:resize',
                'pagespeed:images:optimize',
                'images:resize',
                'images:optimize'
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configHelper = $this->configHelper;
        if (!$configHelper->isEnable()) {
            $message = "Please check if 'Pagespeed' module is enabled in store backend";
            $output->writeln("<error><fg=red;options=bold>Error: {$message}</></error>");
            $output->writeln("<info>Checking: bin/magento config:show pagespeed/main/enable</info>");
            $output->writeln("<info>Enabling: bin/magento config:set pagespeed/main/enable 1</info>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if (!$configHelper->isImageOptimizerEnable()) {
            $message = 'Please check if \'Catalog Image Optimisation\' is enabled in store backend';
            $output->writeln("<error><fg=red;options=bold>Error: {$message}</></error>");
            $output->writeln("<info>Checking: bin/magento config:show pagespeed/image/optimize_enable</info>");
            $output->writeln("<info>Enabling: bin/magento config:set pagespeed/image/optimize_enable 1</info>");
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        if (!$this->checker->isAllExecutable()) {

            foreach ($this->checker->getMessages() as $errorMessage) {
                $output->writeln("<error><fg=red>Error: {$errorMessage}</></error>");
            }

            if ($this->checker->isAllNotExecutable()) {
                $message = $this->checker->getMainMessage();
                $output->writeln("<error><fg=red;options=bold>Error: {$message}</></error>");
                return \Magento\Framework\Console\Cli::RETURN_FAILURE;
            }
        }

        if (!$configHelper->isWebPEnable()) {
            $output->writeln("<info><fg=yellow;options=bold>Warning: Webp support is disabled in store backend</></info>");
            $output->writeln("<info>Checking: bin/magento config:show pagespeed/image/optimize_webp_enable</info>");
            $output->writeln("<info>Enabling: bin/magento config:set pagespeed/image/optimize_webp_enable 1</info>");
        }

        $limit = (int) $input->getOption('limit');
        if ($limit > 0) {
            $this->limit = $limit;
        }
        $this->resize->setLimit($this->limit);

        $filename = (string) $input->getOption('filename');
        if (!empty($filename)) {
            $this->resize->setFilenameFilter($filename);
        }

        try {
            $this->appState->setAreaCode(Area::AREA_GLOBAL);

            $generators = [];
            $withCustom = $input->getOption('with-custom');
            $withCustom = $withCustom === "false" ? false : ((bool) $withCustom || $withCustom === null);
            if ($withCustom) {
                $generators['Custom images resized successfully'] = $this->resize->resizeCustomImages();
            }
            $withProduct = $input->getOption('with-product');
            $withProduct =  $withProduct === "false" ? false : ((bool) $withProduct || $withProduct === null);
            if ($withProduct) {
                $generators['Product responsive images resized successfully'] = $this->resize->resizeAllProductImages();
            }

            foreach ($generators as $label => $generator) {
                /** @var ProgressBar $progress */
                $progress = $this->objectManager->create(ProgressBar::class, [
                    'output' => $output,
                    'max' => $generator->current()
                ]);
                $progress->setFormat(
                    "%current%/%max% [%bar%] %percent:3s%% %elapsed% %memory:6s% \t| <info>%message%</info>"
                );

                if ($output->getVerbosity() !== OutputInterface::VERBOSITY_NORMAL) {
                    $progress->setOverwrite(false);
                }

                $progress->setMessage('');
                // update every 100 iterations
                $progress->setRedrawFrequency(10);
                $progress->setBarWidth(50);
                $progress->start();

                for (; $generator->valid(); $generator->next()) {
                    $progress->setMessage($generator->key());
                    $progress->advance();
                }
                $progress->finish();

                $output->write(PHP_EOL);
                $output->writeln("<info>{$label}</info>");
            }
        } catch (\Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
            // $output->writeln("<error>{$e->getTraceAsString()}</error>");
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        $output->write(PHP_EOL);
        $output->writeln("<info>Done!</info>");

        return 0;
    }
}
