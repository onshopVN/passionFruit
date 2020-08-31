<?php 
namespace App\Core\Command;

use App\Core\Entity\Plugin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Core\Repository\PluginRepository;

class PluginInstallCommand extends Command
{
    const ARG_CODE = 'code';

    /**
     * @inherit
     * @var string 
     */
    protected static $defaultName = 'core:plugin:install';

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var PluginRepository
     */
    protected $pluginR;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param PluginRepository $pluginRepository
     * @param string $name
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        PluginRepository $pluginRepository,
        string $name = null
    ) {
        $this->parameterBag = $parameterBag;   
        $this->pluginR = $pluginRepository; 
        parent::__construct($name);
    }

    /**
     * @inheritdoc
     * @return void 
     */
    protected function configure()
    {
        $this->setDescription('Install a plugin');
        $this->addArgument(self::ARG_CODE, InputArgument::REQUIRED, 'The code of plugin');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $code = $input->getArgument(self::ARG_CODE);
        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $dir = $projectDir.'/src/'.$code;
        if (!is_dir($dir)) {
            $output->writeln('The src directory does not contain ' . $code . ' source code');
            return Command::FAILURE;
        }
        $composer = $dir.'/composer.json';
        if (!is_file($composer)) {
            $output->writeln('The '.$code.' does not contains composer.json');
            return Command::FAILURE;
        }

        $metadata = json_decode(file_get_contents($composer), true);
        if (!isset($metadata['name']) || !isset($metadata['description']) || !isset($metadata['extra']['code']) || !isset($metadata['extra']['priority'])) {
            $output->writeln('The '.$code.' is invalid plugin format');
            return Command::FAILURE;
        }

        $plugin = $this->pluginR->findOneBy(['code' => $metadata['extra']['code']]);
        if ($plugin) {
            $output->writeln('The '.$code.' has already installed');
            return Command::FAILURE;
        }
        $plugin = $this->pluginR->newEntity();
        $plugin = $this->pluginR->copyValues($plugin, [
            'name' => $metadata['name'],
            'description' => $metadata['description'],
            'code' => $metadata['extra']['code'],
            'priority' => $metadata['extra']['priority'],
            'status' => Plugin::STATUS_DISABLE,
            'required' => isset($metadata['require']['skeleton']) ? json_encode($metadata['require']['skeleton']) : json_encode([])
        ]);
        $this->pluginR->save($plugin);

        return Command::SUCCESS;
    }
}
